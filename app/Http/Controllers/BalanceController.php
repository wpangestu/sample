<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\HistoryBalance;
use Illuminate\Support\Facades\DB;


class BalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     //

    // }

    public function customer(Request $request)
    {
        //
        if ($request->ajax()) {
            $data = User::latest()->Role('user')->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('balance', function($row){
                        return rupiah($row->balance);
                    })
                    ->addColumn('action', function($row){   
                        $btn = '
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="#" data-toggle="tooltip"  data-id="'.$row->id.'" data-name="'.$row->name.'" data-userid="'.$row->userid.'" data-original-title="Edit" class="edit btn_add_balance"><i class="fa fa-plus-circle"></i> Tambah Saldo</a></li>
                            <li class="dropdown-item"><a href="#" data-toggle="tooltip"  data-id="'.$row->id.'" data-name="'.$row->name.'" data-userid="'.$row->userid.'" data-original-title="Edit" class="edit btn_min_balance"><i class="fa fa-minus-circle"></i> Kurangi Saldo</a></li>
                            <li class="dropdown-item"><a href="'.route('balance.customer.show',$row->id).'" title="Detail" data-toggle="tooltip"><i class="fa fa-info-circle"></i> Detail</a></li>
                        </ul>
                        ';
                        return $btn;
                            // $btn = '<a href="#" data-toggle="tooltip" data-balance="'.$row->balance.'" data-name="'.$row->name.'" data-id="'.$row->userid.'" data-original-title="Edit" class="btn btn-primary btn-sm btn_change">Ubah Saldo</a>';    
                            // $btn .= '<a href="#" data-toggle="tooltip" data-balance="'.$row->balance.'" data-name="'.$row->name.'" data-id="'.$row->userid.'" data-original-title="Edit" class="btn btn-primary btn-sm btn_change">Ubah Saldo</a>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('balance.index_customer');
    }

    public function engineer(Request $request)
    {
        if ($request->ajax()) {
            $data = User::Role('teknisi')->where('verified',true)->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('balance', function($row){
                        return rupiah($row->balance);
                    })
                    ->addColumn('action', function($row){
                        $btn = '
                            <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                                Aksi
                            </button>
                            <ul class="dropdown-menu">
                                <li class="dropdown-item"><a href="#" data-toggle="tooltip"  data-id="'.$row->id.'" data-name="'.$row->name.'" data-userid="'.$row->userid.'" data-original-title="Edit" class="edit btn_add_balance"><i class="fa fa-plus-circle"></i> Tambah Saldo</a></li>
                                <li class="dropdown-item"><a href="#" data-toggle="tooltip"  data-id="'.$row->id.'" data-name="'.$row->name.'" data-userid="'.$row->userid.'" data-original-title="Edit" class="edit btn_min_balance"><i class="fa fa-minus-circle"></i> Kurangi Saldo</a></li>
                                <li class="dropdown-item"><a href="'.route('balance.engineer.show',$row->id).'" title="Detail" data-toggle="tooltip"><i class="fa fa-info-circle"></i> Detail</a></li>
                            </ul>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('balance.index_engineer');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function storeAddBalance(Request $request)
    {
        // dd('cek');
        try {
            //code...
            $id = $request->input('id');
            $amount = $request->input('amount');
            $description = $request->input('description');

            DB::beginTransaction();

            $user = User::find($id);
            $user->balance = $user->balance+$amount;
            $user->save();

            HistoryBalance::create([
                "user_id" => $user->id,
                "amount" => $amount,
                "description" => $description,
                "created_by" => auth()->user()->id
            ]);

            $causer = auth()->user();
            $atribut = ['attributes' => [
                "userid" => $user->userid,
                "amount" => $amount
            ]];
    
            activity('balance')->performedOn($user)
                        ->causedBy($causer)
                        ->withProperties($atribut)
                        ->log('Pengguna melakukan penambahan saldo');
            
            DB::commit();

            toast('Saldo berhasil ditambah','success');

            return redirect()->back();
            
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th->getMessage());
            toast('Terjadi kesalahan','error');
        }
    }

    public function storeMinBalance(Request $request)
    {
        // dd('cek');
        try {
            //code...
            $id = $request->input('id');
            $amount = $request->input('amount');
            $description = $request->input('description');

            DB::beginTransaction();

            $user = User::find($id);
            $user->balance = $user->balance-$amount;
            $user->save();

            HistoryBalance::create([
                "user_id" => $user->id,
                "amount" => -$amount,
                "description" => $description,
                "created_by" => auth()->user()->id
            ]);

            $causer = auth()->user();
            $atribut = ['attributes' => [
                "userid" => $user->userid,
                "amount" => $amount
            ]];
    
            activity('balance')->performedOn($user)
                        ->causedBy($causer)
                        ->withProperties($atribut)
                        ->log('Pengguna melakukan pengurangan saldo');
            
            DB::commit();

            toast('Saldo berhasil dikurangi','success');

            return redirect()->back();
            
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th->getMessage());
            toast('Terjadi kesalahan','error');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($user_id)
    {
        //
        try {
            
            $user = User::Role('user')->find($user_id);
            $historyBalance = HistoryBalance::where('user_id',$user->id)->latest()->get();

            return view('balance.detail',compact('user','historyBalance'));

        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
        }
    }

    public function showEngineer($user_id)
    {
        //
        try {
            
            $user = User::Role('teknisi')->find($user_id);
            $historyBalance = HistoryBalance::where('user_id',$user->id)->latest()->get();

            return view('balance.detail',compact('user','historyBalance'));

        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
        }
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function updateHistoryBalance(Request $request)
    {

        $request->validate([
            'id' => 'required|integer',
            'description' => 'required'
        ]);

        try {
            //code...
            $id = $request->input('id');
            $description = $request->input('description');

            $history_balance = HistoryBalance::find($id);
            $history_balance->description = $description;
            $history_balance->save();

            toast('Data berhasil di ubah','success');

            return redirect()->back();
            
        } catch (\Throwable $th) {
            //throw $th;
            // dd($th->getMessage());
            toast('Terjadi kesalahan','error');
            return redirect()->back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $request->validate([
            'balance' => 'required|integer'
        ]);

        $user = User::where('userid',$request->userid)->first();
        $old_balance_user = $user->balance;
        $user->balance = $request->balance;
        $update = $user->save();

        $causer = auth()->user();
        $atribut = [
            "attributes" => ["balance" => $user->balance],
            "old" => ["balance" => $old_balance_user]
        ];

        activity('update_balance')->performedOn($user)
                    ->causedBy($causer)
                    ->withProperties($atribut)
                    ->log('Pengguna melakukan pengubahan saldo');

        if($update){
            if($user->hasRole('user')){
                return redirect()->route('balance.customer.index')
                            ->with('success','Data berhasil diubah');
            }else{
                return redirect()->route('balance.engineer.index')
                            ->with('success','Data berhasil diubah');
            }
        }else{
            if($user->hasRole('user')){
                return redirect()->route('balance.customer.index')
                            ->with('error','Opps, Terjadi kesalahan.');
            }else{
                return redirect()->route('balance.engineer.index')
                            ->with('error','Opps, Terjadi kesalahan.');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
        try {
            //code...
            DB::beginTransaction();

            $history_balance = HistoryBalance::find($id);
            $amount = $history_balance->amount;

            $user = User::find($history_balance->user_id);
            $user->balance = $user->balance - $amount;
            $user->save();

            $history_balance->delete();

            DB::commit();

            toast('Data berhasil dihapus','success');

            return redirect()->back();

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            toast('Terjadi kesalahan','error');
            dd($th->getMessage());
        }
    }
}
