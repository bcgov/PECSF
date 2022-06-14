<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PledgeHistoryVendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'charity_bn', 'eff_status', 'effdt', 'name1', 'name2',
            'tgb_reg_district', 'vendor_id', 'yearcd'    
        ];
}
