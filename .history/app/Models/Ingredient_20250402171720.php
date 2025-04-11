<?php
namespace App\Models\API;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Ingredient extends Model
{
    use HasFactory;
    protected $guarded = [];
    use HasApiTokens, Notifiable;

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
