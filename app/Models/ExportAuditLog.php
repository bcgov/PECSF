<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExportAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_job_name', 'schedule_job_id', 'to_application', 'table_name', 'row_id', 'row_values'
    ];

    public static function table_name_options() {

        return self::select('table_name')
                ->distinct()
                ->pluck('table_name');

    }

    public static function to_application_options() {

        return self::select('to_application')
                ->distinct()
                ->pluck('to_application');

    }

    public function schedule_job() 
    {
        return $this->belongsTo(ScheduleJobAudit::Class, 'schedule_job_id', 'id');
    }


}
