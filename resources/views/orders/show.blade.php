@extends('layouts.app')

@section('header', 'Order Details')

@section('content')
    <div class="py-4">
        <!-- Back button -->
        <div class="mb-5">
            <a href="{{ route('orders.index') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Orders
            </a>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-5">
            <div class="px-4 py-5 sm:px-6 flex justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Order #{{ $order->id }}</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $order->created_at->format('F d, Y h:i A') }}</p>
                </div>
                <div>
                    <form action="{{ route('orders.update.status', $order) }}" method="POST" class="flex items-center space-x-4">
                        @csrf
                        @method('PATCH')
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="status" value="pending" {{ $order->status === 'pending' ? 'checked' : '' }}
                                    class="form-radio h-4 w-4 text-yellow-600 border-gray-300 focus:ring-yellow-500">
                                <span class="ml-2 text-sm text-gray-700">Pending</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="status" value="in_progress" {{ $order->status === 'in_progress' ? 'checked' : '' }}
                                    class="form-radio h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">In Progress</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="status" value="completed" {{ $order->status === 'completed' ? 'checked' : '' }}
                                    class="form-radio h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                <span class="ml-2 text-sm text-gray-700">Completed</span>
                            </label>
                        </div>
                        <button type="submit"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Customer Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->user->name }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $order->user->email }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">₦{{ number_format($order->total, 2) }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Shipping Fee</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            ₦{{ number_format($order->shipping_fee, 2) }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-5">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Order Items</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">List of items in this order.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            \Log::info('Order items:', ['items' => $order->items->toArray()]);
                        @endphp
                        @foreach ($order->items as $item)
                            @php
                                \Log::info('Order item:', ['item' => $item->toArray(), 'product' => $item->product ? $item->product->toArray() : null]);
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/150"
                                                alt="Product image">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ₦{{ number_format($item->price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ₦{{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <th scope="row" colspan="3"
                                class="px-6 py-3 text-right text-sm font-medium text-gray-500">Subtotal</th>
                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900">
                                ₦{{ number_format($order->total - $order->shipping_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="3"
                                class="px-6 py-3 text-right text-sm font-medium text-gray-500">Shipping</th>
                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900">
                                ₦{{ number_format($order->shipping_fee, 2) }}</td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="3"
                                class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Total</th>
                            <td class="px-6 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                                ₦{{ number_format($order->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusModal = document.getElementById('status-modal');
            const statusButtons = document.querySelectorAll('.status-change');
            const closeButtons = document.querySelectorAll('.close-modal');

            // Show modal when clicking Change Status button
            statusButtons.forEach(button => {
                button.addEventListener('click', () => {
                    statusModal.classList.remove('hidden');
                });
            });

            // Hide modal when clicking close button or outside the modal
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    statusModal.classList.add('hidden');
                });
            });

            // Hide modal when clicking outside
            window.addEventListener('click', (e) => {
                if (e.target === statusModal) {
                    statusModal.classList.add('hidden');
                }
            });
        });
    </script>
@endpush
