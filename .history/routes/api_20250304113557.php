<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//customer routes
Route::post('/registerCustomer', [VoterController::class,'registerVoter'])->name('registervoter.post');
