<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User_otp extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'otp',
        'email'
    ];

}
