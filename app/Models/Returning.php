<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Inventory;
use \App\Models\ReturningItem;
use \App\Models\ItemStock;

class Returning extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'document_date'
    ];

    protected $guarded = [];
    
    public $timestamps = false;

    public function inventory() {
        return $this->belongsTo( Inventory::class );
    }

    public function item_stock() {
        return $this->belongsTo( ItemStock::class );
    }

    public function returning_item() {
        return $this->hasMany( ReturningItem::class );
    }

    public function returning_items_grouped() {
        return $this->returning_item()
            ->select('returning_items.id', 'returning_items.returning_id',
            'returning_items.item_id', 'returning_items.item_stock_id', ReturningItem::raw('SUM(returning_items.quantity) as quantity'),
            'returning_items.ambulance_id', 'returning_items.reason')
            ->groupBy('returning_items.item_stock_id');
    }
}
