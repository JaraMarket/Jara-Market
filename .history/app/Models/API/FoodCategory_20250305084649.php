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

    public function foodStuffs(): HasMany
    {
        return $this->hasMany(FoodStuff::class);
    }

    public function vehicle(){

        return $this->hasMany(Vehicle::class);

    }
}
