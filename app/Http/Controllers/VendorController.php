<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all vendors with their associated users
        $vendors = Vendor::with('user')
            ->whereHas('user', function($query) {
                $query->where('role', 'VENDOR');
            })
            ->latest()
            ->paginate(10);

        return view('vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vendors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Vendor creation started');
        \Log::info('Request data:', $request->all());

        try {
            $validated = $request->validate([
                'business_name' => ['required', 'string', 'max:255'],
                'business_address' => ['required', 'string'],
                'business_phone' => ['required', 'string', 'max:20'],
                'business_email' => ['required', 'email', 'unique:vendors'],
                'business_registration_number' => ['nullable', 'string', 'max:255'],
                'tax_identification_number' => ['nullable', 'string', 'max:255'],
                'business_description' => ['nullable', 'string'],
                'bank_name' => ['nullable', 'string', 'max:255'],
                'account_number' => ['nullable', 'string', 'max:255'],
                'account_name' => ['nullable', 'string', 'max:255'],
                'firstname' => ['required', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            \Log::info('Validation passed');

            // Create user with VENDOR role
            $user = User::create([
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'VENDOR'
            ]);

            \Log::info('User created:', ['user_id' => $user->id]);

            // Create vendor
            $vendor = Vendor::create([
                'business_name' => $validated['business_name'],
                'business_address' => $validated['business_address'],
                'business_phone' => $validated['business_phone'],
                'business_email' => $validated['business_email'],
                'business_registration_number' => $validated['business_registration_number'] ?? null,
                'tax_identification_number' => $validated['tax_identification_number'] ?? null,
                'business_description' => $validated['business_description'] ?? null,
                'bank_name' => $validated['bank_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'account_name' => $validated['account_name'] ?? null,
                'user_id' => $user->id,
                'is_active' => true,
                'is_verified' => false
            ]);

            \Log::info('Vendor created:', ['vendor_id' => $vendor->id]);

            return redirect()->route('vendors.index')
                ->with('success', 'Vendor created successfully');

        } catch (\Exception $e) {
            \Log::error('Error creating vendor:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while creating the vendor. Please try again.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        return view('vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'business_address' => ['required', 'string'],
            'business_phone' => ['required', 'string', 'max:20'],
            'business_email' => ['required', 'email', 'unique:vendors,business_email,' . $vendor->id],
            'business_registration_number' => ['nullable', 'string', 'max:255'],
            'tax_identification_number' => ['nullable', 'string', 'max:255'],
            'business_description' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
        ]);

        $vendor->update($validated);

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor deleted successfully');
    }

    public function toggleStatus(Vendor $vendor)
    {
        $vendor->is_active = !$vendor->is_active;
        $vendor->save();

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor status updated successfully');
    }

    public function toggleVerification(Vendor $vendor)
    {
        $vendor->is_verified = !$vendor->is_verified;
        $vendor->save();

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor verification status updated successfully');
    }
}
