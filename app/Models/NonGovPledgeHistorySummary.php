<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonGovPledgeHistorySummary extends Model
{
    use HasFactory;
    
    public function details() 
    {
        return $this->hasMany(NonGovPledgeHistory::class, 'id', 'pledge_history_id');
    }
}
