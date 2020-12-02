<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Members; //Load class Members 
use App\Models\User;
use App\Http\Controllers\CategoryServiceController;
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
Route::get('tes', function () {
    auth()->user()->assignRole('user');
});

Route::group(['middleware' => ['auth:sanctum', 'verified']], function() {
    Route::get('/dashboard', function() {
        return view('dashboard/index');
    })->name('dashboard');

    // Route::get('/member', Members::class)->name('member'); //Tambahkan routing ini
    
    Route::resource('service_category', CategoryServiceController::class);
    Route::post('/service_category/{id}/delete', [CategoryServiceController::class,'destroy'])->name('service_category.delete.ajax'); //Tambahkan routing ini

});
