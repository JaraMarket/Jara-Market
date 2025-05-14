<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Category extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    public function products()
    {
      return $this->belongsToMany(Product::class, 'category_product', 'category_id', 'product_id');
    }

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

}
