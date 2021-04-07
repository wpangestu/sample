<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

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

    public function withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'note' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }
        
        try {
            //code...
            Withdraw::create([
                "engineer_id" => auth()->user()->id,
                "amount" => $request->amount,
                "note" => $request->note,
                "withdraw_id" => "W".uniqid()
            ]);

            return response()->json(['message'=>'withdraw successfully created']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
        }
    }
}
