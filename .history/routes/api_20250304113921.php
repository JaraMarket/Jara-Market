<?php

use App\Http\Controllers\API\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//customer routes
Route::post('/registerCustomer', [CustomerController::class,'Customer_'])->name('Customer_signUp.post');
Route::post('/validateCustomerRegisterOTP', [CustomerController::class,'validateCustomerRegisterOTP'])->name('validateCustomerRegisterOTP.post');
Route::post('/resendCustomerRegisterOTP', [CustomerController::class,'resendCustomerRegisterOTP'])->name('resendCustomerRegisterOTP.post');
