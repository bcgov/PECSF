<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PledgeHistorySummary extends Model
{
    use HasFactory;

    protected $appends = [
        'is_annual_campaign',
    ];

    public function fund_supported_pool() {

        if ($this->type == 'P') {
            $region = Region::where('name', $this->region)->first();
            return FSPool::current()->where('region_id', $region->id)->first();
        } else {
            return null;
        }

    }

    public function getIsAnnualCampaignAttribute()
    {
        return ($this->campaign_type == 'Annual') ? true : false;
    }

    public function first_detail() 
    {
        // return $this->hasMany(PledgeHistory::class, 'id', 'pledge_history_id');
        return $this->belongsTo(PledgeHistory::class,  'pledge_history_id', 'id');
    }



}
