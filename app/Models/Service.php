<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    public $fillable =[
        'id',
        'contract_id',
        'charge',
        'mounths',
        'total',
    ];
}