<?php

namespace App\Models;

use App\Models\DonorByBusinessUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessUnit extends Model
{
    use HasFactory;

    protected $fillable =[ 
        'code', 'name', 'status', 'effdt', 'notes', 'created_by_id', 'updated_by_id' 
    ];

    public function created_by() 
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id');
    }

    public function updated_by() 
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id');
    }

    public function donorHistory() {
        return $this->hasMany(DonorByBusinessUnit::class);             
    }

}
