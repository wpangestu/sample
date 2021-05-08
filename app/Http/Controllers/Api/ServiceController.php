<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaseService;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\CategoryService;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    //

    public function index(Request $request)
    {
        try {
            //code...

            $data = Service::where('engineer_id',auth()->user()->id)->latest();
        
            if($request->has('query')){
                $query = $request->get('query');
                $data->where('name', 'like', '%'.$query.'%');
            }
            if($request->has('service_category')){
                $service_category = $request->get('service_category');
                $data->where('category_service_id', $service_category);
            }
            
            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;
            $service = $data->limit($limit)->offset(($page - 1) * $limit);
            $data = $service->get();
            $total = $service->count();
            
            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
            $response['data'] = $data;
    
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

            $baseService = BaseService::where('category_service_id',$categoryService->id)->get();

            $data = $baseService->map(function($item,$key){
                return [
                    "id" => $item->id,
                    "name" => $item->name,
                    "media" => "",
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
            'name' => 'required|string|max:255',
            'service_category' => 'required|integer',
            'skill' => 'required',
            'certificate' => 'required|image|mimes:img,png,jpeg,jpg|max:2048',
            'image' => 'required|image|mimes:img,png,jpeg,jpg|max:2048',
            'price' => 'required|integer',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        try {
            //code...
            DB::beginTransaction();

            $uploadFolder = 'teknisi/service/certificate';
            $photo = $request->file('certificate');
            $photo_path_sertificate = $photo->store($uploadFolder,'public');
    
            if ($request->hasFile('image')) {
    
                $uploadFolder = 'teknisi/service/images';
                $photo = $request->file('image');
                $photo_path_image = $photo->store($uploadFolder,'public');
    
                // $service->image = Storage::disk('public')->url($photo_path);
                // $service->save();
            }
    
            $data = [
                "name" => $request->get('name'),
                "category_service_id" => $request->get('service_category'),
                "engineer_id" => auth()->user()->id,
                "price" => $request->get('price'),
                "skill" => $request->get('skill'),
                "description" => $request->get('description'),
                "sertification_image" => Storage::disk('public')->url($photo_path_sertificate),
                "image" => Storage::disk('public')->url($photo_path_image)
            ]; 

            $service = Service::create($data);

            DB::commit();
    
            return response()->json(["message"=>"Data berhasil disimpan"]);            

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(["message"=>" Terjadi Kesalahan ".$th->getMessage()],422);
        }

    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'service_category' => 'required|integer',
            'skill' => 'required',
            'price' => 'required|integer',
            'service_id' => 'required|integer'
        ]);

        if ($request->has('certificate')) {
            $validator = Validator::make($request->all(), [
                'certificate' => 'required|image|mimes:img,png,jpeg,jpg|max:2048',
            ]);        
        }

        if ($request->has('image')) {
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:img,png,jpeg,jpg|max:2048',
            ]);        
        }

        if($validator->fails()){
            return response()->json(["message" => "Terjadi kesalhan ". $validator->errors()->all()[0]], 422);
        }

        $service_id = $request->get('service_id');

        try {
            //code...
            DB::beginTransaction();

            $service = Service::find($service_id);
            
            $data = [
                "name" => $request->get('name'),
                "category_service_id" => $request->get('service_category'),
                "price" => $request->get('price'),
                "skill" => $request->get('skill'),
                "description" => $request->get('description'),
            ]; 
    
            $service->update($data);
    
            if ($request->hasFile('certificate')) {
    
                $uploadFolder = 'teknisi/service/certificate';
                $photo = $request->file('certificate');
                $photo_path_sertificate = $photo->store($uploadFolder,'public');
    
                $service->sertification_image = Storage::disk('public')->url($photo_path_sertificate);
                $service->save();
            }
            if ($request->hasFile('image')) {
    
                $uploadFolder = 'teknisi/service/images';
                $photo = $request->file('image');
                $photo_path_image = $photo->store($uploadFolder,'public');
    
                $service->image = Storage::disk('public')->url($photo_path_image);
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

            $response = [
                "id" => $data->id,
                "name" => $data->name,
                "category" => $data->service_category->name,
                "skill" => $data->skill,
                "certificate" => $data->sertification_image,
                "price" => $data->price,
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

}
