<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDepositFormAttachments extends Model
{
    use HasFactory;
    protected $fillable =[
        'local_path',
        'bank_deposit_form_id'
    ];

    function form(){
        $this->belongsTo(BankDepositForm::class);
    }

}
