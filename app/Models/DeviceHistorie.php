<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceHistorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'state',
        'comment',
        'device_id',
        'user_id',
        'creator_id'
    ];

    public function device()
    {
        return $this->belongsTo(InventorieDevice::class, 'device_id');
    }
}
