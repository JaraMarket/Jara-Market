<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\FranchiseController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/orders', [OrderController::class, 'store']);
Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
Route::get('/carts/{id}', [CartController::class, 'show']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::get('/orders/{id}/receipt', [OrderController::class, 'receipt']);
Route::get('/orders/{id}/track', [OrderController::class, 'track']);
Route::get('/users/{userId}/orders', [OrderController::class, 'userOrders']);
Route::post('/payments', [PaymentController::class, 'makePayment']);
Route::get('/payments/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
Route::get('/payments', [PaymentController::class, 'viewPayments']);
Route::post('/wallets/fund', [WalletController::class, 'fundWallet']);
Route::get('/orders', [OrderController::class, 'index']);
Route::put('/orders/{id}', [OrderController::class, 'update']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
// Add this line for managing franchises
Route::get('/franchises', [FranchiseController::class, 'index']);


Route::get('/settings', [SettingsController::class, 'index']);
Route::post('/settings', [SettingsController::class, 'store']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

Route::post('/foods', [FoodController::class, 'store']);

Route::get('/reports/orders', [ReportController::class, 'orderReport']);
Route::get('/reports/payments', [ReportController::class, 'paymentReport']);


Route::post('/registerUser', [CustomerController::class, 'Customer_Register']);
Route::post('/validateUserSignupOtp', [CustomerController::class, 'validateCustomerRegisterOTP']);
Route::post('/login', [CustomerController::class, 'Customer_login']);
Route::post('/validateUserLoginOtp', [CustomerController::class, 'validateCustomerLoginOTP']);
Route::get('/fetchProfile/{email}', [CustomerController::class, 'fetchProfile']);
Route::post('/edit-profile/{email}', [CustomerController::class, 'editProfile']);




Route::get('/fetch-foodCategory', [FoodController::class, 'fetchfoodCategory']);
Route::post('/fetch-ingredient', [FoodController::class, 'fetchingredient']);
Route::get('/fetch-food', [FoodController::class, 'fetchfood']);
