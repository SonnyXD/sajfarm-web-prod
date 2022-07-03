<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\AmbulanceType;
use \App\Models\Substation;
use \App\Models\Checklist;
use \App\Models\Consumption;
use \App\Models\ConsumptionItem;

class Ambulance extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public $timestamps = false;

    public function ambulance_type() {
        return $this->hasOne( AmbulanceType::class );
    }

    public function substation() {
        return $this->hasOne( Substation::class );
    }

    public function checklist() {
        return $this->hasMany( Checklist::class );
    }

    public function consumptions() {
        return $this->hasMany( Consumption::class );
    }

    public function consumption_items_grouped() {
        return $this->consumption_item()
            ->select('consumption_items.id', 'consumption_items.consumption_id',
            'consumption_items.item_id', 'consumption_items.item_stock_id', ConsumptionItem::raw('SUM(consumption_items.quantity) as quantity'))
            ->groupBy('consumption_items.item_stock_id');
    }

    public function consumption_items()
    {
        return $this->hasManyThrough(ConsumptionItem::class, Consumption::class, 'ambulance_id', 'consumption_id');
    }
}
