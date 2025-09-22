<?php

namespace App\Http\Controllers\API;

use App\Enums\CategoryTypeEnum;
use App\Models\Uom;
use App\Models\Product;
use App\Models\Category;
use App\Models\Ingredient;
use App\Http\Resources\UomResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\IngredientResource;
use App\Filters\FoodFilter\Type as FoodFilterType;
use App\Filters\FoodFilter\Name as FoodFilterByName;
use App\Filters\FoodFilter\Search as FoodFilterSearch;

class ProductController extends Controller
{
    public function getCategoriesAllProducts()
    {
        // Fetch all data in category model with caching
        $data = Category::with('products.ingredients')->where('category_type_id', CategoryTypeEnum::FOOD())->whereNull('deleted_at')->orderBy('sort_by')->get();
        
        return response()->json([
            'message' => 'Categories retrieved successfuly',
            'data' => CategoryResource::collection($data)
        ], 201);
    }

    public function getVendorCategories()
    {
        // Fetch all data in category model with caching
        $data = Category::with('ingredients')->where('category_type_id', CategoryTypeEnum::VENDOR())->whereNull('deleted_at')->orderBy('sort_by')->get();
        
        return response()->json([
            'message' => 'Categories retrieved successfuly',
            'data' => CategoryResource::collection($data)
        ], 201);
    }

    public function getCategoriesLimitProducts()
    {
        $data =  Category::with(['products' => function ($query) {
                    $query->orderBy('created_at', 'desc')
                        ->limit(8)
                        ->with('ingredients');
                }])
                ->where('category_type_id', CategoryTypeEnum::FOOD())
                ->whereNull('deleted_at')
                ->orderBy('sort_by')
                ->get();

        return response()->json([
            'message' => 'Categories retrieved successfully',
            'data' => CategoryResource::collection($data)
        ], 200);
    }

    public function fetchingredient()
    {
        $data = Ingredient::orderby('name', 'asc')->get();        
        return response()->json([
            'message' => 'Ingredient retrieved successfuly',
            'data' => IngredientResource::collection($data)
        ], 201);
    }

    public function fetchProduct()
    {
        $products = Product::with('ingredients', 'categories')
                ->filterWithPipeline([
                    FoodFilterByName::class,
                    FoodFilterSearch::class,
                    FoodFilterType::class
                ])
                ->orderBy('id', 'desc')
                ->get();

        return response()->json([
            'message' => 'Food retrieved successfuly',
            'data' => ProductResource::collection($products)
        ], 201);
    }

    public function getProductById($id)
    {
        $products =  Product::with('ingredients', 'categories')
                ->filterWithPipeline([
                    FoodFilterByName::class,
                    FoodFilterSearch::class,
                    FoodFilterType::class
                ])
                ->orderBy('id', 'desc')
                ->first();

        return response()->json([
            'message' => 'Food retrieved successfuly',
            'data' => new ProductResource($products)
        ], 201);
    }

    public function fetchUom()
    {
        $data = Uom::orderby('name', 'asc')->get();        
        return response()->json([
            'message' => 'Unit of measurement retrieved successfuly',
            'data' => UomResource::collection($data)
        ], 201);
    }
}
