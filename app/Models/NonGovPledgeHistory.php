<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonGovPledgeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'org_code', "emplid", "pecsf_id", 'pledge_type', 'yearcd', 
        'vendor_id', 'vendor_bn', 'remit_vendor', 'remit_vendor_bn',
        'name', 'first_name', 'last_name', 'city', 'amount',
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
