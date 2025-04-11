<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\Category;
use App\Models\API\Food;

class FoodController extends Controller
{
    public function fetchfoodCategory()
    {
        //fetch all data in foodcategory model
        $data = Category::all();
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

        // Retrieve the food and its associated ingredients
        $food = Food::find($foodId);
        if (!$food) {
            return response()->json(['error' => 'Food not found'], 404);
        }

        $ingredients = $food->ingredients;
        return response()->json($ingredients, 201);
    }
}
