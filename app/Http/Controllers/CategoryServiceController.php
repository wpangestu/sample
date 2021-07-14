<?php

namespace App\Http\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Models\CategoryService;
use Illuminate\Support\Facades\DB;
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

                        $btn = '
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="'.route('service_category.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit"><i class="fa fa-edit"></i> Edit</a></li>
                            <li class="dropdown-item"><a href="'.route('service_category.show',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                            <li class="dropdown-item"><a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('service_category.destroy',$row->id).'" data-original-title="Delete" class="btn_delete"><i class="fa fa-times-circle"></i> Delete</a></li>
                        </ul>';
   
                            // $btn = '<a href="'.route('service_category.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm">Edit</a>';
                            // $btn .= ' <a href="'.route('service_category.show',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-warning btn-sm">Detail</a>';
   
                            // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('service_category.delete.ajax',$row->id).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
    
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

        try {
            //code...
            $photo_path=null;
    
            if ($request->hasFile('icon')) {
    
                $uploadFolder = 'admin/service_category';
                $photo = $request->file('icon');
                $photo_path = $photo->store($uploadFolder,'public');
    
                $icon = Storage::disk('public')->url($photo_path);
            }
    
            $data = [
                "name" => $request->input('name'),
                "status" => $request->input('active')??0,
                'slug' => $request->input('slug'),
                'icon' => $icon
            ];

            CategoryService::create($data);

            toast('Data berhasil ditambah','success');

            return redirect()->route('service_category.index');

        } catch (\Throwable $th) {
            //throw $th;
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

        try {
            //code...

            DB::beginTransaction();

            $service_category = CategoryService::find($id);
            $service_category->update($data);

            if ($request->hasFile('icon')) {
    
                $uploadFolder = 'admin/service_category';
                $photo = $request->file('icon');
                $photo_path = $photo->store($uploadFolder,'public');
    
                $service_category->icon = Storage::disk('public')->url($photo_path);
                $service_category->save();
            }

            DB::commit();

            toast('Data berhasil diubah','success');

            return redirect()->route('service_category.index');            
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
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
        try {
            //code...
            $delete = CategoryService::find($id)->delete();

            toast('Data berhasil dihapus','success');

            return redirect()->route('service_category.index');
            
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('service_category.index')
                        ->with('error','Opps, Terjadi kesalahan.');
        }

    }
}
