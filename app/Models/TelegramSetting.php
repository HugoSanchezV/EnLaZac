<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramSetting extends Model
{
    //
    protected $fillable = [
        'active',
        'api_id',
        'hash'
    ];
}
