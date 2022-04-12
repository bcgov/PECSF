<?php

namespace App\Models;

use App\Models\DonorByBusinessUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessUnit extends Model
{
    use HasFactory;

    protected $fillable =[ 
        'business_unit_code', 'name', 'yearcd'
    ];

    public function donorHistory() {
        return $this->hasMany(DonorByBusinessUnit::class);             
    }

}
