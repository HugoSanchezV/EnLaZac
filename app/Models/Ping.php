<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ping extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "router_id",
        "content",
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }
}
