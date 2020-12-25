<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReviewService;
use App\Models\ServiceOrder;
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
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('service_id',function($row){
                        return $row->service_order->serviceorder_id??'-';
                    })
                    ->addColumn('engineer_id',function($row){
                        return $row->service_order->engineer->name??'-';
                    })
                    ->addColumn('user_id',function($row){
                        return $row->service_order->customer->name??'-';
                    })
                    ->addColumn('date',function($row){
                        return Carbon::parse($row->created_at)->format("d/m/Y H:i");
                        // return $row->service_order->engineer->name??'-';
                    })
                    ->addColumn('action', function($row){
   
                            $btn = '-';
                            // $btn = '<a href="'.route('services.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm">Edit</a>';

                            // $btn .= ' <a href="'.route('services.show',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="edit btn btn-warning btn-sm">Detail</a>';
   
                            // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('service.delete.ajax',$row->id).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('review_service.index',compact('reviewServices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $service_orders = ServiceOrder::all();
        return view('review_service.create',compact('service_orders'));
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
            'service_order_id' => 'required',
            'ratings' => 'required|integer|max:5',
        ]);

        $review = new ReviewService;
        $review->service_order_id = $request->input('service_order_id');
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
    public function update(Request $request, $id)
    {
        //
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
