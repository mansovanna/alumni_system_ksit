<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSpeakers extends Model
{
    //

    protected $table = 'event_speakers';

    protected $fillable = [
        'event_id',
        'name',
        'professional_title',
    ];

    /**
     * Event
     */
    public function event()
    {
        return $this->belongsTo(Events::class);
    }
}
