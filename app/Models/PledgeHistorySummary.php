<?php

namespace App\Models;

use App\Models\FSPool;
use App\Models\Region;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PledgeHistorySummary extends Model
{
    use HasFactory;

    protected $appends = [
        'is_annual_campaign',
    ];

    public function fund_supported_pool() {

        if ($this->source == 'P') {
            $region = Region::where('name', $this->region)->first();
            return FSPool::current()->where('region_id', $region->id)->first();
        } else {
            return null;
        }

    }

    public function region_by_code() {
        return Region::where('code', $this->region)->first();
    }

    // public function region_by_name() {
    //     return Region::where('name', $this->region)->first();
    //     // return $this->belongsTo(Region::class, 'region', 'name');
    // }

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
