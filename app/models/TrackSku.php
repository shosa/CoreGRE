<?php

namespace App\Models;

/**
 * TrackSku Model
 * Table: track_sku
 *
 * Gestione SKU per articoli nel tracking
 */
class TrackSku extends BaseModel
{
    protected $table = 'track_sku';
    protected $primaryKey = 'art';

    // Non-incrementing string primary key
    public $incrementing = false;
    protected $keyType = 'string';

    // Disable timestamps if not present in legacy table
    public $timestamps = false;

    protected $fillable = [
        'art',
        'sku'
    ];

    protected $casts = [];

    /**
     * Relationship with core data records
     */
    public function coreDataRecords()
    {
        return $this->hasMany(CoreData::class, 'Articolo', 'art');
    }
}