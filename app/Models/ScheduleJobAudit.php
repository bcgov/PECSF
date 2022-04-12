<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleJobAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_name', 'start_time', 'end_time', 'status'
    ];
}
