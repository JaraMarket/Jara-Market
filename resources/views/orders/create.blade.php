@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Create New Order</h1>
                <a href="{{ route('orders.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-300">
                    Back to Orders
                </a>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- User Selection -->
                    <div class="col-span-2">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">User</label>
                        <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Meal Prep Instructions -->
                    <div class="col-span-2">
                        <label for="meal_prep" class="block text-sm font-medium text-gray-700">Meal Preparation Instructions</label>
                        <textarea name="meal_prep" id="meal_prep" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Enter any specific instructions for meal preparation (optional)">{{ old('meal_prep') }}</textarea>
                    </div>

                    <!-- Order Items -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Order Items</label>
                        <div class="mt-2 space-y-4">
                            @for($i = 0; $i < 3; $i++)
                                <div class="order-item flex gap-4">
                                    <div class="flex-1">
                                        <select name="items[{{ $i }}][product_id]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" {{ $i == 0 ? 'required' : '' }}>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ old("items.$i.product_id") == $product->id ? 'selected' : '' }}>{{ $product->name }} - ₦{{ number_format($product->price, 2) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-32">
                                        <input type="number" name="items[{{ $i }}][quantity]" min="1" value="{{ old("items.$i.quantity", 1) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" {{ $i == 0 ? 'required' : '' }}>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Total Amount -->
                    <div class="col-span-2">
                        <label for="total" class="block text-sm font-medium text-gray-700">Total Amount</label>
                        <div class="mt-1">
                            <input type="number" step="0.01" name="total" id="total" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required value="{{ old('total') }}">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-300">
                        Create Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 