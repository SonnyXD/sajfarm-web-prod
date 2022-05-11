<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Checklist;

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
}
