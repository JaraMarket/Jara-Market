<?php

use App\Http\Controllers\API\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\FoodController

Route::post('/registerCustomer', [CustomerController::class, 'Customer_Register']);
Route::post('/validateCustomerSignupOtp', [CustomerController::class, 'validateCustomerRegisterOTP']);
Route::post('/login', [CustomerController::class, 'Customer_login']);
Route::post('/validateCustomerLoginOtp', [CustomerController::class, 'validateCustomerLoginOTP']);
Route::get('/fetchProfile/{email}', [CustomerController::class, 'fetchProfile']);
Route::post('/edit-profile/{email}', [CustomerController::class, 'editProfile']);
Route::get('/fetch-foodCategory', [FoodController::class, 'fetchfoodCategory']);
