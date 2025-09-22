<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Enums\UserPermissionsEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Throwable;

class AuthController extends Controller
{
    /**
     * Handle new user registration.
     */
    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::create([
                'firstname'     => $validated['firstname'],
                'lastname'      => $validated['lastname'],
                'email'         => $validated['email'],
                'password'      => $validated['password'], // handled by mutator
                'role'          => UserPermissionsEnum::CUSTOMER(),
                'referral_code' => Str::random(10),
            ]);

            event(new Registered($user));
            Auth::logout();

            return redirect()
                ->route('login.show')
                ->with('success', 'Your account was created successfully');
        } catch (Throwable $e) {
            Log::error('Registration failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['register' => 'Something went wrong. Please try again later.']);
        }
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email'    => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);

            if (Auth::attempt($credentials, $request->boolean('remember'))) {

                $user = Auth::user();

                $allowedRoles = [
                    UserPermissionsEnum::ADMIN(),
                    UserPermissionsEnum::QA(),
                    UserPermissionsEnum::ACCOUNT(),
                ];
        
                if (! in_array($user->role, $allowedRoles)) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Unauthorized role.',
                    ])->onlyInput('email');
                }
        
                // Check if user is active
                if (! $user->is_active) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Your account is inactive.',
                    ])->onlyInput('email');
                }
        
                $request->session()->regenerate();
      
                return redirect()->intended('/dashboard')
                    ->with('success', 'Welcome back!');
            }

            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ])->onlyInput('email');
        } catch (Throwable $e) {
            Log::error('Login failed: ' . $e->getMessage());
            return back()->withErrors(['login' => 'Something went wrong. Please try again.']);
        }
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        try {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login.show')
                ->with('success', 'You have been logged out successfully.');
        } catch (Throwable $e) {
            Log::error('Logout failed: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors(['logout' => 'Logout failed.']);
        }
    }
}
