<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StateRepresentativeController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Franchise;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/', function () {
    return view('auth.login');
})->middleware('guest')->name('login.show');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// Dashboard
Route::get('/dashboard', function () {
    $totalOrders = Order::count();
    $totalUsers = User::count();
    $totalProducts = Product::count();
    $totalCategories = Category::count();
    $recentOrders = Order::with('user')->latest()->take(5)->get();
    $latestUsers = User::latest()->take(5)->get();

    // Monthly sales data for chart
    $monthlySales = Order::selectRaw("strftime('%m', created_at) as month, SUM(total) as total")
        ->whereRaw("strftime('%Y', created_at) = ?", [date('Y')])
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->pluck('total', 'month')
        ->toArray();

    $months = [];
    $salesData = [];

    for ($i = 1; $i <= 12; $i++) {
        $months[] = date('F', mktime(0, 0, 0, $i, 1));
        $salesData[] = $monthlySales[sprintf("%02d", $i)] ?? 0;
    }

    // Format data for the sales chart
    $salesChartData = [
        'labels' => $months,
        'data' => $salesData
    ];

    // Get top 5 products by quantity sold
    $topProducts = DB::table('order_items')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
        ->groupBy('products.name')
        ->orderByDesc('total_quantity')
        ->limit(5)
        ->get();

    // Format data for the products chart
    $productsChartData = [
        'labels' => $topProducts->pluck('name')->toArray(),
        'data' => $topProducts->pluck('total_quantity')->toArray()
    ];

    return view('dashboard', compact(
        'totalOrders',
        'totalUsers',
        'totalProducts',
        'totalCategories',
        'recentOrders',
        'latestUsers',
        'salesChartData',
        'productsChartData'
    ));
})->middleware('auth')->name('dashboard');

// Order Management Routes
Route::prefix('orders')->middleware('auth')->group(function () {
    // List all orders
    Route::get('/', function () {
        $orders = Order::with('user')->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    })->name('orders.index');

    // Show order details
    Route::get('/{order}', function (Order $order) {
        $order->load('user', 'items.product');
        return view('orders.show', compact('order'));
    })->name('orders.show');

    // Update order status
    Route::put('/{order}/status', function (Request $request, Order $order) {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'processing', 'completed', 'cancelled'])],
        ]);

        $order->update(['status' => $validated['status']]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order status updated successfully');
    })->name('orders.update.status');

    // Delete order
    Route::delete('/{order}', function (Order $order) {
        $order->delete();
        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully');
    })->name('orders.destroy');
});

// User Management Routes
Route::prefix('users')->middleware('auth')->group(function () {
    // List all users
    Route::get('/', function () {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    })->name('users.index');

    // Show user create form
    Route::get('/create', function () {
        return view('users.create');
    })->name('users.create');

    // Store new user
    Route::post('/', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    })->name('users.store');

    // Show user edit form
    Route::get('/{user}/edit', function (User $user) {
        return view('users.edit', compact('user'));
    })->name('users.edit');

    // Update user
    Route::put('/{user}', function (Request $request, User $user) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    })->name('users.update');

    // Toggle user active status
    Route::put('/{user}/toggle-status', function (User $user) {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->route('users.index')
            ->with('success', "User {$status} successfully");
    })->name('users.toggle.status');

    // Delete user
    Route::delete('/{user}', function (User $user) {
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    })->name('users.destroy');
});

// Category Management Routes
Route::prefix('categories')->middleware('auth')->group(function () {
    // List all categories
    Route::get('/', function () {
        $categories = Category::withCount('products')->latest()->paginate(10);
        return view('categories.index', compact('categories'));
    })->name('categories.index');

    // Show category create form
    Route::get('/create', function () {
        return view('categories.create');
    })->name('categories.create');

    // Store new category
    Route::post('/', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories'],
            'description' => ['nullable', 'string'],
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully');
    })->name('categories.store');

    // Show category edit form
    Route::get('/{category}/edit', function (Category $category) {
        return view('categories.edit', compact('category'));
    })->name('categories.edit');

    // Update category
    Route::put('/{category}', function (Request $request, Category $category) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully');
    })->name('categories.update');

    // Delete category
    Route::delete('/{category}', function (Category $category) {
        $category->delete();
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully');
    })->name('categories.destroy');
});

