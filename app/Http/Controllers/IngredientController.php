<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::latest()->paginate(10);
        return view('ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('ingredients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ingredients',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:20',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $ingredient = new Ingredient();
        $ingredient->name = $validated['name'];
        $ingredient->description = $validated['description'];
        $ingredient->price = $validated['price'];
        $ingredient->discounted_price = $validated['discounted_price'];
        $ingredient->unit = $validated['unit'];
        $ingredient->stock = $validated['stock'];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('ingredients', 'public');
            $ingredient->image_url = Storage::url($path);
        }

        $ingredient->save();

        return redirect()->route('ingredients.index')
            ->with('success', 'Ingredient created successfully.');
    }

    public function edit(Ingredient $ingredient)
    {
        return view('ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ingredients,name,' . $ingredient->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:20',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $ingredient->name = $validated['name'];
        $ingredient->description = $validated['description'];
        $ingredient->price = $validated['price'];
        $ingredient->discounted_price = $validated['discounted_price'];
        $ingredient->unit = $validated['unit'];
        $ingredient->stock = $validated['stock'];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($ingredient->image_url) {
                $oldPath = str_replace('/storage/', '', $ingredient->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('image')->store('ingredients', 'public');
            $ingredient->image_url = Storage::url($path);
        }

        $ingredient->save();

        return redirect()->route('ingredients.index')
            ->with('success', 'Ingredient updated successfully.');
    }

    public function destroy(Ingredient $ingredient)
    {
        // Delete image if exists
        if ($ingredient->image_url) {
            $path = str_replace('/storage/', '', $ingredient->image_url);
            Storage::disk('public')->delete($path);
        }

        $ingredient->delete();

        return redirect()->route('ingredients.index')
            ->with('success', 'Ingredient deleted successfully.');
    }
} 