<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDepositForm extends Model
{
    use HasFactory;

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
        'pecsf_id'
    ];

    function attachments(){
        $this->hasMany(BankDepositFormAttachments::class);
    }

    function organizations(){
        $this->hasMany(BankDepositFormOrganizations::class);
    }

}
