<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankDepositForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =[
        'organization_code',
        'form_submitter_id',
        'event_type',
        'sub_type',
        'deposit_date',
        'deposit_amount',
        'description',
        'employment_city',
        'region_id',
        'department_id',
        'address_line_1',
        'address_line_2',
        'address_city',
        'address_province',
        'address_postal_code',
        'regional_pool_id',
        'bc_gov_id',
        'pecsf_id',
        'business_unit',
        'approved',
        'created_at',
    ];

    function attachments(){
        $this->hasMany(BankDepositFormAttachments::class);
    }

    function organizations(){
        return $this->hasMany(BankDepositFormOrganizations::class,'bank_deposit_form_id','id');
    }

    public function fund_supported_pool() {
       
        return $this->belongsTo(FSPool::class, 'regional_pool_id', 'id')->withDefault();
        
    } 

}
