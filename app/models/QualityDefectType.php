<?php

namespace App\Models;

/**
 * Quality Defect Type Model (Tipi Difetti CQ)
 * Gestisce i tipi di difetti del controllo qualità
 */
class QualityDefectType extends BaseModel
{
    protected $table = 'cq_deftypes';

    protected $fillable = [
        'descrizione',
        'categoria',
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
     * Relationship with quality exceptions
     */
    public function qualityExceptions()
    {
        return $this->hasMany(QualityException::class, 'tipo_difetto', 'descrizione');
    }

    /**
     * Scope for active defect types
     */
    public function scopeActive($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope for ordered defect types
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('categoria')->orderBy('ordine')->orderBy('descrizione');
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('categoria', $category);
    }

    /**
     * Get defect type display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->categoria ? "{$this->categoria} - {$this->descrizione}" : $this->descrizione;
    }

    /**
     * Get active status display
     */
    public function getStatusDisplayAttribute()
    {
        return $this->attivo ? 'Attivo' : 'Non Attivo';
    }

    /**
     * Get available categories
     */
    public static function getCategories()
    {
        return static::whereNotNull('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');
    }

    /**
     * Static method to get active defect types for select options
     */
    public static function getActiveOptions()
    {
        return static::active()
            ->ordered()
            ->pluck('descrizione', 'id');
    }

    /**
     * Static method to get active defect types grouped by category
     */
    public static function getActiveGroupedOptions()
    {
        return static::active()
            ->ordered()
            ->get()
            ->groupBy('categoria')
            ->map(function($items) {
                return $items->pluck('descrizione', 'id');
            });
    }
}