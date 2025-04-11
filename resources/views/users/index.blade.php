@extends('layouts.app')

@section('header', 'User Management')

@section('content')
    <div class="py-4">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h2 class="text-lg leading-6 font-medium text-gray-900">Users</h2>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">A list of all users registered in your application.</p>
                </div>
                <div class="flex space-x-3">
                    <div class="relative">
                        <select id="status-filter"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Users</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="relative">
                        <input type="text" id="search" name="search" placeholder="Search users..."
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Registered On</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $user->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full"
                                                src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                                                alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('users.edit', $user) }}"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    {{-- <button type="button" class="text-indigo-600 hover:text-indigo-900 mr-3 status-toggle"
                                        data-user-id="{{ $user->id }}"
                                        data-status="{{ $user->active ? 'active' : 'inactive' }}">
                                        {{ $user->active ? 'Deactivate' : 'Activate' }}
                                    </button> --}}
                                    <button type="button" class="text-red-600 hover:text-red-900 delete-user"
                                        data-user-id="{{ $user->id }}">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="delete-form" action="{{ route('users.destroy', 0) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Delete User
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete this user? All of their data will be permanently
                                        removed. This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
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
        // Delete user modal functionality
        const deleteModal = document.getElementById('delete-modal');
        const deleteButtons = document.querySelectorAll('.delete-user');
        const closeButtons = document.querySelectorAll('.close-modal');
        const deleteForm = document.getElementById('delete-form');

        deleteButtons.forEach(button => {
            button.addEventListener('click', () => {
                const userId = button.getAttribute('data-user-id');
                deleteForm.action = deleteForm.action.replace(/\/\d+$/, `/${userId}`);
                deleteModal.classList.remove('hidden');
            });
        });

        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                deleteModal.classList.add('hidden');
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                deleteModal.classList.add('hidden');
            }
        });

        // Status toggle functionality
        const statusToggleButtons = document.querySelectorAll('.status-toggle');

        statusToggleButtons.forEach(button => {
            button.addEventListener('click', async () => {
                const userId = button.getAttribute('data-user-id');
                const currentStatus = button.getAttribute('data-status');
                const newStatus = currentStatus === 'active' ? 'inactive' : 'active';

                // We'd typically make an AJAX request to toggle the status
                try {
                    // Example AJAX request (you would implement this)
                    // const response = await fetch(`/users/${userId}/toggle-status`, {
                    //     method: 'POST',
                    //     headers: {
                    //         'Content-Type': 'application/json',
                    //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    //     }
                    // });

                    // if (response.ok) {
                    //     // Toggle the button text and data attributes
                    //     button.textContent = newStatus === 'active' ? 'Deactivate' : 'Activate';
                    //     button.setAttribute('data-status', newStatus);

                    //     // Update the status badge
                    //     const statusBadge = button.closest('tr').querySelector('td:nth-child(5) span');
                    //     statusBadge.textContent = newStatus === 'active' ? 'Active' : 'Inactive';
                    //     statusBadge.className = `px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${newStatus === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
                    // }

                    // For demonstration purposes, let's just reload the page
                    alert(`User status would be toggled to ${newStatus}`);
                    // window.location.reload();
                } catch (error) {
                    console.error('Error toggling user status:', error);
                }
            });
        });

        // Filter and search functionality (same concept as orders)
        const statusFilter = document.getElementById('status-filter');
        const searchInput = document.getElementById('search');

        statusFilter.addEventListener('change', filterUsers);
        searchInput.addEventListener('input', filterUsers);

        function filterUsers() {
            const status = statusFilter.value;
            const search = searchInput.value.toLowerCase();

            // You would typically use AJAX to filter on the server
            // This is a placeholder for the actual implementation
            window.location.href = `{{ route('users.index') }}?status=${status}&search=${search}`;
        }
    </script>
@endsection
