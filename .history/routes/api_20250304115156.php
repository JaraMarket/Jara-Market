<?php

use App\Http\Controllers\API\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//customer routes
Route::post('/api/registerCustomer', [CustomerController::class,'Customer_Register'])->name('Customer_signUp.post');
Route::post('/api/validateCustomerRegisterOTP', [CustomerController::class,'validateCustomerRegisterOTP'])->name('validateCustomerRegisterOTP.post');
Route::post('/api/resendCustomerRegisterOTP', [CustomerController::class,'resendCustomerRegisterOTP'])->name('resendCustomerRegisterOTP.post');
