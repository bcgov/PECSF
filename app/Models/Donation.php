<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable =[
        'org_code', 'pecsf_id', 'name', 'yearcd', 
        'pay_end_date', 'source_type', 'frequency', 'amount', 
        'process_history_id', 'process_status', 'process_date', 'created_by_id', 'updated_by_id', 
    ];

    protected $appends = [
        'source_type_descr',  
    ];


    public const SOURCE_TYPE_LIST = 
    [
        "01" => "Cash",
        "02" => "Credit Card",
        "04" => "Fund Raiser Event",
        "05" => "Gaming",
        "06" => "Personal Cheque",
        "10" => "Pledge",
        "11" => "Pledge One-time",
        "12" => "Special Campaign",
        "13" => "Donate Today",
        "99" => "Other",

    ];

    public const PROCESS_STATUS_LIST = 
    [
        'A' => 'Donation Allocation',
        'C' => 'Donation Cancelled',
        'D' => 'Donation Posted',
        'E' => 'Error/Inactive',
        'G' => 'General Deduction Set Up',
        'T' => 'Allocations Transmitted',
    ];


    public function getSourceTypeDescrAttribute()
    {
        //return $this->designation_name();
        return array_key_exists($this->source_type, self::SOURCE_TYPE_LIST) ? self::SOURCE_TYPE_LIST[$this->source_type] : '';
    }


    public function created_by()
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id');
    }

    public function updated_by()
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id');
    }


}
