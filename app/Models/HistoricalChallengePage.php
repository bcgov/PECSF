<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricalChallengePage extends Model
{
    use HasFactory;

    protected $fillable =[
        'business_unit_code', 'organization_name', 'participation_rate', 'previous_participation_rate',	'change', 
            'donors', 	'dollars', 'year'
    ];
}
