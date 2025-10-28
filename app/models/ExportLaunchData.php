<?php

namespace App\Models;

/**
 * Export Launch Data Model - Dati lanci DDT
 * Tabella: exp_dati_lanci_ddt
 */
class ExportLaunchData extends BaseModel
{
    protected $table = 'exp_dati_lanci_ddt';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_doc',
        'lancio',
        'articolo',
        'paia',
        'note'
    ];

    // Map Eloquent timestamps to existing columns (only created_at available)
    const CREATED_AT = 'data_creazione';
    const UPDATED_AT = null; // Disable updated_at

    protected $casts = [
        'id_doc' => 'integer',
        'paia' => 'integer',
        'data_creazione' => 'datetime'
    ];

    /**
     * Relationship with document
     */
    public function documento()
    {
        return $this->belongsTo(ExportDocument::class, 'id_doc');
    }

    /**
     * Get display text
     */
    public function getDisplayTextAttribute()
    {
        $parts = array_filter([
            "Lancio: {$this->lancio}",
            $this->articolo ? "Articolo: {$this->articolo}" : null,
            $this->paia ? "Paia: {$this->paia}" : null
        ]);

        return implode(' - ', $parts);
    }

    /**
     * Scope for launches with pairs
     */
    public function scopeWithPairs($query)
    {
        return $query->where('paia', '>', 0);
    }

    /**
     * Scope by launch code
     */
    public function scopeByLaunch($query, $lancio)
    {
        return $query->where('lancio', $lancio);
    }
}