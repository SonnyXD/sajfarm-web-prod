<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Checklist;

class Medic extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;

    public function checklist() {
        return $this->hasMany( Checklist::class );
    }
}
