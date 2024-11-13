<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MercadoPagoAccount extends Model
{
    use HasFactory;

    // Define los campos que se pueden llenar masivamente.
    protected $fillable = [
        'mode',               // Modo: sandbox o live.
        'sandbox_public_key', // Clave pública para sandbox.
        'sandbox_access_token', // Token de acceso para sandbox.
        'live_public_key',    // Clave pública para live.
        'live_access_token',  // Token de acceso para live.
        'currency',           // Moneda en formato ISO (ej: USD).
    ];
}
