@extends('layouts.app')

@section('header', 'Vendor Management')

@section('content')
<div class="py-4">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h2 class="text-lg leading-6 font-medium text-gray-900">Vendors</h2>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">A list of all vendors registered in your application.</p>
            </div>
            <div class="flex space-x-3">
                <div class="relative">
                    <select id="status-filter"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md">
                        <option value="">All Vendors</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div
            </div>
        </div>
    </div>
        <div class="overflow-x-auto">
            <table id="vendors-table" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th>SN</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                        <th>Business Name</th>
                        <th>Business Address</th>
                        <th>Shop Size</th>
                        <th>Registered On</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    let table = $('#vendors-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('vendors.data') }}",
            data: function (d) {
                d.status = $('#status-filter').val();
                d.search = $('#search').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'firstname', name: 'firstname' },
            { data: 'lastname', name: 'lastname' },
            { data: 'phone_number', name: 'phone_number' },
            { data: 'email', name: 'email' },
            { data: 'business_name', name: 'business_name' },
            { data: 'business_address', name: 'business_address' },
            { data: 'shop_size', name: 'shop_size' },
            { data: 'created_at', name: 'created_at' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        responsive: true,
        order: [[3, 'desc']]
    });

     // Filters
     $('#status-filter').change(() => table.ajax.reload());
     $('#search').keyup(() => table.ajax.reload());

    // Delete vendor with SweetAlert2
    $(document).on('click', '.delete-vendor', function () {
        let vendorId = $(this).data('vendor-id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This vendor will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/vendors/${vendorId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        table.ajax.reload(null, false);
                        Swal.fire(
                            'Deleted!',
                            'Vendor has been deleted.',
                            'success'
                        );
                    },
                    error: function () {
                        Swal.fire(
                            'Error!',
                            'Something went wrong. Vendor not deleted.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endsection
