<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecialCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'banner_text', 'charity_id', 'start_date', 'end_date',
        'image', 'created_by_id', 'updated_by_id'
    ];

    protected $appends = [
        'status',  
    ];

    public function getStatusAttribute() {
        return ( $this->start_date <= today() and $this->end_date >= today() ) ? 'Open' : 'Close';
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
}
