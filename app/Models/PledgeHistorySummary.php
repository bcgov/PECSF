<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PledgeHistorySummary extends Model
{
    use HasFactory;
    
    public function details() 
    {
        return $this->hasMany(PledgeHistory::class, 'id', 'pledge_history_id');
    }
}
