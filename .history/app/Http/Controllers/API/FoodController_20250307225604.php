<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\Category;
use App\Models\API\Food;
use Illuminate\Support\Facades\DB;


class FoodController extends Controller
{
    public function fetchfoodCategory()
    {
        //fetch all data in foodcategory model
        $data = Category::all();
        return response()->json($data, 201);
    }
    public function fetchfood()
    {
        //fetch all data in food model
        $data = Food::all();
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

    $ingredients = DB::table('ingredients')
        ->join('food_ingredients', 'ingredients.id', '=', 'food_ingredient.ingredient_id')
        ->where('food_ingredient.food_id', $foodId)
        ->select('ingredients.*')
        ->get();

    // If no ingredients are found, return an error
    if ($ingredients->isEmpty()) {
        return response()->json(['error' => 'No ingredients found for the selected food'], 404);
    }

    // Return the ingredients as JSON
    return response()->json($ingredients, 201);
}
}
