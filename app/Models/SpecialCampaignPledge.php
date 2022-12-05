<?php

namespace App\Models;

use App\Models\SpecialCampaign;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpecialCampaignPledge extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'organization_id', 'user_id', 'pecsf_id', 'yearcd', 'seqno',
        'special_campaign_id', 'one_time_amount', 'deduct_pay_from',
        'ods_export_status', 'ods_export_at', 'created_by_id','updated_by_id',
    ];

    protected $casts = [
        'deduct_pay_from' => 'date',
    ];


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
    

    public function canSendToPSFT() {

        if (!($this->cancelled == null and $this->ods_export_status == null)) {
            return false;
        }

        // Found the previous Saturday
        $dt = $this->deduct_pay_from;
        for($i=0; $i <= 6; $i++) {
            if ($dt->isSaturday()) {
                break;
            }
            $dt->subDay(1) ;
        }

        // echo today() . ' - ' . $dt . PHP_EOL;
        if ( today() >= $dt) {

            return true;
        }

        return false;;

    }

    
}
