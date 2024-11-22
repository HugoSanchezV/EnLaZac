<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSanction extends Model
{
    use HasFactory;
    public $fillable = ['contract_id','status'];

    public function contract(){
        return $this->belongTo(Contract::class, 'contract_id');
    }
}
