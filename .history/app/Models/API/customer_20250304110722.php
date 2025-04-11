<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Has


class Customer extends Model
{
    use HasApiTokens, HasFactory;
    protected $guarded = [];

}
