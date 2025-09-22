@extends('layouts.app')

@section('header', 'Create Commission')

@section('content')
<div class="py-4">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form action="{{ route('commissions.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="min_amount" class="block text-sm font-medium text-gray-700">Minimum Amount</label>
                        <input type="text" name="min_amount" id="min_amount" required
                            class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('min_amount') border-red-500 @enderror"
                            value="{{ old('min_amount') }}">
                        @error('min_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_amount" class="block text-sm font-medium text-gray-700">Maximum Amount</label>
                        <input type="text" name="max_amount" id="max_amount" required
                            class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('max_amount') border-red-500 @enderror"
                            value="{{ old('max_amount') }}">
                        @error('max_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="percentage" class="block text-sm font-medium text-gray-700">Percentage</label>
                        <input type="text" name="percentage" id="percentage" required
                            class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 @enderror"
                            value="{{ old('percentage') }}">
                        @error('percentage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('commissions.index') }}" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Create Commission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection