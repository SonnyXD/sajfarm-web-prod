<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Consumption;
use \App\Models\Item;
use \App\Models\ItemStock;

class ConsumptionItem extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;

    public function item() {
        return $this->belongsTo( Item::class );
    }

    public function item_stock() {
        return $this->belongsto( ItemStock::class );
    }
}
