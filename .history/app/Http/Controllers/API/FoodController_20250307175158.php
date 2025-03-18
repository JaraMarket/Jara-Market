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
        public function fetchfoodStuff()
    {
        //fetch all ingredient model using with. remember foodstuff and foodcategory has a relationship. display foodstuff with related food category


        }
}