// Product Management Routes
Route::prefix('products')->middleware('auth')->group(function () {
    // List all products
    Route::get('/', function () {
        $products = Product::with('categories')->latest()->paginate(10);
        return view('products.index', compact('products'));
    })->name('products.index');

    // Show product create form
    Route::get('/create', function () {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    })->name('products.create');

    // Store new product
    Route::post('/', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'categories' => ['required', 'array'],
            'categories.*' => ['exists:categories,id'],
            'ingredients' => ['required', 'string'],
            'preparation_steps' => ['required', 'string'],
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'],
            'stock' => $validated['stock'],
            'ingredients' => $validated['ingredients'],
            'preparation_steps' => $validated['preparation_steps'],
        ]);

        $product->categories()->attach($validated['categories']);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    })->name('products.store');

    // Show product edit form
    Route::get('/{product}/edit', function (Product $product) {
        $categories = Category::all();
        $product->load('categories');
        return view('products.edit', compact('product', 'categories'));
    })->name('products.edit');

    // Update product
    Route::put('/{product}', function (Request $request, Product $product) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'categories' => ['required', 'array'],
            'categories.*' => ['exists:categories,id'],
            'ingredients' => ['required', 'string'],
            'preparation_steps' => ['required', 'string'],
        ]);

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'discount_price' => $validated['discount_price'],
            'stock' => $validated['stock'],
            'ingredients' => $validated['ingredients'],
            'preparation_steps' => $validated['preparation_steps'],
        ]);

        $product->categories()->sync($validated['categories']);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    })->name('products.update');

    // Delete product
    Route::delete('/{product}', function (Product $product) {
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    })->name('products.destroy');
});

Route::prefix('settings')->middleware('auth')->group(function () {
    // Show settings
    Route::get('/', function () {
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    })->name('settings.index');

    // Update settings
    Route::post('/', function (Request $request) {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_description' => ['nullable', 'string'],
            'contact_email' => ['required', 'email'],
            'contact_phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'currency' => ['required', 'string', 'size:3'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'shipping_fee' => ['required', 'numeric', 'min:0'],
            'social_facebook' => ['nullable', 'url'],
            'social_twitter' => ['nullable', 'url'],
            'social_instagram' => ['nullable', 'url'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully');
    })->name('settings.update');
});

// Franchise Management Routes
Route::prefix('franchises')->middleware('auth')->group(function () {
    // List all franchises
    Route::get('/', function () {
        $franchises = Franchise::with('owner')->latest()->paginate(10);
        return view('franchises.index', compact('franchises'));
    })->name('franchises.index');

    // Show franchise create form
    Route::get('/create', function () {
        $users = User::all();
        return view('franchises.create', compact('users'));
    })->name('franchises.create');

    // Store new franchise
    Route::post('/', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'owner_id' => ['required', 'exists:users,id'],
        ]);

        Franchise::create($validated);

        return redirect()->route('franchises.index')
            ->with('success', 'Franchise created successfully');
    })->name('franchises.store');

    // Show franchise edit form
    Route::get('/{franchise}/edit', function (Franchise $franchise) {
        $users = User::all();
        return view('franchises.edit', compact('franchise', 'users'));
    })->name('franchises.edit');

    // Update franchise
    Route::put('/{franchise}', function (Request $request, Franchise $franchise) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'owner_id' => ['required', 'exists:users,id'],
        ]);

        $franchise->update($validated);

        return redirect()->route('franchises.index')
            ->with('success', 'Franchise updated successfully');
    })->name('franchises.update');

    // Delete franchise
    Route::delete('/{franchise}', function (Franchise $franchise) {
        $franchise->delete();
        return redirect()->route('franchises.index')
            ->with('success', 'Franchise deleted successfully');
    })->name('franchises.destroy');
});

