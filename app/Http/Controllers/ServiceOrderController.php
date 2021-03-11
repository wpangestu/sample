<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Models\Service;
use App\Models\Order;
use App\Models\OrderDetail;
use DataTables;
use App\Models\CategoryService;
use Carbon\Carbon;
use DB;

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
            $data = Order::latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){

                        // $btn = '<a href="'.route('service_order.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->userid.'" data-original-title="Edit" class="edit btn btn-info btn-sm">Edit</a>';
   
                        // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('service_order.delete.ajax',$row->id).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';

                        // return $btn;

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
                    ->addColumn('order_status',function($row){
                        if($row->order_status==null){
                            return "-";
                        }elseif($row->order_status=="pending"){
                            return '<badge class="badge badge-warning">Menunggu Pembayaran</badge>';
                        }elseif ($row->order_status=="waiting_order") {
                            return '<badge class="badge badge-info">Menunggu Konfirmasi Teknisi</badge>';
                        }
                        elseif($row->order_status=="denied") {
                            return '<badge class="badge badge-danger">Ditolak</badge>';
                        }
                        elseif($row->order_status=="processed") {
                            return '<badge class="badge badge-primary">Diproses</badge>';
                        }
                        elseif($row->order_status=="take_away") {
                            return '<badge class="badge badge-info">Take Away</badge>';
                        }
                        elseif($row->order_status=="canceled") {
                            return '<badge class="badge badge-secondary">Dibatalkan</badge>';
                        }
                        elseif($row->order_status=="done") {
                            return '<badge class="badge badge-success">Selesai</badge>';
                        }
                    })
                    ->addColumn('created_at', function($row){
                        return Carbon::parse($row->created_at)->format("d/m/Y H:i");
                    })
                    ->addColumn('customer_id', function($row){
                        return $row->customer->name;
                    })
                    ->addColumn('engineer_id', function($row){
                        return $row->engineer->name??'-';
                    })
                    ->rawColumns(['action','order_status'])
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
        $customers = User::Role('user')->where('is_active',1)->get();
        $engineers = User::Role('teknisi')->where('verified',true)->get();
        $category_services = CategoryService::where('status',true)->get();
        $services = Service::all();
        $status = ['pending','process','finish'];
        return view('service_order.create',compact('customers','engineers','services','status','category_services'));
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
            'service_id' => 'required',
        ]);

        try {
            //code...
            DB::beginTransaction();

            $service_input = $request->service_id;
            $service_input = explode("_",$service_input);
    
            $order_id = uniqid();
    
            $service = Service::find($service_input[0]);
            $shipping = $request->shipping;
            $data = [
                "order_number" => $order_id,
                "order_type" => "reguler",
                "order_status" => "pending",
                "is_take_away" => false,
                "customer_id" => $request->customer_id,
                "engineer_id" => $service->engineer_id,
                "shipping" => $shipping,
                "note" => $request->description
            ];
            
            $order = Order::create($data);
            
            $data_service = [
                "order_id" => $order->id,
                "name" => $service->name,
                "qty" => 1,
                "price" => $service->price
            ];

            $total_payment = $service->price+$shipping;
    
            $order_detail = OrderDetail::create($data_service);
    
            $address = [];
            if($request->has('latitude') && $request->has('longitude')){
                $address = [
                    "name" => $request->map_address,
                    "lat" => $request->latitude,
                    "lng" => $request->longitude,
                ];
            }

            $order->total_payment = $total_payment;
            $order->total_payment_receive = $total_payment;
            $order->address = $address;
            $order->save();

            DB::commit();

            return redirect()->route('service_order.index')
                        ->with('success','Data berhasil ditambahkan');

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th->getMessage());
            return redirect()->route('service_order.index')
            ->with('error','Opps, Terjadi kesalahan.');

        }

        // $insert = ServiceOrder::create($data);

        // if($insert){
        //     return redirect()->route('service_order.index')
        //                 ->with('success','Data berhasil ditambahkan');
        // }else{
        //     return redirect()->route('service_order.index')
        //                 ->with('error','Opps, Terjadi kesalahan.');
        // }
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
        $status = ['pending','process','finish'];
        return view('service_order.edit',compact('customers','engineers','services','service_order','status'));
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
            'status' => 'required',
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
