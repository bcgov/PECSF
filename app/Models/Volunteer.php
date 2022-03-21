<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    protected $fillable = [
        'user_id', 'no_of_years', 'new_address', 'address_type', 'preferred_role', 'organization_id'
    ];
    
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function organization() {
        return $this->belongsTo(Organization::class);
    }
}
