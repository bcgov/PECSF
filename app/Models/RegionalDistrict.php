<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegionalDistrict extends Model
{
    use HasFactory;

    protected $fillable =[ 
        'development_region', 'provincial_quadrant', 'reg_district_desc', 'tgb_reg_district'
    ];


}
