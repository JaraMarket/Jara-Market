<?php
namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use HasFactory;
    protected $guarded = [];
    use HasApiTokens, HasFactory, Notifiable;
    protected $primaryKey = 'id';
    public $incrementing = true;
    public function referrer()
    {
        return $this->belongsTo(self::class, 'referrer_id');
    }
}
