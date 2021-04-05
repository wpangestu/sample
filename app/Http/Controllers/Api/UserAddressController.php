<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Validator;
use Mapper;

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

        $data_arr = [];
        foreach($data as $val){
            $data_arr[]=[
                "id" => $val->id,
                "name" => $val->name,
                "address" => $val->address,
                "description" => $val->note,
                "geometry" => [
                    "lat" => $val->lat,
                    "lng" => $val->lng
                ]
            ];
        }

        $response['page'] = (int)$page;
        $response['size'] = (int)$limit;
        $response['total'] = (int)$total;
        $response['data'] = $data_arr;

        return response()->json($response);           
    }

    function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'description' => '',
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        $data = [
            "name" => $request->get('name'),
            "address" => $request->get('address'),
            "note" => $request->get('description'),
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
            return response()->json(["message" => "Terjadi Kesalahan ".$th->getMessage()],422);

        }

    }

    public function update(Request $request, $id)
    {
        try {
            
            $user_address = UserAddress::find($id);

            if($request->has('name')){
                $user_address->name = $request->name;
            }
            if($request->has('address')){
                $user_address->address = $request->address;
            }
            if($request->has('description')){
                $user_address->note = $request->description;
            }
            if($request->has('lat')){
                $user_address->lat = $request->lat;
            }
            if($request->has('lng')){
                $user_address->lng = $request->lng;
            }

            $user_address->save();

            return response()->json(["message"=>"Data berhasil disimpan"]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi Kesalahan ".$th->getMessage()],422);
        }
    }
    
    public function recommendation(Request $request){
        try {
            $query = $request->input('query');
            $map = Mapper::location($query);
            return response()->json($map);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi Kesalahan ".$th->getMessage()],422);
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
            return response()->json(["message" => "Terjadi Kesalahan ".$th->getMessage()],422);
        }
    }
}
