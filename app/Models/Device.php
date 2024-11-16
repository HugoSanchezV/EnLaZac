<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        "device_internal_id",
        "router_id",
        "device_id",
        "user_id",
        "comment",
        "list",
        "address",
        "creation_time",
        "disabled"
    ];

    public function router() {
        return $this->belongsTo(Router::class);
    }
    public function contract() {

        return $this->hasOne(Contract::class);
    }
    public function inventorieDevice() {
        return $this->belongsTo(InventorieDevice::class, 'device_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function contracts () {
        return $this->hasMany(Contract::class);
    }
}
