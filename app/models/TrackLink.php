<?php

namespace App\Models;

/**
 * TrackLink Model
 * Table: track_links
 *
 * Collegamenti tra cartellini e lotti per il tracking
 */
class TrackLink extends BaseModel
{
    protected $table = 'track_links';
    protected $primaryKey = 'id';

    // Use timestamp as created_at equivalent
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = null; // Disable updated_at

    protected $fillable = [
        'cartel',
        'type_id',
        'lot',
        'note',
        'timestamp'
    ];

    protected $casts = [
        'cartel' => 'integer',
        'type_id' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Relationship with track type
     */
    public function trackType()
    {
        return $this->belongsTo(TrackType::class, 'type_id');
    }

    /**
     * Relationship with core data (cartellino)
     */
    public function coreData()
    {
        return $this->belongsTo(CoreData::class, 'cartel', 'Cartel');
    }

    /**
     * Relationship with lot info
     */
    public function lotInfo()
    {
        return $this->belongsTo(TrackLotInfo::class, 'lot', 'lot');
    }

    /**
     * Scope for specific cartellino
     */
    public function scopeForCartel($query, $cartel)
    {
        return $query->where('cartel', $cartel);
    }

    /**
     * Scope for specific lot
     */
    public function scopeForLot($query, $lot)
    {
        return $query->where('lot', $lot);
    }

    /**
     * Scope for specific type
     */
    public function scopeForType($query, $typeId)
    {
        return $query->where('type_id', $typeId);
    }

    /**
     * Scope for recent links (last N days)
     */
    public function scopeRecent($query, $days = 7)
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $query->where('timestamp', '>=', $date);
    }
}