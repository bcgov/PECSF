<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class CampaignYear extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    

    /** The attribute that are mass assignable
     *
     */
    protected $fillable =[
        'calendar_year',
        'number_of_periods',
        'status',
        'start_date',
        'end_date',
        'close_date',
        'volunteer_start_date',
        'volunteer_end_date',
        'created_by_id',
        'modified_by_id',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'close_date' => 'date:Y-m-d',
        'volunteer_start_date' => 'date:Y-m-d',
        'volunteer_end_date' => 'date:Y-m-d',
    ];

    // Class Method 
    public static function isAnnualCampaignOpenNow() {
        $today = today();
        $cy = self::where('start_date', '<=',  $today) 
                ->where('end_date', '>=', $today)
                ->where('calendar_year', $today->year + 1)
                ->first();

        if ($cy && $cy->status == 'A') {
            return true;
        } else {
            return false;
        }
        
    }

    public static function isVolunteerRegistrationOpenNow() {
        $today = today();
        $cy = self::where('volunteer_start_date', '<=',  $today) 
                ->where('volunteer_end_date', '>=', $today)
                ->where('calendar_year', $today->year + 1)
                ->first();

        if ($cy) {
            return true;
        } else {
            return false;
        }
    }

    public static function defaultCampaignYear() {

        $default_campaign_year = today()->year - 1; 
        $temp_campaign_year = CampaignYear::where('calendar_year', today()->year +1 )->first();
        if ($temp_campaign_year && today() >= $temp_campaign_year->start_date ) {
            $default_campaign_year = today()->year; 
        } 

        return $default_campaign_year;

    }
   

    public function created_by()
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id');
    }

    public function modified_by()
    {
        return $this->hasOne(User::Class, 'id', 'modified_by_id');
    }

    public function isOpen() {

        $today = today();
        return ($this->status == 'A' && ($today >= $this->start_date && $today <= $this->end_date));
    }

    public function isActive() {
        return ($this->status == 'A');
    }

    public function isVolunteerRegistrationOpen() {

        $today = today();
        return ($today >= $this->volunteer_start_date && $today <= $this->volunteer_end_date);
    }

    public function canSendToPSFT() {

        $today = today();
        $from_date = $this->start_date;
        $to_date =  $this->end_date->addDays(1);

        return ( $this->status == 'I' && (!($today <= $to_date)) );

    }


}
