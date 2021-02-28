<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryServiceController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\EngineerController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/ 


Route::post('/teknisi/user/register', [UserController::class,'register']);
Route::post('/teknisi/user/login', [UserController::class,'login']);

Route::post('/teknisi/user/forgot-password', [UserController::class,'forgot_password']);
Route::post('/teknisi/user/register/request-otp', [UserController::class,'request_otp']);
Route::post('/teknisi/user/forgot-password/input-otp', [UserController::class,'forgot_password_input_otp']);
Route::put('/teknisi/user/forgot-password/change_password', [UserController::class,'change_password']);

Route::post('/teknisi/user/confirmation-otp', [UserController::class,'confirmation_otp']);

Route::get('/testing', [UserController::class, 'index'])->name('api.testing');


Route::middleware(['jwt.verify'])->group(function () {

    # TEKNISI
    //Service
    Route::get('teknisi/service',[ServiceController::class,'index']);
    Route::post('teknisi/service',[ServiceController::class,'store']);
    Route::put('teknisi/service',[ServiceController::class,'update']);
    Route::get('teknisi/service/{id}',[ServiceController::class,'show']);
    // User
    Route::get('teknisi/user', [UserController::class,'userEngineer']);
    Route::post('teknisi​/user​/token', [UserController::class,'store_fcm_token']);
    Route::get('teknisi/wallet/balance', [UserController::class,'EngineerBalance']);

    Route::get('teknisi​/bank-account​/bank', [BankController::class,'index']);
    Route::get('teknisi​/bank-account​', [BankController::class,'get_user_bank_account']);
    Route::post('teknisi​/bank-account​', [BankController::class,'store_user_bank_account']);

    Route::get('/teknisi​/address', [UserAddressController::class, 'index']);
    Route::post('/teknisi​/address', [UserAddressController::class, 'store']);
    Route::delete('/teknisi​/address/{id}', [UserAddressController::class, 'destroy']);
    
    Route::get('/​teknisi​/notification', [NotificationController::class, 'index']);

    Route::get('/​teknisi​/chat', [ChatController::class, 'get_message_chat']);
    Route::post('/​teknisi​/chat/pin', [ChatController::class, 'pinned_chat']);
    Route::get('/​teknisi​/chat/{id}', [ChatController::class, 'get_message_by_chatroom_id']);
    Route::post('/​teknisi​/chat/delete', [ChatController::class, 'delete_chat']);
    
    Route::get('/​teknisi​/history/chat', [ChatController::class, 'get_history_message_chat']);
    Route::get('/​teknisi​/history/chat/{id}', [ChatController::class, 'get_history_message_by_chatroom_id']);
    Route::post('/​teknisi​/history/chat/delete', [ChatController::class, 'delete_history_chat']);
    Route::post('/​teknisi​/history/chat/pin', [ChatController::class, 'pinned_history_chat']);
    
    Route::post('/​teknisi​/chat/send', [ChatController::class, 'send_message']);

    Route::get('customer/{id}',[CustomerController::class,'show']);
    Route::put('customer/{id}/update',[CustomerController::class,'update']);

    Route::get('category_service',[CategoryServiceController::class,'index']);
    Route::get('service/category_service/{id}',[ServiceController::class,'getServiceByCategoryId']);
    
    Route::get('engineer/{id}',[EngineerController::class,'show']);

    // Route::get('teknisi/user', [UserController::class,'getAuthenticatedUser']);
    


});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
