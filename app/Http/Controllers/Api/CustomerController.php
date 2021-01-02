<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    //

    public function show($id){
        $data = User::Role('user')->find($id);
        $response['success'] = true;
        $response['message'] = "Data Ditemukan";
        $response['data'] = $data;
        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'phone' => 'required|unique:users',
            'address' => ''
        ]);

        if($validator->fails()){
            $response['status'] = false;
            $response['message'] = 'Failed to update';
            $response['error'] = $validator->errors();
            return response()->json($response, 400);
        }

        $customer = User::Role('user')->find($id)->first();
        $update = $customer->update($request->all());

        if($update){
            $response['status'] = true;
            $response['message'] = 'Update Success';
            $response['data'] = $customer;
            return response()->json($response);
        }else{
            $response['status'] = false;
            $response['message'] = 'Opps, There is something wrong';
            $response['data'] = [];
            return response()->json($response);
        }

    }
}
