<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExtendContract extends Model
{
    use HasFactory;
    
    public $fillable =[
        'contract_id',
        'days',
        'status',
    ];

    public function contract(){
        return $this->hasOne(Contract::class, 'id','contract_id');
    }

}
