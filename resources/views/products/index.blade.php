@extends('layouts.app')

@section('header', 'Food')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Food</h2>
                <p class="text-sm text-gray-500">Manage your food products</p>
            </div>
            <a href="{{ route('products.create') }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md shadow hover:bg-green-700">
                + Add Food
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6 grid grid-cols-1 gap-4 sm:grid-cols-6">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" id="search" placeholder="Product name..."
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select id="category"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="">All Categories</option>
                        @foreach (\App\Models\Category::all() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Stock</label>
                    <select id="stock"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="">All</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low_stock">Low Stock (&lt; 10)</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table id="products-table" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Image</th>
                        <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Food Name</th>
                        <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Rating</th>
                        <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
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
$(document).ready(function () {
    let table = $('#products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('products.data') }}",
            data: function (d) {
                d.search = $('#search').val();
                d.category = $('#category').val();
                d.stock = $('#stock').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // #
            { 
                data: 'image', 
                name: 'image', 
                orderable: false, 
                searchable: false,
                render: function(data) {
                    if(data){
                        return `<img src="${data}" class="h-10 w-10 rounded-full object-cover">`;
                    }
                    return `<div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>`;
                }
            }, 
            { data: 'name', name: 'name' },
            { data: 'price', name: 'price', orderable: false },
            { data: 'stock', name: 'stock', orderable: false },
            { 
                data: 'categories', 
                name: 'categories', 
                orderable: false, 
                searchable: false,
                render: function(data) {
                    if(Array.isArray(data) && data.length){
                        return data.map(c => `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-1">${c.name}</span>`).join(' ');
                    }
                    return '<span class="text-gray-400">No categories</span>';
                }
            },
            { 
                data: 'rating', 
                name: 'rating', 
                orderable: false, 
                searchable: false,
                render: function(data) {
                    if(!data) return '<span class="text-gray-400">Not rated</span>';
                    let stars = '';
                    for(let i = 1; i <= 5; i++){
                        stars += `<svg class="h-5 w-5 ${i <= data ? 'text-yellow-400' : 'text-gray-300'}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>`;
                    }
                    return `<div class="flex items-center">${stars}<span class="ml-1 text-gray-600">${data.toFixed(1)}</span></div>`;
                }
            },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        responsive: true,
        order: [[2, 'asc']]
    });

    // Filters
    $('#search, #category, #stock').on('change keyup', function () {
        table.ajax.reload();
    });

    // Delete
    $(document).on('click', '.delete-product', function () {
        let productId = $(this).data('product-id');
        Swal.fire({
            title: 'Delete this product?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/products/${productId}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function () {
                        table.ajax.reload();
                        Swal.fire('Deleted!', 'Product removed successfully.', 'success');
                    },
                    error: function () {
                        Swal.fire('Error!', 'Failed to delete product.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
