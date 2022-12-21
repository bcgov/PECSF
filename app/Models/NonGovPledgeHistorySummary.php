<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonGovPledgeHistorySummary extends Model
{
    use HasFactory;
    
    public function first_detail() 
    {
        // return $this->hasMany(NonGovPledgeHistory::class, 'id', 'pledge_history_id');
        return $this->belongsTo(NonGovPledgeHistory::class, 'pledge_history_id', 'id');
    }
}
