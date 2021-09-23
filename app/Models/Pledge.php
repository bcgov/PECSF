<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Pledge extends Model
{
    protected $fillable = [
        'user_id',
        'frequency',
        'amount',
        'goal_amount'
    ];

    public function charities() {
        return $this->hasMany(PledgeCharity::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function markAsGenerated() {
        $this->report_generated_at = Carbon::now();
        return $this;
    }

    /* public function charities() {
        return $this->belongsToMany('App\Models\Charity', 'pledge_charities', 'pledge_id', 'charity_id')
        ->using('App\Models\PledgeCharity')->withTimestamps();
    } */
}
