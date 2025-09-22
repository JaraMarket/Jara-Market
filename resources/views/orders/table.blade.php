<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meal Prep</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $order->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $order->reference }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $order->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">₦{{ number_format($order->total, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 inline-flex text-xs font-semibold rounded-full
                                {{ $order->status === 'processing' ? 'bg-yellow-100 text-yellow-800' :
                                   ($order->status === 'completed' ? 'bg-green-100 text-green-800' :
                                   'bg-red-100 text-red-800') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 truncate max-w-xs">
                            {{ $order->meal_prep ?? 'No instructions' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $order->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ route('orders.show', $order) }}" class="text-green-600 hover:text-green-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
