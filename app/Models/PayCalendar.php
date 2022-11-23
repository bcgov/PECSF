<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayCalendar extends Model
{

    use HasFactory;

    protected $fillable = ['pay_end_dt', 'pay_begin_dt', 'check_dt', 'close_dt'];

    // Class Method 
    public static function nextDeductPayFrom() {
        
        // Deduct from pay auto-populates using the same logic as the donor side (current period+2)
        $current = self::whereRaw(" ( date(SYSDATE()) between pay_begin_dt and pay_end_dt) ")->first();

        $check_dt = null;
        if ($current) {
            $period = self::where('check_dt', '>=',  $current->check_dt )->skip(2)->take(1)->orderBy('check_dt')->first();
            $check_dt = $period ? $period->check_dt : null;
        }

        return $check_dt;
        
    }
    
}
