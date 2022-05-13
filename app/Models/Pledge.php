<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\CampaignYear;
use Illuminate\Database\Eloquent\Model;

class Pledge extends Model
{
    protected $fillable = [
        'organization_id',
        'user_id',
        'campaign_year_id',
        'p_s_pool_id',
        'frequency',
        'one_time_amount',
        'pay_period_amount',
        'amount',
        'goal_amount'
    ];

    public function charities() {
        return $this->hasMany(PledgeCharity::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function markAsGenerated() {
        $this->report_generated_at = Carbon::now();
        return $this;
    }

    public function campaign_year() {
        return $this->belongsTo(CampaignYear::class);
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
