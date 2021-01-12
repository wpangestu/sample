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


Route::post('/register', [UserController::class,'register']);
Route::post('/confirmation-otp', [UserController::class,'confirmation_otp']);
Route::post('/login', [UserController::class,'login']);
Route::get('/testing', [UserController::class, 'index'])->name('api.testing');


Route::middleware(['jwt.verify'])->group(function () {
    Route::get('category_service',[CategoryServiceController::class,'index']);
    Route::get('service',[ServiceController::class,'index']);
    Route::get('service/category_service/{id}',[ServiceController::class,'getServiceByCategoryId']);
    Route::get('service/{id}',[ServiceController::class,'show']);
    Route::get('customer/{id}',[CustomerController::class,'show']);
    Route::put('customer/{id}/update',[CustomerController::class,'update']);

    Route::get('engineer/{id}',[EngineerController::class,'show']);

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
