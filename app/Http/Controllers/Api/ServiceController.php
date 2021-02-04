<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    //

    public function index(Request $request)
    {
        $data = Service::latest();
        
        if($request->has('query')){
            $query = $request->get('query');
            // $data->wherelike

        }
        // $data->
        // ->with('service_category')->paginate(10);
        
        // $response['success'] = true;
        // $response['message'] = count($data)." Data Ditemukan";
        // $response['data'] = $data;
        // dd($data->toArray()['total']);
        return response()->json($data);           

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
            return response()->json(["message" => $validator->errors()], 400);
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
            return response()->json(["message"=>$th->getMessage()],422);
        }

    }

    public function getServiceByCategoryId($id)
    {
        $data = Service::where('category_service_id',$id)->latest()->get();
        $response['success'] = true;
        $response['message'] = count($data)." Data Ditemukan";
        $response['data'] = $data;
        return response()->json($response);
    }

    public function show($id)
    {
        $data = Service::find($id);
        $response['success'] = true;
        $response['message'] = "Data Ditemukan";
        $response['data'] = $data;
        return response()->json($response);
    }

}
