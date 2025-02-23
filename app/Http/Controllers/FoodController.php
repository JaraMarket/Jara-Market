<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Food;

/**
 * @OA\Info(title="JaraMarket API", version="1.0")
 * @OA\Server(url="http://localhost:8000")
 * @OA\PathItem(
 *     path="/orders",
 *     description="Operations related to orders"
 * )
 */
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
