<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class BankDepositFormOrganizations extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

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

    public function charity() {
        return $this->belongsTo(Charity::class, 'vendor_id', 'id');
    }
}
