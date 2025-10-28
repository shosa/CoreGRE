<?php

namespace App\Models;

/**
 * SCM Launch Model - Lanci di produzione
 * Tabella: scm_launches
 */
class ScmLaunch extends BaseModel
{
    protected $table = 'scm_launches';
    protected $primaryKey = 'id';

    protected $fillable = [
        'launch_number',
        'laboratory_id',
        'launch_date',
        'phases_cycle',
        'status',
        'blocked_reason',
        'notes'
    ];

    protected $casts = [
        'launch_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const STATUS_PREPARING = 'IN_PREPARAZIONE';
    const STATUS_ACTIVE = 'IN_LAVORAZIONE';
    const STATUS_BLOCKED = 'BLOCCATO';
    const STATUS_COMPLETED = 'COMPLETATO';

    /**
     * Relationship with laboratory
     */
    public function laboratory()
    {
        return $this->belongsTo(ScmLaboratory::class, 'laboratory_id');
    }

    /**
     * Relationship with articles
     */
    public function articles()
    {
        return $this->hasMany(ScmLaunchArticle::class, 'launch_id');
    }

    /**
     * Relationship with phases
     */
    public function phases()
    {
        return $this->hasMany(ScmLaunchPhase::class, 'launch_id');
    }

    /**
     * Relationship with progress tracking
     */
    public function progressTracking()
    {
        return $this->hasMany(ScmProgressTracking::class, 'launch_id');
    }

    /**
     * Scope for active launches
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_BLOCKED]);
    }

    /**
     * Scope for preparing launches
     */
    public function scopePreparing($query)
    {
        return $query->where('status', self::STATUS_PREPARING);
    }

    /**
     * Scope for completed launches
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for blocked launches
     */
    public function scopeBlocked($query)
    {
        return $query->where('status', self::STATUS_BLOCKED);
    }

    /**
     * Scope by date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('launch_date', [$from, $to]);
    }

    /**
     * Get phases as array
     */
    public function getPhasesArrayAttribute()
    {
        return $this->phases_cycle ? explode(';', $this->phases_cycle) : [];
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->launch_date ? $this->launch_date->format('d/m/Y') : '';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PREPARING:
                return 'bg-yellow-100 text-yellow-800';
            case self::STATUS_ACTIVE:
                return 'bg-blue-100 text-blue-800';
            case self::STATUS_BLOCKED:
                return 'bg-red-100 text-red-800';
            case self::STATUS_COMPLETED:
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PREPARING:
                return 'In Preparazione';
            case self::STATUS_ACTIVE:
                return 'In Lavorazione';
            case self::STATUS_BLOCKED:
                return 'Bloccato';
            case self::STATUS_COMPLETED:
                return 'Completato';
            default:
                return 'Sconosciuto';
        }
    }

    /**
     * Check if launch is blocked
     */
    public function isBlocked()
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    /**
     * Check if launch is completed
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Get total pairs
     */
    public function getTotalPairsAttribute()
    {
        return $this->articles()->sum('total_pairs');
    }
}