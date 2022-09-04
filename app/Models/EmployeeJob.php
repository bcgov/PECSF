<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'emplid', 'empl_rcd', 'effdt', 'effseq', 
        'empl_status', 'empl_class', 'empl_ctg', 'job_indicator',
        'position_number', 'position_title', 'appointment_status', 
        'first_name', 'last_name', 'name', 'email', 'guid', 'idir', 
        'business_unit', 'business_unit_id',  'deptid', 'dept_name', 'tgb_reg_district', 'region_id', 
        'office_address1', 'office_address2', 'office_city', 'office_stateprovince', 'office_country', 'office_postal',
        'city', 'stateprovince', 'country', 
        'organization', 'level1_program', 'level2_division', 'level3_branch', 'level4', 
        'supervisor_emplid', 'supervisor_name', 'supervisor_email', 
         'date_updated', 'date_deleted', 'created_by_id', 'updated_by_id'
    ];

    protected $attributes = [
        'first_name' => '', 
        'last_name' => '',
        'name' => '', 
        'email' => '',
        'business_unit' => '',
        'dept_name' => '',
        'organization' => '',
        'tgb_reg_district' => '',
        'level1_program' => '',
        'level2_division' => '',
        'level3_branch' => '',
        'level4' => '', 
    ];

    protected $appends = [
        'organization_name',        // Organization under the Org Chart 
    ];

    public function region() 
    {
        return $this->belongsTo(Region::Class, 'region_id', 'id')->withDefault();
    }

    public function bus_unit() 
    {
        return $this->belongsTo(BusinessUnit::Class, 'business_unit_id', 'id')->withDefault();
    }

    public function organization() 
    {
        return $this->belongsTo(Organization::Class, 'organization_id', 'id')->withDefault();
    }

    public function getOrganizationNameAttribute()
    {
        return $this->organization;
    }




}
