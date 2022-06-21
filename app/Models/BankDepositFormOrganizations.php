<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDepositFormOrganizations extends Model
{
    use HasFactory;

    function form(){
        $this->belongsTo(BankDepositForm::class);
    }

}
