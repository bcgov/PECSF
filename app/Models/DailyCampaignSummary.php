<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class DailyCampaignSummary extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'campaign_year',
        'as_of_date',
   
        'donors',
        'dollars',

        'notes',

        'created_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'as_of_date' => 'date:Y-m-d',
    ];

    public function created_by()
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id');
    }

    public function updated_by()
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id');
    }
}
