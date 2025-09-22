<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class FoodService
{
    public function __construct()
    { }

    public function create(array $data){
        return DB::transaction(function () use ($data){
            $image_url = upload_image("food", $data['image_url']);
            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'preparation_steps' => $data['preparation_steps'],
                'image_url' => $image_url
            ]);
            $product->categories()->attach($data['categories']);
            foreach ($data['ingredients'] as $ingredient) {
                $product->ingredients()->attach($ingredient['ingredient_id'], [
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit']
                ]);
            }
            return $product;
        });
    }

    public function update(array $data, Product $product){
        return DB::transaction(function () use ($data, $product){
            if (!empty($data['image_url'])) {
                $image_url = upload_image("food", $data['image_url'], $product->image_url);
            } else {
                $image_url = $product->image_url;
            }
           
            $product->update([
                'name' => $data['name'],
                'rating' => $data['rating'],
                'description' => $data['description'],
                'price' => $data['price'],
                'discount_price' => $data['discount_price'],
                'preparation_steps' => $data['preparation_steps'],
                'image_url' => $image_url,
                'stock'  => $data['stock'],
            ]);

            $product->categories()->sync($data['categories']);

            foreach ($data['ingredients'] as $ingredient) {
                $product->ingredients()->sync($ingredient['ingredient_id'], [
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit']
                ]);
            }
            return $product;
        });
    }

    public function delete($product)
    {
        return DB::transaction(function () use ($product) {
            if ($product->image_url && Storage::disk('public')->exists($product->image_url)) {
                Storage::disk('public')->delete($product->image_url);
            }
            $product->categories()->detach();
            $product->ingredients()->detach();
            $product->delete();
            return true;
        });
    }
}