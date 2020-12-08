<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Members; //Load class Members 
use App\Models\User;
use App\Http\Controllers\CategoryServiceController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EnginnerController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\ReviewServiceController;
use App\Http\Controllers\PaymentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});
Route::get('/cek', function () {
    dd(uniqid());
});

Route::group(['middleware' => ['auth:sanctum', 'verified']], function() {
    Route::get('/dashboard', function() {
        return view('dashboard/index');
    })->name('dashboard');

    // Route::get('/member', Members::class)->name('member'); //Tambahkan routing ini
    
    // Kategori Jas
    Route::resource('service_category', CategoryServiceController::class);
    Route::post('/service_category/{id}/delete', [CategoryServiceController::class,'destroy'])->name('service_category.delete.ajax');
    // Jasa
    Route::resource('services', ServiceController::class);
    Route::post('services/{id}/delete', [ServiceController::class,'destroy'])->name('service.delete.ajax');;
    
    // Pelanggan
    Route::resource('customer', CustomerController::class);
    Route::post('customer/{id}/delete', [CustomerController::class,'destroy'])->name('customer.delete.ajax');;
    // Teknisi
    Route::resource('engineer', EnginnerController::class);
    Route::post('engineer/{id}/delete', [EnginnerController::class,'destroy'])->name('engineer.delete.ajax');;

    // Service Order
    Route::resource('service_order', ServiceOrderController::class);
    Route::post('service_order/{id}/delete', [ServiceOrderController::class,'destroy'])->name('service_order.delete.ajax');;

    // Review Service
    Route::get('review_service', [ReviewServiceController::class,'index'])->name('review_service.index');

    // Kofirmasi Pembayaran
    Route::get('payment', [PaymentController::class,'index'])->name('payment.index');

});
