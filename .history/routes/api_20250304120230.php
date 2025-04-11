<?php

use App\Http\Controllers\API\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/registerCustomer', [CustomerController::class, 'Customer_Register']);
Route::post('/login', [LoginController::class, 'login']);
