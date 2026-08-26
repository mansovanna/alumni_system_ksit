<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAgenda extends Model
{
    protected $table = 'event_agendas';

    protected $fillable = [
        'event_id',
        'time',
        'title',
    ];

    protected $casts = [
        'time' => 'string',
    ];

    /**
     * Event
     */
    public function event()
    {
        return $this->belongsTo(Events::class);
    }
    //
}
