<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FSPoolCharity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FSPool extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =[
        'region_id', 'start_date', 'status', 'created_by_id', 'updated_by_id', 'created_at'
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
    ];

    protected $appends = [
        'canEdit', 'canDelete', 'effectiveType', 'hasPledge'];
        
    // Scope 
    public function scopeCurrent($query)
    {
        $query->where('start_date', function($query) {
                return $query->selectRaw('max(start_date)')
                        ->from('f_s_pools as A')
                        ->whereColumn('A.region_id', 'f_s_pools.region_id')
                        ->whereNull('deleted_at')
                        ->where('A.start_date', '<=', today());
        });
    }

    public function scopeAsOfDate($query, $specifyDate) {
        $query->where('start_date', function($query) use($specifyDate) {
            return $query->selectRaw('max(start_date)')
                    ->from('f_s_pools as A')
                    ->whereColumn('A.region_id', 'f_s_pools.region_id')
                    ->whereNull('deleted_at')
                    ->where('A.start_date', '<=', $specifyDate);
        });
    }

    public function region() 
    {
        return $this->belongsTo(Region::Class, 'region_id', 'id')->withDefault();
    }

    public function charities() 
    {
        return $this->hasMany(FSPoolCharity::class);
    }

    public function getCanEditAttribute()
    {

        if ($this->start_date <= today()) {
            return false;
        };

        return true;
    }

    public function getCanDeleteAttribute()
    {

        if ($this->start_date < today()) {
            return false;
        };

        // if ( $this->transactionExists ) {
        //     return false;
        // }

        return true;
    }

    public function getEffectiveTypeAttribute()
    {
        $current = FSPool::current()->where('region_id', $this->region_id)->first();

        if ($current) {
            return ( $this->start_date < $current->start_date ? 'H' :
                     ( $this->start_date > $current->start_date ? 'F' : 'C')
            );
        } else {
            return 'F';
        }

    }

    public function getHasPledgeAttribute()
    {
        if ( $this->annual_campaign_pledges()->exists() ) {
            return true;
        }

        if ( $this->donate_now_pledges()->exists() ) {
            return true;
        }

        return false;
        
    }

    public function annual_campaign_pledges() {

        return $this->hasMany(Pledge::class, 'region_id', 'region_id')
                    ->whereRaw("Date(pledges.created_at) >= '" . $this->start_date . "'");
    }

    public function donate_now_pledges() {
        return $this->hasMany(DonateNowPledge::class, 'region_id', 'region_id')
                    ->whereRaw("Date(donate_now_pledges.created_at) >= '" . $this->start_date . "'");

    }

}
