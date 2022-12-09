<?php

namespace App\Models;

use App\Models\FSPool;
use App\Models\Region;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ViewPledgeHistory extends Model
{
    
    protected $table = 'pledge_history_view';
    public $timestamps = false;

    protected $appends = [
        'is_annual_campaign',  
    ];

    // public const CAMPAIGN_TYPE = 
    // [
    //     "Annual" => "Annual Campaign",
    //     "Event" => "Event",
    //     "Donate Today" => "Donate Today",
    // ];

    public function user()
    {
        if ($this->source == 'GF') {
            return $this->belongsTo(User::class);
        } else {
            return $this->belongsTo(User::class, 'GUID', 'guid');
        }

    }

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
        return ($this->donation_type == 'Annual') ? true : false;
    }

    

}
