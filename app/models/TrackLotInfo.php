<?php

namespace App\Models;

/**
 * TrackLotInfo Model
 * Table: track_lots_info
 *
 * Informazioni dettagliate sui lotti nel tracking
 */
class TrackLotInfo extends BaseModel
{
    protected $table = 'track_lots_info';
    protected $primaryKey = 'lot';

    // Non-incrementing string primary key
    public $incrementing = false;
    protected $keyType = 'string';

    // Disable timestamps if not present in legacy table
    public $timestamps = false;

    protected $fillable = [
        'lot',
        'doc',
        'date',
        'note'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    /**
     * Relationship with track links
     */
    public function trackLinks()
    {
        return $this->hasMany(TrackLink::class, 'lot', 'lot');
    }
}