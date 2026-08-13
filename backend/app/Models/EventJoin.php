<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventJoin extends Model
{
    //

    protected $table = 'event_joins';

    protected $fillable = [
        'user_id',
        'event_id',
        'status'
    ];
}
