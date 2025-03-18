<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\Category;
use F
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

    // Fetch the food with the given ID
    $food = Food::find($foodId);

    // If the food is not found, return an error
    if (!$food) {
        return response()->json(['error' => 'Food not found'], 404);
    }

    // Fetch the ingredients for the food
    $ingredients = $food->ingredients;

    // Return the ingredients as JSON
    return response()->json($ingredients, 201);
}
}
