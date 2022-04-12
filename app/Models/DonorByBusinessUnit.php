<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorByBusinessUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_unit_code', 'yearcd', 'dollars', 'donors', 'business_unit_id', 
    ];

    public function business_unit() {
        return $this->belongsTo('App\Models\BusinessUnit');
    }

}
