<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Uom;
use App\Services\FoodService;
use App\Enums\CategoryTypeEnum;
use Exception;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index');
    }

    public function getData(Request $request)
    {
        $query = Product::with('categories')->select('products.*');
    
        // Search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
    
        // Category filter (many-to-many)
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }
    
        // Stock filter
        if ($request->stock === 'in_stock') {
            $query->where('stock', '>', 10);
        } elseif ($request->stock === 'low_stock') {
            $query->whereBetween('stock', [1, 10]);
        } elseif ($request->stock === 'out_of_stock') {
            $query->where('stock', 0);
        }
    
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('image', function ($product) {
                if ($product->image_url) {
                    return get_media_url($product->image_url); // just return URL for JS render
                }
                return null;
            })
            ->addColumn('categories', function ($product) {
                // Convert categories collection to plain array of objects for JS
                return $product->categories->map(function($c) {
                    return ['name' => $c->name];
                })->toArray();
            })
            ->editColumn('price', function ($product) {
                if ($product->discount_price) {
                    return "<span class='text-green-600 font-semibold'>₦".number_format($product->discount_price, 2)."</span>
                            <span class='line-through text-gray-400 ml-1'>₦".number_format($product->price, 2)."</span>";
                }
                return "₦".number_format($product->price, 2);
            })
            ->editColumn('stock', function ($product) {
                if ($product->stock > 10) {
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">'.$product->stock.' in stock</span>';
                } elseif ($product->stock > 0) {
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Low stock: '.$product->stock.'</span>';
                }
                return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Out of stock</span>';
            })
            ->addColumn('rating', function ($product) {
                return $product->rating ?? 0;
            })
            ->addColumn('actions', function ($product) {
                return '
                    <a href="'.route('products.edit', $product).'" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                    <button type="button" class="text-red-600 hover:text-red-900 delete-product" data-product-id="'.$product->id.'">Delete</button>
                ';
            })
            ->rawColumns(['price','stock','actions'])
            ->make(true);
    } 

    public function create()
    {
        $categories = Category::where('category_type_id', CategoryTypeEnum::FOOD())->get();
        $ingredients = Ingredient::all();
        $ingredientss = []; // if you want an empty array as placeholder
        $uoms = Uom::all();

        return view('products.create', compact('categories', 'ingredients', 'ingredientss', 'uoms'));
    }

    public function store(ProductRequest $request)
    {
        try {
            $data = $request->validated();
            app(FoodService::class)->create($data);

            return redirect()->back()
                ->with('success', 'Food created successfully');
        }  catch (Exception $e) {      
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::where('category_type_id', CategoryTypeEnum::FOOD())->get();
        $ingredients = Ingredient::all();
        $uoms = Uom::all();

        return view('products.edit', compact('product', 'categories', 'ingredients', 'uoms'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        try {
            $data = $request->validated();
            app(FoodService::class)->update($data, $product);

            return redirect()->back()
                ->with('success', 'Food updated successfully');
        }  catch (Exception $e) {      
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try{
            app(FoodService::class)->delete($product);

            return redirect()->back()
                ->with('success', 'Food deleted successfully');
        }  catch (Exception $e) {      
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
        
    }
}
