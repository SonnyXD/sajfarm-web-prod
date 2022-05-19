<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\AmbulanceType;
use \App\Models\Substation;
use \App\Models\Checklist;

class Ambulance extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;

    public function ambulance_type() {
        return $this->hasOne( AmbulanceType::class );
    }

    public function substation() {
        return $this->hasOne( Substation::class );
    }

    public function checklist() {
        return $this->hasMany( Checklist::class );
    }
}
