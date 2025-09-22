@extends('layouts.app')

@section('header')
    Edit Commission
@endsection

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Edit Commission
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Update Commission details.
        </p>
    </div>

    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
        <form action="{{ route('commissions.update', $commission) }}" method="POST"  class="space-y-6">
            @csrf
            @method('PUT')

            <div class="col-span-6 sm:col-span-4">
                <label for="min_amount" class="block text-sm font-medium text-gray-700">Minimum Amount</label>
                <input type="text" name="min_amount" id="min_amount" value="{{ old('min_amount', $commission->min_amount) }}" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="col-span-6 sm:col-span-4">
                <label for="max_amount" class="block text-sm font-medium text-gray-700">Maximum Amount</label>
                <input type="text" name="max_amount" id="max_amount" value="{{ old('max_amount', $commission->max_amount) }}" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="col-span-6 sm:col-span-4">
                <label for="percentage" class="block text-sm font-medium text-gray-700">Percentage</label>
                <input type="text" name="percentage" id="percentage" value="{{ old('percentage', $commission->percentage) }}" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>          

            <div class="flex justify-end">
                <a href="{{ route('commissions.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Cancel
                </a>
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 