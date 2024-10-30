<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    public $fillable = [
        'id',
        'subject',
        'description',
        'status',
        'user_id',
        'technical_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function technical()
    {
        return $this->belongsTo(User::class);
    }
}
