<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientResource extends JsonResource
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
            'price' => number_format($this->price,2),
            'discounted_price' => number_format($this->price, 2),
            'unit' => $this->unit,
            'stock' => $this->stock,
            'image_url' => get_media_url($this->image_url),
            'products'  => ProductResource::collection($this->whenLoaded('products')),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
