<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class FoodStuff extends Model
{
    use HasFactory;
    protected $guarded = [];
    use HasApiTokens, HasFactory, Notifiable;

    public function foodCategory()
    {
        return $this->belongsTo(FoodCategory::class);
    }
    public function foodCategory()
    {
        return $this->belongsTo(FoodCategory::class);
    }
}
