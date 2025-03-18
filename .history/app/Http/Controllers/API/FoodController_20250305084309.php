<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\FoodCategory;
use App\Models\API\FoodStuff;

class FoodController extends Controller
{
    public function fetchfoodCategory()
    {
        //fetch all data in foodcategory model
        $data = FoodCategory::all();
        return response()->json($data, 201);
    }
        public function fetchfoodStuff()
    {
        //fetch all data in food stuff model
        $data = FoodStuff::all();
        return response()->json($data, 201);
    }
}
