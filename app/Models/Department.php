<?php

namespace App\Models;

use App\Models\DonorByDepartment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
  use HasFactory;

  protected $fillable =[ 
        'bi_department_id', 'department_name', 'group', 'yearcd', 'business_unit_code', 'business_unit_name',
        'business_unit_id'
  ];

  public function business_unit() {
    return $this->belongsTo('App\Models\BusinessUnit');
  }

  public function donorHistory() {
    return $this->hasMany(DonorByDepartment::class);             
  }


}
