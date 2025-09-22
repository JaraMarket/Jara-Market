<?php

use Arcanedev\LogViewer\Http\Controllers\LogViewerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\PaymentReportController;
use App\Http\Controllers\FranchiseController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\StateRepresentativeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // 👇 Default route to login
    Route::redirect('/', '/login');

    Route::get('/register', fn () => view('auth.register'))->name('register.show');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::get('/login', fn () => view('auth.login'))->name('login.show');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::prefix('password')->group(function () {
        Route::get('/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/forgot', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    
        Route::get('/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
    });
    
    // Email Verification
    Route::prefix('email')->group(function () {
        Route::get('/verify', [EmailVerificationController::class, 'notice'])->middleware('auth')->name('verification.notice');
        Route::get('/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
        Route::post('/verification-notification', [EmailVerificationController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
    });
});




Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/data', [OrderController::class, 'getData'])->name('orders.data');

        //Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update.status');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/data', [CategoryController::class, 'getData'])->name('categories.data');
        Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    Route::prefix('commissions')->group(function () {
        Route::get('/', [CommissionController::class, 'index'])->name('commissions.index');
        Route::get('/create', [CommissionController::class, 'create'])->name('commissions.create');
        Route::post('/', [CommissionController::class, 'store'])->name('commissions.store');
        Route::get('/{commission}', [CommissionController::class, 'show'])->name('commissions.show');
        Route::get('/{commission}/edit', [CommissionController::class, 'edit'])->name('commissions.edit');
        Route::put('/{commission}', [CommissionController::class, 'update'])->name('commissions.update');
        Route::delete('/{commission}', [CommissionController::class, 'destroy'])->name('commissions.destroy');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/data', [UserController::class, 'getData'])->name('users.data');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::put('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle.status');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::get('/data', [ProductController::class, 'getData'])->name('products.data');
        Route::get('/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/', [SettingsController::class, 'update'])->name('settings.update');
    });

    Route::prefix('franchises')->group(function () {
        Route::get('/', [FranchiseController::class, 'index'])->name('franchises.index');
        Route::get('/create', [FranchiseController::class, 'create'])->name('franchises.create');
        Route::post('/', [FranchiseController::class, 'store'])->name('franchises.store');
        Route::get('/{franchise}/edit', [FranchiseController::class, 'edit'])->name('franchises.edit');
        Route::put('/{franchise}', [FranchiseController::class, 'update'])->name('franchises.update');
        Route::delete('/{franchise}', [FranchiseController::class, 'destroy'])->name('franchises.destroy');
    });

    Route::prefix('summary')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('summary');
        Route::get('/data', [ReportController::class, 'getSummary'])->name('summary.data');
    });

    Route::prefix('reports')->group(function () {
        Route::get('/orders', [ReportController::class, 'orders'])->name('reports.orders');
        Route::get('/orders/export', [ReportController::class, 'exportOrders'])->name('reports.orders.export');
        Route::get('/products', [ReportController::class, 'products'])->name('reports.products');
    
        Route::get('/payments', [PaymentReportController::class, 'index'])->name('reports.payments');
        Route::get('/payments/export', [PaymentReportController::class, 'export'])->name('reports.payments.export');
    
        // If you want a separate payment module (filter, show, export)
        Route::prefix('payments')->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('payments.index');
            Route::get('/filter', [PaymentController::class, 'filter'])->name('payments.filter');
            Route::get('/{payment}', [PaymentController::class, 'show'])->name('payments.show');
            Route::get('/export', [PaymentController::class, 'export'])->name('payments.export');
        });
    });

    Route::prefix('representatives')->group(function () {
        Route::get('/', [StateRepresentativeController::class, 'index'])->name('representatives.index');
        Route::get('/create', [StateRepresentativeController::class, 'create'])->name('representatives.create');
        Route::post('/', [StateRepresentativeController::class, 'store'])->name('representatives.store');
        Route::get('/{representative}/edit', [StateRepresentativeController::class, 'edit'])->name('representatives.edit');
        Route::put('/{representative}', [StateRepresentativeController::class, 'update'])->name('representatives.update');
        Route::delete('/{representative}', [StateRepresentativeController::class, 'destroy'])->name('representatives.destroy');
        Route::patch('/{representative}/toggle-status', [StateRepresentativeController::class, 'toggleStatus'])->name('representatives.toggle-status');
    });

    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/create', [AdminController::class, 'create'])->name('admin.create');
        Route::post('/', [AdminController::class, 'store'])->name('admin.store');
        Route::get('/{admin}/edit', [AdminController::class, 'edit'])->name('admin.edit');
        Route::put('/{admin}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/{admin}', [AdminController::class, 'destroy'])->name('admin.destroy');
        Route::patch('/{admin}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.toggle-status');
    
        // Profile routes
        Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
        Route::put('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    });

    Route::get('/ingredients/data', [IngredientController::class, 'getData'])->name('ingredients.data');

    Route::resource('ingredients', IngredientController::class);
    Route::resource('advertisements', AdvertisementController::class);

    Route::prefix('vendors')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('vendors.index');
        Route::get('/data', [VendorController::class, 'getData'])->name('vendors.data');
        Route::get('/create', [VendorController::class, 'create'])->name('vendors.create');
        Route::post('/', [VendorController::class, 'store'])->name('vendors.store');
        Route::get('/{id}/edit', [VendorController::class, 'show'])->name('vendors.edit');
        Route::put('/{user}/update', [VendorController::class, 'update'])->name('vendors.update');
        Route::delete('/{user}', [VendorController::class, 'destroy'])->name('vendors.destroy');
        Route::patch('/{user}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendors.toggle-status');
        Route::patch('/{user}/toggle-verification', [VendorController::class, 'toggleVerification'])->name('vendors.toggle-verification');
    });

    Route::group([
        'prefix'     => 'admin/log-viewer',
        'namespace'  => 'Arcanedev\LogViewer\Http\Controllers',
    ], function () {
        Route::get('/', 'LogViewerController@index')->name('log-viewer::dashboard');
        Route::get('list', 'LogViewerController@listLogs')->name('log-viewer::logs.list');
        Route::delete('delete', 'LogViewerController@delete')->name('log-viewer::logs.delete');
        Route::get('{date}', 'LogViewerController@show')->name('log-viewer::logs.show');
        Route::get('{date}/download', 'LogViewerController@download')->name('log-viewer::logs.download');
        Route::get('{date}/{level}', 'LogViewerController@showByLevel')->name('log-viewer::logs.filter');
    });

});

