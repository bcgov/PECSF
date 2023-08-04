<?php

namespace App\Models;

use App\Models\DailyCampaignSummary;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoricalChallengePage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public static function finalize_challenge_page_data($campaign_year, $as_of_date) {

        // Finalize -- Proceed to Copy to the historial data 
        $rows = self::where('campaign_year', $campaign_year)
                    ->where('as_of_date', $as_of_date)
                    ->where('daily_type', 0)
                    ->get();

        // Clean up Old data 
        HistoricalChallengePage::where('year', $campaign_year)->delete();

        $donor_count = 0;
        $total_dollar = 0;

        foreach ($rows as $row) {

            HistoricalChallengePage::create([
                'business_unit_code' => $row->business_unit,
                'organization_name' => $row->business_unit_name,
                'participation_rate' => $row->participation_rate,
                'previous_participation_rate' => $row->previous_participation_rate,
                'change' => $row->change_rate, 
                'donors' => $row->donors,
                'dollars' => $row->dollars,
                'year' => $row->campaign_year, 
            ]);

            $donor_count += $row->donors;
            $total_dollar += $row->dollars;
        }

        DailyCampaignSummary::updateOrCreate([
            'campaign_year' => $campaign_year,
        ],[
            'as_of_date' => $as_of_date,

            'donors' => $donor_count,
            'dollars' => $total_dollar,

            'updated_by_id' => Auth::id(),
        ]);

        return true;

    }

}
