<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    use HasFactory;

    public function getCommand(){
        return unserialize(json_decode($this->payload)->data->command);
    }

    public function getLastModified(){
        return gmdate("Y-m-d H:i:s", strtotime($this->attributesToArray()['created_at']." -7 hours"));
    }

    public function getFailedAttempts(){
        return $this->attributesToArray()['attempts'];
    }

}
