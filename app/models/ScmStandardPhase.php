<?php

namespace App\Models;

/**
 * SCM Standard Phase Model - Fasi standard
 * Tabella: scm_standard_phases
 */
class ScmStandardPhase extends BaseModel
{
    protected $table = 'scm_standard_phases';
    protected $primaryKey = 'id';

    protected $fillable = [
        'phase_name',
        'description',
        'category',
        'phase_order',
        'is_active'
    ];

    protected $casts = [
        'phase_order' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scope for active phases
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return $this->is_active
            ? 'bg-green-100 text-green-800'
            : 'bg-red-100 text-red-800';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Attiva' : 'Disattiva';
    }

    /**
     * Get all active phases as cycle string
     */
    public static function getActivePhasesAsString()
    {
        $phases = self::active()->ordered()->pluck('phase_name');
        return $phases->implode(';');
    }
}