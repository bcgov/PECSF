<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'banner_text', 'charity_id', 'start_date', 'end_date',
        'image', 'created_by_id', 'updated_by_id'
    ];

    public function charity() 
    {
        return $this->belongsTo(Charity::Class, 'charity_id', 'id');
    }
    
    public static function activeBannerText() {

        $banner_text = null;

        $special_campaigns = self::where('start_date', '<=', today())
                                     ->where('end_date', '>=', today())
                                     ->orderBy('start_date')
                                     ->get();

        if ($special_campaigns->count() > 0) {
            $banner_text = $special_campaigns->first()->banner_text;
        }

        return $banner_text;
    }
}
