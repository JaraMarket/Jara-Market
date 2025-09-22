<?php

namespace App\Http\Controllers;

use Exception;
use App\Enums\CategoryTypeEnum;
use App\Models\Category;
use App\Models\Uom;
use App\Models\Ingredient;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index()
    {
        return view('ingredients.index');
    }

    public function getData()
    {
        $query = Ingredient::with('category')->select('ingredients.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('image', function($ingredient) {
                if ($ingredient->image_url) {
                    return '<img src="'.get_media_url($ingredient->image_url).'" class="h-10 w-10 rounded-full object-cover">';
                }
                return '<div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>';
            })
            ->addColumn('category', function($ingredient) {
                return $ingredient->category?->name ?? 'Uncategorized';
            })
            ->editColumn('price', function($ingredient) {
                if($ingredient->discounted_price) {
                    return "₦".number_format($ingredient->discounted_price,2)." <span class='line-through text-gray-400'>₦".number_format($ingredient->price,2)."</span>";
                }
                return "₦".number_format($ingredient->price,2);
            })
            ->editColumn('stock', function($ingredient) {
                if($ingredient->stock > 10) return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">'.$ingredient->stock.'</span>';
                if($ingredient->stock > 0) return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">'.$ingredient->stock.'</span>';
                return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">0</span>';
            })
            ->addColumn('actions', function($ingredient) {
                return '
                    <a href="'.route('ingredients.edit', $ingredient).'" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                    <button type="button" class="text-red-600 hover:text-red-900 delete-ingredient" data-id="'.$ingredient->id.'">Delete</button>
                ';
            })
            ->rawColumns(['image','price','stock','actions'])
            ->make(true);
    }

    public function create()
    {
        $units = Uom::all();
        $categories = Category::where('category_type_id', CategoryTypeEnum::VENDOR())->orderby('sort_by')->get();
        return view('ingredients.create', compact('units','categories'));
    }

    public function store(Request $request)
    {
        try {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ingredients',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:20',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $ingredient = new Ingredient();
        $ingredient->name = $validated['name'];
        $ingredient->description = $validated['description'];
        $ingredient->price = $validated['price'];
        $ingredient->category_id = $validated['category_id'];
        $ingredient->discounted_price = $validated['discounted_price'];
        $ingredient->unit = $validated['unit'];
        $ingredient->stock = $validated['stock'];

        if ($request->hasFile('image')) {
            $ingredient->image_url = upload_image("ingredients", $request->image);
        }

        $ingredient->save();

        return redirect()->back()
            ->with('success', 'Ingredient created successfully.');
    } catch (Exception $e) {      
        return redirect()->back()
            ->with('error', $e->getMessage());
    }
    }

    public function edit(Ingredient $ingredient)
    {
        $units = Uom::all();
        $categories = Category::where('category_type_id', CategoryTypeEnum::VENDOR())->orderby('sort_by')->get();
        return view('ingredients.edit', compact('ingredient', 'units','categories'));
    }

    public function show(Ingredient $ingredient)
    {
        $units = Uom::all();
        $categories = Category::where('category_type_id', CategoryTypeEnum::VENDOR())->orderby('sort_by')->get();
        return view('ingredients.edit', compact('ingredient', 'units','categories'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        try{
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:ingredients,name,' . $ingredient->id,
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'discounted_price' => 'nullable|numeric|min:0',
                'unit' => 'required|string|max:20',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $ingredient->name = $validated['name'];
            $ingredient->description = $validated['description'];
            $ingredient->price = $validated['price'];
            $ingredient->discounted_price = $validated['discounted_price'];
            $ingredient->unit = $validated['unit'];
            $ingredient->stock = $validated['stock'];
            $ingredient->category_id = $validated['category_id'];

            if ($request->hasFile('image')) {
                $ingredient->image_url = upload_image("ingredients", $request->image, $ingredient->image_url);
            }

            $ingredient->save();

            return redirect()->back()
                ->with('success', 'Ingredient updated successfully.');
        } catch (Exception $e) {      
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
}

    public function destroy(Ingredient $ingredient)
    {
       try {
        $ingredient->delete();
        return redirect()->back()
            ->with('success', 'Ingredient deleted successfully.');
       } catch (Exception $e) {      
            return redirect()->back()
            ->with('error', $e->getMessage());
        }
    }
} 