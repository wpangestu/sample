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
use App\Http\Controllers\ManajemenController;
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
    Route::get('/dashboard/statistik_engineer', [DashboardController::class,'get_statistik_engineer_register'])->name('dashboard.statistik.engineer.register');
    Route::get('/dashboard/statistik_customer', [DashboardController::class,'get_statistik_customer_register'])->name('dashboard.statistik.customer.register');
    Route::get('/dashboard/statistik_order', [DashboardController::class,'get_statistik_order'])->name('dashboard.statistik.order');

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
    Route::post('services/get_by_category', [ServiceController::class,'get_data_bycategory'])->name('service.by_category.ajax');

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

    Route::post('regency',[EnginnerController::class,'getListRegency'])->name('regency.index');
    Route::post('district',[EnginnerController::class,'getListDistict'])->name('district.index');
    Route::post('village',[EnginnerController::class,'getListVillage'])->name('village.index');

    // Service Order
    Route::get('service_order/status', [ServiceOrderController::class,'process_decline_order'])->name('service_order.process_decline');
    Route::resource('service_order', ServiceOrderController::class);
    Route::post('service_order/{id}/delete', [ServiceOrderController::class,'destroy'])->name('service_order.delete.ajax');;

    // Review Service
    // Route::resource('review_service', ReviewServiceController::class);
    Route::get('review_service',[ ReviewServiceController::class,'index'])->name('review_service.index');
    Route::get('review_service/order/{id}/create',[ ReviewServiceController::class,'create'])->name('review_service.create');
    Route::get('review_service/{id}/detail',[ ReviewServiceController::class,'show'])->name('review_service.detail');
    Route::post('review_service/order/{orderid}/store',[ ReviewServiceController::class,'store'])->name('review_service.store');
    Route::get('review_service/order/{orderid}/edit',[ ReviewServiceController::class,'edit'])->name('review_service.edit');
    Route::post('review_service/order/{orderid}/update',[ ReviewServiceController::class,'update'])->name('review_service.update');
    // Route::get('review_service', [ReviewServiceController::class,'index'])->name('review_service.index');

    // Saldo
    Route::get('balance/customer',[BalanceController::class,'customer'])->name('balance.customer.index');
    Route::get('balance/engineer',[BalanceController::class,'engineer'])->name('balance.engineer.index');
    Route::post('balance/update',[BalanceController::class,'update'])->name('balance.update');

    // Kofirmasi Pembayaran
    Route::get('payment', [PaymentController::class,'index'])->name('payment.index');
    Route::get('payment/create/order/{id}', [PaymentController::class,'create'])->name('payment.create');
    Route::post('payment/store/order/{id}', [PaymentController::class,'store'])->name('payment.order.store');
    Route::get('payment/{id}/edit/', [PaymentController::class,'edit'])->name('payment.order.edit');
    Route::post('payment/{id}/update/', [PaymentController::class,'update'])->name('payment.order.update');
    Route::get('payment/{id}/detail/', [PaymentController::class,'show'])->name('payment.order.detail');
    Route::get('payment/{id}/confirm_accept/', [PaymentController::class,'confirm_accept'])->name('payment.order.confirm_acc');
    Route::get('payment/{id}/confirm_decline/', [PaymentController::class,'confirm_decline'])->name('payment.order.confirm_dec');
    
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
    Route::get('chat/customer', [ChatController::class,'index_customer'])->name('chat.index.customer');
    Route::get('chat/customer/{id?}', [ChatController::class,'show'])->name('chat.customer.show');
    Route::get('chat/engineer/{id?}', [ChatController::class,'show'])->name('chat.engineer.show');
    Route::post('chat/engineer/update_user_chat', [ChatController::class,'update_list_user_chat'])->name('ajax.chat.update.list_user');
    
    Route::get('notification/test', [NotificationController::class,'test'])->name('notofication.test');
    Route::post('notification/save_token_to_server', [NotificationController::class,'saveTokenToServer'])->name('notofication.update.token');
    
    Route::post('chat_user', [ChatController::class,'get_user_chat'])->name('chat.user');
    Route::post('post_chat_user', [ChatController::class,'store_chat'])->name('post.chat.user');

    Route::get('account',[ManajemenController::class,'index'])->name('manajement_account.index');
    Route::get('account/create',[ManajemenController::class,'create'])->name('manajement_account.create');
    Route::post('account',[ManajemenController::class,'store'])->name('manajement_account.store');
    Route::put('account/{id}',[ManajemenController::class,'update'])->name('manajement_account.update');
    Route::get('account/{id}/edit',[ManajemenController::class,'edit'])->name('manajement_account.edit');
    Route::get('account/{id}/detail',[ManajemenController::class,'show'])->name('manajement_account.show');
    Route::post('account/{id}/delete',[ManajemenController::class,'destroy'])->name('manajement_account.delete');
});
