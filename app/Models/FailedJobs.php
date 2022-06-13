<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedJobs extends Model
{
    use HasFactory;

    public function getCommand(){
        return unserialize(json_decode($this->payload)->data->command);
    }

    public function getLastModified(){
        return gmdate("Y-m-d H:i:s", strtotime($this->attributesToArray()['failed_at']));
    }

    public function getFailedAttempts(){
        return 3;
    }
}
