<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;


class ProductController extends Controller
{
    public function fetchProductCategory()
    {
        // Fetch all data in Product category model with caching
        $data = Cache::remember('Product-categories', 60, function () {
            return Category::all();
        });

        return response()->json($data, 201);
    }

    public function fetchProduct()
    {
        // Fetch all data in food model with caching
        $data = Cache::remember('products', 60, function () {
            return Product::all();
        });
        return response()->json($data, 201);
    }
}
