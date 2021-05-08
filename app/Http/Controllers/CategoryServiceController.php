<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoryService;
use DataTables;
use Illuminate\Support\Facades\Storage;

class CategoryServiceController extends Controller
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
            $data = CategoryService::latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status', function($row){
                        if($row->status==1){
                            $status = '<span class="badge badge-success">aktif</span>';
                        }else{
                            $status = '<span class="badge badge-secondary">non aktif</span>';
                        }
                        return $status;
                    })
                    ->addColumn('icon', function($row){
                        return '<img src="'.$row->icon.'" height="120px">';
                    })
                    ->addColumn('action', function($row){
   
                            $btn = '<a href="'.route('service_category.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm">Edit</a>';
                            $btn .= ' <a href="'.route('service_category.show',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-warning btn-sm">Detail</a>';
   
                            $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('service_category.delete.ajax',$row->id).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action','status','icon'])
                    ->make(true);
        }

        return view('category_service.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('category_service.create');
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
            'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            'slug' => 'required|unique:category_services'
        ]);

        $data = [
            "name" => $request->input('name'),
            "status" => $request->input('active')??0,
            'slug' => $request->input('slug')
        ];
 
        $insert = CategoryService::create($data);

        if ($request->hasFile('icon')) {

            $uploadFolder = 'admin/service_category';
            $photo = $request->file('icon');
            $photo_path = $photo->store($uploadFolder,'public');

            $insert->icon = Storage::disk('public')->url($photo_path);
            $insert->save();
        }

        if($insert){

            $causer = auth()->user();

            activity('add_category_service')->performedOn($insert)
                        ->causedBy($causer)
                        ->log('Pengguna melakukan penambahan kategori jasa');

            return redirect()->route('service_category.index')
                        ->with('success','Data berhasil ditambahkan');
        }else{
            return redirect()->route('service_category.index')
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
        $categoryService = CategoryService::find($id);
        return view('category_service.detail',compact('categoryService'));
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
        $data = CategoryService::find($id);
        return view('category_service.edit',compact('data'));
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
            'slug' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            "name" => $request->input('name'),
            "status" => $request->input('active')??0,
            "slug" => $request->input('slug')
        ];

        $service_category = CategoryService::find($id);
        $update = $service_category->update($data);

        if ($request->hasFile('icon')) {

            $uploadFolder = 'admin/service_category';
            $photo = $request->file('icon');
            $photo_path = $photo->store($uploadFolder,'public');

            $service_category->icon = Storage::disk('public')->url($photo_path);
            $service_category->save();
        }

        if($update){
            return redirect()->route('service_category.index')
                        ->with('success','Data berhasil diubah');            
        }else{
            return redirect()->route('service_category.index')
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
        $delete = CategoryService::find($id)->delete();

        return Response()->json($delete);
    }
}
