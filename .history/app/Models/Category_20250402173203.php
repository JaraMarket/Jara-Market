<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Category extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function products()
    {
    return $this->hasMany(Product::class);
    }
}
