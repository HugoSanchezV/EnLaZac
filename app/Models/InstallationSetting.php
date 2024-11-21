<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallationSetting extends Model
{
    use HasFactory;
    public $fillable =[
        'installation_id',
        'exemption_months',
    ];

    public function installation()
    {
        return $this->hasOne(Installation::class, 'id','installation_id');
    }
}
