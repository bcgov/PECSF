<?php

namespace App\Models;

use App\Models\FSPool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FSPoolCharity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =[
        'charity_id', 'status', 'name', 'description', 
        'percentage',  'contact_title', 'contact_name', 'contact_email', 'notes',
        'image'
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
