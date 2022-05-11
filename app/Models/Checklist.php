<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Medic;
use \App\Models\Inventory;
use \App\Models\Ambulance;

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
        return $this->hasOne( Medic::class );
    }

    public function inventory() {
        return $this->hasOne( Inventory::class );
    }

    public function ambulance() {
        return $this->hasOne( Ambulance::class );
    }
}
