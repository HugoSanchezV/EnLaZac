<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceDevice extends Model
{
    use HasFactory;
    protected $fillable = [
        'device_id',
        'rate',
        'byte',  
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
