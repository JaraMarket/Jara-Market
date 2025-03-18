<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Cas
class Food extends Model
{
    use HasFactory;
    protected $guarded = [];
    use HasApiTokens, HasFactory, Notifiable;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function food_stuffs()
    {
        return $this->hasMany(FoodStuff::class);
    }

    public function foods()
    {
        return $this->hasMany(Food::class);
    }
}
