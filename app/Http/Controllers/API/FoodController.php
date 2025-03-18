<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\Category;
use App\Models\API\Food;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Foods",
 *     description="API Endpoints for managing food items and categories"
 * )
 */
class FoodController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/food/categories",
     *     tags={"Foods"},
     *     summary="Fetch all food categories",
     *     description="Get a list of all food categories with caching",
     *     @OA\Response(
     *         response=201,
     *         description="Categories retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
     *         )
     *     )
     * )
     */
    public function fetchfoodCategory()
    {
        // Fetch all data in foodcategory model with caching
        $data = Cache::remember('food-categories', 60, function () {
            return Category::all();
        });

        return response()->json($data, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/foods",
     *     tags={"Foods"},
     *     summary="Fetch all food items",
     *     description="Get a list of all food items with caching",
     *     @OA\Response(
     *         response=201,
     *         description="Food items retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
     *         )
     *     )
     * )
     */
    public function fetchfood()
    {
        // Fetch all data in food model with caching
        $data = Cache::remember('foods', 60, function () {
            return Food::all();
        });

        return response()->json($data, 201);
    }
    /**
     * @OA\Get(
     *     path="/api/food/ingredients",
     *     tags={"Foods"},
     *     summary="Fetch ingredients for a specific food",
     *     description="Get a list of ingredients for a specific food item with caching",
     *     @OA\Parameter(
     *         name="food_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ingredients retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Food ID is required",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Food ID is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No ingredients found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="No ingredients found for the selected food")
     *         )
     *     )
     * )
     */
    public function fetchingredient(Request $request)
{
    // Get the food ID from the request
    $foodId = $request->input('food_id');

    // Validate the food ID
    if (!$foodId) {
        return response()->json(['error' => 'Food ID is required'], 422);
    }

    // Use caching to store the result of the query
    $ingredients = Cache::remember("ingredients-$foodId", 60, function () use ($foodId) {
        return DB::table('ingredients')
            ->join('food_ingredients', 'ingredients.id', '=', 'food_ingredients.ingredient_id')
            ->where('food_ingredients.food_id', $foodId)
            ->select('ingredients.*')
            ->get();
    });

    // If no ingredients are found, return an error
    if ($ingredients->isEmpty()) {
        return response()->json(['error' => 'No ingredients found for the selected food'], 404);
    }

    // Return the ingredients as JSON
    return response()->json($ingredients, 201);
}
}
