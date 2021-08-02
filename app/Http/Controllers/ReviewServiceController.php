<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReviewService;
use App\Models\ServiceOrder;
use App\Models\Order;
use DataTables;
use Carbon\carbon;

class ReviewServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $reviewServices = ReviewService::all();

        if ($request->ajax()) {
            $data = ReviewService::latest()->get();
            return Datatables::of($data->load('order.engineer','order.customer'))
                    ->addIndexColumn()
                    ->addColumn('service_id',function($row){
                        return $row->order_number_id??'-';
                    })
                    ->addColumn('engineer_id',function($row){
                        return $row->order->engineer->name??'-';
                    })
                    ->addColumn('user_id',function($row){
                        return $row->order->customer->name??'-';
                    })
                    ->addColumn('date',function($row){
                        return Carbon::parse($row->created_at)->format("d/m/Y")."<br>".$row->created_at->format('H:i:s');
                        // return $row->service_order->engineer->name??'-';
                    })
                    ->addColumn('action', function($row){
   
                        $btn = '
                        <button type="button" class="btn btn-xs btn-secondary dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                        <li class="dropdown-item"><a href="'.route('review_service.detail',$row->id).'" data-original-title="Detail" class="Detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                        ';
                        // <li class="dropdown-item"><a href="'.route('review_service.edit',$row->id).'" data-original-title="Edit" class="edit"><i class="fa fa-edit"></i> Ubah</a></li>
                        $btn .= '</ul>';
                        return $btn;

                    })
                    ->rawColumns(['action','date'])
                    ->make(true);
        }

        return view('review_service.index',compact('reviewServices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($orderid)
    {
        //
        // $service_orders = ServiceOrder::all();
        $order = Order::find($orderid);

        if(is_null($orderid)){
            return redirect()->back()->with('error','Terjadi kesalahan');
        }
        // dd($order);
        return view('review_service.create',compact('order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$orderid)
    {
        //

        $request->validate([
            'orderid' => 'required',
            'ratings' => 'required|integer|max:5',
        ]);

        $review = new ReviewService;
        $review->order_number_id = $orderid;
        $review->ratings = $request->input('ratings');
        $review->description = $request->input('description');
        $review->save();
        
        return redirect()->route('review_service.index')
            ->with('success','Data berhasil ditambahkan');

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
        $review = ReviewService::find($id);

        if(is_null($review)){
            return redirect()->back()->with('error','Terjadi kesalahan');
        }

        return view('review_service.detail',compact('review'));
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
        $review = ReviewService::find($id);

        if(is_null($review)){
            return redirect()->back()->with('error','Maaf terjadi kesalahan');
        }

        return view('review_service.edit',compact('review'));
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
            'orderid' => 'required',
            'ratings' => 'required|integer|max:5',
        ]);


        try {
            //code...
            $review = ReviewService::find($id);

            $review->ratings = $request->input('ratings');
            $review->description = $request->input('description');
            $review->save();

            return redirect()->route('review_service.index')->with('success','Data berhasil diubah');
            
        } catch (\Throwable $th) {
            return redirect()->route('review_service.index')->with('error','Maaf terjadi kesalahan');
            //throw $th;
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
