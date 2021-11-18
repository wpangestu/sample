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

        $user = User::find(auth()->user()->id);
        
        $check = Withdraw::where('user_id',$user->id)
                            ->where('status','pending')
                            ->get();
                            
        if($check->isNotEmpty()){
            return response()->json(["message" => "Mohon tunggu pengajuan sebelumnya terkonfirmasi"], 422);
        }

        try {
            //code...

            $amount = $request->amount;

            if ($amount > $user->balance) {
                return response()->json(["message" => "Tidak dapat di proses"],422);
            }

            // $user->balance = $user->balance - $amount;
            // $user->save();

            Withdraw::create([
                "user_id" => auth()->user()->id,
                "amount" => $request->amount,
                "note" => $request->note,
                "withdraw_id" => "W".uniqid(),
                "balance_before" => $user->balance
            ]);

            return response()->json(['message'=>'withdraw successfully created']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
        }
    }
}
