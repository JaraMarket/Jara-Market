<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Model;

class Ingredients extends Model
{
    use HasFactory;
    protected $guarded = [];
    use HasApiTokens, HasFactory, Notifiable;

}
