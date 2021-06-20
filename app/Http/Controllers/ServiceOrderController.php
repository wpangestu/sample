<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Models\Service;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
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

                        $btn = '
                        <button type="button" class="btn btn-xs btn-secondary dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="'.route('service_order.show',$row->id).'" data-original-title="Edit" class="edit"><i class="fa fa-edit"></i> Detail</a></li>
                            ';
                            if($row->order_status ==="pending"){
                                $btn .= '<li class="dropdown-item"><a href="'.route('payment.order.edit',$row->payment_id??'!#').'" data-original-title="Buat Pembayaran"><i class="fa fa-money-bill"></i> Buat Pembayaran</a></li>';
                            }
                        $btn .= '</ul>';
                        // <li class="dropdown-item"><a href="'.route('services.show',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                        // <li class="dropdown-item"><a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('service.delete.ajax',$row->id).'" data-original-title="Delete" class="btn_delete"><i class="fa fa-times-circle"></i> Delete</a></li>
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
                            return $btn;
                    })
                    ->addColumn('order_status',function($row){
                        if($row->order_status==null){
                            return "-";
                        }elseif($row->order_status=="waiting_payment"){
                            return '<badge class="badge badge-warning">Menunggu Pembayaran</badge>';
                        }elseif ($row->order_status=="payment_success") {
                            return '<badge class="badge badge-info">Pembayaran Sukses</badge>';
                        }elseif ($row->order_status=="waiting_order") {
                            return '<badge class="badge bg-indigo">Mencari Teknisi</badge>';
                        }
                        elseif($row->order_status=="accepted") {
                            return '<badge class="badge bg-teal">Diterima Teknisi</badge>';
                        }
                        elseif($row->order_status=="processed") {
                            return '<badge class="badge bg-lime">Diproses</badge>';
                        }
                        elseif($row->order_status=="extend") {
                            return '<badge class="badge bg-olive">Extend</badge>';
                        }
                        elseif($row->order_status=="canceled") {
                            return '<badge class="badge badge-danger">Dibatalkan</badge>';
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
            'payment_gateway'=>'required'
        ]);

        // dd($request->all());

        try {
            //code...
            DB::beginTransaction();

            $service_input = $request->service_id[0];
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

            $list_order = [];
            $list_order[] = $order->id;

            $data_payment = [
                "customer_id" => $order->customer->id,
                "amount" => $total_payment,
                "paymentid" => "P".uniqid(),
                "type" => $request->input('payment_gateway'),
                "orders" => $list_order,
            ];

            $payment = Payment::create($data_payment);

            $order->payment_id = $payment->id;
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

    public function process_decline_order(Request $request)
    {

        // dd('dadsa');
        $orderid = $request->get('orderid');
        $status = $request->get('status');

        if(empty($orderid) || empty($status)){
            return redirect()->back()->with('error','Terjadi kesalahan');
        }

        try {
            //code...
            $order = Order::where('order_number',$orderid)->first();

            // dd($status);

            $order->order_status = $status;
            $order->save();
            
            return redirect()->back()->with('success','Order berhasil diupdate');
            
        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
            return redirect()->back()->with('error','Terjadi kesalahan data');
        }
    }

    public function show($id)
    {
        //
        $order = Order::find($id);
        if(is_null($order)){
            return redirect()->back()->with('error','Data tidak ditemukan');
        }
        if($order->order_status==null){
            $status = "-";
        }elseif($order->order_status=="waiting_payment"){
            $status = '<badge class="badge badge-warning">Menunggu Pembayaran</badge>';
        }elseif ($order->order_status=="payment_success") {
            $status = '<badge class="badge badge-info">Pembayaran Sukses</badge>';
        }elseif ($order->order_status=="waiting_order") {
            $status =  '<badge class="badge badge-info">Cari Teknisi</badge>';
        }
        elseif($order->order_status=="accepted") {
            $status =  '<badge class="badge badge-primary">Diterima Teknisi</badge>';
        }
        elseif($order->order_status=="processed") {
            $status =  '<badge class="badge badge-indigo">Diproses</badge>';
        }
        elseif($order->order_status=="extend") {
            $status =  '<badge class="badge badge-navy">Extend</badge>';
        }
        elseif($order->order_status=="canceled") {
            $status =  '<badge class="badge badge-danger">Dibatalkan</badge>';
        }
        elseif($order->order_status=="done") {
            $status =  '<badge class="badge badge-success">Selesai</badge>';
        }
        return view('service_order.detail',compact('order','status'));
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

    public function update_waiting_order($order_id){
        try {
            //code...
            $order = Order::find($order_id);
            $order->order_status = "waiting_order";
            $order->save();

            fcm()->toTopic("technician")
            ->priority('high')
            ->timeToLive(0)
            ->data([
                'click_action' => "FLUTTER_NOTIFICATION_CLICK",
                'main_click_action' => "OPEN_INCOMING_ORDER",
                'action_data' => [
                    "task" => "SHOW_INCOMING_ORDER",
                    "order_id" => $order->order_number,
                    "duration" => 30
                ]
            ])
            ->notification([
                'title' => 'New Order',
                'body' => ucfirst($order->order_type)." Order",
            ])
            ->send();
            
            return response()->json(["success"=>true,"message"=>"update successfully"]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["success"=>false,"message"=>$th->getMessage()]);
        }
    }
}
