<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::latest()->paginate(10);
        return view('admin.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'is_active' => true,
        ]);

        return redirect()->route('admin.index')
            ->with('success', 'Admin created successfully');
    }

    public function show(Admin $admin)
    {
        return view('admin.show', compact('admin'));
    }

    public function edit(Admin $admin)
    {
        return view('admin.edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,' . $admin->id],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['nullable', Password::defaults()],
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->phone = $validated['phone'];

        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->route('admin.index')
            ->with('success', 'Admin updated successfully');
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();
        return redirect()->route('admin.index')
            ->with('success', 'Admin deleted successfully');
    }

    public function toggleStatus(Admin $admin)
    {
        $admin->is_active = !$admin->is_active;
        $admin->save();

        return redirect()->route('admin.index')
            ->with('success', 'Admin status updated successfully');
    }

    public function profile()
    {
        $admin = auth()->user();
        return view('admin.profile', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = User::find(Auth::id());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,' . $admin->id],
            'phone' => ['required', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:new_password'],
            'new_password' => ['nullable', Password::defaults()],
        ]);

        if (!empty($validated['current_password'])) {
            if (!Hash::check($validated['current_password'], $admin->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }
            $admin->password = Hash::make($validated['new_password']);
        }

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->phone = $validated['phone'];
        $admin->save();

        return redirect()->route('admin.profile')
            ->with('success', 'Profile updated successfully');
    }
}
