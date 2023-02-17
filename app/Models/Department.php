<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

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

  public function report($request){
     return Department::select(DB::raw('departments.id,departments.bi_department_id,departments.business_unit_name,departments.department_name, donor_by_departments.donors'))
          ->join("donor_by_departments", "donor_by_departments.department_id", "=", "departments.id")
         ->orderBy("donor_by_departments.donors","desc")

         ->where('donor_by_departments.yearcd', "=", $request->start_date);
  }

}
