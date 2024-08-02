<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Setting extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable =[
        'system_lockdown_start', 'system_lockdown_end',
        'challenge_start_date', 'challenge_end_date', 'volunteer_start_date', 'volunteer_end_date','campaign_end_date','campaign_start_date',"campaign_final_date","challenge_final_date","volunteer_language",
        "campaign_processed_final_date","challenge_processed_final_date",
        'ee_snapshot_date_1', 'ee_snapshot_date_2', 

    ];

    protected $casts = [
        'system_lockdown_start' => 'datetime',
        'system_lockdown_end' => 'datetime',

        'challenge_start_date' => 'date:Y-m-d',
        'challenge_end_date' => 'date:Y-m-d',
        'challenge_final_date' => 'date:Y-m-d',
        'challenge_processed_final_date' => 'date:Y-m-d',        
        
        'campaign_start_date' => 'date:Y-m-d',
        'campaign_end_date' => 'date:Y-m-d',
        'campaign_final_date' => 'date:Y-m-d',
        'campaign_processed_final_date' => 'date:Y-m-d',

        'ee_snapshot_date_1' => 'date:Y-m-d', 
        'ee_snapshot_date_2' => 'date:Y-m-d',
    ];

    protected $appends = [
        'is_system_lockdown',  
    ];

    public function getIsSystemLockdownAttribute() 
    {
        $system_lockdown = false;
        if (($this->system_lockdown_start) && ($this->system_lockdown_end)) {
            if (now() >= $this->system_lockdown_start && now() <= $this->system_lockdown_end) {
                $system_lockdown = true;
            }
        }
        return $system_lockdown;
    }

    public static function challenge_page_campaign_year( $in_date = null ) {

        $in_date = $in_date ?? today();

        $year = $in_date->year;

        $campaign_year = CampaignYear::where('calendar_year', $year + 1)->first();

        if ($campaign_year) {
            if ($in_date < $campaign_year->start_date) {
                $year = $in_date->year - 1;
            }
        } else {
            if ($in_date->month < 9 ) {
                $year = $in_date->year - 1;
            }
        }

        return $year;
    }

    public static function isCampaignPeriodActive( $in_date = null ) {

        $in_date = $in_date ?? today();

        $setting = self::first();
        if ($in_date > $setting->campaign_start_date && $in_date <= $setting->campaign_final_date) {
            return true;
        }

        return false;
    }

    public static function isOrgParticipationTrackerActive( $in_date = null ) {

        $in_date = $in_date ?? today();

        $setting = self::first();
        if ($in_date >= $setting->ee_snapshot_date_2 && CampaignYear::isAnnualCampaignOpenNow() ) {
            return true;
        }

        return false;
    }
}
