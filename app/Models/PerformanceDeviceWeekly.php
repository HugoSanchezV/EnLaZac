<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceDeviceWeekly extends Model
{
    use HasFactory;
    protected $fillable = [
        'device_id',
        'amount',
        'rate',
        'byte',  
        'created_at'
    ];
    protected $casts = [
        'rate'  => 'array',
        'byte'  => 'array',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
