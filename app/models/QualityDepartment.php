<?php

namespace App\Models;

/**
 * Quality Department Model (Reparti CQ)
 * Gestisce i reparti del controllo qualità
 */
class QualityDepartment extends BaseModel
{
    protected $table = 'cq_departments';

    protected $fillable = [
        'nome_reparto',
        'attivo',
        'ordine'
    ];

    protected $casts = [
        'attivo' => 'boolean',
        'ordine' => 'integer',
        'data_creazione' => 'datetime'
    ];

    // Disable updated_at since this table only has data_creazione
    public $timestamps = false;

    protected $dates = ['data_creazione'];

    /**
     * Relationship with quality records
     */
    public function qualityRecords()
    {
        return $this->hasMany(QualityRecord::class, 'reparto', 'nome_reparto');
    }

    /**
     * Scope for active departments
     */
    public function scopeActive($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope for ordered departments
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordine')->orderBy('nome_reparto');
    }

    /**
     * Get department display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->nome_reparto;
    }

    /**
     * Get active status display
     */
    public function getStatusDisplayAttribute()
    {
        return $this->attivo ? 'Attivo' : 'Non Attivo';
    }

    /**
     * Static method to get active departments for select options
     */
    public static function getActiveOptions()
    {
        return static::active()
            ->ordered()
            ->pluck('nome_reparto', 'id');
    }
}