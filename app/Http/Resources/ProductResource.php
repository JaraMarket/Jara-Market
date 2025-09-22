<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'stock'   => $this->stock,
            'preparation_steps' => collect(explode(',', $this->preparation_steps))->map(fn($step) => trim($step))->toArray(),
            'rating' => $this->rating,
            'image_url' => get_media_url($this->image_url),
            'ingredients' => IngredientResource::collection($this->whenLoaded('ingredients')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'created_at' => $this->created_at->diffForHumans()
        ];
    }
}
