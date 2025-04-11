<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function fetchProductCategory()
    {
        // Fetch all data in category model with caching
        $data = Cache::remember('Product-categories', 60, function () {
            return Category::with('products')->get();
        });

        return response()->json($data, 201);
    }

    public function fetchingredient()
    {
        $data = Cache::remember('ingredients', 60, function () {
            return Ingredient::with('products')->get();
        });
        return response()->json($data, 201);
    }

    public function fetchProduct()
    {
        // Fetch all data in Product model with caching
        $data = Cache::remember('products', 60, function () {
            return Product::with('ingredients')->get();
        });
        return response()->json($data, 201);
    }
}
