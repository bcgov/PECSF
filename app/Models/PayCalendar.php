<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayCalendar extends Model
{

    use HasFactory;

    protected $fillable = ['pay_end_dt', 'pay_begin_dt', 'check_dt', 'close_dt'];

}
