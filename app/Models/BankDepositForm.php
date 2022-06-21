<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDepositForm extends Model
{
    use HasFactory;

    function attachments(){
        $this->hasMany(BankDepositFormAttachments::class);
    }

    function organizations(){
        $this->hasMany(BankDepositFormOrganizations::class);
    }

}
