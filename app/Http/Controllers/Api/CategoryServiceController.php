<?php

namespace App\Http\Controllers\Api;

use App\Models\CategoryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CategoryServiceController extends Controller
{
    //

    public function index(){

        $data_category = CategoryService::latest()->get();
        $data['success'] = true;
        $data['message'] = count($data_category)." Data Ditemukan";
        $data['data'] = $data_category;
        return response()->json($data);   
    }

}
