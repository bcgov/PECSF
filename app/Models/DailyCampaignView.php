<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyCampaignView extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_campaign_view';
    
    public function business_unit() {
        return $this->belongsTo(BusinessUnit::class, 'business_unit_code', 'code');
    }

}
