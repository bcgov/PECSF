<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =[
        'code', 'name', 'status', 'effdt', 'notes', 'created_by_id', 'updated_by_id'
    ];

    protected $appends = [
        'hasPledge',
    ];

    public function created_by()
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id');
    }

    public function updated_by()
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id');
    }

    public static function report($request){
                return BusinessUnit::select(DB::raw('business_units.id,business_units.name, donor_by_business_units.donors,donor_by_business_units.dollars'))
                    ->join("donor_by_business_units", "donor_by_business_units.business_unit_id", "=", "business_units.id")
                    ->orderBy("donor_by_business_units.dollars","desc")
                    ->where('donor_by_business_units.yearcd', "=", $request->start_date);
    }

    public function getHasPledgeAttribute()
    {
        if ( $this->pledge_histories()->exists() ) {
            return true;
        }

        if ( $this->non_gov_pledge_histories()->exists() ) {
            return true;
        }

        return false;
    }

    public function pledge_histories() {
        return $this->hasMany(PledgeHistory::class, 'business_unit', 'code');
    }

    public function non_gov_pledge_histories() {
        return $this->hasMany(NonGovPledgeHistory::class, 'business_unit', 'code');
    }

}
