<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\ReturningChecklist;
use \App\Models\Item;
use \App\Models\ItemStock;
use \App\Models\Ambulance;

class ReturningChecklistItem extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;

    public function item() {
        return $this->belongsTo( Item::class );
    }

    public function item_stock() {
        return $this->belongsTo( ItemStock::class );
    }

    public function returning_checklist() {
        return $this->hasMany( ReturningChecklist::class );
    }

    public function ambulance() {
        return $this->belongsTo( Ambulance::class);
    }
}
