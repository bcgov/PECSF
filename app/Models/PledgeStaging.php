<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PledgeStaging extends Model
{
    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function organization() {
        return $this->belongsTo(Organization::class, 'organization_code', 'code');
    }

    public function campaign_year() {
        return $this->belongsTo(CampaignYear::class, 'calendar_year', 'calendar_year');
    }

    public function region() {
        return $this->belongsTo(Region::class, 'tgb_reg_district', 'code');
    }

    public function business_unit() {
        return $this->belongsTo(BusinessUnit::class, 'business_unit_code', 'code');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id', 'id');
    }

}
