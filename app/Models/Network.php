<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;

class Network extends Model
{
    use HasFactory;

    protected $fillable = ['router_id', 'address', 'network'];

    public function router() {
        return $this->belongsTo(Route::class);
    }
}
