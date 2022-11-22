<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CampaignYear extends Model
{
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
        'created_by_id',
        'modified_by_id',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'close_date' => 'date:Y-m-d',
    ];

    // Class Method 
    public static function isAnnualCampaignOpenNow() {
        $today = today();
        $cy = self::where('start_date', '<=',  $today) 
                ->where('end_date', '>=', $today)
                ->first();

        if ($cy && $cy->status == 'A') {
            return true;
        } else {
            return false;
        }
        
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
        return ($this->status == 'A' && ($today >= $this->start_date && $today < $this->end_date));
    }

    public function isActive() {
        return ($this->status == 'A');
    }

    public function canSendToPSFT() {

        $today = today();
        $from_date = $this->start_date;
        $to_date =  $this->end_date->addDays(7);

        return ( $this->status == 'I' && (!($today >= $from_date && $today <= $to_date)) );

    }


}
