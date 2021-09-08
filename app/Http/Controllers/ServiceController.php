<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\User;
use App\Models\BaseService;
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
            $data = Service::where('status', 'active')->orWhere('status', 'non_active')->latest()->get();
            return Datatables::of($data->load('base_service','engineer','base_service.service_category'))
                ->addIndexColumn()
                ->addColumn('service_category_id', function ($row) {
                    return $row->base_service->service_category->name ?? '-';
                })
                ->addColumn('name', function ($row) {
                    return $row->base_service->name ?? '-';
                })
                ->addColumn('engineer_id', function ($row) {
                    return $row->engineer->name ?? '-';
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->updated_at->format('d-m-Y')."<br>".$row->updated_at->format('H:i:s');
                })
                ->addColumn('status', function ($row) {
                    if ($row->status === "active") {
                        $status = "<span class='badge badge-success'>Active</span>";
                    } else {
                        $status = "<span class='badge badge-secondary'>Not Active</span>";
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {

                    $btn = '
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="' . route('services.edit', $row->id) . '" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit"><i class="fa fa-edit"></i> Edit</a></li>
                            <li class="dropdown-item"><a href="' . route('services.show', $row->id) . '" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                            <li class="dropdown-item"><a href="javascript:void(0)" data-toggle="tooltip" data-url="' . route('service.delete.ajax', $row->id) . '" data-original-title="Delete" class="btn_delete"><i class="fa fa-times-circle"></i> Delete</a></li>
                        </ul>
                        ';
                    return $btn;
                })
                ->rawColumns(['action', 'status','updated_at'])
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
            $services = Service::where('category_service_id', $category)
                ->where('status', 'active')
                ->with('engineer')
                ->get();
            return response()->json(["success" => true, "data" => $services]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["success" => false, "data" => [], "message" => $th->getMessage()]);
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
        $engineers = User::Role('teknisi')->where('verified', true)->get();
        $categoryServices = CategoryService::where('status', 1)->get();
        return view('service.create', compact('categoryServices', 'engineers'));
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
            'base_service_id' => 'required|integer', 
            'engineer_id' => 'required|integer',
            'skill' => 'required',
            'sertification_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $sertificate_image = null;
        if ($request->hasFile('sertification_image')) {
            $uploadFolder = 'teknisi/service/certificate';
            $photo = $request->file('sertification_image');
            $photo_path_sertificate = $photo->store($uploadFolder, 'public');
            $sertificate_image = Storage::disk('public')->url($photo_path_sertificate);
        }

        $data = [
            "engineer_id"           => $request->engineer_id,
            "skill"                 => $request->skill,
            'base_service_id'       => $request->base_service_id,
            'sertification_image'   => $sertificate_image
        ];

        $service = Service::create($data);

        if ($service) {
            return redirect()->route('services.confirmation')
                ->with('success', 'Data berhasil ditambahkan');
        } else {
            return redirect()->route('services.confirmation')
                ->with('error', 'Opps, Terjadi kesalahan.');
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
        return view('service.detail', compact('service'));
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
        $categoryServices = CategoryService::where('status', 1)->get();
        $service = Service::find($id);
        // dd($categoryServices);
        return view('service.edit', compact('categoryServices', 'service'));
    }

    public function detail_confirmation($id)
    {
        $service = Service::find($id);
        return view('service.detail_confirmation', compact('service'));
    }

    public function confirm_accept($id)
    {

        try {
            //code...
            DB::beginTransaction();

            activity()->disableLogging();
            $service = Service::find($id);
            $service->status = 'active';
            $service->verified_by = auth()->user()->id;
            $service->verified_at = date("Y-m-d H:i:s");
            $service->save();
            activity()->enableLogging();

            $title = "Jasa: " . $service->base_service->name;
            $body = "Telah aktif";
            Notification::create([
                "title" => $title,
                "type" => "service_info",
                "user_id" => $service->engineer_id,
                "id_data" => $service->id,
                "service_status" => "active",
                "subtitle"=> $body,
                "subtitle_color" => "#27AE60"
            ]);

            $technician = User::find($service->engineer_id);
            $token[] = $technician->fcm_token; 
            fcm()->to($token)
                    ->priority('high')
                    ->timeToLive(60)
                    ->notification([
                        'title' => $title,
                        'body' => $body,
                    ])
                    ->send();
            $properties = [
                'old'=>['status'=>'review'],
                'attributes'=>['status'=>'active']
            ];
            $causer = auth()->user();
            activity('confirm_service')->performedOn($service)
                ->causedBy($causer)
                ->withProperties($properties)
                ->log('Pengguna melakukan konfirmasi ACC Jasa #:subject.id');

            DB::commit();

            return redirect()->route('services.index')
                ->with('success', 'Jasa Teknisi berhasil dikonfirmasi');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th->getMessage());
        }
    }

    public function confirm_danied($id)
    {

        try {
            //code...
            DB::beginTransaction();
            activity()->disableLogging();
            $service = Service::find($id);
            $service->status = 'danied';
            $service->verified_by = auth()->user()->id;
            $service->verified_at = date("Y-m-d H:i:s");
            $service->save();
            activity()->enableLogging();

            $title = "Jasa: " . $service->base_service->name;
            $body = "Telah ditolak";
            Notification::create([
                "title" => $title,
                "type" => "service_info",
                "user_id" => $service->engineer_id,
                "id_data" => $service->id,
                "service_status" => "danied",
                "subtitle"=> $body,
                "subtitle_color" => "#FF0000"
            ]);

            $technician = User::find($service->engineer_id);
            $token[] = $technician->fcm_token; 
            fcm()->to($token)
                    ->priority('high')
                    ->timeToLive(60)
                    ->notification([
                        'title' => $title,
                        'body' => $body,
                    ])
                    ->send();


            $causer = auth()->user();
            activity('confirm_service')->performedOn($service)
                ->causedBy($causer)
                ->log('Pengguna melakukan konfirmasi Tolak Jasa');

            DB::commit();

            return redirect()->route('services.confirmation')
                ->with('success', 'Jasa Teknisi berhasil ditolak');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return dd($th->getMessage());
        }
    }

    public function confirmation(Request $request)
    {
        // dd('ke');
        if ($request->ajax()) {
            $data = Service::where('status', 'review')->orWhere('status', 'danied')->latest()->get();
            return Datatables::of($data->load('base_service','engineer','base_service.service_category'))
                ->addIndexColumn()
                ->addColumn('service_category_id', function ($row) {
                    return $row->base_service->service_category->name ?? '-';
                })
                ->addColumn('name', function ($row) {
                    return $row->base_service->name ?? '-';
                })
                ->addColumn('price', function ($row) {
                    return rupiah($row->base_service->price??0);
                })
                ->addColumn('engineer', function ($row) {
                    return $row->engineer->name??'-';
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->updated_at->format('d-m-Y')."<br>".$row->updated_at->format('H:i:s');
                })
                ->addColumn('status', function ($row) {
                    if ($row->status === "review") {
                        $status = "<span class='badge badge-warning'>Menunggu<br>Kofirmasi</span>";
                    } else {
                        $status = "<span class='badge badge-danger'>Ditolak</span>";
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="' . route('services.confirmation.detail', $row->id) . '" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Detail" class="detail"><i class="fa fa-info-circle"></i> Detail</a></li>
                        </ul>
                    ';
                    return $btn;
                })
                ->rawColumns(['action', 'status','updated_at'])
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
            'skill' => 'required',
            'sertification_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {

            $service = Service::find($id);

            $service->skill = $request->skill;

            if ($request->hasFile('sertification_image')) {

                $uploadFolder = 'teknisi/service/certificate';
                $photo = $request->file('sertification_image');
                $photo_path_sertificate = $photo->store($uploadFolder, 'public');

                $service->sertification_image = Storage::disk('public')->url($photo_path_sertificate);
            }

            $service->save();

            toast('Data berhasil diubah', 'success');

            return redirect()->route('services.index');
        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
            toast('Opps, Terjadi kesalahan.', 'error');
            return redirect()->route('services.index');
        }
    }

    public function getServiceByCategoryId(Request $request)
    {
        $id = $request->get('id');

        try {
            //code...
            $base_service = BaseService::where('category_service_id',$id)->get();

            return response()->json([
                "success" => true,
                "data" => $base_service
            ]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                "success" => false,
                "data" => "",
                "message" => $th->getMessage()
            ]);

        }

    }

    public function detail_service(Request $request)
    {
        $id = $request->get('id');
        try {
            //code...
            $base_service = BaseService::find($id);

            return response()->json([
                "success" => true,
                "data" => $base_service
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>$th->getMessage(),"success"=>false]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //

        try {

            DB::beginTransaction();

            $notif = Notification::where('service_id', $id)->first();
            if(isset($notif)){
                $notif->delete();
            }

            $delete = Service::find($id)->delete();

            DB::commit();

            if ($request->ajax()) {
                return Response()->json($delete);
            }

            toast('Data berhasil dihapus', 'success');

            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollback();
            dd($th->getMessage());
            //throw $th;
        }
    }
}
