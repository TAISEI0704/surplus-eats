<?php

use App\Http\Controllers\Auth\Seller;
use App\Http\Controllers\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/*
|--------------------------------------------------------------------------
| User認証API（一般ユーザー）
|--------------------------------------------------------------------------
*/

// 公開エンドポイント（認証不要）
Route::prefix('auth/user')->name('auth.user.')->group(function () {
    Route::post('register', User\RegisterController::class)->name('register');
    Route::post('login', User\LoginController::class)->name('login');
});

// 認証済みユーザー専用エンドポイント
Route::prefix('auth/user')->name('auth.user.')->middleware('auth:sanctum')->group(function () {
    Route::post('logout', User\LogoutController::class)->name('logout');
    Route::get('profile', User\ProfileController::class)->name('profile');
    Route::patch('profile', User\UpdateProfileController::class)->name('profile.update');
    Route::patch('password', User\UpdatePasswordController::class)->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Seller認証API（販売者）
|--------------------------------------------------------------------------
*/

// 公開エンドポイント（認証不要）
Route::prefix('auth/seller')->name('auth.seller.')->group(function () {
    Route::post('register', Seller\RegisterController::class)->name('register');
    Route::post('login', Seller\LoginController::class)->name('login');
});

// 認証済み販売者専用エンドポイント
Route::prefix('auth/seller')->name('auth.seller.')->middleware('auth:sanctum')->group(function () {
    Route::post('logout', Seller\LogoutController::class)->name('logout');
    Route::get('profile', Seller\ProfileController::class)->name('profile');
    Route::patch('profile', Seller\UpdateProfileController::class)->name('profile.update');
    Route::patch('password', Seller\UpdatePasswordController::class)->name('password.update');
});

/*
|--------------------------------------------------------------------------
| 後方互換用エンドポイント
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
