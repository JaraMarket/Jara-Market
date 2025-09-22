@extends('layouts.app')

@section('header')
    Edit Category
@endsection

@section('content')
<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Edit Category
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Update Category details.
        </p>
    </div>

    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
        <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="col-span-6 sm:col-span-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>

            <div class="col-span-6 sm:col-span-4">
                <label for="unit" class="block text-sm font-medium text-gray-700">Type</label>
                <select name="category_type_id" id="category_type_id" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <option value="">select</option>
                    @foreach($category_types as $row)
                    <option {{$category->category_type_id === $row->id ? 'selected' : ''}} value="{{$row->id}}">{{$row->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <label for="price" class="block text-sm font-medium text-gray-700">Sort Order</label>
                <input type="number" step="0.01" min="0" name="sort_by" id="sort_by" value="{{ old('sort_by', $category->sort_by) }}" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            </div>

            <div class="col-span-6">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('description', $category->description) }}</textarea>
            </div>

           

            <div class="flex justify-end">
                <a href="{{ route('categories.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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