<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PledgeExport extends Model
{
    protected $table = 'pledge_export';    

    protected $appends = [
        'EMPLOYEEID'
    ];

    public function getEMPLOYEEIDAttribute() {
        return "00000".$this->attributes['EMPLOYEEID'];
    }
}
