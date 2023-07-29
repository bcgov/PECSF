<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','body', 'status', 'start_date', 'end_date', 'created_by_id', 'updated_by_id',
    ];

    public static function hasAnnouncement() {

        $today = today()->format('Y-m-d');

        $rec = self::first();

        if ($rec && $rec->status == 'A' && $today >= $rec->start_date && $today <= $rec->end_date ) {
            return true;
        } else {
            return false;
        }
    }

    public function created_by()
    {
        return $this->hasOne(User::Class, 'id', 'created_by_id')->withDefault();
    }

    public function updated_by()
    {
        return $this->hasOne(User::Class, 'id', 'updated_by_id')->withDefault();
    }

    
}
