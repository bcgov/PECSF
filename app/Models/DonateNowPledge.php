<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonateNowPledge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'user_id', 'pecsf_id', 'yearcd', 'seqno',
        'type', 'f_s_pool_id', 'charity_id', 'special_program',
        'one_time_amount', 'deduct_pay_from',
        'first_name', 'last_name', 'city', 'cancelled', 'created_by_id', 'created_at',
        'ods_export_status', 'ods_export_at',
        'created_by_id', 'updated_by_id',
    ];

    protected $appends = [
        'in_support_of',
    ];

    public function getInSupportOfAttribute()
    {
        $in_support_of = "";
        if ($this->type == 'P')  {
            $pool  = FSPool::current()->where('id', $this->f_s_pool_id)->first();
            $in_support_of = $pool ? $pool->region->name : '';
        } else {
            $charity = Charity::where('id', $this->charity_id)->first();
            $in_support_of = $charity ? $charity->charity_name : '';
        }
    
        return $in_support_of;

    }
  
    public function organization() {
        return $this->belongsTo(Organization::class)->withDefault();
    }

    public function user() {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function fund_supported_pool() {
        return $this->belongsTo(FSPool::class, 'f_s_pool_id', 'id')->withDefault();
    }

    public function charity() {
        return $this->belongsTo('App\Models\Charity')->withDefault();
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





}
