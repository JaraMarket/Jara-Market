<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



//customer routes
Route::post('/registerVoter', [VoterController::class,'registerVoter'])->name('registervoter.post');
Route::post('/validateVoterRegisterOTP', [VoterController::class,'validateVoterRegisterOTP'])->name('validateVoterRegisterOTP.post');
Route::post('/resendRegisterOtp', [VoterController::class,'resendRegisterOtp'])->name('resendRegisterOtp.post');
Route::post('/resendOtp', [VoterController::class,'resendOtp'])->name('resendOtp.post');
Route::post('/validateVoterOTP', [VoterController::class,'validateVoterOTP'])->name('validateVoterOTP.post');
Route::post('/login', [VoterController::class,'login']);
Route::post('/validateLoginOTP', [VoterController::class,'validateLoginOTP'])->name('validateLoginOTP.post');

