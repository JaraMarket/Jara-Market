@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Edit Product</h1>
                <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-300">
                    Back to Products
                </a>
            </div>

            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Name -->
                    <div class="col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="price" id="price" step="0.01" value="{{ old('price', $product->price) }}"
                                class="pl-7 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('price') border-red-500 @enderror">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Discount Price -->
                    <div>
                        <label for="discount_price" class="block text-sm font-medium text-gray-700">Discount Price (Optional)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="discount_price" id="discount_price" step="0.01" value="{{ old('discount_price', $product->discount_price) }}"
                                class="pl-7 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('discount_price') border-red-500 @enderror">
                        </div>
                        @error('discount_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock -->
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('stock') border-red-500 @enderror">
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rating -->
                    <div>
                        <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                        <input type="number" name="rating" id="rating" min="0" max="5" step="0.1" value="{{ old('rating', $product->rating) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('rating') border-red-500 @enderror">
                        @error('rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Categories -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Categories</label>
                        <div class="mt-2 space-y-2">
                            @foreach($categories as $category)
                                <div class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}" id="category_{{ $category->id }}"
                                        {{ in_array($category->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'checked' : '' }}
                                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                    <label for="category_{{ $category->id }}" class="ml-2 block text-sm text-gray-900">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('categories')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price (Calculated) -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Calculated Price</label>
                        <div class="mt-1">
                            <input type="text" id="calculated_price" readonly class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 bg-gray-50">
                            <p class="mt-1 text-sm text-gray-500">Price is calculated based on ingredients</p>
                        </div>
                    </div>

                    <!-- Ingredients -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Ingredients</label>
                        <div id="ingredients-container" class="mt-2 space-y-4">
                            @foreach($product->ingredients as $index => $ingredient)
                            <div class="ingredient-item flex gap-4">
                                <div class="flex-1">
                                    <select name="ingredients[{{ $index }}][ingredient_id]" class="ingredient-select block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                        <option value="">Select Ingredient</option>
                                        @foreach($ingredients as $ing)
                                            <option value="{{ $ing->id }}" 
                                                    data-price="{{ $ing->price }}"
                                                    data-unit="{{ $ing->unit }}"
                                                    {{ $ing->id == $ingredient->id ? 'selected' : '' }}>
                                                {{ $ing->name }} ({{ $ing->price }}/{{ $ing->unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-32">
                                    <input type="number" name="ingredients[{{ $index }}][quantity]" min="0.01" step="0.01" value="{{ $ingredient->pivot->quantity }}" class="quantity-input block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                </div>
                                <div class="w-32">
                                    <select name="ingredients[{{ $index }}][unit]" class="unit-select block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                        <option value="piece" {{ $ingredient->pivot->unit == 'piece' ? 'selected' : '' }}>Piece</option>
                                        <option value="kg" {{ $ingredient->pivot->unit == 'kg' ? 'selected' : '' }}>Kilogram</option>
                                        <option value="g" {{ $ingredient->pivot->unit == 'g' ? 'selected' : '' }}>Gram</option>
                                        <option value="l" {{ $ingredient->pivot->unit == 'l' ? 'selected' : '' }}>Liter</option>
                                        <option value="ml" {{ $ingredient->pivot->unit == 'ml' ? 'selected' : '' }}>Milliliter</option>
                                        <option value="cup" {{ $ingredient->pivot->unit == 'cup' ? 'selected' : '' }}>Cup</option>
                                        <option value="tbsp" {{ $ingredient->pivot->unit == 'tbsp' ? 'selected' : '' }}>Tablespoon</option>
                                        <option value="tsp" {{ $ingredient->pivot->unit == 'tsp' ? 'selected' : '' }}>Teaspoon</option>
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
                            @endforeach
                        </div>
                        <div class="mt-2">
                            <button type="button" id="add-ingredient" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Ingredient
                            </button>
                        </div>
                    </div>

                    <!-- Preparation Steps -->
                    <div class="col-span-2">
                        <label for="preparation_steps" class="block text-sm font-medium text-gray-700">Preparation Steps</label>
                        <textarea name="preparation_steps" id="preparation_steps" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('preparation_steps') border-red-500 @enderror">{{ old('preparation_steps', $product->preparation_steps) }}</textarea>
                        @error('preparation_steps')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-300">
                        Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ingredientsContainer = document.getElementById('ingredients-container');
    const addIngredientButton = document.getElementById('add-ingredient');
    const calculatedPriceInput = document.getElementById('calculated_price');
    let ingredientCount = {{ count($product->ingredients) }};

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
</script>
@endpush
@endsection 