<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Provider;

class AvizEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'provider_id', 
        'number', 
        'document_date', 
        'due_date',
        'discount_procent',
        'discount_value',
        'total'
    ]; 

    protected $guarded = [];
    
    public $timestamps = false;

    public function provider() {
        return $this->hasOne( Provider::class );
    }
}
