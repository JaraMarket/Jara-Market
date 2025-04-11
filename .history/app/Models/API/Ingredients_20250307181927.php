<?php

// app/Models/API/Ingredient.php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Ingredient extends Model
{
    use HasFactory;
    protected $guarded = [];
    use HasApiTokens, HasFactory, Notifiable;

    public function foods()
    {
        return $this->belongsToMany(Food::class, 'foods');
    }
}
