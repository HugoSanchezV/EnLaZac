<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventorieDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'mac_address',
        'status',
        'description',
        'brand',
    ];

    public function device()
    {
        return $this->hasOne(Device::class, 'id');
    }
    public function histories()
    {
        return $this->hasMany(DeviceHistorie::class, 'id');
    }
}
