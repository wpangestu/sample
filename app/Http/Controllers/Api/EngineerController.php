<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class EngineerController extends Controller
{
    //
    public function show($id){
        $data = User::Role('teknisi')->with('Role')->find($id);
        $response['success'] = true;
        $response['message'] = "Data Ditemukan";
        $response['data'] = $data;
        return response()->json($response);
    }
}
