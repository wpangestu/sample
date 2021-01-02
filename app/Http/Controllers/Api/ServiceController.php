<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;


class ServiceController extends Controller
{
    //

    public function index()
    {
        $data = Service::with('service_category')->latest()->get();
        $response['success'] = true;
        $response['message'] = count($data)." Data Ditemukan";
        $response['data'] = $data;
        return response()->json($response);           

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
