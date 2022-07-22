<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleJobAudit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_name', 'start_time', 'end_time', 'status', 'created_by_id', 'updated_by_id'
    ];

    // Static function for gettig the list of status
    public static function job_status_options() {

        return ScheduleJobAudit::whereNotNull('status')
                ->select('status')
                ->distinct()
                ->orderBy('status')
                ->pluck('status');
    }

    public function created_by()
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id')->withDefault();
    }

    public function updated_by()
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id')->withDefault();
    }

    

}
