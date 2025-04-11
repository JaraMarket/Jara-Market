<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\FoodCategory;


class FoodController extends Controller
{
    public function fetchfoodCategory()
    {
        //fetch all data in foodcategory model
        $data = FoodCategory::all();
        return response()->json($data, 201);
        }
}
