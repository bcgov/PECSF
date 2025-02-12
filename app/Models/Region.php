<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Region extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable =[
        'code', 'name', 'status', 'effdt', 'notes', 'created_by_id', 'updated_by_id'
    ];

    protected $appends = [
        'hasFSPool',
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
        return Region::select(DB::raw('regions.id,regions.name, donor_by_regional_districts.donors,donor_by_regional_districts.dollars'))
            ->join("donor_by_regional_districts", "donor_by_regional_districts.regional_district_id", "=", "regions.id")
            ->orderBy("donor_by_regional_districts.dollars","desc")
            ->where('donor_by_regional_districts.yearcd', "=", $request->start_date);

    }

    public function getHasFSPoolAttribute()
    {
        if ( $this->f_s_pools()->exists() ) {
            return true;
        }

        return false;
    }

    public function f_s_pools() {
        return $this->hasMany(FSPool::class, 'region_id', 'id');
    }

}
