<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends Controller
{
    //

    public function index(Request $request){

        $data = UserAddress::where('user_id',auth()->user()->id)->latest();

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('size') ? $request->get('size') : 10;
        $user_address = $data->limit($limit)->offset(($page - 1) * $limit);
        $data = $user_address->get();
        $total = $user_address->count();

        $response['page'] = (int)$page;
        $response['size'] = (int)$limit;
        $response['total'] = (int)$total;
        $response['data'] = $data;

        return response()->json($response);           
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

    public function destroy($id)
    {
        try {
            //code...
            $useraddress = UserAddress::find($id);
            $useraddress->delete();

            return response()->json(["message"=>"Data berhasil dihapus"]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi Kesalahan ".$th->getMessage()]);
        }
    }
}
