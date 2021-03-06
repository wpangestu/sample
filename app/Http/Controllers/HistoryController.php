<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use DataTables;
use Illuminate\Database\Eloquent\Builder;


class HistoryController extends Controller
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
            $data = Activity::whereHas('causer',function(Builder $query){
                $query->whereHas('roles',function(Builder $query){
                    $query->whereNotIn('name',['teknisi']);
                });
            })->latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('name',function($row){
                        return $row->causer->name??'-';
                    })
                    ->addColumn('created_at',function($row){
                        return $row->created_at->format('m-d-Y')."<br>".$row->created_at->format('H:i:s');
                    })
                    ->addColumn('role',function($row){
                        return $roles = $row->causer->getRoleNames()->implode(', ');
                    })
                    ->addColumn('action', function($row){
                    
                    $btn = '
                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-dice-three"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li class="dropdown-item"><a href="'.route('history.index.detail',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                    </ul>
                    ';

                            // $btn = '<a href="'.route('services.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm">Edit</a>';

                            // $btn .= ' <a href="'.route('services.show',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="edit btn btn-warning btn-sm">Detail</a>';
   
                            // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('service.delete.ajax',$row->id).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action','status','created_at'])
                    ->make(true);
        }

        return view('history.index');        
    }

    public function index_teknisi(Request $request)
    {
        if ($request->ajax()) {
            $data = Activity::whereHas('causer',function(Builder $query){
                $query->whereHas('roles',function(Builder $query){
                    $query->whereIn('name',['teknisi']);
                });
            })->latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('name',function($row){
                        return $row->causer->name??'-';
                    })
                    ->addColumn('created_at',function($row){
                        return $row->created_at->format('m-d-Y')."<br>".$row->created_at->format('H:i:s');
                    })
                    ->addColumn('role',function($row){
                        return $roles = $row->causer->getRoleNames()->implode(', ');
                    })
                    ->addColumn('action', function($row){
                    
                    $btn = '
                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown">
                        Aksi
                    </button>
                    <ul class="dropdown-menu">
                        <li class="dropdown-item"><a href="'.route('history.index.detail',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                    </ul>
                    ';

                            // $btn = '<a href="'.route('services.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm">Edit</a>';

                            // $btn .= ' <a href="'.route('services.show',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="edit btn btn-warning btn-sm">Detail</a>';
   
                            // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('service.delete.ajax',$row->id).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action','status','created_at'])
                    ->make(true);
        }

        return view('history.index_engineer');                
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
    public function show($id)
    {
        //
        try {
            //code...
            $data = Activity::find($id);
            return view('history.detail',compact('data'));            
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
