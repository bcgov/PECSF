<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonateNowPledge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 
        'emplid',
        'user_id',  // Not in use in the near future due to multiple GUID to emplid issue
        'pecsf_id', 'yearcd', 'seqno',
        'type', 'f_s_pool_id', 'charity_id', 'special_program',
        'one_time_amount', 'deduct_pay_from',
        'first_name', 'last_name', 'city', 'cancelled', 'cancelled_by_id', 'cancelled_at',
        'ods_export_status', 'ods_export_at',
        'created_by_id', 'updated_by_id', 'created_at',
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
