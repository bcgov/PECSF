<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionTimesMonitoring extends Model
{

    protected $table = 'transaction_times_monitoring';

    protected $fillable =[
        'user_id',
        'action_type',
        'table_name',
        'tran_id',
        'browser_name',
        'platform',
        'device',
        'ip',
        'page',
        'start_time',
        'end_time',
        'duration',
    ];

    public function user()
    {
        return $this->hasOne(User::Class, 'id', 'user_id')->withDefault();
    }
}
