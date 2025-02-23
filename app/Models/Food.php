<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * Get the ingredients for the food.
     */
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    /**
     * Get the preparation steps for the food.
     */
    public function steps()
    {
        return $this->hasMany(Step::class);
    }
}
