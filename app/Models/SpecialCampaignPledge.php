<?php

namespace App\Models;

use App\Models\SpecialCampaign;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialCampaignPledge extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'organization_id', 'user_id', 'pecsf_id', 'yearcd', 'seqno',
        'special_campaign_id', 'one_time_amount', 'deduct_pay_from',
        'first_name', 'last_name', 'city', 'cancelled', 'cancelled_by_id', 'cancelled_at',
        'ods_export_status', 'ods_export_at', 'created_by_id','updated_by_id',
    ];

    protected $casts = [
        'deduct_pay_from' => 'date:Y-m-d',
    ];

    protected $appends = [
        'in_support_of',
    ];

    public function getInSupportOfAttribute()
    {
        $in_support_of = "";
        if ($this->special_campaign) {

            $in_support_of = $this->special_campaign->charity ? $this->special_campaign->charity->charity_name : '';
        }
        return $in_support_of;

    }


    public function organization() {
        return $this->belongsTo(Organization::class)->withDefault();
    }

    public function user() {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function special_campaign() 
    {
        return $this->belongsTo(SpecialCampaign::Class, 'special_campaign_id', 'id')->withDefault();
    }

    public function campaign_year() {
        return $this->belongsTo(CampaignYear::class, 'yearcd', 'calendar_year');
    }
    
    public function created_by()
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id')->withDefault();
    }

    public function updated_by()
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id')->withDefault();
    }

    public function cancelled_by()
    {
        return $this->hasOne(User::Class, 'id', 'cancelled_by_id')->withDefault();
    }
    
    public function canSendToPSFT() {

        if (!($this->cancelled == null and $this->ods_export_status == null)) {
            return false;
        }

        // Found the previous Saturday
        $dt = $this->deduct_pay_from;
        $dt->subDay(7);
        // for($i=0; $i <= 6; $i++) {
        //     if ($dt->isFriday()) {
        //         break;
        //     }
        //     $dt->subDay(1) ;
        // }

        // echo today() . ' - ' . $dt . PHP_EOL;
        if ( today() >= $dt) {
            return true;
        }

        return false;;

    }

    
}
