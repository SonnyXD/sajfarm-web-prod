<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Checklist;

class Ambulancier extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;

    public function checklist() 
    {
        $this->hasMany( Checklist::class );
    }
}
