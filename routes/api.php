<?php


use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\API\BankController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LgaController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\VendorCategoryController;
use App\Http\Controllers\API\ResetPasswordController;
use App\Http\Controllers\API\ForgotPasswordController;

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

Route::prefix('jaram')->group(function () {
    
    Route::prefix('webhook')->controller(PaymentController::class)->group(function () {
        Route::post('/paystack', 'handlePaystackWebhook')->middleware('paystack-webhook');
    });

    Route::get('verify-transaction/{slug}', [PaymentController::class, 'verifyTransaction']);

    Route::middleware('guest')->group(function () {
        Route::post('/register', [UserController::class, 'registerUser']);
        Route::post('/validate-otp', [UserController::class, 'validateUserRegisterOTP']);
        Route::post('/validate-email', [UserController::class, 'verifyEmailWithOTP']);
        Route::post('/resend-otp', [UserController::class, 'resendOtp']);
        Route::post('/login', [UserController::class, 'login']);
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
        Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
        Route::post('/profile-update/{email}', [UserController::class, 'updateProfile']);
        Route::post('/update-vendor-categories/{email}', [VendorCategoryController::class, 'store']);
    });

    Route::prefix('states')->group(function () {
        Route::get('/', [StateController::class, 'index']);
        Route::get('/{state}', [StateController::class, 'findState']);
    });
    Route::prefix('lgas')->group(function () {
        Route::get('/', [LgaController::class, 'index']);
        Route::get('/{lga}', [LgaController::class, 'findLga']);
    });

    Route::prefix('country')->group(function () {
        Route::get('/', [CountryController::class, 'index']);
        Route::get('/{country}/states', [CountryController::class, 'states']);
    });

    Route::prefix('vendors')->group(function (){
        Route::get('/categories', [ProductController::class, 'getVendorCategories']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/fetch-user', [UserController::class, 'fetchUserProfile']);
        Route::get('/my-referrals', [UserController::class, 'myRefferals']);
        Route::get('/fetch-user', [UserController::class, 'fetchUserProfile']);
        Route::get('/fetch-wallet', [UserController::class, 'fetchUserWallet']);
        Route::post('/update-profile', [UserController::class, 'editUserProfile']);
        Route::get('/users', [UserController::class, 'index']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::post('/logout', [UserController::class,'logout']);
        Route::patch('/user/change-password', [UserController::class,'changePassword']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        
        Route::get('/banks', [BankController::class, 'index']);

        Route::post('/wallet/transfer-to-bank', [WalletController::class, 'transfer']);
        
        Route::controller(PaymentController::class)->prefix('payments')->group(function () {
            Route::get('/', 'all');
            Route::get('{id}', 'show');
            Route::post('/initialize-transaction', 'fundWallet');
        });

        Route::controller(PaymentController::class)->prefix('transfers')->group(function () {
            Route::get('/', 'getTransfers');
        });

        Route::controller(SettingsController::class)->prefix('settings')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
        });

        Route::controller(NotificationController::class)->prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
            Route::get('/unread-count', function () {
                return response()->json([
                    'unread' => auth()->user()->unreadNotifications()->count()
                ]);
            });
        });

        Route::controller(FoodController::class)->prefix('foods')->group(function () {
            Route::post('/', 'store');
        });

        Route::controller(OrderController::class)->prefix('orders')->group(function () {
            Route::get('/', 'all');
            Route::get('/{order}', 'show');
            Route::post('/', 'store');
            Route::post('/{order}/cancel', 'cancel');
        });

        Route::controller(FavoritesController::class)->prefix('favorites')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::delete('/{id}',  'destroy');
        });

        Route::controller(ProductController::class)->prefix('fetch')->group(function () {
            Route::get('/categories-all-products', 'getCategoriesAllProducts');
            Route::get('/categories-limit-products', 'getCategoriesLimitProducts');
            Route::get('/ingredients', 'fetchingredient');
            Route::get('/product',  'fetchProduct');
            Route::get('/uom',  'fetchUom');
            Route::get('/product/{id}',  'getProductById');
        });

        Route::controller(AddressController::class)->prefix('addresses')->group(function () {
            Route::get('/', 'index');
            Route::post('/','store');
            Route::put('/{address}','update');
        });

        Route::controller(SupportController::class)->prefix('supports')->group(function () {
            Route::get('/', 'index');
            Route::get('/{support}', 'show');
            Route::post('/', 'store');
        });

        Route::controller(AdvertisementController::class)->prefix('advertisements')->group(function () {
            Route::get('/', 'fetch_adverts');
        });

        Route::prefix('pin')->group(function () {
            Route::post('/set', [PinController::class, 'setPin']);
            Route::post('/verify', [PinController::class, 'verifyPin']);
            Route::get('/validate', [PinController::class, 'validatePinToken']);
            Route::post('/clear', [PinController::class, 'clearPinToken']);
            Route::post('/request-reset', [PinController::class, 'requestReset']);
            Route::post('/reset', [PinController::class, 'resetPin']);
        });
    });
});

