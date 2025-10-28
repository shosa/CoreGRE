<?php

namespace App\Models;

/**
 * Export Article Model - Articoli nei documenti
 * Tabella: exp_dati_articoli
 */
class ExportArticle extends BaseModel
{
    protected $table = 'exp_dati_articoli';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_documento',
        'codice_articolo',
        'descrizione',
        'voce_doganale',
        'um',
        'qta_originale',
        'qta_reale',
        'prezzo_unitario',
        'is_mancante',
        'rif_mancante'
    ];

    // Map Eloquent timestamps to existing columns
    const CREATED_AT = 'data_creazione';
    const UPDATED_AT = 'data_modifica';

    protected $casts = [
        'id_documento' => 'integer',
        'qta_originale' => 'decimal:2',
        'qta_reale' => 'decimal:2',
        'prezzo_unitario' => 'decimal:3',
        'is_mancante' => 'boolean',
        'data_creazione' => 'datetime',
        'data_modifica' => 'datetime'
    ];

    /**
     * Relationship with document
     */
    public function documento()
    {
        return $this->belongsTo(ExportDocument::class, 'id_documento');
    }

    /**
     * Scope for missing articles
     */
    public function scopeMissing($query)
    {
        return $query->where('is_mancante', true);
    }

    /**
     * Scope for available articles
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_mancante', false);
    }

    /**
     * Calculate total value
     */
    public function getTotalValueAttribute()
    {
        return $this->qta_reale * $this->prezzo_unitario;
    }

    /**
     * Get quantity difference
     */
    public function getQuantityDifferenceAttribute()
    {
        return $this->qta_reale - $this->qta_originale;
    }

    /**
     * Check if quantity matches original
     */
    public function isQuantityMatching()
    {
        return $this->qta_reale == $this->qta_originale;
    }

    /**
     * Get formatted unit of measure
     */
    public function getFormattedUmAttribute()
    {
        return $this->um ?: 'pz';
    }

    /**
     * Get display code
     */
    public function getDisplayCodeAttribute()
    {
        return $this->codice_articolo ?: 'N/A';
    }
}