<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorByDepartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bi_department_id', 'yearcd', 'date', 'dollars', 'donors', 'department_id'
    ];

    public function department() {
        return $this->belongsTo('App\Models\Department');
    }
}
