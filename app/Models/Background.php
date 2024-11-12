<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Background extends Model
{
    use HasFactory;

    public $fillable =[
        'name',
        'period',
        'status',
    ];
}
