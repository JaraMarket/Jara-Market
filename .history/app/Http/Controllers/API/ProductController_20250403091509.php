<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


class ProductController extends Controller
{
    public function fetchProductCategory()
    {
        // Fetch all data in foodcategory model with caching
        $data = Cache::remember('Product-categories', 60, function () {
            return Category::all();
        });

        return response()->json($data, 201);
    }

    public function fetchProduct()
    {
        // Fetch all data in food model with caching
        $data = Cache::remember('products', 60, function () {
            return Product::all();
        });

        return response()->json($data, 201);
    }
    public function fetchingredient(Request $request)
{
    // Get the food ID from the request
    $ProductId = $request->input('product_id');

    // Validate the food ID
    if (!$ProductId) {
        return response()->json(['error' => 'Product ID is required'], 422);
    }

    // Use caching to store the result of the query
    $ingredients = Cache::remember("ingredients-$foodId", 60, function () use ($foodId) {
        return DB::table('ingredients')
            ->join('food_ingredients', 'ingredients.id', '=', 'food_ingredients.ingredient_id')
            ->where('food_ingredients.food_id', $foodId)
            ->select('ingredients.*')
            ->get();
    });

    // If no ingredients are found, return an error
    if ($ingredients->isEmpty()) {
        return response()->json(['error' => 'No ingredients found for the selected food'], 404);
    }

    // Return the ingredients as JSON
    return response()->json($ingredients, 201);
}
}
