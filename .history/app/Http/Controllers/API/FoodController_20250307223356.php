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
    public function fetchfood()
    {
        //fetch all data in food model
        $data = Food::all();
        return response()->json($data, 201);
    }
    public function fetchingredient(Request $request)
    {



    }
}
