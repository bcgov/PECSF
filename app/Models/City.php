<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable =[
        'city',
        'country',
        'province',
        'TGB_REG_DISTRICT',
        'DescrShort',
    ];

    public function region()
    {
        return $this->belongsTo(Region::Class, 'TGB_REG_DISTRICT', 'code')->withDefault();
    }
}
