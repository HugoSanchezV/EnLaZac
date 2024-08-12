<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    public $fillable =[
        'id',
        'description',
        'ubication',
        'create_date',
        'status',
        'user_id'
    ];
    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
}
