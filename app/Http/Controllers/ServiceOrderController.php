<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Models\Service;
use DataTables;
use Carbon\Carbon;

class ServiceOrderController extends Controller
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
            $data = ServiceOrder::latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){

                        $btn = '<a href="'.route('service_order.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->userid.'" data-original-title="Edit" class="edit btn btn-info btn-sm">Edit</a>';
   
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('service_order.delete.ajax',$row->id).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';

                        return $btn;

                            // $btn = '<button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                            //             <span class="sr-only">Toggle Dropdown</span>
                            //                 Aksi 
                            //             <div class="dropdown-menu" role="menu">
                            //                 <a class="dropdown-item" href="'.route('service_order.edit',$row->id).'"><i class="fa fa-edit"></i> Ubah</a>
                            //                 <a class="dropdown-item btn_delete" data-url="'.route('service_order.delete.ajax',$row->id).'" href="'.route('dashboard').'"><i class="fa fa-times"></i> Hapus</a>
                            //             </div>
                            //         </button>';
                            // return $btn;
                    })
                    ->addColumn('status','-')
                    ->addColumn('created_at', function($row){
                        return Carbon::parse($row->created_at)->format("d/m/Y H:i");
                    })
                    ->addColumn('customer_id', function($row){
                        return $row->customer->name;
                    })
                    ->addColumn('engineer_id', function($row){
                        return $row->engineer->name;
                    })
                    ->addColumn('service_id', function($row){
                        return $row->service->name;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('service_order.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $customers = User::Role('user')->get();
        $engineers = User::Role('teknisi')->get();
        $services = Service::all();
        return view('service_order.create',compact('customers','engineers','services'));
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
            'customer_id' => 'required|integer',
            'engineer_id' => 'required|integer',
            'service_id' => 'required|integer',
        ]);

        $data = [
            "customer_id" => $request->customer_id,
            "engineer_id" => $request->engineer_id,
            "service_id" => $request->service_id,
            "serviceorder_id" => uniqid()
        ];

        $insert = ServiceOrder::create($data);

        if($insert){
            return redirect()->route('service_order.index')
                        ->with('success','Data berhasil ditambahkan');
        }else{
            return redirect()->route('service_order.index')
                        ->with('error','Opps, Terjadi kesalahan.');
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
        $service_order = ServiceOrder::find($id);
        $customers = User::Role('user')->get();
        $engineers = User::Role('teknisi')->get();
        $services = Service::all();
        return view('service_order.edit',compact('customers','engineers','services','service_order'));
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
            'customer_id' => 'required|integer',
            'engineer_id' => 'required|integer',
            'service_id' => 'required|integer',
        ]);

        $update = ServiceOrder::find($id)->update($request->all());

        if($update){
            return redirect()->route('service_order.index')
                        ->with('success','Data berhasil diubah');
        }else{
            return redirect()->route('service_ordeSSSr.index')
                        ->with('error','Opps, Terjadi kesalahan.');
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
        $delete = ServiceOrder::find($id)->delete();

        return Response()->json($delete);
    }
}
