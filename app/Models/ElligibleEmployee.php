<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElligibleEmployee extends Model
{
    use HasFactory;

    protected $fillable =[
        'as_of_date',
        'ee_count',
        'business_unit',
        'business_unit_name',
        'cde',
        'year',
    ];


}
