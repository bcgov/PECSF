<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FSPoolCharity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FSPool extends Model
{
    use HasFactory;

    protected $fillable =[
        'region_id', 'start_date', 'status', 'created_by_id', 'updated_by_id' 
    ];
    protected $appends = ['canDelete'];

    // Scope 
    public function scopeCurrent($query)
    {
        $query->where('start_date', function($query) {
                return $query->selectRaw('max(start_date)')
                        ->from('f_s_pools as A')
                        ->whereColumn('A.region_id', 'f_s_pools.region_id')
                        ->where('A.start_date', '<=', today());
        });
    }

    public function region() 
    {
        return $this->belongsTo(Region::Class, 'region_id', 'id');
    }

    public function charities() 
    {
        return $this->hasMany(FSPoolCharity::class);
    }


    public function getCanDeleteAttribute()
    {
        
        return ( $this->start_date >= today() );
        
    }


}
