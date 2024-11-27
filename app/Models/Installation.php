<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installation extends Model
{
    use HasFactory;
    public $fillable =[
        'contract_id',
        'description',
        'assigned_date',
    ];

    public function contract(){
        return $this->belongsTo(Contract::class);
    }
    public function installationSettings(){
        return $this->hasOne(InstallationSetting::class, 'installation_id', 'id'); // Clave foránea correcta
    }

}
