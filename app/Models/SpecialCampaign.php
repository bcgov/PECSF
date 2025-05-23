<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class SpecialCampaign extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name', 'description', 'banner_text', 'charity_id', 'start_date', 'end_date',
        'image', 'mime', 'image_data', 'created_by_id', 'updated_by_id'
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];

    protected $appends = [
        'status',  
    ];

    public function getStatusAttribute() {
        return ( $this->start_date <= today() and $this->end_date >= today() ) ? 'Active' : 'Inactive';
    } 

    public function charity() 
    {
        return $this->belongsTo(Charity::Class, 'charity_id', 'id');
    }

    public static function activeBannerText() {

        $special_campaigns = self::where('start_date', '<=', today())
                                     ->where('end_date', '>=', today())
                                     ->orderBy('start_date')
                                     ->pluck('banner_text');

        return $special_campaigns->toArray();
    }

   
    public static function hasActiveSpecialCampaign() {

        if ( count(self::activeBannerText()) > 0 ) {
            return true;
        } 

        return false;

    }

    public function hasPledge()
    {
        if ( $this->special_campaign_pledges()->exists() ) {
            return true;
        }

        return false;
        
    }

    public function special_campaign_pledges() {
        return $this->hasMany(SpecialCampaignPledge::class, 'special_campaign_id', 'id');
    }


}
