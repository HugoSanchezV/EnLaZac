<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalPay extends Model
{
    use HasFactory;

    protected $table = 'local_pays';

    protected $fillable = [
        'user_id',
        'payment_info',
        'total_amount',
        'token',
        'status',
    ];

    protected $casts = [
        'payment_info' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class,  'user_id');
    }
}
