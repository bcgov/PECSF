<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharityStaging extends Model
{
    use HasFactory;

    protected $fillable = [
        'history_id', 'registration_number', 'charity_name', 'charity_status',
    ];

}
