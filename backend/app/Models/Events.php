<?php
// app/Models/Event.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Events extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'event_type',
        'banner_image',
        'location',
        'start_date',
        'end_date',
        'registration_deadline',
        'max_participants',
        'external_link',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'max_participants' => 'integer',
    ];


    protected $appends = [
        'banner_image_url',
    ];

    public function getBannerImageUrlAttribute(): ?string
    {
        if (!$this->banner_image) {
            return null;
        }

        return asset($this->banner_image);
    }


    /* ------------------- Relationships ------------------- */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // In app/Models/Events.php
    public function registrations()
    {
        return $this->hasMany(
            EventRegistrations::class,
            'event_id',
            'id'
        );
    }

     public function eventRegistration()
    {
        return $this->hasOne(EventRegistrations::class);
    }

    public function alumni(): BelongsToMany
    {
        return $this->belongsToMany(Alumni::class, 'event_registrations')
            ->withPivot(['attendance_status', 'registered_at']);
    }

    /* ------------------- Scopes ------------------- */
    public function agendas(): HasMany
    {
        return $this->hasMany(EventAgenda::class, 'event_id');
    }

    public function speakers(): HasMany
    {
        return $this->hasMany(EventSpeakers::class, 'event_id');
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
