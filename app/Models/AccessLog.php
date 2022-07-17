<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'login_at',
        'login_out',
        'login_ip',
        'login_method',
        'identity_provider',
    ];

    public function user()
    {
        return $this->hasOne(User::Class, 'id', 'user_id')->withDefault();
    }
}
