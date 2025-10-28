<?php

namespace App\Models;

/**
 * TrackType Model
 * Table: track_types
 *
 * Gestione tipi di tracking per il sistema di monitoraggio lotti
 */
class TrackType extends BaseModel
{
    protected $table = 'track_types';

    // Disable timestamps since legacy table likely doesn't have them
    public $timestamps = false;

    protected $fillable = [
        'name',
        'note'
    ];

    /**
     * Relationship with track links
     */
    public function trackLinks()
    {
        return $this->hasMany(TrackLink::class, 'type_id');
    }

    /**
     * Scope ordered by name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}