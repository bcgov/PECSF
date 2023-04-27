<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;


class Organization extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable =[
        'code', 'name',  'status', 'effdt', 'bu_code', 'created_by_id', 'updated_by_id' 
    ];

    public function business_unit() 
    {
        return $this->belongsTo(BusinessUnit::class, 'bu_code', 'code');   
    }

    public function created_by() 
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id');
    }

    public function updated_by() 
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id');
    }

    public function hasPledge()
    {
        if ( $this->pledges()->exists() ) {
            return true;
        }
       
        if ( $this->donate_now_pledges()->exists() ) {
            return true;
        }

        if ( $this->special_campaign_pledges()->exists() ) {
            return true;
        }

        if ( $this->bank_deposit_forms()->exists() ) {
            return true;
        }

        if ( $this->non_gov_pledge_histories()->exists() ) {
            return true;
        }

        return false;
    }

    public function pledges() {
        return $this->hasMany(Pledge::class, 'organization_id', 'id');
    }

    public function donate_now_pledges() {
        return $this->hasMany(DonateNowPledge::class, 'organization_id', 'id');
    }

    public function special_campaign_pledges() {
        return $this->hasMany(SpecialCampaignPledge::class, 'organization_id', 'id');
    }

    public function bank_deposit_forms() {
        return $this->hasMany(BankDepositForm::class, 'organization_code', 'code');
    }

    public function non_gov_pledge_histories() {
        return $this->hasMany(NonGovPledgeHistory::class, 'org_code', 'code');
    }

}
