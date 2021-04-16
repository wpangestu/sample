<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\HistoryBalance;


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
                        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="'.route('services.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit"><i class="fa fa-plus-circle"></i> Tambah Saldo</a></li>
                            <li class="dropdown-item"><a href="'.route('services.show',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="detail"><i class="fa fa-minus-circle"></i> Kurangi Saldo</a></li>
                            <li class="dropdown-item"><a href="'.route('balance.show',$row->id).'" title="Detail" data-toggle="tooltip"><i class="fa fa-info-circle"></i> Detail</a></li>
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
                    ->addColumn('action', function($row){
   
                            $btn = '<a href="#" data-toggle="tooltip" data-balance="'.$row->balance.'" data-name="'.$row->name.'" data-id="'.$row->userid.'" data-type="engineer" data-original-title="Edit" class="btn btn-info btn-sm btn_change">Ubah Saldo</a>';
                            // $btn .= ' <a href="'.route('customer.show',$row->userid).'" data-toggle="tooltip"  data-id="'.$row->userid.'" data-original-title="Detail" class="edit btn btn-warning btn-sm">Detail</a>';
                            // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('customer.delete.ajax',$row->userid).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
    
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
            $historyBalance = HistoryBalance::where('user_id',$user->id)->get();

            return view('balance.detail',compact('user'));

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function showEngineer($user_id)
    {
        //
        try {
            
            $user = User::Role('engineer')->find($user_id);
            $historyBalance = HistoryBalance::where('user_id',$user->id)->get();

            return view('balance.detail',compact('user'));

        } catch (\Throwable $th) {
            //throw $th;
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
    public function destroy($id)
    {
        //
    }
}
