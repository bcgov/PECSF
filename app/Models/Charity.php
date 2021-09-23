<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charity extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'charity_name',
        'charity_status',
        'effective_date_of_status',
        'sanction',
        'designation_code',
        'category_code',
        'address',
        'city',
        'province',
        'country',
        'postal_code'
    ];

    protected $casts = [
        'effective_date_of_status' => 'date'
    ];

    public function pledges() {
        return $this->belongsToMany('App\Models\Pledge', 'pledge_charities', 'charity_id', 'pledge_id')
        ->using('App\Models\PledgeCharity')->withTimestamps();
    }
}
