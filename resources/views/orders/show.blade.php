@extends('layouts.app')

@section('header', 'Order Details')

@section('content')
    <div class="py-4">
        <!-- Back button -->
        <div class="mb-5">
            <a href="{{ route('orders.index') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
                    <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                    {{ $order->status === 'completed'
                        ? 'bg-green-100 text-green-800'
                        : ($order->status === 'processing'
                            ? 'bg-blue-100 text-blue-800'
                            : ($order->status === 'cancelled'
                                ? 'bg-red-100 text-red-800'
                                : 'bg-yellow-100 text-yellow-800')) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                    <button type="button"
                        class="ml-3 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 status-change"
                        data-order-id="{{ $order->id }}">
                        Change Status
                    </button>
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
                        @foreach ($order->orderItems as $item)
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

@section('modals')
    <!-- Status Change Modal (same as in index.blade.php) -->
    <div id="status-modal" class="fixed inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="status-form" action="{{ route('orders.update.status') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="order_id" id="order_id">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Update Order Status
                                </h3>
                                <div class="mt-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select id="status" name="status"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="pending">Pending</option>
                                        <option value="processing">Processing</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Update
                        </button>
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm close-modal">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Status change modal functionality (same as in index.blade.php)
        const statusModal = document.getElementById('status-modal');
        const statusButtons = document.querySelectorAll('.status-change');
        const closeButtons = document.querySelectorAll('.close-modal');
        const orderIdInput = document.getElementById('order_id');

        statusButtons.forEach(button => {
            button.addEventListener('click', () => {
                const orderId = button.getAttribute('data-order-id');
                orderIdInput.value = orderId;
                statusModal.classList.remove('hidden');
            });
        });

        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                statusModal.classList.add('hidden');
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === statusModal) {
                statusModal.classList.add('hidden');
            }
        });
    </script>
@endsection
