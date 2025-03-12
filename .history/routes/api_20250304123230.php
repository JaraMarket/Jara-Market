<?php

use App\Http\Controllers\API\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/registerCustomer', [CustomerController::class, 'Customer_Register']);
//Route::post('/login', [LoginController::class, 'login']);
