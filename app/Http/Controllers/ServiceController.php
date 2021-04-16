<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\User;
use App\Models\CategoryService;
use App\Models\Notification;
use DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
            $data = Service::where('status','active')->orWhere('status','non_active')->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('service_category_id',function($row){
                        return $row->service_category->name??'-';
                    })
                    ->addColumn('engineer_id',function($row){
                        return $row->engineer->name??'-';
                    })
                    ->addColumn('price', function($row){
                        return "Rp ".number_format($row->price,0,',','.');
                    })
                    ->addColumn('status',function($row){
                        if($row->status==="active"){
                            $status = "<span class='badge badge-success'>Active</span>";
                        }else{
                            $status = "<span class='badge badge-secondary'>Not Active</span>";
                        }
                        return $status;
                    })
                    ->addColumn('action', function($row){
                    
                    $btn = '
                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown">
                        Aksi
                    </button>
                    <ul class="dropdown-menu">
                        <li class="dropdown-item"><a href="'.route('services.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit"><i class="fa fa-edit"></i> Edit</a></li>
                        <li class="dropdown-item"><a href="'.route('services.show',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                        <li class="dropdown-item"><a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('service.delete.ajax',$row->id).'" data-original-title="Delete" class="btn_delete"><i class="fa fa-times-circle"></i> Delete</a></li>
                    </ul>
                    ';

                            // $btn = '<a href="'.route('services.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm">Edit</a>';

                            // $btn .= ' <a href="'.route('services.show',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="edit btn btn-warning btn-sm">Detail</a>';
   
                            // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('service.delete.ajax',$row->id).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action','status'])
                    ->make(true);
        }

        return view('service.index');
    }

    public function get_data_bycategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required'
        ]);

        try {
            //code...
            $category = $request->input('category_id');
            $services = Service::where('category_service_id',$category)
                                ->where('status','active')
                                ->with('engineer')    
                                ->get();
            return response()->json(["success"=>true,"data"=>$services]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["success"=>false,"data"=>[],"message"=>$th->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $engineers = User::Role('teknisi')->where('verified',true)->get();
        $categoryServices = CategoryService::where('status',1)->get();
        return view('service.create',compact('categoryServices','engineers'));
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
            'price' => 'required|integer',
            'engineer_id' => 'required|integer',
            'skill' => 'required',
            'sertification_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('sertification_image')) {

            $uploadFolder = 'teknisi/service/certificate';
            $photo = $request->file('sertification_image');
            $photo_path_sertificate = $photo->store($uploadFolder,'public');

            // $service->sertification_image = Storage::disk('public')->url($photo_path);
            // $service->save();
        }

        $data = [
            "name"                  => $request->name,
            "category_service_id"   => $request->category_service_id,
            "price"                 => $request->price,
            "engineer_id"           => $request->engineer_id,
            "skill"                 => $request->skill,
            "description"           => $request->description,
            'sertification_image'   => Storage::disk('public')->url($photo_path_sertificate)
        ];

        $service = Service::create($data);

        if ($request->hasFile('image')) {

            $uploadFolder = 'teknisi/service/images';
            $photo = $request->file('image');
            $photo_path = $photo->store($uploadFolder,'public');

            $service->image = Storage::disk('public')->url($photo_path);
            $service->save();
        }

        if($service){
            return redirect()->route('services.confirmation')
                        ->with('success','Data berhasil ditambahkan');
        }else{
            return redirect()->route('services.confirmation')
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
        // dd('dad');
        $service = Service::find($id);
        return view('service.detail',compact('service'));
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

    public function detail_confirmation($id){
        $service = Service::find($id);
        return view('service.detail_confirmation',compact('service'));
    }

    public function confirm_accept($id){

        try {
            //code...
            DB::beginTransaction();

            $service = Service::find($id);
            $service->status = 'active';
            $service->verified_by = auth()->user()->id;
            $service->verified_at = date("Y-m-d H:i:s");
            $service->save();

            Notification::create([
                "title" => "Jasa: ".$service->name,
                "type" => "service_info",
                "user_id" => $service->engineer_id,
                "service_id" => $service->id
            ]);
            
            $causer = auth()->user();
            activity('confirm_service')->performedOn($service)
                ->causedBy($causer)
                ->log('Pengguna melakukan konfirmasi ACC Jasa');

            DB::commit();

            return redirect()->route('services.index')
            ->with('success','Jasa Teknisi berhasil dikonfirmasi');

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th->getMessage());
        }
    }

    public function confirm_danied($id){

        try {
            //code...
            DB::beginTransaction();

            $service = Service::find($id);
            $service->status = 'danied';
            $service->verified_by = auth()->user()->id;
            $service->verified_at = date("Y-m-d H:i:s");
            $service->save();

            Notification::create([
                "title" => "Jasa: ".$service->name,
                "type" => "service_info",
                "user_id" => $service->engineer_id,
                "service_id" => $service->id
            ]);

            $causer = auth()->user();
            activity('confirm_service')->performedOn($service)
                ->causedBy($causer)
                ->log('Pengguna melakukan konfirmasi Tolak Jasa');

            DB::commit();

            return redirect()->route('services.confirmation')
                ->with('success','Jasa Teknisi berhasil ditolak');

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return dd($th->getMessage());
        }

    }

    public function confirmation(Request $request){
        // dd('ke');
        if ($request->ajax()) {
            $data = Service::where('status','review')->orWhere('status','danied')->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('service_category_id',function($row){
                        return $row->service_category->name??'-';
                    })
                    ->addColumn('price',function($row){
                        return rupiah($row->price);
                    })
                    ->addColumn('status',function($row){
                        if($row->status==="review"){
                            $status = "<span class='badge badge-warning'>Menunggu Kofirmasi</span>";
                        }else{
                            $status = "<span class='badge badge-danger'>Ditolak</span>";
                        }
                        return $status;
                    })
                    ->addColumn('action', function($row){
   
                            // $btn = '<a href="'.route('services.edit',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-info btn-sm">Edit</a>';

                            $btn = ' <a href="'.route('services.confirmation.detail',$row->id).'" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Detail" class="edit btn btn-warning btn-sm">Detail</a>';
   
                            // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-url="'.route('service.delete.ajax',$row->id).'" data-original-title="Delete" class="btn btn-danger btn-sm btn_delete">Delete</a>';
    
                            return $btn;
                    })
                    ->rawColumns(['action','status'])
                    ->make(true);
        }

        return view('service.confirmation');
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
            'price' => 'required|integer',
            'skill' => 'required',
            'sertification_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {

            $service = Service::find($id);

            $service->name = $request->name;
            $service->category_service_id = $request->category_service_id;
            $service->price = $request->price;
            $service->skill = $request->skill;
            $service->description = $request->description;
    
            if ($request->hasFile('sertification_image')) {
    
                $uploadFolder = 'teknisi/service/certificate';
                $photo = $request->file('sertification_image');
                $photo_path_sertificate = $photo->store($uploadFolder,'public');
    
                $service->sertification_image = Storage::disk('public')->url($photo_path_sertificate);
            }
    
            if ($request->hasFile('image')) {
    
                $uploadFolder = 'teknisi/service/images';
                $photo = $request->file('image');
                $photo_path = $photo->store($uploadFolder,'public');
    
                $service->image = Storage::disk('public')->url($photo_path);
            }
    
            $service->save();

            return redirect()->route('services.index')
                                ->with('success','Data berhasil diubah');

        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
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
