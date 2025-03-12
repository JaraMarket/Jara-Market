<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//customer routes
Route::post('/registerVoter', [VoterController::class,'registerVoter'])->name('registervoter.post');
