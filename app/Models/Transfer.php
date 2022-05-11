<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Inventory;

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

    public function inventory() {
        return $this->hasMany( Inventory::class );
    }
}
