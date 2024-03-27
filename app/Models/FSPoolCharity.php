<?php

namespace App\Models;

use App\Models\FSPool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class FSPoolCharity extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable =[
        'f_s_pool_id', 'charity_id', 'status', 'name', 'description', 
        'percentage',  'contact_title', 'contact_name', 'contact_email', 'notes',
        'image', 'mime', 'image_data' 
    ];

    public function charity() 
    {
        return $this->belongsTo(Charity::Class, 'charity_id', 'id');
    }

    public function fund_suppported_pool() 
    {
        return $this->belongsTo(FSPool::Class, 'f_s_pool_id');
    }
}
