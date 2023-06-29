<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
           "campaign_year",
            "as_of_date",
            "daily_type",
            "organization_code",
            'business_unit',
            'business_unit_name',
            'region_code',
            'region_name',
            'deptid',
            'dept_name',
            'participation_rate',
            'previous_participation_rate',
            'change_rate',
            'rank',
            'eligible_employee_count',
            'donors',
            "dollars",
    ];

    public const TYPE_LIST = 
    [
        0 => "Business Unit",
        1 => "Region",
        2 => "Department",
    ];


}
