<?php

namespace App\Http\Controllers\Api;

use Mapper;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends Controller
{
    //

    private $api_search_places = "https://maps.googleapis.com/maps/api/place/textsearch/json";

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

            return response()->json(["message"=>"Data berhasil diubah"]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi Kesalahan ".$th->getMessage()],422);
        }
    }
    
    public function recommendation(Request $request){

        $validator = Validator::make($request->all(), [
            'query' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        $key = env('GOOGLE_API_KEY','');
        // dd('lah gimana');
        try {
            $response = Http::get($this->api_search_places,[
                "query" => $request->get('query'),
                "location" => $request->lat.",".$request->lng,
                "language" => "id",
                "key" => $key,
            ]);
            
            if($response->successful()){
                $result_response = [];
                $result = $response->json()['results'];
                foreach ($result as $key => $value) {
                    # code...
                    $result_response[] = [
                        "id" => $value['place_id'],
                        "place_name" => $value['name'],
                        "description" => $value['formatted_address'],
                        "geometry" => [
                            "lat" => $value['geometry']['location']['lat'],
                            "lng" => $value['geometry']['location']['lng']
                        ]
                    ];
                }
                return response()->json($result_response);
            }else{
                $errors = json_decode($res->getBody()->getContents());
                return response()->json(["message" => "Terjadi Kesalahan ".$errors],422);                
            }
        
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
