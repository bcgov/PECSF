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
        'number_of_charities'
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

    public function bi_charities() {

        return $this->hasMany('PledgeHistory','emplid', 'emplid')
                                    ->where('yearcd', $this->yearcd)
                                    ->where('source', $this->type == 'P' ? 'Pool' : 'Non-Pool' )
                                    ->where('campaign_type', $this->donation_type)
                                    ->where('frequency', $this->frequency);

    }


    public function pledge_data() {

        if ($this->type == 'C') {
            if ($this->donation_type == 'Annual') {

                return $this->belongsTo('Pledge','id', 'id');

            } else if ($this->donation_type == 'Donate Now') {
                
                return $this->belongsTo('DonateNowPledge','id', 'id')->with('charity');

            } else if ($this->donation_type == 'Special Campaign') {

                return $this->belongsTo('SpecialCampaignPledge','id', 'id');

            } else if ($this->donation_type == 'Event') {

                return $this->belongsTo('BankDepositForm','id', 'id');
            }
                
        } 
        
        return null;

    }
    

    public function getIsAnnualCampaignAttribute()
    {
        return ($this->donation_type == 'Annual') ? true : false;
    }

    public function getNumberOfCharitiesAttribute()
    {
        $count = null;
        if ($this->source == 'GF') {
           
            if ($this->donation_type == 'Annual') {
                $pledge = $this->pledge_data;
                return $pledge->charities->count();
            } else if ($this->donation_type == 'Donate Now') {
                return 1;
            } else if ($this->donation_type == 'Special Campaign') {
                return 1;
            } else if ($this->donation_type == 'Event') {
                $count = BankDepositFormOrganizations::where('bank_deposit_form_id', $this->id)
                                        ->count();
            }

        } else {
            
            $count = PledgeHistory::where('emplid', $this->emplid)
                                    ->where('yearcd', $this->yearcd)
                                    ->where('source', $this->type == 'P' ? 'Pool' : 'Non-Pool' )
                                    ->where('campaign_type', $this->donation_type)
                                    ->where('frequency', $this->frequency)
                                    ->count();

        }

        return $count;
    }

    

}
