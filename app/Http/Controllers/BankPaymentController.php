<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankPayment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BankPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if ($request->ajax()) {
            $data = BankPayment::latest('updated_at')->with('bank')->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created_at', function($row){   
                        return $row->created_at->format('d/m/Y')."<br>".$row->created_at->format('H:i:s');
                    })
                    ->addColumn('updated_at', function($row){   
                        return $row->updated_at->format('d/m/Y')."<br>".$row->updated_at->format('H:i:s');
                    })
                    ->addColumn('name', function($row){   
                        return $row->bank->name;
                    })
                    ->addColumn('is_active', function($row){   
                        if($row->is_active){
                            return "<span class='badge badge-success'>Aktif</span>";
                        }else{
                            return "<span class='badge badge-secondary'>Non Aktif</span>";
                        }
                    })
                    ->addColumn('action', function($row){   
                        $btn = '
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="'.route('bank_payments.edit',$row->id).'" data-toggle="tooltip" data-original-title="Ubah"><i class="fa fa-edit"></i> Ubah</a></li>
                        </ul>
                        ';
                        // <li class="dropdown-item"><a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('banks.destroy',$row->id).'" data-original-title="Delete" class="btn_delete"><i class="fa fa-times"></i> Hapus</a></li>
                        return $btn;
                            // $btn = '<a href="#" data-toggle="tooltip" data-balance="'.$row->balance.'" data-name="'.$row->name.'" data-id="'.$row->userid.'" data-original-title="Edit" class="btn btn-primary btn-sm btn_change">Ubah Saldo</a>';    
                            // $btn .= '<a href="#" data-toggle="tooltip" data-balance="'.$row->balance.'" data-name="'.$row->name.'" data-id="'.$row->userid.'" data-original-title="Edit" class="btn btn-primary btn-sm btn_change">Ubah Saldo</a>';
                    })
                    ->rawColumns(['action','logo','created_at','updated_at','is_active'])
                    ->make(true);
        }

        return view('bank_payment.index');        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $banks = Bank::where('is_active',true)->get();
        return view('bank_payment.create',compact('banks'));
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
        $request->validate([
            'bank' => 'required',
            'account_number' => 'required',
        ]);

        try {
            //code...

            BankPayment::create([
                'bank_id' => $request->input('bank'),
                'account_number' => $request->input('account_number')
            ]);

            toast('Data berhasil ditambah','success');
                        
            return redirect()->route('bank_payments.index');


        } catch (\Throwable $th) {
            //throw $th;
            toast('Maaf terjadi kesalahan','error');
                        
            return redirect()->back()->withInput();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $bankPayment = BankPayment::find($id);
        $banks = Bank::where('is_active',true)->get();
        return view('bank_payment.edit',compact('bankPayment','banks'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'bank' => 'required',
            'account_number' => 'required',
            'inputStatus' => 'required'
        ]);

        try {
            //code...
            $bankPayment = BankPayment::find($id);
            $bankPayment->update([
                'bank_id' => $request->input('bank'),
                'account_number' => $request->input('account_number'),
                'is_active' => $request->input('inputStatus')=="on"?true:false
            ]);

            toast('Data berhasil diubah','success');
                        
            return redirect()->route('bank_payments.index');

        } catch (\Throwable $th) {
            //throw $th;
            toast('Terjadi kesalahan','error');
                        
            return redirect()->route('bank_payments.index');
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
