<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_job_name', 'schedule_job_id', 'to_application', 'table_name', 'row_id', 'row_values'
    ];
}
