<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    public $fillable = [
        'id',
        'inv_device_id',
        'plan_id',
        'start_date',
        'end_date',
        'active',
        'address',
        'rural_community_id',
        'geolocation',
    ];

    protected $casts = [
        'geolocation'  => 'array',
    ];

    public function extendContract(){
        return $this->hasOne(ExtendContract::class, 'contract_id','id');
    }
    public function inventorieDevice(){
        return $this->belongsTo(InventorieDevice::class, 'inv_device_id');
    }
    public function paymentSanction(){ 
        return $this->belongsTo(PaymentSanction::class, 'id', 'contract_id');}
    
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function ruralCommunity()
    {
        return $this->belongsTo(RuralCommunity::class);
    }

    public function installations()
    {
        return $this->hasMany(Installation::class);
    }

    public function charges()
    {
        return $this->hasMany(Charge::class , 'contract_id', 'id');
    }
}
