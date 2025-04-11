<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class FoodCategory extends Model
{
    use HasFactory;
    protected $guarded = [];
    use HasApiTokens, HasFactory, Notifiable;

    public function foodStuffs()
    {
    return $this->hasMany(FoodStuff::class, 'category_id);
    }
}
