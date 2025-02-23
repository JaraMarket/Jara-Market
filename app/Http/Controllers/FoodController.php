<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Food;

class FoodController extends Controller
{
    public function store(Request $request)
    {
        $food = Food::create($request->only('name', 'description'));

        foreach ($request->ingredients as $ingredient) {
            $food->ingredients()->create($ingredient);
        }

        foreach ($request->steps as $step) {
            $food->steps()->create($step);
        }

        return response()->json($food, 201);
    }
}
