<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'discount_price',
        'stock',
        'rating'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
