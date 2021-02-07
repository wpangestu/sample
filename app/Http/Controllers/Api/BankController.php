<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank;

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
            'account_number' => 'required|string|max:255',
            'account_holder' => 'required|string|email|max:255|unique:users,email,'.$id,
            'bank_account_id' => 'required|unique:users',
            'address' => ''
        ]);
    }    
}
