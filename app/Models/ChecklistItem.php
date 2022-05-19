<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Item;
use \App\Models\ItemStock;
use \App\Models\Checklist;

class ChecklistItem extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;

    public function item() {
        return $this->hasMany( Item::class );
    }

    public function item_stock() {
        return $this->hasMany( ItemStock::class );
    }

    public function checklist() {
        return $this->hasMany( Checklist::class );
    }

    public function checklist_item() {
        return $this->belongsTo( ChecklistItem::class);
    }
}
