<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    public $fillable =[
        'id',
        'subject',
        'description',
        'status',
        'user_id'
    ];
    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
