@extends('layouts.app')

@section('title', 'Add New Product')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between mb-6">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Add New Product
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Create a new food product with ingredients and preparation steps.
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0">
                    <a href="{{ route('products.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                clip-rule="evenodd" />
                        </svg>
                        Back to Products
                    </a>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data"
                    class="divide-y divide-gray-200">
                    @csrf

                    <!-- Basic Information -->
                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Product Name <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price (Calculated) -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Calculated Price</label>
                                <div class="mt-1">
                                    <input type="text" name="price" id="calculated_price" readonly class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-gray-50">
                                    <p class="mt-1 text-sm text-gray-500">Price is calculated based on ingredients</p>
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <div class="mt-1">
                                    <textarea id="description" name="description" rows="3"
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('description') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">{{ old('description') }}</textarea>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Brief description of the product.</p>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <label for="categories" class="block text-sm font-medium text-gray-700">
                                    Categories <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($categories as $category)
                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="category-{{ $category->id }}" name="categories[]"
                                                    type="checkbox" value="{{ $category->id }}"
                                                    {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                                                    class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="category-{{ $category->id }}"
                                                    class="font-medium text-gray-700">{{ $category->name }}</label>
                                                @if ($category->description)
                                                    <p class="text-gray-500">{{ Str::limit($category->description, 50) }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('categories')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Ingredients -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Ingredients</label>
                                <div id="ingredients-container" class="mt-2 space-y-4">
                                    <div class="ingredient-item flex gap-4">
                                        <div class="flex-1">
                                            <select name="ingredients[0][ingredient_id]" class="ingredient-select block rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                                <option value="">Select Ingredient</option>
                                                @foreach($ingredients as $ingredient)
                                                    <option value="{{ $ingredient->id }}" 
                                                            data-price="{{ $ingredient->price }}"
                                                            data-unit="{{ $ingredient->unit }}">
                                                        {{ $ingredient->name }} ({{ $ingredient->price }}/{{ $ingredient->unit }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="w-32">
                                            <input type="number" name="ingredients[0][quantity]" min="0.01" step="0.01" value="1" class="quantity-input block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                        </div>
                                        <div class="w-32">
                                            <select name="ingredients[0][unit]" class="unit-select block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                                <option value="piece">Piece</option>
                                                <option value="kg">Kilogram</option>
                                                <option value="g">Gram</option>
                                                <option value="l">Liter</option>
                                                <option value="ml">Milliliter</option>
                                                <option value="cup">Cup</option>
                                                <option value="tbsp">Tablespoon</option>
                                                <option value="tsp">Teaspoon</option>
                                            </select>
                                        </div>
                                        <div class="flex items-center">
                                            <button type="button" class="remove-ingredient text-red-600 hover:text-red-800">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="add-ingredient" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Add Ingredient
                                </button>
                            </div>

                            <div class="sm:col-span-6">
                                <label for="preparation_steps" class="block text-sm font-medium text-gray-700">
                                    Preparation Steps <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <textarea id="preparation_steps" name="preparation_steps" rows="6" required
                                        class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('preparation_steps') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                        placeholder="Enter each step on a new line">{{ old('preparation_steps') }}</textarea>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Detailed steps to prepare this food item.</p>
                                @error('preparation_steps')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                                <div class="mt-1 flex items-center">
                                    <img id="image-preview" class="hidden h-32 w-32 object-cover rounded-lg" src="#" alt="Image preview">
                                    <div class="ml-4">
                                        <input type="file" name="image" id="image" accept="image/*" 
                                            class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md @error('image') border-red-500 @enderror"
                                            onchange="previewImage(this)">
                                    </div>
                                </div>
                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('products.index') }}"
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mr-2">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview functionality
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');
            const imagePreviewContainer = document.getElementById('image-preview-container');

            imageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreviewContainer.style.display = 'block';
                    }

                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Rich text editor for description (optional - can be enhanced with a library like TinyMCE or CKEditor)
            // This is a placeholder for where you might initialize a rich text editor

            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                let hasError = false;

                // Check if at least one category is selected
                const categoryCheckboxes = document.querySelectorAll('input[name="categories[]"]:checked');
                if (categoryCheckboxes.length === 0) {
                    event.preventDefault();
                    hasError = true;
                    alert('Please select at least one category');
                }

                // Additional client-side validation can be added here
            });

            const ingredientsContainer = document.getElementById('ingredients-container');
            const addIngredientButton = document.getElementById('add-ingredient');
            const calculatedPriceInput = document.getElementById('calculated_price');
            let ingredientCount = 1;

            // Add new ingredient
            addIngredientButton.addEventListener('click', function() {
                const newIngredient = document.createElement('div');
                newIngredient.className = 'ingredient-item flex gap-4 mt-4';
                newIngredient.innerHTML = `
                    <div class="flex-1">
                        <select name="ingredients[${ingredientCount}][ingredient_id]" class="ingredient-select block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Select Ingredient</option>
                            @foreach($ingredients as $ingredient)
                                <option value="{{ $ingredient->id }}" 
                                        data-price="{{ $ingredient->price }}"
                                        data-unit="{{ $ingredient->unit }}">
                                    {{ $ingredient->name }} ({{ $ingredient->price }}/{{ $ingredient->unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-32">
                        <input type="number" name="ingredients[${ingredientCount}][quantity]" min="0.01" step="0.01" value="1" class="quantity-input block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div class="w-32">
                        <select name="ingredients[${ingredientCount}][unit]" class="unit-select block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="piece">Piece</option>
                            <option value="kg">Kilogram</option>
                            <option value="g">Gram</option>
                            <option value="l">Liter</option>
                            <option value="ml">Milliliter</option>
                            <option value="cup">Cup</option>
                            <option value="tbsp">Tablespoon</option>
                            <option value="tsp">Teaspoon</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <button type="button" class="remove-ingredient text-red-600 hover:text-red-800">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                `;
                ingredientsContainer.appendChild(newIngredient);
                ingredientCount++;
            });

            // Remove ingredient
            ingredientsContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-ingredient')) {
                    const ingredient = e.target.closest('.ingredient-item');
                    if (ingredientsContainer.children.length > 1) {
                        ingredient.remove();
                        calculatePrice();
                    }
                }
            });

            // Calculate price
            function calculatePrice() {
                let total = 0;
                document.querySelectorAll('.ingredient-item').forEach(item => {
                    const ingredientSelect = item.querySelector('.ingredient-select');
                    const quantityInput = item.querySelector('.quantity-input');
                    const unitSelect = item.querySelector('.unit-select');
                    const selectedOption = ingredientSelect.options[ingredientSelect.selectedIndex];
                    
                    if (selectedOption && selectedOption.value) {
                        const pricePerUnit = parseFloat(selectedOption.dataset.price);
                        const baseUnit = selectedOption.dataset.unit;
                        const quantity = parseFloat(quantityInput.value);
                        const selectedUnit = unitSelect.value;
                        
                        // Convert quantity to base unit
                        const convertedQuantity = convertToBaseUnit(quantity, selectedUnit, baseUnit);
                        total += convertedQuantity * pricePerUnit;
                    }
                });
                calculatedPriceInput.value = total.toFixed(2);
            }

            // Convert units
            function convertToBaseUnit(quantity, fromUnit, toUnit) {
                const conversionRates = {
                    'kg': { 'g': 1000, 'piece': 1 },
                    'g': { 'kg': 0.001, 'piece': 1 },
                    'l': { 'ml': 1000, 'cup': 4, 'tbsp': 66.67, 'tsp': 200 },
                    'ml': { 'l': 0.001, 'cup': 0.004, 'tbsp': 0.067, 'tsp': 0.2 },
                    'cup': { 'l': 0.25, 'ml': 250, 'tbsp': 16, 'tsp': 48 },
                    'tbsp': { 'l': 0.015, 'ml': 15, 'cup': 0.0625, 'tsp': 3 },
                    'tsp': { 'l': 0.005, 'ml': 5, 'cup': 0.0208, 'tbsp': 0.333 },
                    'piece': { 'kg': 1, 'g': 1 }
                };

                if (fromUnit === toUnit) return quantity;
                if (!conversionRates[fromUnit] || !conversionRates[fromUnit][toUnit]) return quantity;
                
                return quantity * conversionRates[fromUnit][toUnit];
            }

            // Update price when ingredients change
            ingredientsContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('ingredient-select') || 
                    e.target.classList.contains('quantity-input') || 
                    e.target.classList.contains('unit-select')) {
                    calculatePrice();
                }
            });

            // Initial price calculation
            calculatePrice();
        });

        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
