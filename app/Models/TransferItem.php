<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Item;
use \App\Models\ItemStock;
use \App\Models\InvoiceItem;
use \App\Models\Transfer;

class TransferItem extends Model
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

    public function item_detail() {
        return $this->belongsTo( Item::class, 'item_id' );
    }

    public function item_stock_detail() {
        return $this->belongsto( ItemStock::class, 'item_stock_id' );
    }

    public function transfer() {
        return $this->belongsTo( Transfer::class );
    }
}
