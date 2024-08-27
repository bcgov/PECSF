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
        'deptid',
        'dept_name',
        'approved',
        'created_at',
        'update_at',
        'created_by_id',
        'updated_by_id',
        'approved_by_id',
        'approved_at',
        'employee_name'
    ];

    protected $appends = [
        'status',
        'charity_selection',
        'challenge_business_unit',
    ];

    protected $casts = [
        'deposit_date' => 'date:Y-m-d',
        'approved_at' => 'datetime',
    ];

     public const STATUS = [
        '0' => "Pending",
        '1' => "Approved",
        '2' => "Locked",
    ];

    public function getStatusAttribute() {

        return $this->approved ? self::STATUS[$this->approved] : '';
        
    }

    public function getCharitySelectionAttribute() {
        return ($this->regional_pool_id ? 'fsp' : 'dc');
    }

    public function getChallengeBusinessUnitAttribute()
    {

        // Special Rule -- To split GCPE employees from business unit BC022 
        $bu = BusinessUnit::where('id', $this->business_unit)->first();
        $business_unit_code = $bu ? $bu->linked_bu_code : null;
        if ($bu->code == 'BC022' && str_starts_with($this->dept_name, 'GCPE')) {
            $business_unit_code  = 'BGCPE';
        }
        $linked_bu = BusinessUnit::where('code', $business_unit_code)->first();

        return $linked_bu;

    }

    function attachments(){
        return $this->hasMany(BankDepositFormAttachments::class,'bank_deposit_form_id','id');
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

    public function organization() {
        return $this->belongsTo(Organization::class, 'organization_code', 'code')->withDefault();
    }

    public function region() {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function bu() {
        return $this->belongsTo(BusinessUnit::class, 'business_unit', 'id');
    }


    public function form_submitted_by()
    {
        return $this->belongsTo(User::Class, 'form_submitter_id', 'id')->withDefault();
    }

    public function created_by()
    {
        return $this->belongsTo(User::Class, 'created_by_id', 'id')->withDefault();
    }

    public function updated_by()
    {
        return $this->belongsTo(User::Class, 'updated_by_id', 'id')->withDefault();
    }

    public function approved_by()
    {
        return $this->belongsTo(User::Class,  'approved_by_id', 'id')->withDefault();
    }

}
