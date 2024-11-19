<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistorie extends Model
{
    use HasFactory;
    public $fillable = [
        'id',
        'user_id',
        'worker',
        'amount',
        'content',
        'payment_method',
        'transaction_id',
        'receipt_url',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
