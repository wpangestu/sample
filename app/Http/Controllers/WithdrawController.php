<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use App\Models\HistoryBalance;
use App\Models\Notification;
use App\Models\UserBankAccount;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    //
    public function index_engineer(Request $request)
    {
        $data = Withdraw::whereHas('user',function($query){
            $query->Role('teknisi')->where('verified',true);
        })->get();
        if ($request->ajax()) {
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at',function($row){
                        return $row->created_at->format('d/m/Y')."<br>".$row->created_at->format('H:i:s');
                    })
                    ->addColumn('name',function($row){
                        return $row->user->name;
                    })
                    ->addColumn('amount',function($row){
                        return rupiah( $row->amount);
                    })
                    ->addColumn('status', function($row){
                        if($row->status == "pending"){
                            $status = "<span class='badge badge-warning'>Menunggu Konfirmasi</span>";
                        }elseif($row->status == "success"){
                            $status = "<span class='badge badge-success'>Sukses</span>";
                        }elseif($row->status == "decline"){
                            $status = "<span class='badge badge-danger'>Dibatalkan</span>";
                        }
                        return $status;
                    })
                    ->addColumn('action', function($row){
                        
                        $btn = '
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="'.route('withdraw.technician.show',$row->id).'" data-toggle="tooltip" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                        </ul>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['action','status','created_at'])
                    ->make(true);
        }

        return view('withdraw.index_engineer');
    }

    public function index_customer(Request $request)
    {
        $data = Withdraw::whereHas('user',function($query){
            $query->Role('user');
        })->get();

        if ($request->ajax()) {
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at',function($row){
                        return $row->created_at->format('d/m/Y')."<br>".$row->created_at->format('H:i:s');
                    })
                    ->addColumn('name',function($row){
                        return $row->user->name;
                    })
                    ->addColumn('amount',function($row){
                        return rupiah( $row->amount);
                    })
                    ->addColumn('status', function($row){
                        if($row->status == "pending"){
                            $status = "<span class='badge badge-warning'>Menunggu Konfirmasi</span>";
                        }elseif($row->status == "success"){
                            $status = "<span class='badge badge-success'>Sukses</span>";
                        }elseif($row->status == "decline"){
                            $status = "<span class='badge badge-danger'>Dibatalkan</span>";
                        }
                        return $status;
                    })
                    ->addColumn('action', function($row){
                        
                        $btn = '
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="'.route('withdraw.customer.show',$row->id).'" data-toggle="tooltip" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                        </ul>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['action','status','created_at'])
                    ->make(true);
        }

        return view('withdraw.index_user');
    }

    public function show_engineer($id)
    {
        $data = Withdraw::where('id',$id)
                        ->whereHas('user',function($query) use ($id){
                            $query->Role('teknisi')
                                    ->where('verified',true);
                        })->first();

        $bank_accounts = UserBankAccount::where('user_id',$data->user_id)->get();

        if(is_null($data)){
            toast('Terjadi kesalahan','error');
            return redirect()->back();
        }
        return view('withdraw.show_engineer',compact('data','bank_accounts'));
    }

    public function show_customer($id)
    {
        $data = Withdraw::where('id',$id)
                        ->whereHas('user',function($query) use ($id){
                            $query->Role('user');
                        })->first();
        $bank_accounts = UserBankAccount::where('user_id',$id)->get();
        if(is_null($data)){
            toast('Terjadi kesalahan','error');
            return redirect()->back();
        }
        return view('withdraw.show_customer',compact('data','bank_accounts'));
    }

    public function confirm_accept($id){
        try {
            //code...

            DB::beginTransaction();

            $withdraw = Withdraw::find($id);
            $withdraw->status = "success";
            $withdraw->verified_by = auth()->user()->id;
            $withdraw->verified_at = date('Y-m-d H:i:s');
            $withdraw->save();

            HistoryBalance::create([
                "user_id" => $withdraw->user_id,
                "amount" => -($withdraw->amount),
                "description" => "Penarikan Saldo oleh teknisi",
                "created_by" => auth()->user()->id
            ]);

            $title = "Penarikan saldo sebesar ".rupiah($withdraw->amount);
            $subtitle = "Berhasil dikirim ke nomor rekening kamu";
            Notification::create([
                "title" => $title,
                "type" => "wallet",
                "user_id" => $withdraw->user_id,
                "read" => false,
                "id_data" => $withdraw->id,
                "subtitle" => $subtitle,
                "subtitle_color" => "#00FF00"
            ]);

            $token[] = $withdraw->user->fcm_token;
            fcm()->to($token)
                    ->priority('high')
                    ->timeToLive(60)
                    ->notification([
                        'title' => $title,
                        'body' => $subtitle,
                    ]);            

            DB::commit();            

            toast('Konfirmasi Withdraw Sukses','success');

            return redirect()->back();

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            dd($th->getMessage());
        }
    }

    public function confirm_decline($id){

        try {
            //code...

            DB::beginTransaction();

            $withdraw = Withdraw::find($id);
            $withdraw->status = "decline";
            $withdraw->verified_by = auth()->user()->id;
            $withdraw->verified_at = date('Y-m-d H:i:s');
            $withdraw->user->balance = $withdraw->user->balance+
            $withdraw->save();

            $title = "Penarikan saldo sebesar ".rupiah($withdraw->amount);
            $subtitle = "Ditolak oleh admin";
            Notification::create([
                "title" => $title,
                "type" => "wallet",
                "user_id" => $withdraw->user_id,
                "read" => false,
                "id_data" => $withdraw->id,
                "subtitle" => $subtitle,
                "subtitle_color" => "#FF0000"
            ]);

            $token[] = $withdraw->user->fcm_token;
            fcm()->to($token)
                    ->priority('high')
                    ->timeToLive(60)
                    ->notification([
                        'title' => $title,
                        'body' => $subtitle,
                    ]);            
            DB::commit();            

            toast('Konfirmasi Withdraw Ditolak','success');

            return redirect()->back();

        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
        }
    }


}
