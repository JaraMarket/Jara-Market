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
        'unit',
        'price_per_unit'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'unit')
            ->withTimestamps();
    }
}
