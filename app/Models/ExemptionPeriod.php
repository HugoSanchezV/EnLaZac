<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExemptionPeriod extends Model
{
    use HasFactory;
    public $fillable =[
        'start_day',
        'end_day',
        'month_next',
        'month_after_next'
    ];

    
    
}
