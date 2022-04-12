<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorByRegionalDistrict extends Model
{
    use HasFactory;

    protected $fillable = [
        'tgb_reg_district', 'yearcd', 'dollars', 'donors', 'regional_district_id'
    ];

    public function regional_district() {
        return $this->belongsTo('App\Models\RegionalDistrict');
    }
}
