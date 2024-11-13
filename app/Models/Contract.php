<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    public $fillable = [
        'id',
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
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
        return $this->hasMany(Installation::class, 'contract_id');
    }

    public function charges()
    {
        return $this->hasMany(Charge::class);
    }
}
