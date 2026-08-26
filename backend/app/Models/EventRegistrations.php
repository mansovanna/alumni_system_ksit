<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistrations extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'alumni_id',
        'registered_at',
        'attendance_status',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    /* ------------------- Relationships ------------------- */

    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class);
    }

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class);
    }
}
