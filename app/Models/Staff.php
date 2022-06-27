<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Inventory;

class Staff extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;

    public function inventory() {
        return $this->belongsTo( Inventory::class );
    }
}
