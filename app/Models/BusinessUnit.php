<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class BusinessUnit extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable =[
        'code', 'name', 'status', 'effdt', 'notes', 'created_by_id', 'updated_by_id'
    ];

    public function scopeCurrent($query)
    {
        $query->where('effdt', function($query) {
                return $query->selectRaw('max(effdt)')
                        ->from('business_units as A')
                        ->whereColumn('A.code', 'business_units.code')
                        ->whereNull('deleted_at')
                        ->where('A.effdt', '<=', today());
        });
    }

    public function created_by()
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id');
    }

    public function updated_by()
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id');
    }

    public function hasPledge()
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
