<?php

use Illuminate\Http\Request;
use App\Http\Middleware\APIAuth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\BikeController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\orderController;
use App\Http\Controllers\API\ryderController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\API\reviewController;
use App\Http\Controllers\API\messageController;
use App\Http\Controllers\API\supportController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\API\websiteInfoController;
use App\Http\Controllers\API\FCMNotificationController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
