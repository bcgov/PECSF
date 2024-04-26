<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class VolunteerProfile extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $fillable = [

        'campaign_year', 'organization_code', 'emplid', 'pecsf_id', 'first_name', 'last_name',
        'business_unit_code', 'no_of_years', 'preferred_role', 'address_type', 
        'address', 'city', 'province', 'postal_code', 'opt_out_recongnition',
        'created_by_id', 'updated_by_id',
    ];

    protected $appends = [
        'preferred_role_name',
        'province_name',
        'fullname',
        'full_address',
        'is_renew_profile',
    ];

    public const PROVINCE_LIST = [
        "AB" => "Alberta",
        "BC" => "British Columbia",
        "MB" => "Manitoba",
        "NB" => "New Brunswick",
        "NL" => "Newfoundland and Labrador",
        "NT" => "Northwest Territories",
        "NS" => "Nova Scotia",
        "NU" => "Nunavut",
        "ON" => "Ontario",
        "OC" => "Outside of Canada",
        "PE" => "Prince Edward Island",
        "QC" => "Quebec",
        "SK" => "Saskatchewan",
        "YT" => "Yukon",
    ];

    public const ROLE_LIST = [
        "CR" => "Canvasser",
        "CM" => "Committee Member ",
        "EC" => "Event Coordinator",
        "ES" => "Executive Sponsor",
        "LC" => "Lead Coordinator",
        "VR" => "Volunteer",
    ];

    public function getPreferredRoleNameAttribute()
    {
        $name = self::ROLE_LIST[ $this->preferred_role];
        return $name;
    }
    
    public function getProvinceNameAttribute()
    {
        $name = $this->province ? self::PROVINCE_LIST[ $this->province] : '';
        return $name;
    }

    public function getFullnameAttribute()
    {
        $fullname = $this->first_name . ', ' . $this->last_name;
        if ($this->organization_code == 'GOV') { 
            $job = $this->emplid ? $this->primary_job()->first() : null; 
            $fullname = $job ? $job->name : '';
        }
        return $fullname;
    }

    public function getFullAddressAttribute()
    {
        $address = '';
        if ($this->address_type == 'G') { 

            // $user = User::where('id', $this->user_id)->first();
            $job = $this->emplid ? $this->primary_job()->first() : null; 

            if ($job) {
                $address = $job->full_address;
            }
        } else {
            $address = $this->address .', '. $this->city .', '. $this->province .', '. $this->postal_code;
        }
        return $address;
    }

    public function getIsRenewProfileAttribute()
    {

        $registed_in_the_past = VolunteerProfile::where('organization_code', $this->organization_code)
                    ->where('emplid', $this->emplid)
                    ->where('pecsf_id', $this->pecsf_id)
                    ->where('campaign_year', '<', $this->campaign_year)
                    ->first();
        $is_renew = $registed_in_the_past ? true : false;
       
        return $is_renew;
    }


    public function primary_job() {
        return $this->belongsTo(EmployeeJob::class, 'emplid', 'emplid')
            ->where( function($query) {
                $query->where('employee_jobs.empl_rcd', '=', function($q) {
                    $q->from('employee_jobs as J2') 
                        ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                        ->selectRaw('min(J2.empl_rcd)');
                })
                ->orWhereNull('employee_jobs.empl_rcd');
            })->withDefault([]);
    }

    public function organization() {
        return $this->belongsTo(Organization::class, 'organization_code', 'code')->withDefault([
            'name' => '',
        ]);
    }

    public function business_unit() {
        return $this->belongsTo(BusinessUnit::class, 'business_unit_code', 'code');
    }

    public function created_by()
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id')->withDefault();
    }

    public function updated_by()
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id')->withDefault();
    }
    
}
