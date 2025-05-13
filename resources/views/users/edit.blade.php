@extends('layouts.app')

@section('header', 'Edit User')

@section('content')
    <div class="py-4">
        <!-- Back button -->
        <div class="mb-5">
            <a href="{{ route('users.index') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Users
            </a>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit User</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Update user information and settings.</p>
            </div>
            <div class="border-t border-gray-200">
                <form action="{{ route('users.update', $user) }}" method="POST" class="divide-y divide-gray-200">
                    @csrf
                    @method('PUT')

                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" name="password" id="password"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Leave blank to keep current password">
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm
                                    Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="active" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="active" name="active"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    <option value="1" {{ old('active', $user->active) ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0" {{ old('active', $user->active) ? '' : 'selected' }}>Inactive
                                    </option>
                                </select>
                                @error('active')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- User's Orders Section -->
        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">User's Orders</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Order history for this user.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order
                                ID</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">View</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($user->orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $order->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ₦{{ number_format($order->total, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $order->status === 'completed'
                                    ? 'bg-green-100 text-green-800'
                                    : ($order->status === 'processing'
                                        ? 'bg-green-100 text-green-800'
                                        : ($order->status === 'cancelled'
                                            ? 'bg-red-100 text-red-800'
                                            : 'bg-yellow-100 text-yellow-800')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('orders.show', $order) }}"
                                        class="text-green-600 hover:text-green-900">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No orders found for this user
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('header', 'Application Settings')

@section('content')
    <div class="py-4">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- General Settings -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">General Settings</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Basic application settings.</p>
                </div>
                <div class="border-t border-gray-200">
                    <form action="{{ route('settings.update') }}" method="POST" class="divide-y divide-gray-200">
                        @csrf
                        <div class="px-4 py-5 sm:p-6">
                            <div class="space-y-6">
                                <div>
                                    <label for="site_name" class="block text-sm font-medium text-gray-700">Site
                                        Name</label>
                                    <input type="text" name="settings[site_name]" id="site_name"
                                        value="{{ $settings['site_name'] ?? '' }}"
                                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div>
                                    <label for="site_description" class="block text-sm font-medium text-gray-700">Site
                                        Description</label>
                                    <textarea name="settings[site_description]" id="site_description" rows="3"
                                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ $settings['site_description'] ?? '' }}</textarea>
                                </div>

                                <div>
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact
                                        Email</label>
                                    <input type="email" name="settings[contact_email]" id="contact_email"
                                        value="{{ $settings['contact_email'] ?? '' }}"
                                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div>
                                    <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                                    <select id="currency" name="settings[currency]"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        <option value="USD"
                                            {{ ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="EUR"
                                            {{ ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                        <option value="GBP"
                                            {{ ($settings['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <button type="submit" name="section" value="general"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Save General Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delivery Settings -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Delivery Settings</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Configure delivery options and fees.</p>
                </div>
                <div class="border-t border-gray-200">
                    <form action="{{ route('settings.update') }}" method="POST" class="divide-y divide-gray-200">
                        @csrf
                        <div class="px-4 py-5 sm:p-6">
                            <div class="space-y-6">
                                <div>
                                    <label for="delivery_fee" class="block text-sm font-medium text-gray-700">Default
                                        Delivery Fee</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="text" name="settings[delivery_fee]" id="delivery_fee"
                                            value="{{ $settings['delivery_fee'] ?? '0.00' }}"
                                            class="focus:ring-green-500 focus:border-green-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                                            placeholder="0.00">
                                    </div>
                                </div>

                                <div>
                                    <label for="free_delivery_threshold"
                                        class="block text-sm font-medium text-gray-700">Free Delivery Threshold</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="text" name="settings[free_delivery_threshold]"
                                            id="free_delivery_threshold"
                                            value="{{ $settings['free_delivery_threshold'] ?? '50.00' }}"
                                            class="focus:ring-green-500 focus:border-green-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                                            placeholder="0.00">
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Orders above this amount qualify for free
                                        delivery.</p>
                                </div>

                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="enable_delivery" name="settings[enable_delivery]" type="checkbox"
                                            value="1" {{ ($settings['enable_delivery'] ?? 0) == 1 ? 'checked' : '' }}
                                            class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="enable_delivery" class="font-medium text-gray-700">Enable
                                            Delivery</label>
                                        <p class="text-gray-500">Toggle delivery option for customers.</p>
                                    </div>
                                </div>

                                <div>
                                    <label for="delivery_time" class="block text-sm font-medium text-gray-700">Estimated
                                        Delivery Time (minutes)</label>
                                    <input type="number" name="settings[delivery_time]" id="delivery_time"
                                        value="{{ $settings['delivery_time'] ?? '30' }}"
                                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <button type="submit" name="section" value="delivery"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Save Delivery Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Settings -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Payment Settings</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Configure payment gateways and options.</p>
                </div>
                <div class="border-t border-gray-200">
                    <form action="{{ route('settings.update') }}" method="POST" class="divide-y divide-gray-200">
                        @csrf
                        <div class="px-4 py-5 sm:p-6">
                            <div class="space-y-6">
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="enable_paypal" name="settings[enable_paypal]" type="checkbox"
                                            value="1" {{ ($settings['enable_paypal'] ?? 0) == 1 ? 'checked' : '' }}
                                            class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="enable_paypal" class="font-medium text-gray-700">Enable PayPal</label>
                                    </div>
                                </div>

                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="enable_stripe" name="settings[enable_stripe]" type="checkbox"
                                            value="1" {{ ($settings['enable_stripe'] ?? 0) == 1 ? 'checked' : '' }}
                                            class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="enable_stripe" class="font-medium text-gray-700">Enable Stripe</label>
                                    </div>
                                </div>

                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="enable_cod" name="settings[enable_cod]" type="checkbox"
                                            value="1" {{ ($settings['enable_cod'] ?? 0) == 1 ? 'checked' : '' }}
                                            class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="enable_cod" class="font-medium text-gray-700">Enable Cash on
                                            Delivery</label>
                                    </div>
                                </div>

                                <div>
                                    <label for="paypal_client_id" class="block text-sm font-medium text-gray-700">PayPal
                                        Client ID</label>
                                    <input type="text" name="settings[paypal_client_id]" id="paypal_client_id"
                                        value="{{ $settings['paypal_client_id'] ?? '' }}"
                                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div>
                                    <label for="stripe_key" class="block text-sm font-medium text-gray-700">Stripe Public
                                        Key</label>
                                    <input type="text" name="settings[stripe_key]" id="stripe_key"
                                        value="{{ $settings['stripe_key'] ?? '' }}"
                                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <button type="submit" name="section" value="payment"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Save Payment Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Notification Settings</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Configure email and push notifications.</p>
                </div>
                <div class="border-t border-gray-200">
                    <form action="{{ route('settings.update') }}" method="POST" class="divide-y divide-gray-200">
                        @csrf
                        <div class="px-4 py-5 sm:p-6">
                            <div class="space-y-6">
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="order_confirmation_email" name="settings[order_confirmation_email]"
                                            type="checkbox" value="1"
                                            {{ ($settings['order_confirmation_email'] ?? 1) == 1 ? 'checked' : '' }}
                                            class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="order_confirmation_email" class="font-medium text-gray-700">Order
                                            Confirmation Email</label>
                                        <p class="text-gray-500">Send email to customer when order is placed.</p>
                                    </div>
                                </div>

                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="order_status_email" name="settings[order_status_email]"
                                            type="checkbox" value="1"
                                            {{ ($settings['order_status_email'] ?? 1) == 1 ? 'checked' : '' }}
                                            class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="order_status_email" class="font-medium text-gray-700">Order Status
                                            Updates</label>
                                        <p class="text-gray-500">Send email when order status changes.</p>
                                    </div>
                                </div>

                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="admin_new_order_notification"
                                            name="settings[admin_new_order_notification]" type="checkbox" value="1"
                                            {{ ($settings['admin_new_order_notification'] ?? 1) == 1 ? 'checked' : '' }}
                                            class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="admin_new_order_notification" class="font-medium text-gray-700">Admin
                                            Order Notification</label>
                                        <p class="text-gray-500">Send email to admin when new order is placed.</p>
                                    </div>
                                </div>

                                <div>
                                    <label for="admin_email" class="block text-sm font-medium text-gray-700">Admin Email
                                        for Notifications</label>
                                    <input type="email" name="settings[admin_email]" id="admin_email"
                                        value="{{ $settings['admin_email'] ?? '' }}"
                                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <button type="submit" name="section" value="notification"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Save Notification Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Flash a success message when settings are saved
        // This assumes you're using a session flash for success messages
        document.addEventListener('DOMContentLoaded', () => {
            // Implementation would depend on your Laravel setup
        });
    </script>
@endsection
