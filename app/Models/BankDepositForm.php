<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class BankDepositForm extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;    

    protected $fillable =[
        'organization_code',
        'form_submitter_id',
        'campaign_year_id',
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

    protected $casts = [
        'deposit_date' => 'date:Y-m-d',
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

    public function charities() {
        return $this->hasMany(BankDepositFormOrganizations::class, 'bank_deposit_form_id', 'id');
    }

    public function campaign_year() {
        return $this->belongsTo(CampaignYear::class, 'campaign_year_id', 'id');
    }

}
