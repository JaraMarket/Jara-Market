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
        //fetch all data in food stuff model using with. remember foodstuff and foodcategory has a relationship
        $data = FoodStuff::with('foodcategory')->get();
        return response()->json($data, 201);
    }


}
