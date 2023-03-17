<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SupplyOrderForm extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'calendar',
'posters',
'stickers',
'first_name',
'last_name',
'business_unit_id',
"include_name",
"unit_suite_floor",
"physical_address",
"city",
"province",
"postal_code",
"po_box",
"date_required",
"comments",
"address_type"
    ];
}
