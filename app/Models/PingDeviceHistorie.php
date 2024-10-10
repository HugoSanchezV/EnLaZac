<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PingDeviceHistorie extends Model
{
    use HasFactory;
    protected $fillable = [
        'device_id',
        'router_id',
        'status',
        'created_at'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
