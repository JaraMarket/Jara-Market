<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//customer routes
Route::post('/registerCustomer', [Cu::class,'registerVoter'])->name('registervoter.post');
