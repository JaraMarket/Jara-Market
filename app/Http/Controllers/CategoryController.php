<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

/**
 * @OA\Info(title="JaraMarket API", version="1.0")
 * @OA\Server(url="http://localhost:8000")
 * @OA\Tag(
 *     name="Categories",
 *     description="API Endpoints for managing product categories"
 * )
 */
class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="List all categories",
     *     description="Returns a list of all product categories",
     *     operationId="listCategories",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Electronics"),
     *                 @OA\Property(property="description", type="string", example="Electronic devices and accessories"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $categories = Category::withCount('products')->latest()->paginate(10);
        return view('categories.index', compact('categories'));
    }

    /** 
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Get a category by ID",
     *     description="Returns a single product category by its ID",
     *     operationId="getCategoryById",
     *        tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of category to return",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Electronics"),
     *             @OA\Property(property="description", type="string", example="Electronic devices and accessories"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function show($id)
    {
        $category = Category::with('products')->findOrFail($id);
        
        if (request()->wantsJson()) {
            return response()->json($category);
        }

        return view('categories.show', compact('category'));
    }
    
    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create a new category",
     *     description="Creates a new product category",
     *     operationId="createCategory",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Electronics"),
     *             @OA\Property(property="description", type="string", example="Electronic devices and accessories")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Electronics"),
     *             @OA\Property(property="description", type="string", example="Electronic devices and accessories"),
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
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string'
        ]);

        $category = Category::create($validated);

        if ($request->wantsJson()) {
            return response()->json($category, 201);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     summary="Update a category",
     *     description="Updates an existing product category",
     *     operationId="updateCategory",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of category to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Electronics"),
     *             @OA\Property(property="description", type="string", example="Updated description for electronic devices")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Category updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        $category->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Category updated successfully']);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Delete a category",
     *     description="Deletes an existing product category",
     *     operationId="deleteCategory",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of category to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Category deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Category deleted successfully']);
        }

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function create()
    {
        return view('categories.create');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('categories.edit', compact('category'));
    }
}
