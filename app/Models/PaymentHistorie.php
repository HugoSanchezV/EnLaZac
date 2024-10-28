<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistorie extends Model
{
    use HasFactory;
    public $fillable =[
        'id',
        'user_id',
        'contract_id',
        'amount',
        'content',
        'payment_method',
        'transaction_id',
        'receipt_url',
    ];
}
