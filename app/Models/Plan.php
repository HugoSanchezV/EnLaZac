<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    public $fillable =[
        'id',
        'name',
        'price',
        'description',
        'burst_limit',
        'burst_threshold',
        'burst_time',
        'limite_at',
        'max_limit',
    ];
    protected $casts = [
        'burst_limit'  => 'array',
        'burst_threshold'  => 'array',
        'burst_time'  => 'array',
        'limite_at'  => 'array',
        'max_limit'  => 'array',
    ];
}