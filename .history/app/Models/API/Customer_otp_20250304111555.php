<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\Contracts\HasApiTokens;

class customer_otps extends Model
{
    use HasApiTokens, HasFactory;
    protected $guarded = [];
}
