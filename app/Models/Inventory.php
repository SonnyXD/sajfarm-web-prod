<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\ItemStock;
use \App\Models\Item;
use \App\Models\Checklist;

class Inventory extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;

    public function item_stock()
    {
        return $this->hasMany(ItemStock::class);
    }

    public function checklist()
    {
        return $this->hasMany( Checklist::class );
    }
}
