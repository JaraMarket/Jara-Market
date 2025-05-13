<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $favorites = Favorite::where('user_id', $user->id)
            ->with('product')
            ->get();
            
        return response()->json([
            'status' => 'success',
            'data' => $favorites
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = Auth::user();
        
        // Check if already favorited
        $existingFavorite = Favorite::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingFavorite) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product already in favorites'
            ], 400);
        }

        $favorite = Favorite::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to favorites',
            'data' => $favorite
        ], 201);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $favorite = Favorite::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$favorite) {
            return response()->json([
                'status' => 'error',
                'message' => 'Favorite not found'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Favorite removed successfully'
        ]);
    }
} 