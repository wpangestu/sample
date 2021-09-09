<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use App\Models\BaseService;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\CategoryService;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    //

    public function index(Request $request)
    {
        try {
            //code...
            $data = Service::where('engineer_id',auth()->user()->id)->latest();
        
            $query_search = $request->get('query');
            $data->when($query_search, function ($query, $query_search) {
                return $query->whereHas('base_service', function ($query) use ($query_search) {
                    return $query->where('name', 'like', '%'.$query_search.'%');
                });
            });
            $filter = $request->get('filter');
            if($filter != "all"){
                $data->when($filter, function ($query, $filter) {
                    return $query->whereHas('base_service', function ($query) use ($filter) {
                        return $query->whereHas('service_category', function ($query2) use ($filter) {
                            return $query2->where('slug', $filter);
                        });
                    });
                });
            }
            
            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;
            $service = $data->limit($limit)->offset(($page - 1) * $limit);
            $data = $service->get();
            $total = $service->count();

            $data_map = $data->map(function($item,$key){
                return [
                    "id" => $item->id,
                    "id_base_service" => $item->base_service_id??'',
                    "name" => $item->base_service->name??'',
                    'media' => $item->base_service->image??'',
                    'price' => (int)($item->base_service->price??0),
                    "status" => $item->status??'-'
                ];
            });
            
            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
            $response['data'] = $data_map;
    
            return response()->json($response);           

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
        }
    }

    public function get_base_service_by_category(Request $request){
        try {
            //code...

            $slug_category = $request->get('category');
            $categoryService = CategoryService::where('slug',$slug_category)->first();

            if(is_null($categoryService)){
                return response()->json(["message"=> "Terjadi kesalahan, slug category tidak ditemukan"],422);
            }

            $baseService = BaseService::where('category_service_id',$categoryService->id)->get();

            $data = $baseService->map(function($item,$key){
                return [
                    "id" => $item->id,
                    "name" => $item->name,
                    "media" => $item->image??'',
                    "price" => (int)$item->price
                ];
            });
            $response = $data;
            return response()->json($response);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=> "Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'service_id' => 'required|integer',
            'skill' => 'required',
            'certificate' => 'required|image|mimes:img,png,jpeg,jpg|max:2048',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        if ($request->has('certificate')) {
            $validator = Validator::make($request->all(), [
                'certificate' => 'required|image|mimes:img,png,jpeg,jpg|max:2048',
            ]);        
        }

        try {
            //code...
            DB::beginTransaction();

            $data = [
                "base_service_id" => $request->get('service_id'),
                "engineer_id" => auth()->user()->id,
                "price" => $request->get('price'),
                "skill" => $request->get('skill'),
            ]; 

            $service = Service::create($data);
            if ($request->hasFile('certificate')) {
    
                $uploadFolder = 'teknisi/service/certificate';
                $photo = $request->file('certificate');
                $photo_path_sertificate = $photo->store($uploadFolder,'public');
    
                $service->sertification_image = Storage::disk('public')->url($photo_path_sertificate);
                $service->save();
            }

            DB::commit();
    
            return response()->json(["message"=>"Data berhasil disimpan"]);            

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(["message"=>" Terjadi Kesalahan ".$th->getMessage()],422);
        }

    }

    public function update(Request $request,$service_id)
    {
        $validator = Validator::make($request->all(), [
            'service_base_id' => 'required|integer',
            'status' => 'required',
            'skill' => 'null',
        ]);

        if ($request->has('certificate')) {
            $validator = Validator::make($request->all(), [
                'certificate' => 'required|image|mimes:img,png,jpeg,jpg|max:2048',
            ]);        
        }

        if($validator->fails()){
            return response()->json(["message" => "Terjadi kesalahan ". $validator->errors()->all()[0]], 422);
        }

        try {
            //code...
            DB::beginTransaction();

            $service = Service::find($service_id);
            
            $data = [
                "status" => $request->get('status'),
                "skill" => $request->get('skill'),
            ]; 
    
            $service->update($data);
    
            if ($request->hasFile('certificate')) {
    
                $uploadFolder = 'teknisi/service/certificate';
                $photo = $request->file('certificate');
                $photo_path_sertificate = $photo->store($uploadFolder,'public');
    
                $service->sertification_image = Storage::disk('public')->url($photo_path_sertificate);
                $service->save();
            }

            DB::commit();

            return response()->json(["message"=>"Data berhasil diubah"]);            

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(["message"=> "Terjadi kesalahan ".$th->getMessage()],422);
        }
    }
    
    public function getServiceByCategoryId($id)
    {
        try {
            //code...
            $data = Service::where('category_service_id',$id)->latest()->get();
            $response['success'] = true;
            $response['message'] = count($data)." Data Ditemukan";
            $response['data'] = $data;
            return response()->json($response);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=> "Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function show($id)
    {
        try {
            //code...
            $data = Service::with('service_category')->find($id);

            $skill = [];
            foreach($data->skill as $val){
                $skill[] = [
                    "name" => $val
                ];
            }

            $response = [
                "id" => $data->id,
                "id_base_service" => $data->base_service->id??'-',
                "name" => $data->base_service->name??'-',
                "category" => $data->base_service->service_category->name??'-',
                "skill" => $skill,
                "certificate" => $data->sertification_image??'-',
                "price" => (int)$data->base_service->price??0,
                "status" => $data->status
            ];

            return response()->json($response);
        
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function price_category(Request $request)
    {
        try {
            //code...
            $category = $request->input('category');

            $service_category = CategoryService::where('name',$category)->first();

            return response()->json(["price" => (int)$service_category->price??0]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function destroy($id){
        try {
            //code...
            $notif = Notification::where('service_id', $id)->first();
            if(!is_null($notif)){
                $notif->delete();
            }

            $service = Service::find($id);
            $service->delete();

            return response()->json(['message'=>'delete success']);
            
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

}
