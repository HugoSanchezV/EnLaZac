<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuralCommunity extends Model
{
    use HasFactory;
    public $fillable =[
        'name',
        'installation_cost',
        'contract_id',
    ];
}
