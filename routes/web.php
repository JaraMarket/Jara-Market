<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware('auth'); // Ensure only authenticated users can access

Route::get('/admin/settings', function () {
    return view('admin.settings');
})->middleware('auth'); // Ensure only authenticated users can access

Route::get('/admin/categories', function () {
    return view('admin.category_management');
})->middleware('auth'); // Ensure only authenticated users can access

Route::get('/admin/foods', function () {
    return view('admin.food_management');
})->middleware('auth'); // Ensure only authenticated users can access

Route::get('/admin/reports', function () {
    return view('admin.report_generation');
})->middleware('auth'); // Ensure only authenticated users can access
