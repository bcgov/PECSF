<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{

    public const EVENT_TYPES = 
    [
        "created" => "created",
        "updated" => "updated",
        "deleted" => "deleted",
        "restored" => "restored",
    ];

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    // protected $casts = [
    //     'old_values'   => 'json',
    //     'new_values'   => 'json',
    //     // Note: Please do not add 'auditable_id' in here, as it will break non-integer PK models
    // ];

    // public function getSerializedDate($date)
    // {
    //     return $this->serializeDate($date);
    // }

    public function audit_user()
    {
        return $this->hasOne(User::class, 'id', 'user_id' );
    }
}
