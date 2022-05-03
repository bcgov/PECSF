<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
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

}
