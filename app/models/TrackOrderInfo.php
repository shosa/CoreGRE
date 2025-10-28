<?php

namespace App\Models;

/**
 * TrackOrderInfo Model
 * Table: track_order_info
 *
 * Informazioni aggiuntive sugli ordini per il tracking
 */
class TrackOrderInfo extends BaseModel
{
    protected $table = 'track_order_info';
    protected $primaryKey = 'id';

    // Non-incrementing primary key
    public $incrementing = false;
    protected $keyType = 'string';

    // Disable timestamps if not present in legacy table
    public $timestamps = false;

    protected $fillable = [
        'ordine',
        'date',
    ];

    protected $casts = [
        'id' => 'integer',
        'ordine' => 'integer',
        'date' => 'date'
    ];

    /**
     * Relationship with core data records
     */
    public function coreDataRecords()
    {
        return $this->hasMany(CoreData::class, 'Ordine', 'ordine');
    }

    /**
     * Scope for specific date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    /**
     * Scope for recent orders
     */
    public function scopeRecent($query, $days = 30)
    {
        $date = date('Y-m-d', strtotime("-{$days} days"));
        return $query->where('date', '>=', $date);
    }
}