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
use App\Http\Controllers\Api\TransactionController;

use App\Http\Controllers\Api\Customer\UserController as CustomerUserController;

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
Route::post('/teknisi/user/forgot-password/input-otp', [UserController::class,'forgot_password_input_otp']);
Route::put('/teknisi/user/forgot-password/change-password', [UserController::class,'change_password']);

Route::post('/teknisi/user/register/request-otp', [UserController::class,'request_otp']);
Route::post('/teknisi/user/register/confirmation-otp', [UserController::class,'confirmation_otp']);


Route::get('/testing', [UserController::class, 'index'])->name('api.testing');
Route::get('/testing/email', [UserController::class, 'testing']);

Route::prefix('customer')->group(function () {
    Route::post('/user/register', [CustomerUserController::class,'register']);
    Route::post('/user/login', [CustomerUserController::class,'login']);
    Route::post('/user/register/request-otp', [CustomerUserController::class,'request_otp']);
    Route::post('/user/register/confirmation-otp', [CustomerUserController::class,'confirmation_otp']);
    Route::post('/user/forgot-password', [UserController::class,'forgot_password']);
    Route::post('/user/forgot-password/input-otp', [CustomerUserController::class,'forgot_password_input_otp']);
    Route::put('/user/forgot-password/change-password', [UserController::class,'change_password']);

    Route::get('/service/category', [CustomerUserController::class, 'service_category']);
    Route::get('/address/recommendation', [UserAddressController::class, 'recommendation']);
    Route::get('/bank/payment', [BankController::class, 'bank_payment']);
});

Route::get('teknisi/service/category',[CustomerUserController::class,'service_category']);
Route::get('teknisi/service/list',[ServiceController::class,'get_base_service_by_category']);
Route::get('/teknisi​/address/recommendation', [UserAddressController::class, 'recommendation']);

Route::get('teknisi​/bank-account​/bank', [BankController::class,'index']);

