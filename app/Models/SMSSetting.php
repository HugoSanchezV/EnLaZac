<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'active',       // Proveedor del servicio (Twilio, Nexmo, etc.)
        'account_sid',    // SID de la cuenta
        'auth_token',     // Token de autenticación
        'phone_number',   // Número de teléfono
    ];
}
