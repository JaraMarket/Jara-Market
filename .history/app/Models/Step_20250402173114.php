<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'description'];

    /**
     * Get the food that owns the step.
     */
    public function pro()
    {
        return $this->belongsTo(Food::class);
    }
}
