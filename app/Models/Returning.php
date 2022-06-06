<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Inventory;

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
}
