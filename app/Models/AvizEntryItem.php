<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\AvizEntry;
use \App\Models\Item;

class AvizEntryItem extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;
    
    public function aviz_entry() {
        return $this->hasOne( AvizEntry::class );
    }

    public function item() {
        return $this->hasOne( Item::class );
    }
}
