<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\UserBankAccount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class BankController extends Controller
{
    //
    public function index()
    {

        try {
            //code...

            $bank = Bank::all();
            $bank_list = [];
            foreach ($bank as $key => $value) {
                # code...
                $bank_list[] = [
                    "id" => $value->id,
                    "name" => $value->name,
                    "logo" => $value->logo
                ];
            }

            return response()->json($bank_list);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan : ".$th->getMessage(),422]);
        }


    }

    public function store_user_bank_account(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required',
            'account_holder' => 'required|string',
            'bank_account_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        $data = [
            "account_number" => $request->get('account_number'),
            "account_holder" => $request->get('account_holder'),
            "bank_id" => $request->get('bank_account_id'),
            "user_id" => auth()->user()->id
        ];

        try {
            //code...
            $userBankAccount = UserBankAccount::create($data);

            return response()->json(["message" => "Bank account berhasil ditambahkan"]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi Kesalahan ".$th->getMessage()],422);
        }
    }

    public function get_user_bank_account(Request $request)
    {
        try {
            //code...

            $data = UserBankAccount::with('bank')->where('user_id',auth()->user()->id)->latest();

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;
            $banks = $data->limit($limit)->offset(($page - 1) * $limit);
            $data = $banks->get();
            $total = $banks->count();

            $data_array = [];
            foreach ($data as $key => $value) {
                # code...
                $data_array[] = [
                    "id" => $value->id,
                    "bank" => [
                        "id" => $value->bank->id,
                        "name" => $value->bank->name,
                        "logo" => $value->bank->logo
                    ],
                    "account_holder" => $value->account_holder,
                    "account_number" => $value->account_number
                ];
            }
            
            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
            $response['data'] = $data_array;
    
            return response()->json($response);           

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi Kesalahan ".$th->getMessage()],422);
        }


    }

    public function bank_payment(){
        try {
            //code...
            $bank = Bank::all();
            $bank_list = [];
            foreach ($bank as $key => $value) {
                # code...
                $bank_list[] = [
                    "id" => $value->id,
                    "name" => $value->name,
                    "account_number" => $value->account_number,
                    "logo" => $value->logo
                ];
            }

            return response()->json($bank_list);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi Kesalahan ".$th->getMessage()],422);
        }
    }
}
