<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonGovPledgeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        // 'org_code', "emplid", "pecsf_id", 'pledge_type', 'yearcd', 
        // 'vendor_id', 'vendor_bn', 'remit_vendor', 'remit_vendor_bn',
        // 'name', 'first_name', 'last_name', 'city', 'amount',
        'pledge_type', 'source', 'tgb_reg_district', 'charity_bn', 'yearcd',
        'org_code', 'emplid', 'pecsf_id', 'name', 'first_name', 'last_name',
        'guid', 'vendor_id', 'additional_info','frequency',
        'per_pay_amt', 'pledge', 'percent', 'amount', 'deduction_code',
        'vendor_name1', 'vendor_name2', 'vendor_bn', 'remit_vendor',
        'deptid', 'city', 
        'business_unit', 'event_descr', 'event_type', 'event_sub_type',
        'created_date',
        'created_by_id', 'updated_by_id',       


    ];

    public function created_by() 
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id');
    }

    public function updated_by() 
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id');
    }


}
