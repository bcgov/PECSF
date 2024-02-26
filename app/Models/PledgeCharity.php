<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// class PledgeCharity extends Pivot implements Auditable
class PledgeCharity extends Model implements Auditable
{

    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'pledge_charities';
    protected $fillable = [
        'charity_id',
        'pledge_id',
        'frequency',
        'additional',
        'percentage',
        'amount',
        'goal_amount'/* ,
        'cheque_pending' */
    ];

    public function pledge() {
        return $this->belongsTo('App\Models\Pledge');
    }

    public function charity() {
        return $this->belongsTo('App\Models\Charity')->withDefault();
    }
/* 
    public function setChequePendingAttribute($value) {
        if ($this->amount === $this->goal_amount) {
            $this->attributes['cheque_pending'] = 1; // One-time
        } else {
            $this->attributes['cheque_pending'] = 26; // Bi-weekly
        }
    } */
}
