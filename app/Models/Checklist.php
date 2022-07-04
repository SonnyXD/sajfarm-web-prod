<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Medic;
use \App\Models\Inventory;
use \App\Models\Assistent;
use \App\Models\Ambulancier;
use \App\Models\Ambulance;
use \App\Models\ChecklistItem;

class Checklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id', 
        'medic_id', 
        'ambulance_id', 
        'checklist_date',
        'patient_number',
        'tour'
    ]; 

    protected $guarded = [];
    
    public $timestamps = false;

    public function medic() {
        return $this->belongsTo( Medic::class );
    }

    public function inventory() {
        return $this->belongsTo( Inventory::class );
    }

    public function ambulance() {
        return $this->belongsTo( Ambulance::class );
    }

    public function checklistitems() {
        return $this->hasMany( ChecklistItem::class );
    }

    public function checklistitems_grouped() {
        return $this->checklistitems()
            ->select('checklist_items.id', 'checklist_items.checklist_id as c_id',
            'checklist_items.item_id', 'checklist_items.item_stock_id', ChecklistItem::raw('SUM(checklist_items.quantity) as quantity'));
    }

    public function assistent() {
        return $this->belongsTo( Assistent::class );
    }

    public function ambulancier() {
        return $this->belongsTo( Ambulancier::class );
    }
}
