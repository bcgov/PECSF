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


    public function special_campaign() 
    {
        return $this->belongsTo(SpecialCampaign::Class, 'special_campaign_id', 'id')->withDefault();
    }
    
}
