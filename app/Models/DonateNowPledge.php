<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonateNowPledge extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'user_id', 'pecsf_id', 'yearcd', 'seqno',
        'type', 'f_s_pool_id', 'charity_id', 'special_program',
        'one_time_amount',
        'first_name', 'last_name', 'city',
        'ods_export_status', 'ods_export_at',
        'created_by_id', 'updated_by_id',
    ];


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

    
    public function created_by()
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id')->withDefault();
    }

    public function updated_by()
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id')->withDefault();
    }



}
