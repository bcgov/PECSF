<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BankDepositFormAttachments extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable =[
        'local_path',
        'bank_deposit_form_id'
    ];

    function form(){
        $this->belongsTo(BankDepositForm::class);
    }

}
