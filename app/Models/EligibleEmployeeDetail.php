<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EligibleEmployeeDetail extends Model
{
    use HasFactory;

    protected $fillable = [ 'as_of_date', 'year',
        'organization_code', 'emplid', 
        'empl_status', 'name', 'business_unit', 'business_unit_name', 'deptid', 'dept_name',
        'tgb_reg_district',             
        'office_address1',  'office_address2',
        'office_city', 'office_stateprovince', 'office_postal', 'office_country',
        'organization_name', 'employee_job_id', 
        'created_by_id', 'updated_by_id',
    ];

    public static function organization_list()
    {
        return EligibleEmployeeDetail::where('organization_name', '<>', '')
                    ->orderBy('organization_name')
                    ->distinct()
                    ->pluck('organization_name');
    }

    public static function office_city_list()
    {
        return EligibleEmployeeDetail::where('office_city', '<>', '')
                    ->orderBy('office_city')
                    ->distinct()
                    ->pluck('office_city');
    }

    public function related_business_unit() 
    {
        return $this->belongsTo(BusinessUnit::class, 'business_unit', 'code');   
    }

    public function related_organization() 
    {
        return $this->belongsTo(Organization::class, 'organization_code', 'code');   
    }

    public function related_region() 
    {
        return $this->belongsTo(Region::Class, 'tgb_reg_district', 'code');
    }
}
