<?php

use App\Http\Controllers\API\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//customer routes
Route::post('/registerCustomer', [CustomerController::class,'Customer_signUp'])->name('Customer_signUp.post');
Route::post('/validateCustRegisterOTP', [VoterController::class,'validateVoterRegisterOTP'])->name('validateVoterRegisterOTP.post');
Route::post('/resendRegisterOtp', [VoterController::class,'resendRegisterOtp'])->name('resendRegisterOtp.post');
