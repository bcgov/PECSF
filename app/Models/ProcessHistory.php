<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessHistory extends Model
{
    use HasFactory;

    protected $fillable =[
        'batch_id', 'process_name',  'parameters',
        'status', 'message', 'submitted_at', 'start_at', 'end_at', 
        'original_filename', 'filename', 'done_count', 'total_count',
        'created_by_id', 'updated_by_id'
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
