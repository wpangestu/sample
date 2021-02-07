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
        $bank = Bank::all();

        return response()->json($bank);
    }

    public function store_user_bank_account(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required',
            'account_holder' => 'required|string',
            'bank_account_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()], 400);
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
            return response()->json(["message" => "Terjadi Kesalahan ".$th->getMessage()]);
        }
    }

    public function get_user_bank_account(Request $request)
    {

        $data = UserBankAccount::with('bank')->where('user_id',auth()->user()->id)->latest();

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('size') ? $request->get('size') : 10;
        $banks = $data->limit($limit)->offset(($page - 1) * $limit);
        $data = $banks->get();
        $total = $banks->count();
        
        $response['page'] = $page;
        $response['size'] = $limit;
        $response['total'] = $total;
        $response['data'] = $data;

        return response()->json($response);           
    }
}
