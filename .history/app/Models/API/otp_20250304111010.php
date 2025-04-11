<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class otp extends Model
{
    use HasApiTokens, HasFactory;
    protected $guarded = [];
}
