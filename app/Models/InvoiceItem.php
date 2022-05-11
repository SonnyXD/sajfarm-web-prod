<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Item;
use \App\Models\Invoice;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 
        'item_id', 
        'cim_code', 
        'product_code',
        'quantity',
        'exp_date',
        'lot',
        'measure_unit_id',
        'price',
        'tva',
        'tva_price',
        'value'
    ]; 

    protected $guarded = [];
    
    public $timestamps = false;

    public function item() {
        return $this->belongsTo( Item::class );
    }

    public function invoice() {
        return $this->belongsTo( Invoice::class );
    }
}
