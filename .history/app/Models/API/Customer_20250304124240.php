<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\Contracts\HasApiTokens;


class Customer extends Model
{
    use HasApiTokens;
    protected $guarded = [];

}
