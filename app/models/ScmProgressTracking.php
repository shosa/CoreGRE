<?php

namespace App\Models;

/**
 * SCM Progress Tracking Model - Tracciamento progresso
 * Tabella: scm_progress_tracking
 */
class ScmProgressTracking extends BaseModel
{
    protected $table = 'scm_progress_tracking';
    protected $primaryKey = 'id';

    protected $fillable = [
        'launch_id',
        'article_id',
        'phase_id',
        'status',
        'completed_pairs',
        'is_blocked',
        'blocked_reason'
    ];

    protected $casts = [
        'completed_pairs' => 'integer',
        'is_blocked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const STATUS_NOT_STARTED = 'NON_INIZIATA';
    const STATUS_IN_PROGRESS = 'IN_CORSO';
    const STATUS_COMPLETED = 'COMPLETATA';
    const STATUS_BLOCKED = 'BLOCCATA';

    /**
     * Relationship with launch
     */
    public function launch()
    {
        return $this->belongsTo(ScmLaunch::class, 'launch_id');
    }

    /**
     * Relationship with article
     */
    public function article()
    {
        return $this->belongsTo(ScmLaunchArticle::class, 'article_id');
    }

    /**
     * Relationship with phase
     */
    public function phase()
    {
        return $this->belongsTo(ScmLaunchPhase::class, 'phase_id');
    }

    /**
     * Scope for completed tracking
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for in progress tracking
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope for blocked tracking
     */
    public function scopeBlocked($query)
    {
        return $query->where('status', self::STATUS_BLOCKED);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case self::STATUS_NOT_STARTED:
                return 'bg-gray-100 text-gray-800';
            case self::STATUS_IN_PROGRESS:
                return 'bg-blue-100 text-blue-800';
            case self::STATUS_COMPLETED:
                return 'bg-green-100 text-green-800';
            case self::STATUS_BLOCKED:
                return 'bg-red-100 text-red-800';
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
            case self::STATUS_NOT_STARTED:
                return 'Non Iniziata';
            case self::STATUS_IN_PROGRESS:
                return 'In Corso';
            case self::STATUS_COMPLETED:
                return 'Completata';
            case self::STATUS_BLOCKED:
                return 'Bloccata';
            default:
                return 'Sconosciuto';
        }
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentageAttribute()
    {
        if (!$this->article || $this->article->total_pairs == 0) return 0;

        return round(($this->completed_pairs / $this->article->total_pairs) * 100, 2);
    }

    /**
     * Check if tracking is blocked
     */
    public function isBlocked()
    {
        return $this->is_blocked || $this->status === self::STATUS_BLOCKED;
    }

    /**
     * Check if tracking is completed
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}