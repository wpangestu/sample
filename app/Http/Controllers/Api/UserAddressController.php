<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends Controller
{
    //

    public function index(){

    }

    function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'note' => '',
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()], 400);
        }

        $data = [
            "name" => $request->get('name'),
            "address" => $request->get('address'),
            "note" => $request->get('note'),
            "lat" => $request->get('lat'),
            "lng" => $request->get('lng'),
            "user_id" => auth()->user()->id
        ];

        try {
            //code...
            UserAddress::create($data);

            return response()->json(["message"=>"Data berhasil disimpan"]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi Kesalahan ".$th->getMessage()]);

        }

    }

}
