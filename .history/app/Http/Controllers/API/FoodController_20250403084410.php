<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Food;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


class FoodController extends Controller
{
    public function fetchfoodCategory()
    {
        // Fetch all data in foodcategory model with caching
        $data = Cache::remember('food-categories', 60, function () {
            return Category::all();
        });

        return response()->json($data, 201);
    }

    public function fetchfood()
    {
        // Fetch all data in food model with caching
        $data = Cache::remember('foods', 60, function () {
            return Product::all();
        });

        return response()->json($data, 201);
    }
    public function fetchingredient(Request $request)
{
    // Get the food ID from the request
    $foodId = $request->input('food_id');

    // Validate the food ID
    if (!$foodId) {
        return response()->json(['error' => 'Food ID is required'], 422);
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
