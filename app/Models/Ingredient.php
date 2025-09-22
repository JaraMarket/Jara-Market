<?php
namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Ingredient extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'description',
        'price',
        'discounted_price',
        'unit',
        'stock',
        'image_url',
        'category_id'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'ingredient_product', 'product_id', 'ingredient_id')
            ->withPivot('quantity', 'unit')
            ->withTimestamps();
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'unit', 'code');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
