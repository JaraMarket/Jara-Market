<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'category_id',
        'vendor_id',
        'preparation_steps',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class)
            ->withPivot('quantity', 'unit')
            ->withTimestamps();
    }

    public function calculatePrice()
    {
        $total = 0;
        foreach ($this->ingredients as $ingredient) {
            $quantity = $ingredient->pivot->quantity;
            $unit = $ingredient->pivot->unit;
            $pricePerUnit = $ingredient->price_per_unit;
            
            // Convert quantity to base unit if needed
            $convertedQuantity = $this->convertToBaseUnit($quantity, $unit);
            $total += $convertedQuantity * $pricePerUnit;
        }
        
        return $total;
    }

    private function convertToBaseUnit($quantity, $unit)
    {
        $conversionRates = [
            'kg' => 1,
            'g' => 0.001,
            'l' => 1,
            'ml' => 0.001,
            'piece' => 1,
            'cup' => 0.25, // Assuming 1 cup = 0.25 liters
            'tbsp' => 0.015, // Assuming 1 tbsp = 15ml
            'tsp' => 0.005, // Assuming 1 tsp = 5ml
        ];

        return $quantity * ($conversionRates[$unit] ?? 1);
    }
}
