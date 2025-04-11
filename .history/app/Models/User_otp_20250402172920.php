<?php
namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User_otp extends Model
{
    use HasFactory;
    protected $fillable = [
        'otp',
        'user_id'
    ];

}
