<?php

use App\Http\Controllers\API\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/registerCustomer', [CustomerController::class, 'Customer_Register']);
Route::post('/validateCustomerSignupOtp', [CustomerController::class, 'validateCustomerRegisterOTP']);
Route::post('/login', [CustomerController::class, 'Customer_login']);
Route::post('/validateCustomerLoginOtp', [CustomerController::class, 'validateCustomerLoginOTP']);
Route::get('/viewProfile/{email}', [CustomerController::class, 'fetchProfile']);
