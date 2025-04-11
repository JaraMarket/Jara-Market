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
        // Validate the request to ensure the food ID is provided
        $this->validate($request, [
            'food_id' => 'required|integer',
        ]);

        // Retrieve the food ID from the request
        $foodId = $request->input('food_id');

        // Retrieve the ingredients for the selected food
        $ingredients = Food::find($foodId)->ingredients;

        // Return the ingredients as a JSON response
        return response()->json($ingredients, 201);
    }
}
