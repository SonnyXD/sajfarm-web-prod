<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Inventory;
use \App\Models\TransferItem;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_inventory_id', 
        'to_inventory_id', 
        'document_date'
    ]; 

    protected $guarded = [];
    
    public $timestamps = false;

    public function inventory_from() {
        return $this->belongsTo( Inventory::class , 'from_inventory_id');
    }

    public function inventory_to() {
        return $this->belongsTo( Inventory::class , 'to_inventory_id');
    }

    public function transfer_item() {
        return $this->hasMany( TransferItem::class );
    }
}
