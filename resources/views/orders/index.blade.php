@extends('layouts.app')

@section('header', 'Order Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Orders</h1>
        <a href="#" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
            Create New Order
        </a>
    </div>

    <!-- Filters -->
    <div class="flex gap-4 mb-4">
        <select id="status-filter" class="border rounded px-3 py-2">
            <option value="">All</option>
            <option value="processing" selected>Processing</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table id="orders-table" class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th>ID</th>
            <th>Reference</th>
            <th>Customer</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
    </table>
    </div>
</div>
@endsection
@section('scripts')
<script>
$(function () {
    // Initialize DataTable
    var table = $('#orders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('orders.data') }}",
            data: function (d) {
                d.status = $('#status-filter').val(); // pass dropdown value
            }
        },
        columns: [
            { data: null, orderable: false, searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'reference' },
            { data: 'customer', name: 'customer' },
            { data: 'total' },
            { data: 'status' },
            { data: 'created_at' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        responsive: true
    });

    $('#status-filter').on('change', function () {
        table.ajax.reload();
    });
});
</script>

@endsection
