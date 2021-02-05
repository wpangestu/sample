<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryServiceController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\EngineerController;

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
    Route::get('teknisi/service',[ServiceController::class,'index']);
    Route::post('teknisi/service',[ServiceController::class,'store']);
    Route::put('teknisi/service',[ServiceController::class,'update']);
    Route::get('teknisi/service/{id}',[ServiceController::class,'show']);
    
    Route::get('customer/{id}',[CustomerController::class,'show']);
    Route::put('customer/{id}/update',[CustomerController::class,'update']);

    Route::get('category_service',[CategoryServiceController::class,'index']);
    Route::get('service/category_service/{id}',[ServiceController::class,'getServiceByCategoryId']);
    
    Route::get('engineer/{id}',[EngineerController::class,'show']);

    // Route::get('teknisi/user', [UserController::class,'getAuthenticatedUser']);
    Route::get('teknisi/user', [UserController::class,'userEngineer']);
    Route::get('teknisi/wallet/balance', [UserController::class,'EngineerBalance']);

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