// Reports Routes
Route::prefix('reports')->middleware('auth')->group(function () {
    // Orders report
    Route::get('/orders', function (Request $request) {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $orders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('user')
            ->latest()
            ->paginate(10);

        // Calculate total orders and revenue
        $totalOrders = $orders->total();
        $totalRevenue = $orders->sum('total');

        // Calculate average order value
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Calculate growth rates
        $previousPeriodStart = now()->subDays(60)->format('Y-m-d');
        $previousPeriodEnd = now()->subDays(31)->format('Y-m-d');

        $previousOrders = Order::whereBetween('created_at', [$previousPeriodStart . ' 00:00:00', $previousPeriodEnd . ' 23:59:59'])->count();
        $previousRevenue = Order::whereBetween('created_at', [$previousPeriodStart . ' 00:00:00', $previousPeriodEnd . ' 23:59:59'])->sum('total');
        $previousAOV = $previousOrders > 0 ? $previousRevenue / $previousOrders : 0;

        $orderGrowth = $previousOrders > 0 ? (($totalOrders - $previousOrders) / $previousOrders) * 100 : 0;
        $revenueGrowth = $previousRevenue > 0 ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
        $aovGrowth = $previousAOV > 0 ? (($averageOrderValue - $previousAOV) / $previousAOV) * 100 : 0;

        // Prepare data for Orders Over Time chart
        $ordersByDate = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $orderChartLabels = $ordersByDate->pluck('date')->toArray();
        $orderChartData = $ordersByDate->pluck('count')->toArray();
        $revenueChartData = $ordersByDate->pluck('revenue')->toArray();

        // Prepare data for Revenue by Status chart
        $revenueByStatus = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('status, SUM(total) as total')
            ->groupBy('status')
            ->get();

        $statusChartLabels = $revenueByStatus->pluck('status')->toArray();
        $statusChartData = $revenueByStatus->pluck('total')->toArray();

        // Assuming 5% conversion rate for demonstration
        $conversionRate = 5;
        $conversionGrowth = 0;

        $ordersByStatus = $orders->groupBy('status')
            ->map(function ($statusOrders) {
                return $statusOrders->count();
            });

        $customers = User::whereHas('orders')->get();

        return view('reports.orders', compact(
            'orders',
            'totalOrders',
            'totalRevenue',
            'averageOrderValue',
            'conversionRate',
            'orderGrowth',
            'revenueGrowth',
            'aovGrowth',
            'conversionGrowth',
            'ordersByStatus',
            'startDate',
            'endDate',
            'customers',
            'orderChartLabels',
            'orderChartData',
            'revenueChartData',
            'statusChartLabels',
            'statusChartData'
        ));
    })->name('reports.orders');

    // Payments report
    Route::get('/payments', function (Request $request) {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $payments = Payment::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('user')
            ->latest()
            ->get();

        $totalPayments = $payments->sum('amount');
        $paymentsByStatus = $payments->groupBy('status')
            ->map(function ($statusPayments) {
                return $statusPayments->count();
            });

        return view('payments.index', compact('payments', 'totalPayments', 'paymentsByStatus', 'startDate', 'endDate'));
    })->name('payments.index');

    Route::prefix('payments')->middleware('auth')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/filter', [PaymentController::class, 'filter'])->name('payments.filter');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('/export', [PaymentController::class, 'export'])->name('payments.export');
    });

    // Products report
    Route::get('/products', function () {
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'), DB::raw('SUM(order_items.price * order_items.quantity) as total_sales'))
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $lowStockProducts = Product::where('stock', '<', 10)->get();

        return view('reports.products', compact('topProducts', 'lowStockProducts'));
    })->name('reports.products');

    // Export orders report to CSV
    Route::get('/orders/export', function (Request $request) {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $orders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('user')
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-report.csv"',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order ID', 'Customer', 'Total', 'Status', 'Date']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->user->name,
                    $order->total,
                    $order->status,
                    $order->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    })->name('reports.orders.export');

    // Export payments report to CSV
    Route::get('/payments/export', function (Request $request) {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $payments = Payment::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('user')
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments-report.csv"',
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Payment ID', 'Customer', 'Amount', 'Status', 'Transaction ID', 'Date']);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->user->name,
                    $payment->amount,
                    $payment->status,
                    $payment->transaction_id,
                    $payment->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    })->name('reports.payments.export');
});

// Profile Routes
Route::prefix('profile')->middleware('auth')->group(function () {
    // Show profile
    Route::get('/', function () {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    })->name('profile.index');

    // Update profile
    Route::put('/', function (Request $request) {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        User::where('id', $user->id)->update($userData);

        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully');
    })->name('profile.update');
});

// Login Routes
Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $remember = $request->boolean('remember');

    if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->middleware('guest');

// Registration Routes
Route::get('/register', function () {
    return view('auth.register');
})->middleware('guest')->name('register');

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'terms' => ['required', 'accepted'],
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    event(new Registered($user));

    Auth::login($user);

    return redirect('dashboard');
})->middleware('guest');

// Logout Route
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// Password Reset Routes
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
    ]);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => ['required'],
        'email' => ['required', 'email'],
        'password' => ['required', 'min:8', 'confirmed'],
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request) {
    $user = User::find($request->route('id'));

    if (!$user || !hash_equals(
        sha1($user->getEmailForVerification()),
        $request->route('hash')
    )) {
        throw new AuthorizationException;
    }

    if ($user->hasVerifiedEmail()) {
        return redirect()->intended('dashboard');
    }

    $user->markEmailAsVerified();

    return redirect()->intended('dashboard')->with('status', 'Your email has been verified!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/create', [AdminController::class, 'create'])->name('admin.create');
    Route::post('/', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/{admin}', [AdminController::class, 'show'])->name('admin.show');
    Route::get('/{admin}/edit', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('/{admin}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/{admin}', [AdminController::class, 'destroy'])->name('admin.destroy');
    Route::patch('/{admin}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.toggle-status');

    // Profile routes
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
});

// State Representatives Routes
Route::prefix('representatives')->middleware('auth')->group(function () {
    Route::get('/', [StateRepresentativeController::class, 'index'])->name('representatives.index');
    Route::get('/create', [StateRepresentativeController::class, 'create'])->name('representatives.create');
    Route::post('/', [StateRepresentativeController::class, 'store'])->name('representatives.store');
    Route::get('/{representative}/edit', [StateRepresentativeController::class, 'edit'])->name('representatives.edit');
    Route::put('/{representative}', [StateRepresentativeController::class, 'update'])->name('representatives.update');
    Route::delete('/{representative}', [StateRepresentativeController::class, 'destroy'])->name('representatives.destroy');
    Route::patch('/{representative}/toggle-status', [StateRepresentativeController::class, 'toggleStatus'])->name('representatives.toggle-status');
});
