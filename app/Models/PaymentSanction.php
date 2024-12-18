<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSanction extends Model
{
    use HasFactory;
    public $fillable = ['contract_id','status', 'applied'];

    public function contract(){
        return $this->belongsTo(Contract::class);
    }
}
