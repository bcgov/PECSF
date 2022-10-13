<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialCampaignPledge extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'organization_id', 'user_id', 'pecsf_id', 'yearcd', 'seqno',
        'special_campaign_id', 'one_time_amount', 'ods_export_status',
        'ods_export_at', 'created_by_id','updated_by_id',
    ];
    
}
