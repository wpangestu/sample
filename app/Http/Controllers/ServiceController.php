<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\CategoryService;
use DataTables;

class ServiceController extends Controller
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
            $data = Service::latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('service_category_id',function($row){
                        return $row->service_category->name??'-';
                    })
                    ->addColumn('action', function($row){
   
                            $btn = '<a href="'.route('services.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm">Edit</a>';
   
                            $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('service.delete.ajax',$row->id).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('service.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categoryServices = CategoryService::where('status',1)->get();
        return view('service.create',compact('categoryServices'));
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
            'name' => 'required',
            'category_service_id' => 'required|integer',
            'price' => 'required|integer'
        ]);

        $insert = Service::create($request->all());

        if($insert){
            return redirect()->route('services.index')
                        ->with('success','Data berhasil ditambahkan');
        }else{
            return redirect()->route('services.index')
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
        $categoryServices = CategoryService::where('status',1)->get();
        $service = Service::find($id);
        return view('service.edit',compact('categoryServices','service'));
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
            'name' => 'required',
            'category_service_id' => 'required|integer',
            'price' => 'required|integer'
        ]);

        $update = Service::find($id)->update($request->all());

        if($update){
            return redirect()->route('services.index')
                        ->with('success','Data berhasil diubah');
        }else{
            return redirect()->route('services.index')
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
        $delete = Service::find($id)->delete();

        return Response()->json($delete);        
    }
}
