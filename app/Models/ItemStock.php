<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Item;
use \App\Models\Inventory;
use \App\Models\Invoice;

class ItemStock extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;

    public function item() {
        return $this->hasMany( Item::class );
    }

    public function inventory() {
        return $this->hasMany( Inventory::class );
    }

    public function invoice() {
        return $this->hasOne( Invoice::class );
    }
}