Route::middleware(['jwt.verify'])->group(function () {

    # TEKNISI
    //Service
    Route::get('teknisi/service',[ServiceController::class,'index']);

    Route::post('teknisi/service',[ServiceController::class,'store']);
    Route::put('teknisi/service/{id}',[ServiceController::class,'update']);
    Route::get('teknisi/service/price-category',[ServiceController::class,'price_category']);
    Route::get('teknisi/service/{id}',[ServiceController::class,'show']);
    Route::delete('teknisi/service/{id}',[ServiceController::class,'destroy']);


    // User
    Route::get('teknisi/user', [UserController::class,'userEngineer']);
    Route::post('teknisi/user', [UserController::class,'updateEngineer']);
    Route::post('teknisi​/user​/token', [UserController::class,'store_fcm_token']);
    Route::delete('teknisi​/user​/token', [UserController::class,'delete_fcm_token']);
    Route::get('teknisi/wallet/balance', [UserController::class,'EngineerBalance']);
    Route::post('teknisi/wallet/withdraw', [EngineerController::class,'withdraw']);


    Route::get('teknisi​/bank-account​', [BankController::class,'get_user_bank_account']);
    Route::post('teknisi​/bank-account​', [BankController::class,'store_user_bank_account']);

    Route::get('/teknisi​/address', [UserAddressController::class, 'index']);
    Route::post('/teknisi​/address', [UserAddressController::class, 'store']);

    Route::get('/teknisi​/address/cek', [UserAddressController::class, 'cek']);
    Route::put('/teknisi​/address/{id}', [UserAddressController::class, 'update']);
    Route::delete('/teknisi​/address/{id}', [UserAddressController::class, 'destroy']);
    
    Route::get('/​teknisi​/notification', [NotificationController::class, 'index']);
    Route::post('/​teknisi​/notification/read', [NotificationController::class, 'read']);

    Route::get('/teknisi/chat/support', [ChatController::class, 'get_support_chat']);
    Route::post('/teknisi/chat/support', [ChatController::class, 'send_chat_support']);

    Route::get('/​teknisi​/chat', [ChatController::class, 'get_message_chat']);
    Route::post('/​teknisi​/chat/pin', [ChatController::class, 'pinned_chat']);
    Route::get('/​teknisi​/chat/{id}', [ChatController::class, 'get_message_by_chatroom_id']);
    Route::post('/​teknisi​/chat/delete', [ChatController::class, 'delete_chat']);
    
    Route::get('/​teknisi​/history/chat', [ChatController::class, 'get_history_message_chat']);
    Route::get('/​teknisi​/history/chat/{id}', [ChatController::class, 'get_history_message_by_chatroom_id']);
    Route::post('/​teknisi​/history/chat/delete', [ChatController::class, 'delete_history_chat']);
    Route::post('/​teknisi​/history/chat/pin', [ChatController::class, 'pinned_history_chat']);
    

    
    Route::post('/​teknisi​/chat/send', [ChatController::class, 'send_message']);
    
    // Route::get('customer/{id}',[CustomerController::class,'show']);
    // Route::put('customer/{id}/update',[CustomerController::class,'update']);
    
    // Route::get('category_service',[CategoryServiceController::class,'index']);
    // Route::get('service/category_service/{id}',[ServiceController::class,'getServiceByCategoryId']);
    
    // Route::get('engineer/{id}',[EngineerController::class,'show']);
    
    Route::get('/teknisi/transaction-summary',[TransactionController::class,'index']);
    Route::get('/teknisi/order/review',[TransactionController::class,'review']);
    Route::get('/teknisi/order/{id}',[TransactionController::class,'show']);
    Route::post('/teknisi/order/accept/{id}',[TransactionController::class,'order_accept']);
    Route::post('/teknisi/order/decline/{id}',[TransactionController::class,'order_decline']);
    Route::post('/teknisi/order/process/{id}',[TransactionController::class,'order_process']);
    Route::post('/teknisi/order/complete/{id}',[TransactionController::class,'order_complete']);
    Route::post('/teknisi/order/extend/{id}',[TransactionController::class,'order_extend']);
    
    // Route::get('teknisi/user', [UserController::class,'getAuthenticatedUser']);
    Route::post('/teknisi/user/change-password',[UserController::class,'change_password_user']);
    Route::post('/teknisi/user/change-profile-photo',[UserController::class,'update_user_profile']);
    

    
    Route::prefix('customer')->group(function () {

        Route::get('/user', [CustomerUserController::class,'showUser']);
        // Route::get('/user​', [CustomerUserController::class,'showUser']);
        // Route::get('/user​', [CustomerUserController::class,'show']);
        Route::post('/user​', [CustomerUserController::class,'update']);

        Route::get('/wallet/balance', [CustomerUserController::class,'balance']);
        Route::post('/user​/token', [UserController::class,'store_fcm_token']);
        Route::delete('/user​/token', [UserController::class,'delete_fcm_token']);
        
        
        Route::get('/address', [UserAddressController::class, 'index']);
        Route::post('/address', [UserAddressController::class, 'store']);
        Route::get('/address/top', [CustomerUserController::class, 'top_address']);
        Route::get('/address/cek', [UserAddressController::class, 'cek']);
        Route::put('/address/{id}', [UserAddressController::class, 'update']);
        Route::delete('/address/{id}', [UserAddressController::class, 'destroy']);
        
        Route::get('/service', [CustomerUserController::class, 'service']);

        Route::get('/service/recommendation', [CustomerUserController::class, 'service_recommendation']);
        Route::get('/service/{id}', [CustomerUserController::class, 'service_detail']);

        Route::get('/chat/support', [ChatController::class, 'get_support_chat']);
        Route::post('/chat/support', [ChatController::class, 'send_chat_support']);
        
        Route::post('/chat/delete', [ChatController::class, 'delete_chat']);
        Route::post('/chat/pin', [ChatController::class, 'pinned_chat']);
        Route::get('/chat', [ChatController::class, 'get_message_chat']);
        Route::post('/chat/{id}', [ChatController::class, 'send_new_chat']);
        Route::get('/chat/{id}', [ChatController::class, 'get_message_by_chatroom_id']);

        Route::get('/history/chat', [ChatController::class, 'get_history_message_chat']);
        Route::post('/history/chat/delete', [ChatController::class, 'delete_history_chat']);
        Route::post('/history/chat/pin', [ChatController::class, 'pinned_history_chat']);
        Route::get('/history/chat/{id}', [ChatController::class, 'get_history_message_by_chatroom_id']);
        
        Route::post('order/generate-payment', [CustomerUserController::class, 'order_generate_payment']);
        Route::post('order/checkout', [CustomerUserController::class, 'order_checkout']);
        Route::post('order/cancel/{id}', [CustomerUserController::class, 'cancel_order']);
        Route::post('order/payment-approval/{id}', [CustomerUserController::class, 'payment_approval_store']);
        Route::get('order/{id}', [CustomerUserController::class, 'order']);
        
        Route::post('user/change-profile-photo',[UserController::class,'update_user_profile']);

        Route::get('transaction',[CustomerUserController::class,'transaction']);
        Route::get('transaction/ongoing',[CustomerUserController::class,'transaction_on_going']);

    });

    // Route::get('/customer/user​', [UserController::class,'userCustomer']);
    
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
