@extends('layouts.app')

@section('title', 'Website Settings')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between mb-6">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Website Settings
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage your website configuration and preferences.
                    </p>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('success') }}
                            </p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button type="button" onclick="this.parentElement.parentElement.parentElement.remove()"
                                    class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <span class="sr-only">Dismiss</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Settings Tabs -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs" x-data="{ activeTab: 'general' }">
                        <button @click="activeTab = 'general'"
                            :class="{ 'border-green-500 text-green-600': activeTab === 'general', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'general' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            General
                        </button>
                        <button @click="activeTab = 'contact'"
                            :class="{ 'border-green-500 text-green-600': activeTab === 'contact', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'contact' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Contact Information
                        </button>
                        <button @click="activeTab = 'payment'"
                            :class="{ 'border-green-500 text-green-600': activeTab === 'payment', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'payment' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Payment & Shipping
                        </button>
                        <button @click="activeTab = 'social'"
                            :class="{ 'border-green-500 text-green-600': activeTab === 'social', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'social' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Social Media
                        </button>
                    </nav>
                </div>

                <form action="{{ route('settings.update') }}" method="POST" class="divide-y divide-gray-200">
                    @csrf

                    <!-- General Settings -->
                    <div x-show="activeTab === 'general'" class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <label for="site_name" class="block text-sm font-medium text-gray-700">
                                    Site Name <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="site_name" id="site_name"
                                        value="{{ old('site_name', $settings['site_name'] ?? 'JaraMarket') }}" required
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('site_name') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('site_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <label for="site_description" class="block text-sm font-medium text-gray-700">
                                    Site Description
                                </label>
                                <div class="mt-1">
                                    <textarea id="site_description" name="site_description" rows="3"
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('site_description') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">{{ old('site_description', $settings['site_description'] ?? 'Your favorite food delivery service') }}</textarea>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Brief description of your website.</p>
                                @error('site_description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="timezone" class="block text-sm font-medium text-gray-700">
                                    Timezone
                                </label>
                                <div class="mt-1">
                                    <select id="timezone" name="timezone"
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @php
                                            $timezones = [
                                                'UTC' => 'UTC',
                                                'America/New_York' => 'Eastern Time (US & Canada)',
                                                'America/Chicago' => 'Central Time (US & Canada)',
                                                'America/Denver' => 'Mountain Time (US & Canada)',
                                                'America/Los_Angeles' => 'Pacific Time (US & Canada)',
                                                'Europe/London' => 'London',
                                                'Europe/Paris' => 'Paris',
                                                'Asia/Tokyo' => 'Tokyo',
                                                'Asia/Shanghai' => 'Shanghai',
                                                'Australia/Sydney' => 'Sydney',
                                            ];
                                        @endphp
                                        @foreach ($timezones as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('timezone', $settings['timezone'] ?? 'UTC') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('timezone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="date_format" class="block text-sm font-medium text-gray-700">
                                    Date Format
                                </label>
                                <div class="mt-1">
                                    <select id="date_format" name="date_format"
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @php
                                            $now = new DateTime();
                                            $formats = [
                                                'Y-m-d' => $now->format('Y-m-d'),
                                                'm/d/Y' => $now->format('m/d/Y'),
                                                'd/m/Y' => $now->format('d/m/Y'),
                                                'F j, Y' => $now->format('F j, Y'),
                                                'j F, Y' => $now->format('j F, Y'),
                                            ];
                                        @endphp
                                        @foreach ($formats as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('date_format', $settings['date_format'] ?? 'Y-m-d') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('date_format')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div x-show="activeTab === 'contact'" class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <label for="contact_email" class="block text-sm font-medium text-gray-700">
                                    Contact Email <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <input type="email" name="contact_email" id="contact_email"
                                        value="{{ old('contact_email', $settings['contact_email'] ?? 'contact@example.com') }}"
                                        required
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('contact_email') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('contact_email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-4">
                                <label for="contact_phone" class="block text-sm font-medium text-gray-700">
                                    Contact Phone
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="contact_phone" id="contact_phone"
                                        value="{{ old('contact_phone', $settings['contact_phone'] ?? '+1 (555) 123-4567') }}"
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('contact_phone') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('contact_phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <label for="address" class="block text-sm font-medium text-gray-700">
                                    Business Address
                                </label>
                                <div class="mt-1">
                                    <textarea id="address" name="address" rows="3"
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('address') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">{{ old('address', $settings['address'] ?? '123 Main St, Anytown, CA 12345') }}</textarea>
                                </div>
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-4">
                                <label for="support_email" class="block text-sm font-medium text-gray-700">
                                    Support Email
                                </label>
                                <div class="mt-1">
                                    <input type="email" name="support_email" id="support_email"
                                        value="{{ old('support_email', $settings['support_email'] ?? 'support@example.com') }}"
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('support_email') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('support_email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Payment & Shipping -->
                    <div x-show="activeTab === 'payment'" class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="currency" class="block text-sm font-medium text-gray-700">
                                    Currency <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <select id="currency" name="currency" required
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @php
                                            $currencies = [
                                                'USD' => 'US Dollar ($)',
                                                'EUR' => 'Euro (€)',
                                                'GBP' => 'British Pound (£)',
                                                'JPY' => 'Japanese Yen (¥)',
                                                'CAD' => 'Canadian Dollar (C$)',
                                                'AUD' => 'Australian Dollar (A$)',
                                                'INR' => 'Indian Rupee (₹)',
                                                'CNY' => 'Chinese Yuan (¥)',
                                            ];
                                        @endphp
                                        @foreach ($currencies as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('currency', $settings['currency'] ?? 'USD') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('currency')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="tax_rate" class="block text-sm font-medium text-gray-700">
                                    Tax Rate (%) <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <input type="number" name="tax_rate" id="tax_rate"
                                        value="{{ old('tax_rate', $settings['tax_rate'] ?? '8.5') }}" step="0.01"
                                        min="0" max="100" required
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('tax_rate') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('tax_rate')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="shipping_fee" class="block text-sm font-medium text-gray-700">
                                    Default Shipping Fee <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="shipping_fee" id="shipping_fee"
                                        value="{{ old('shipping_fee', $settings['shipping_fee'] ?? '5.99') }}"
                                        step="0.01" min="0" required
                                        class="focus:ring-green-500 focus:border-green-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md @error('shipping_fee') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('shipping_fee')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="payment_methods" class="block text-sm font-medium text-gray-700">
                                    Accepted Payment Methods
                                </label>
                                <div class="mt-2 space-y-2">
                                    @php
                                        $paymentMethods = [
                                            'credit_card' => 'Credit Card',
                                            'paypal' => 'PayPal',
                                            'bank_transfer' => 'Bank Transfer',
                                            'cash_on_delivery' => 'Cash on Delivery',
                                        ];
                                        $savedMethods = old(
                                            'payment_methods',
                                            $settings['payment_methods'] ?? 'credit_card,paypal',
                                        );
                                        $savedMethodsArray = explode(',', $savedMethods);
                                    @endphp

                                    @foreach ($paymentMethods as $value => $label)
                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="payment_{{ $value }}" name="payment_methods[]"
                                                    type="checkbox" value="{{ $value }}"
                                                    {{ in_array($value, $savedMethodsArray) ? 'checked' : '' }}
                                                    class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="payment_{{ $value }}"
                                                    class="font-medium text-gray-700">{{ $label }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('payment_methods')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="order_statuses" class="block text-sm font-medium text-gray-700">
                                    Available Order Statuses
                                </label>
                                <div class="mt-1">
                                    <textarea id="order_statuses" name="order_statuses" rows="3"
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('order_statuses') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                        placeholder="Enter each status on a new line">{{ old('order_statuses', $settings['order_statuses'] ?? "pending\nprocessing\nshipped\ndelivered\ncancelled") }}</textarea>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Enter each status on a new line.</p>
                                @error('order_statuses')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div x-show="activeTab === 'social'" class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <label for="social_facebook" class="block text-sm font-medium text-gray-700">
                                    Facebook URL
                                </label>
                                <div class="mt-1">
                                    <input type="url" name="social_facebook" id="social_facebook"
                                        value="{{ old('social_facebook', $settings['social_facebook'] ?? 'https://facebook.com/JaraMarket') }}"
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('social_facebook') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('social_facebook')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-4">
                                <label for="social_twitter" class="block text-sm font-medium text-gray-700">
                                    Twitter URL
                                </label>
                                <div class="mt-1">
                                    <input type="url" name="social_twitter" id="social_twitter"
                                        value="{{ old('social_twitter', $settings['social_twitter'] ?? 'https://twitter.com/JaraMarket') }}"
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('social_twitter') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('social_twitter')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-4">
                                <label for="social_instagram" class="block text-sm font-medium text-gray-700">
                                    Instagram URL
                                </label>
                                <div class="mt-1">
                                    <input type="url" name="social_instagram" id="social_instagram"
                                        value="{{ old('social_instagram', $settings['social_instagram'] ?? 'https://instagram.com/JaraMarket') }}"
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('social_instagram') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('social_instagram')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-4">
                                <label for="social_youtube" class="block text-sm font-medium text-gray-700">
                                    YouTube URL
                                </label>
                                <div class="mt-1">
                                    <input type="url" name="social_youtube" id="social_youtube"
                                        value="{{ old('social_youtube', $settings['social_youtube'] ?? 'https://youtube.com/JaraMarket') }}"
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('social_youtube') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('social_youtube')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-4">
                                <label for="social_tiktok" class="block text-sm font-medium text-gray-700">
                                    TikTok URL
                                </label>
                                <div class="mt-1">
                                    <input type="url" name="social_tiktok" id="social_tiktok"
                                        value="{{ old('social_tiktok', $settings['social_tiktok'] ?? 'https://tiktok.com/@JaraMarket') }}"
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('social_tiktok') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('social_tiktok')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide success message after 5 seconds
            setTimeout(function() {
                const successAlert = document.querySelector('.bg-green-50');
                if (successAlert) {
                    successAlert.remove();
                }
            }, 5000);

            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                // Additional client-side validation can be added here
            });
        });
    </script>
@endpush
