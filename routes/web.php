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
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChatController;
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
    dd(auth()->user()->isRole);
});

Route::group(['middleware' => ['auth:sanctum', 'verified']], function() {
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    // Route::get('/member', Members::class)->name('member'); //Tambahkan routing ini
    
    // Kategori Jas
    Route::resource('service_category', CategoryServiceController::class);
    Route::post('/service_category/{id}/delete', [CategoryServiceController::class,'destroy'])->name('service_category.delete.ajax');
    // Jasa
    Route::get('services/confirmation', [ServiceController::class,'confirmation'])->name('services.confirmation');
    Route::get('services/confirmation/{id}/detail', [ServiceController::class,'detail_confirmation'])->name('services.confirmation.detail');
    Route::get('services/confirmation/{id}/accept', [ServiceController::class,'confirm_accept'])->name('services.confirmation.accept');
    Route::get('services/confirmation/{id}/danied', [ServiceController::class,'confirm_danied'])->name('services.confirmation.danied');
    Route::resource('services', ServiceController::class);
    Route::post('services/{id}/delete', [ServiceController::class,'destroy'])->name('service.delete.ajax');
    
    // Pelanggan
    Route::get('customer/export', [CustomerController::class,'export'])->name('customer.export');
    Route::get('customer/import', [CustomerController::class,'import'])->name('customer.import');
    Route::post('customer/store_import', [CustomerController::class,'storeImport'])->name('customer.store.import');
    Route::resource('customer', CustomerController::class);
    Route::post('customer/{id}/delete', [CustomerController::class,'destroy'])->name('customer.delete.ajax');
    // Teknisi
    Route::get('engineer/confirm', [EnginnerController::class,'confirmation'])->name('engineer.confirm.index');
    Route::get('engineer/confirm/{id}/detail', [EnginnerController::class,'show_confirmation'])->name('engineer.confirm.detail');
    Route::get('engineer/confirm/{id}/accept', [EnginnerController::class,'accept_engineer'])->name('engineer.confirm.accept');
    Route::get('engineer/confirm/{id}/decline', [EnginnerController::class,'decline_engineer'])->name('engineer.confirm.decline');
    Route::resource('engineer', EnginnerController::class);
    Route::post('engineer/{id}/delete', [EnginnerController::class,'destroy'])->name('engineer.delete.ajax');

    // Service Order
    Route::resource('service_order', ServiceOrderController::class);
    Route::post('service_order/{id}/delete', [ServiceOrderController::class,'destroy'])->name('service_order.delete.ajax');;

    // Review Service
    Route::resource('review_service', ReviewServiceController::class);
    // Route::get('review_service', [ReviewServiceController::class,'index'])->name('review_service.index');

    // Saldo
    Route::get('balance/customer',[BalanceController::class,'customer'])->name('balance.customer.index');
    Route::get('balance/engineer',[BalanceController::class,'engineer'])->name('balance.engineer.index');
    Route::post('balance/update',[BalanceController::class,'update'])->name('balance.update');

    // Kofirmasi Pembayaran
    Route::get('payment', [PaymentController::class,'index'])->name('payment.index');
    
    // Pengaturan
    //Privacy Policy
    Route::get('setting/privacy_policy', [SettingController::class,'privacyPolicy'])->name('setting.privacy_policy');
    Route::post('setting/privacy_policy', [SettingController::class,'storePrivacyPolicy'])->name('setting.privacy_policy.store');
    Route::post('setting/privacy_policy/{id}/update', [SettingController::class,'updatePrivacyPolicy'])->name('setting.privacy_policy.update');
    //Term of service
    Route::get('setting/term_of_service', [SettingController::class,'termOfService'])->name('setting.term_of_service');
    Route::post('setting/term_of_service', [SettingController::class,'storeTermOfService'])->name('setting.term_of_service.store');
    Route::post('setting/term_of_service/{id}/update', [SettingController::class,'updateTermOfService'])->name('setting.term_of_service.update');
    //Help
    Route::get('setting/help', [SettingController::class,'help'])->name('setting.help');
    Route::post('setting/help', [SettingController::class,'storeHelp'])->name('setting.help.store');
    Route::post('setting/help/{id}/update', [SettingController::class,'updateHelp'])->name('setting.help.update');
    
    Route::get('chat/engineer', [ChatController::class,'index'])->name('chat.index.engineer');
    Route::get('chat/engineer/{id?}', [ChatController::class,'show'])->name('chat.engineer.show');
    Route::get('chat/customer', [ChatController::class,'index_customer'])->name('chat.index.customer');
    Route::get('notification/test', [NotificationController::class,'test'])->name('notofication.test');
    Route::post('chat_user', [ChatController::class,'get_user_chat'])->name('chat.user');
    Route::post('post_chat_user', [ChatController::class,'store_chat'])->name('post.chat.user');
});
