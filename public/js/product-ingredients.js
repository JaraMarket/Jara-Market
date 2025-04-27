document.addEventListener('DOMContentLoaded', function () {
    const ingredientsContainer = document.getElementById('ingredients-container');
    const addIngredientButton = document.getElementById('add-ingredient');
    let ingredientCount = document.querySelectorAll('.ingredient-item').length;

    // Add new ingredient row
    addIngredientButton.addEventListener('click', function () {
        const newRow = document.createElement('div');
        newRow.className = 'ingredient-item flex gap-4 mt-4';
        newRow.innerHTML = `
            <div class="flex-1">
                <select name="ingredients[${ingredientCount}][ingredient_id]" class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    <option value="">Select Ingredient</option>
                    ${window.ingredients.map(ing => `<option value="${ing.id}">${ing.name}</option>`).join('')}
                </select>
            </div>
            <div class="w-32">
                <input type="number" name="ingredients[${ingredientCount}][quantity]" step="0.01" min="0" placeholder="Quantity" class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="w-32">
                <select name="ingredients[${ingredientCount}][unit]" class="shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md">
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
        ingredientsContainer.appendChild(newRow);
        ingredientCount++;
    });

    // Remove ingredient row
    ingredientsContainer.addEventListener('click', function (e) {
        if (e.target.closest('.remove-ingredient')) {
            const ingredientItem = e.target.closest('.ingredient-item');
            if (ingredientsContainer.children.length > 1) {
                ingredientItem.remove();
            }
        }
    });
}); 