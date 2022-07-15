<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Inventory;
use \App\Models\ReturningChecklistItem;

class ReturningChecklist extends Model
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

    public function returning_checklist_item() {
        return $this->hasMany( ReturningChecklistItem::class, 'checklist_id' );
    }

}
