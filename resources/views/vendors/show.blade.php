@extends('layouts.app')

@section('header', 'Vendor Details')

@section('content')
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $vendor->business_name }}</h2>
                        <div class="flex space-x-2">
                            <a href="{{ route('vendors.edit', $vendor) }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                Edit
                            </a>
                            <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                                    onclick="return confirm('Are you sure you want to delete this vendor?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Business Information -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Business Information</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Business Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vendor->business_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Registration Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vendor->business_registration_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vendor->business_address }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vendor->business_phone }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vendor->business_email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vendor->business_description }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Bank Information -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Bank Information</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Bank Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vendor->bank_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Account Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vendor->account_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Account Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $vendor->account_name }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Status Information -->
                    <div class="mt-6 bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Status Information</h3>
                        <div class="flex space-x-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Status:</span>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Verification:</span>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vendor->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $vendor->is_verified ? 'Verified' : 'Unverified' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Associated User -->
                    <div class="mt-6 bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Associated User</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vendor->user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $vendor->user->email }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 