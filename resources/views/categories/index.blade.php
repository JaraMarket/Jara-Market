@extends('layouts.app')

@section('header', 'Category Management')

@section('content')
<div class="py-4">
    <div class="mb-5 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Categories</h2>
            <p class="text-sm text-gray-600">Manage food categories for your store</p>
        </div>
        <a href="{{ route('categories.create') }}" 
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            id="create-category-btn">
            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Category
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto px-4 py-4">
            <table id="categories-table" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Sort Order</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(function() {
    let table = $('#categories-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('categories.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'type_name', name: 'type_name', defaultContent: 'N/A' },
            { data: 'sort_by', name: 'sort_by' },
            { data: 'description', name: 'description', defaultContent: '' },
            { data: 'products_count', name: 'products_count', searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });

    // Delete category
    $(document).on('click', '.delete-category', function() {
        let id = $(this).data('id');
        if(confirm('Are you sure you want to delete this category?')) {
            $.ajax({
                url: '/categories/' + id,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function() { table.ajax.reload(); },
                error: function() { alert('Failed to delete category'); }
            });
        }
    });
});
</script>
@endpush
