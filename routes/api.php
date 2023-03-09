<?php

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
// reports

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('login',  [\App\Http\Controllers\Api\AuthController::class, 'login']);
//        Route::post('phone/verify', \App\Http\Controllers\Api\PhoneVerifyController::class);
//        Route::post('password/forget', \App\Http\Controllers\Api\PhoneVerifyController::class);
//        Route::post('password/reset', \App\Http\Controllers\Api\RestPasswordController::class);
//        Route::post('user/set-fcm-token', [\App\Http\Controllers\Api\V1\AuthController::class, 'setFcmToken'])->middleware('auth:sanctum');
//        Route::get('user', [\App\Http\Controllers\Api\V1\AuthController::class, 'profile'])->middleware('auth:sanctum');
});

