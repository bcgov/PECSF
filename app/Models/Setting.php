<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable =[
        'challenge_start_date', 'challenge_end_date', 'volunteer_start_date', 'volunteer_end_date','campaign_end_date','campaign_start_date',"campaign_final_date","challenge_final_date"
    ];
}
