<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;
    public $fillable = [
        'id',
        'contract_id',
        'interest_id',
        'description',
        'amount',
        'paid',
        'date_paid',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    public function interest(){
        return $this->belongsTo(interest::class, 'id','interest_id' );
    }
}
