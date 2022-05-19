<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\InvoiceItem;

class MeasureUnit extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;

    public function invoiceitem()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
