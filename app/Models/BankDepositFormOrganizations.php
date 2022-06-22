<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDepositFormOrganizations extends Model
{
    use HasFactory;
    protected $fillable =[
        'organization_name',
        'vendor_id',
        'donation_percent',
        'specific_community_or_initiative',
        'bank_deposit_form_id'
    ];

    function form(){
        $this->belongsTo(BankDepositForm::class);
    }

}
