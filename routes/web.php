<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerAuthController;
use App\Models\Models\Seller;

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
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

//
Route::group(['middleware' => 'web'], function () {
    Route::get('/seller-dashboard', function () {
        return view('seller-dashboard');
    })->name('seller.dashboard');
    Route::get('/seller-register', [SellerAuthController::class, 'showRegistrationForm'])->name('seller.register');
    Route::post('/seller-register', [SellerAuthController::class, 'register'])->name('seller.register.post');
    Route::get('/seller-login', [SellerAuthController::class, 'showLoginForm'])->name('seller.login');
    Route::post('/seller-login', [SellerAuthController::class, 'login'])->name('seller.login.post');
    Route::post('/seller-logout', [SellerAuthController::class, 'logout'])->name('seller.logout');
});
