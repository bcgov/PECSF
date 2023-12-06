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
        'bank_deposit_form_id',
        'filename',
        'original_filename',
        'mime',
        'file',
        'local_path',
    ];

    /**
     * Attributes to exclude from the Audit.
     *
     * @var array
     */
    protected $auditExclude = [
        'file',
    ];

    function form(){
        $this->belongsTo(BankDepositForm::class);
    }

}
