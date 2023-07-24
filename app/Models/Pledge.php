<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\CampaignYear;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Pledge extends Model implements Auditable
{
    use SoftDeletes;
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
    ];

    public function getFrequencyAttribute() {

        $frequency = 'both';
        if ($this->one_time_amount > 0 && $this->pay_period_amount == 0) {
            $frequency = 'one-time';
        } if ($this->one_time_amount == 0 && $this->pay_period_amount > 0) {
            $frequency = 'bi-weekly';
        }
        return $frequency;

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
