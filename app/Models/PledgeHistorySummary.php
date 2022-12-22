<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PledgeHistorySummary extends Model
{
    use HasFactory;
    
    public function first_detail() 
    {
        // return $this->hasMany(PledgeHistory::class, 'id', 'pledge_history_id');
        return $this->belongsTo(PledgeHistory::class,  'pledge_history_id', 'id');
    }



}
