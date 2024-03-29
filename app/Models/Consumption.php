<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Checklist;
use \App\Models\Inventory;
use \App\Models\Ambulance;
use \App\Models\Medic;
use \App\Models\ConsumptionItem;

class Consumption extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_id'
    ]; 

    protected $guarded = [];
    
    public $timestamps = false;

    public function checklist() {
        return $this->hasOne( Checklist::class );
    }

    public function inventory() {
        return $this->belongsTo( Inventory::class );
    }

    public function ambulance() {
        return $this->belongsTo(Ambulance::class);
    }

    public function medic() {
        return $this->belongsTo(Medic::class);
    }

    public function consumption_item() {
        return $this->hasMany( ConsumptionItem::class );
    }

    public function consumption_items_pf() {
        return $this->consumption_item()
        ->select('consumption_items.id', 'consumption_items.consumption_id',
        'consumption_items.item_id', 'consumption_items.item_stock_id', ConsumptionItem::raw('SUM(consumption_items.quantity) as quantity'))
             ->groupBy('consumption_items.item_stock_id')
             ->groupBy('consumption_items.consumption_id');
    }

    public function consumption_items_grouped() {
        return $this->consumption_item()
            ->select('consumption_items.id', 'consumption_items.consumption_id',
            'consumption_items.item_id', 'consumption_items.item_stock_id', ConsumptionItem::raw('SUM(consumption_items.quantity) as quantity'))
            ->groupBy('consumption_items.item_stock_id');
    }

}
