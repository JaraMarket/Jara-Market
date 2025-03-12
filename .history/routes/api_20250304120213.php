<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/registerCustomer', [Cust::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
