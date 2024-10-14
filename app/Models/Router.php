<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Router extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'sync',
        'ip_address',
        'user',
        'password',
    ];

    public function devices()
    {
        return $this->hasMany(Device::class, 'router_id');
    }

    public function networks()
    {
        return $this->hasMany(Network::class, 'router_id');
    }
}
