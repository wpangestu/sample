<?php

namespace App\Http\Controllers;

use DataTables;
use App\Models\BaseService;
use Illuminate\Http\Request;
use App\Models\CategoryService;
use Illuminate\Support\Facades\Storage;

class BaseServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        // $baseServices = BaseService::all();

        if ($request->ajax()) {
            $data = BaseService::latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('category_service_id',function($row){
                        return $row->service_category->name??'-';
                    })
                    ->addColumn('price', function($row){
                        return "Rp ".number_format($row->price,0,',','.');
                    })
                    ->addColumn('price_receive', function($row){
                        return "Rp ".number_format($row->price_receive,0,',','.');
                    })
                    ->addColumn('guarantee', function($row){
                        return $row->guarantee==true?'Ya':'Tidak';
                    })
                    ->addColumn('updated_at', function($row){
                        return $row->created_at->format('d-m-Y')."<br>".$row->created_at->format('H:i:s');
                    })
                    ->addColumn('action', function($row){
                        
                        $btn = '
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="'.route('base_services.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit"><i class="fa fa-edit"></i> Edit</a></li>
                            <li class="dropdown-item"><a href="'.route('base_services.show',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                            <li class="dropdown-item"><a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('base_services.destroy',$row->id).'" data-original-title="Delete" class="btn_delete"><i class="fa fa-times-circle"></i> Delete</a></li>
                        </ul>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['action','status','updated_at'])
                    ->make(true);
        }

        return view('base_service.index');
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
        return view('base_service.create',compact('categoryServices'));
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
            'category_service_id' => 'required',
            'name' => 'required',
            'price' => 'required',
            'guarantee' => 'required',
            'price_receive' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            //code...
            $data = [
                "category_service_id" => $request->input('category_service_id'),
                "name" => $request->input('name'),
                'price' => $request->input('price'),
                'price_receive' => $request->input('price_receive'),
                'description' => $request->input('description'),
                'guarentee' => $request->input('guarantee')==1?TRUE:FALSE,
                'long_guarantee' => $request->input('long_guarantee'),
                'is_active' => true
            ];

            $insert = BaseService::create($data);

            if ($request->hasFile('image')) {

                $uploadFolder = 'service/image';
                $photo = $request->file('image');
                $photo_path = $photo->store($uploadFolder, 'public');
    
                $insert->image = Storage::disk('public')->url($photo_path);
                $insert->save();
            }
        
            toast('Data berhasil ditambah','success');
                        
            return redirect()->route('base_services.index');

        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());

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
        $data = BaseService::find($id);
        return view('base_service.detail',compact('data'));
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
        $data = BaseService::find($id);
        return view('base_service.edit',compact('categoryServices','data'));
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
            'category_service_id' => 'required',
            'name' => 'required',
            'price' => 'required',
            'guarantee' => 'required',
            'price_receive' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            "category_service_id" => $request->input('category_service_id'),
            "name" => $request->input('name'),
            'price' => $request->input('price'),
            'price_receive' => $request->input('price_receive'),
            'description' => $request->input('description'),
            'guarentee' => $request->input('guarantee')==1?TRUE:FALSE,
            'long_guarantee' => $request->input('long_guarantee'),
            'is_active' => true
        ];

        try {
            
            $update = BaseService::find($id);
            $update->update($data);

            if ($request->hasFile('image')) {

                $uploadFolder = 'service/image';
                $photo = $request->file('image');
                $photo_path = $photo->store($uploadFolder, 'public');
    
                $update->image = Storage::disk('public')->url($photo_path);
                $update->save();
            }

            toast('Data berhasil diubah','success');

            return redirect()->route('base_services.index');

        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
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

            $delete = BaseService::find($id);
            $delete->delete();

            toast('Data berhasil dihapus','success');

            return redirect()->back();

        } catch (\Throwable $th) {
            dd($th->getMessage());
            //throw $th;
        }
    }
}
