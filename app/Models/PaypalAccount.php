<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaypalAccount extends Model
{
    use HasFactory;
    protected $fillable = ["mode", "live_client_id", "live_client_secret", "currency"];
}
