<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Router extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'sync',
        'ip_address',
        'user',
        'password',
    ];
}
