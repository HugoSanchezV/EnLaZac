<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MercadoPagoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'active',
        'mode',
        'sandbox_client_id',
        'sandbox_client_secret',
        'live_client_id',
        'live_client_secret',
        'currency',
    ];
}