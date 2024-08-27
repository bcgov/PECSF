<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pledge extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'organization_id',
        'emplid',       // Use for checking unique pledge per campaign year
        'user_id',      // Not in use in the near future due to multiple GUID to emplid issue

        "pecsf_id",
        "first_name",
        "last_name",
        "city",

        'business_unit',
        'tgb_reg_district',
        'deptid',
        'dept_name',

        'campaign_year_id',
        'type',
        'region_id',
        'f_s_pool_id',
        // 'frequency',
        // 'amount',
        'one_time_amount',
        'pay_period_amount',
        'goal_amount',
        'ods_export_status', 'ods_export_at',
        'created_by_id',
        'updated_by_id',
'selected_table'

    ];

    protected $appends = [
        'frequency',
        'challenge_business_unit',
    ];

    // Class Method 
    public static function hasDataToSend() {
            
        $row_count = self::whereExists(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('organizations')
                                    ->whereColumn('organizations.id', 'pledges.organization_id')
                                    ->where('organizations.code', 'GOV');
                            })
                            ->whereExists(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('campaign_years')
                                    ->whereColumn('campaign_years.id', 'pledges.campaign_year_id')
                                    ->where('campaign_years.status', 'I')
                                    ->whereRaw("CURDATE() > ADDDATE(end_date, INTERVAL 1 DAY)");
                            })                            
                            ->whereNull('pledges.ods_export_status')
                            ->whereNull('pledges.cancelled')
                            ->count();

        if ($row_count > 0) {
            return true;
        } else {
            return false;
        }

    }  

    public function getFrequencyAttribute() {

        $frequency = 'both';
        if ($this->one_time_amount > 0 && $this->pay_period_amount == 0) {
            $frequency = 'one-time';
        } if ($this->one_time_amount == 0 && $this->pay_period_amount > 0) {
            $frequency = 'bi-weekly';
        }
        return $frequency;

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

    public function charities() {
        return $this->hasMany(PledgeCharity::class);
    }

    public function distinct_charities() {

        if ($this->one_time_amount > 0) {
            return $this->charities()->where('frequency', 'one-time');
        } else {
            return $this->charities()->where('frequency', 'bi-weekly');
        }
    }

    public function one_time_charities() {
        return $this->charities()->where('frequency', 'one-time');
    }

    public function bi_weekly_charities() {
        return $this->charities()->where('frequency', 'bi-weekly');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function created_by()
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id')->withDefault();
    }

    public function updated_by()
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id')->withDefault();
    }

    public function markAsGenerated() {
        $this->report_generated_at = Carbon::now();
        return $this;
    }

    public function campaign_year() {
        return $this->belongsTo(CampaignYear::class)->withDefault([
            'calendar_year' => '',
        ]);
    }

    public function region() {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function current_fund_supported_pool_by_region() {

        if ($this->type == 'P') {
            $region = Region::where('id', $this->region_id)->first();
            return FSPool::current()->where('region_id', $region->id)->first();
        } else {
            return null;
        }
    }


    public function fund_supported_pool() {

            return $this->belongsTo(FSPool::class, 'f_s_pool_id', 'id')->withDefault();

    }

    public function organization() {
        return $this->belongsTo(Organization::class)->withDefault([
            'name' => '',
        ]);
    }

    public function pecsf_user_region() {


        // $region = Region::where('code', '=', function ($query) {
        //     $query->select('TGB_REG_DISTRICT')
        //         ->from('cities')
        //         ->where('cities.city', $this->city)
        //         ->limit(1);
        // })->first();
        
        // return $region;

        return $this->belongsTo(Region::class, 'tgb_reg_district', 'code')->withDefault();


    }

    public function pecsf_user_bu() {

        // $bu = BusinessUnit::where('code', '=', function ($query) {
        //     $query->select('bu_code')
        //         ->from('organizations')
        //         ->where('id', $this->organization_id)
        //         ->limit(1);
        // })->first();

        // return $bu;

        return $this->belongsTo(BusinessUnit::class, 'business_unit', 'code')->withDefault();

    }

    // public function scopeOnlyCampaignYear($query, $campaign_year) {

    //     $cy = CampaignYear::where('calendar_year', $campaign_year )->first();

    //     return $query->whereBetween('created_at',
    //             [$cy->start_date, $cy->end_date->add(1,'day') ]);
    // }

    /* public function charities() {
        return $this->belongsToMany('App\Models\Charity', 'pledge_charities', 'pledge_id', 'charity_id')
        ->using('App\Models\PledgeCharity')->withTimestamps();
    } */
}
