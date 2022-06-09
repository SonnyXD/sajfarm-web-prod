<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Checklist;
use \App\Models\Inventory;
use \App\Models\Ambulance;
use \App\Models\Medic;

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
}
