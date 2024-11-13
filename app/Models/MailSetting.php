<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    use HasFactory;
    public $fillable =[
        'transport',
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'address',
        'name',
    ];
}
