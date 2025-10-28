<?php

namespace App\Models;

/**
 * SCM Launch Phase Model - Fasi dei lanci
 * Tabella: scm_launch_phases
 */
class ScmLaunchPhase extends BaseModel
{
    protected $table = 'scm_launch_phases';
    protected $primaryKey = 'id';

    protected $fillable = [
        'launch_id',
        'phase_name',
        'phase_order'
    ];

    protected $casts = [
        'launch_id' => 'integer',
        'phase_order' => 'integer',
        'created_at' => 'datetime'
    ];

    /**
     * Relationship with launch
     */
    public function launch()
    {
        return $this->belongsTo(ScmLaunch::class, 'launch_id');
    }

    /**
     * Relationship with progress tracking
     */
    public function progressTracking()
    {
        return $this->hasMany(ScmProgressTracking::class, 'phase_id');
    }

    /**
     * Scope ordered by phase order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('phase_order');
    }

    /**
     * Get display name with order
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->phase_order}. {$this->phase_name}";
    }

    /**
     * Get next phase
     */
    public function getNextPhase()
    {
        return self::where('launch_id', $this->launch_id)
            ->where('phase_order', '>', $this->phase_order)
            ->orderBy('phase_order')
            ->first();
    }

    /**
     * Get previous phase
     */
    public function getPreviousPhase()
    {
        return self::where('launch_id', $this->launch_id)
            ->where('phase_order', '<', $this->phase_order)
            ->orderByDesc('phase_order')
            ->first();
    }

    /**
     * Check if this is the first phase
     */
    public function isFirst()
    {
        return $this->phase_order === 1;
    }

    /**
     * Check if this is the last phase
     */
    public function isLast()
    {
        $maxOrder = self::where('launch_id', $this->launch_id)->max('phase_order');
        return $this->phase_order === $maxOrder;
    }
}