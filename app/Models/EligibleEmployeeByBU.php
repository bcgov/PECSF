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
    ];

}
