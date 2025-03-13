<?php

namespace App\Models;

use App\Models\VolunteerProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'emplid', 'empl_rcd', 'effdt', 'effseq', 'hire_dt',
        'empl_status', 'empl_class', 'empl_ctg', 'job_indicator',
        'position_number', 'position_title', 'appointment_status', 
        'first_name', 'last_name', 'name', 'email', 'guid', 'idir', 
        'business_unit', 'business_unit_id',  'deptid', 'dept_name', 'tgb_reg_district', 'region_id', 
        'office_address1', 'office_address2', 'office_city', 'office_stateprovince', 'office_country', 'office_postal',
        'address1', 'address2', 'city', 'stateprovince', 'country', 'postal',
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
        'full_address',
        'office_full_address',
        'years_of_service',
        'years_of_volunteer',
        'total_donations',
        'challenge_business_unit',
    ];

    public const EMPL_STATUS_LIST = 
    [
        'A' => 'Active',
        'D' => 'Deceased',
        'L' => 'Leave',
        'P' => 'Leave W/Py',
        'Q' => 'Ret w/Pay',
        'R' => 'Retired',
        'S' => 'Laid Off',
        'S' => 'Suspended',
        'T' => 'Terminated',
        'U' => 'Term w/Pay',
        'U' => 'Term w/Pay',
        'V' => 'Term w/Pen',
        'W' => 'Work Break',
        'X' => 'Ret - PAdm',
    ];

    public static function organization_list()
    {
        return EmployeeJob::where('organization', '<>', '')
                    ->orderBy('organization')
                    ->distinct()
                    ->pluck('organization');
    }

    public static function office_city_list()
    {
        return EmployeeJob::where('office_city', '<>', '')
                    ->orderBy('office_city')
                    ->distinct()
                    ->pluck('office_city');
    }


    public function region() 
    {
        return $this->belongsTo(Region::Class, 'tgb_reg_district', 'code')->withDefault();
    }

    public function city_by_office_city() 
    {
        return $this->belongsTo(City::Class, 'office_city', 'city')->withDefault();
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

    public function getFullAddressAttribute()
    {
        $address = $this->address1;
        $address .= trim($this->address2) ? ', '. $this->address2 : '';
        $address .= trim($this->city) ? ', '. $this->city : '';
        $address .= trim($this->stateprovince) ? ', '. $this->stateprovince : '';
        $address .= trim($this->postal) ? ', '. $this->postal : '';
       
        return $address;
    }

    public function getOfficeFullAddressAttribute()
    {
        $address = $this->office_address1;
        $address .= trim($this->office_address2) ? ', '. $this->office_address2 : '';
        $address .= trim($this->office_city) ? ', '. $this->office_city : '';
        $address .= trim($this->office_stateprovince) ? ', '. $this->office_stateprovince : '';
        $address .= trim($this->office_postal) ? ', '. $this->office_postal : '';
       
        return $address;
    }

    public function getYearsOfServiceAttribute()
    {
        $years_of_service = null;

        if ($this->hire_dt) {
            // $years_of_service = today()->diffInYears($this->hire_dt);
            $diff = today()->diff($this->hire_dt);
            $years_of_service =  $diff->y + round(($diff->m / 12),1);

        } 
        return $years_of_service;

    }

    public function getYearsOfVolunteerAttribute()
    {

        $cy = today()->year;

        $years = VolunteerProfile::where("campaign_year", '<=', $cy)
                            ->where("organization_code", 'GOV')
                            ->where("emplid", $this->emplid)
                            ->sum('no_of_years');

        // $years = $profiles->sum('no_of_years');                            

        return $years;

    }

    public function getTotalDonationsAttribute()
    {

        // $cy = today()->month < 6 ? today()->year - 1 : today()->year;

        $gov = Organization::where('code','GOV')->first();

        $total_amount = 0;
        // Annual Campaign
        $amount = Pledge::where('organization_id', $gov->id )
                                ->where('emplid', $this->emplid)
                                ->whereNull('cancelled')
                                ->sum('goal_amount');
        $total_amount += $amount;

        // eForm
        $amount = BankDepositForm::where('organization_code', 'GOV')
                                    ->where('bc_gov_id', $this->emplid)
                                    ->where('bank_deposit_forms.approved', 1)
                                    ->sum('deposit_amount');
        $total_amount += $amount;

        // Donate Now
        $amount = DonateNowPledge::where('organization_id', $gov->id)
                                ->where('emplid', $this->emplid)
                                ->whereNull('cancelled')
                                ->sum('one_time_amount');
        $total_amount += $amount;
        
        // Special Campaign 
        $amount = SpecialCampaignPledge::where('organization_id', $gov->id)
                                ->where('emplid', $this->emplid)
                                ->whereNull('cancelled')
                                ->sum('one_time_amount');
        $total_amount += $amount;

        // BI History  
        $amount = PledgeHistorySummary::where('emplid', $this->emplid)
                        ->where( function($query) {
                                $query->whereNull('pledge_history_summaries.event_type')
                                    ->orWhereIn('pledge_history_summaries.event_type', ['Cash', 'Personal Cheque']);
                        })
                        ->sum('pledge');
        $total_amount += $amount;


        return $total_amount;
    }


    public function getChallengeBusinessUnitAttribute()
    {

        // Special Rule -- To split GCPE employees from business unit BC022 
        $bu = BusinessUnit::where('code', $this->business_unit)->first();
        $business_unit_code = $bu ? $bu->linked_bu_code : null;
        if ($this->business_unit == 'BC022' && str_starts_with($this->dept_name, 'GCPE')) {
            $business_unit_code  = 'BGCPE';
        }
        $linked_bu = BusinessUnit::where('code', $business_unit_code)->first();

        return $linked_bu;

    }

}
