<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Setting extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable =[
        'challenge_start_date', 'challenge_end_date', 'volunteer_start_date', 'volunteer_end_date','campaign_end_date','campaign_start_date',"campaign_final_date","challenge_final_date","volunteer_language"
    ];

    protected $casts = [
        'challenge_start_date' => 'date:Y-m-d',
        'challenge_end_date' => 'date:Y-m-d',
        'challenge_final_date' => 'date:Y-m-d',

        'campaign_start_date' => 'date:Y-m-d',
        'campaign_end_date' => 'date:Y-m-d',
        'campaign_final_date' => 'date:Y-m-d',
    ];


}
