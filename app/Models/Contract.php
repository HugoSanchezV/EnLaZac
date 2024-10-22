<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    public $fillable =[
        'id',
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'active',
        'address',
        'geolocation',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $casts = [
        'geolocation'  => 'array',
    ];
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    public function ruralCommunity()
    {
        return $this->hasOne(RuralCommunity::class, 'contract_id');
    }
}
