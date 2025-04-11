<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Food;

/**
 * @OA\Info(title="JaraMarket API", version="1.0")
 * @OA\Server(url="http://localhost:8000")
 * @OA\Tag(
 *     name="Foods",
 *     description="API Endpoints for managing food items"
 * )
 */
class FoodController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/foods",
     *     summary="List all foods",
     *     description="Returns a list of all food items with their ingredients and steps",
     *     operationId="listFoods",
     *     tags={"Foods"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Jollof Rice"),
     *                 @OA\Property(property="description", type="string", example="Nigerian party-style jollof rice"),
     *                 @OA\Property(property="ingredients", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Rice"),
     *                     @OA\Property(property="quantity", type="string", example="2 cups")
     *                 )),
     *                 @OA\Property(property="steps", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="description", type="string", example="Wash the rice thoroughly"),
     *                     @OA\Property(property="order", type="integer", example=1)
     *                 )),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Food::with(['ingredients', 'steps'])->get();
    }

    /**
     * @OA\Post(
     *     path="/api/foods",
     *     summary="Create a new food item",
     *     description="Creates a new food item with ingredients and preparation steps",
     *     operationId="createFood",
     *     tags={"Foods"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Jollof Rice"),
     *             @OA\Property(property="description", type="string", example="Nigerian party-style jollof rice"),
     *             @OA\Property(property="ingredients", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="name", type="string", example="Rice"),
     *                 @OA\Property(property="quantity", type="string", example="2 cups")
     *             )),
     *             @OA\Property(property="steps", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="description", type="string", example="Wash the rice thoroughly"),
     *                 @OA\Property(property="order", type="integer", example=1)
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Food item created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Jollof Rice"),
     *             @OA\Property(property="description", type="string", example="Nigerian party-style jollof rice"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/foods/{id}",
     *     summary="Get a specific food item",
     *     description="Returns detailed information about a specific food item including ingredients and steps",
     *     operationId="getFood",
     *     tags={"Foods"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of food item to return",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Jollof Rice"),
     *             @OA\Property(property="description", type="string", example="Nigerian party-style jollof rice"),
     *             @OA\Property(property="ingredients", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Rice"),
     *                 @OA\Property(property="quantity", type="string", example="2 cups")
     *             )),
     *             @OA\Property(property="steps", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="description", type="string", example="Wash the rice thoroughly"),
     *                 @OA\Property(property="order", type="integer", example=1)
     *             )),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Food item not found"
     *     )
     * )
     */
    public function show($id)
    {
        return Food::with(['ingredients', 'steps'])->findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/foods/{id}",
     *     summary="Update a food item",
     *     description="Updates an existing food item with ingredients and steps",
     *     operationId="updateFood",
     *     tags={"Foods"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of food item to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Jollof Rice"),
     *             @OA\Property(property="description", type="string", example="Updated Nigerian party-style jollof rice"),
     *             @OA\Property(property="ingredients", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="name", type="string", example="Rice"),
     *                 @OA\Property(property="quantity", type="string", example="3 cups")
     *             )),
     *             @OA\Property(property="steps", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="description", type="string", example="Rinse the rice until water runs clear"),
     *                 @OA\Property(property="order", type="integer", example=1)
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Food item updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Food item updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Food item not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $food = Food::findOrFail($id);
        $food->update($request->only('name', 'description'));

        if ($request->has('ingredients')) {
            $food->ingredients()->delete();
            foreach ($request->ingredients as $ingredient) {
                $food->ingredients()->create($ingredient);
            }
        }

        if ($request->has('steps')) {
            $food->steps()->delete();
            foreach ($request->steps as $step) {
                $food->steps()->create($step);
            }
        }

        return response()->json(['message' => 'Food item updated successfully']);
    }

    /**
     * @OA\Delete(
     *     path="/api/foods/{id}",
     *     summary="Delete a food item",
     *     description="Deletes a food item and its associated ingredients and steps",
     *     operationId="deleteFood",
     *     tags={"Foods"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of food item to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Food item deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Food item deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Food item not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $food = Food::findOrFail($id);
        $food->ingredients()->delete();
        $food->steps()->delete();
        $food->delete();

        return response()->json(['message' => 'Food item deleted successfully']);
    }
}
