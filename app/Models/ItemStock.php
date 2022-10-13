<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Item;
use \App\Models\Inventory;
use \App\Models\Invoice;
use \App\Models\InvoiceItem;
use \App\Models\ChecklistItem;
use \App\Models\ConsumptionItem;
use \App\Models\TransferItem;
use \App\Models\ReturningItem;
use \App\Models\ReturningChecklistItem;
use \App\Models\Transfer;

class ItemStock extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;

    public function item() {
        return $this->belongsTo( Item::class );
    }

    public function invoice_item() {
        return $this->belongsTo( InvoiceItem::class );
    }

    public function inventory() {
        return $this->belongsTo( Inventory::class );
    }

    public function invoice() {
        return $this->belongsTo( Invoice::class );
    }

    public function checklist_item() {
        return $this->hasOne( ChecklistItem::class );
    }

    public function transfer_item() {
        return $this->hasOne( TransferItem::class );
    }

    public function returning_item() {
        return $this->hasOne( ReturningItem::class );
    }

    public function consumption_item() {
        return $this->hasOne( ConsumptionItem::class );
    }

    public function returning_checklist_item() {
        return $this->hasOne( ReturningChecklistItem::class );
    }
}
