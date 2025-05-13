@extends('layouts.app')

@section('header', 'Add Vendor')

@section('content')
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('vendors.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-6 gap-6">
                            <!-- Business Information -->
                            <div class="col-span-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Business Information</h3>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="business_name" class="block text-sm font-medium text-gray-700">Business Name</label>
                                <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('business_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="business_registration_number" class="block text-sm font-medium text-gray-700">Business Registration Number</label>
                                <input type="text" name="business_registration_number" id="business_registration_number" value="{{ old('business_registration_number') }}"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('business_registration_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <label for="business_address" class="block text-sm font-medium text-gray-700">Business Address</label>
                                <textarea name="business_address" id="business_address" rows="3"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('business_address') }}</textarea>
                                @error('business_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="business_phone" class="block text-sm font-medium text-gray-700">Business Phone</label>
                                <input type="text" name="business_phone" id="business_phone" value="{{ old('business_phone') }}"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('business_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="business_email" class="block text-sm font-medium text-gray-700">Business Email</label>
                                <input type="email" name="business_email" id="business_email" value="{{ old('business_email') }}"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('business_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <label for="business_description" class="block text-sm font-medium text-gray-700">Business Description</label>
                                <textarea name="business_description" id="business_description" rows="3"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('business_description') }}</textarea>
                                @error('business_description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bank Information -->
                            <div class="col-span-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Bank Information</h3>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="bank_name" class="block text-sm font-medium text-gray-700">Bank Name</label>
                                <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('bank_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="account_number" class="block text-sm font-medium text-gray-700">Account Number</label>
                                <input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('account_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6">
                                <label for="account_name" class="block text-sm font-medium text-gray-700">Account Name</label>
                                <input type="text" name="account_name" id="account_name" value="{{ old('account_name') }}"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('account_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- User Account Information -->
                            <div class="col-span-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">User Account Information</h3>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="firstname" class="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" name="firstname" id="firstname" value="{{ old('firstname') }}"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('firstname')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="lastname" class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" name="lastname" id="lastname" value="{{ old('lastname') }}"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('lastname')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" name="password" id="password"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('vendors.index') }}"
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                Create Vendor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 