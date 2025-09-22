@extends('layouts.app')

@section('header')
    Ingredients
@endsection

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Ingredients List</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage your ingredients here.</p>
        </div>
        <a href="{{ route('ingredients.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
            Add New Ingredient
        </a>
    </div>

    <div class="border-t border-gray-200">
        <div class="overflow-x-auto px-4 py-4">
            <table id="ingredients-table" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Unit</th>
                        <th>Stock</th>
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
    let table = $('#ingredients-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("ingredients.data") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'category', name: 'category' },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description', orderable: false, searchable: false },
            { data: 'price', name: 'price', orderable: false, searchable: false },
            { data: 'unit', name: 'unit' },
            { data: 'stock', name: 'stock', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        order: [[3, 'asc']],
        responsive: true
    });

    // Delete
    $(document).on('click', '.delete-ingredient', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: `/ingredients/${id}`,
                    type: 'DELETE',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function() {
                        table.ajax.reload();
                        Swal.fire('Deleted!', 'Ingredient has been deleted.', 'success');
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to delete ingredient.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush