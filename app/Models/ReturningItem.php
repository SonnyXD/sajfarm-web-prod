<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Item;
use \App\Models\ItemStock;
use \App\Models\Returning;
use \App\Models\Ambulance;

class ReturningItem extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;

    public function item() {
        return $this->hasMany( Item::class );
    }

    public function item_stock() {
        return $this->belongsTo( ItemStock::class );
    }

    public function item_stock_belongs() {
        return $this->belongsTo( ItemStock::class, 'id' );
    }

    public function ambulance() {
        return $this->belongsTo( Ambulance::class );
    }

    public function returning() {
        return $this->belongsTo( Returning::class );
    }
}
