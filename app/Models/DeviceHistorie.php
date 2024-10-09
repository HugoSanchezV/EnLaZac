<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function inventorieDevice()
    {
        return $this->belongsTo(InventorieDevice::class, 'device_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }
}
