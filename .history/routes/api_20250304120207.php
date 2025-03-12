<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/registerCustomer', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
