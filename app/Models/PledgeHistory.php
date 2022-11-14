<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PledgeHistory extends Model
{
    use HasFactory;

    protected $fillable =[
        'campaign_type', 'source', 'frequency', 'yearcd', 'campaign_year_id', 'tgb_reg_district', 'region_id', 
        'name1', 'name2', 'emplid', 'GUID', 'vendor_id', 'additional_info',
        'charity_bn', 'charity_id', 'per_pay_amt', 'pledge', 'percent', 'amount',
        'vendor_name1', 'vendor_name2', 'vendor_bn', 'remit_vendor', 'deptid', 'city', 'created_date',
    ];

    public function region() {
        return $this->belongsTo(Region::class, 'tgb_reg_district', 'code');
    }

    public function fund_supported_pool() {

        // return \App\Models\FSPool::where('region_id', $this->region->id)->first();
        return $this->belongsTo(FSPool::class, 'region_id', 'region_id');
    }

    public function charity() {
        return $this->belongsTo(Charity::class)->withDefault([
            'charity_name' => 'Unknown',
        ]);
    }

    public function campaign_year() {
        return $this->belongsTo(CampaignYear::class)->withDefault([
            'number_of_periods' => '26'
        ]);
    }

}
