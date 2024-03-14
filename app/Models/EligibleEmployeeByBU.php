<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EligibleEmployeeByBU extends Model
{
    use HasFactory;

    protected $table = 'eligible_employee_by_bus';

    protected $fillable =[
        'campaign_year',
        'as_of_date',
        'organization_code',
        'business_unit_code',
        'business_unit_name',
        'ee_count',
        'notes',
        'created_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'as_of_date' => 'date:Y-m-d',
    ];

    public function organization() {
        return $this->belongsTo(Organization::class, 'organization_code', 'code');
    }

    public function business_unit() {
        return $this->belongsTo(BusinessUnit::class, 'business_unit_code', 'code');
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
