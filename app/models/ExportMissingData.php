<?php

namespace App\Models;

/**
 * Export Missing Data Model - Dati mancanti
 * Tabella: exp_dati_mancanti
 */
class ExportMissingData extends BaseModel
{
    protected $table = 'exp_dati_mancanti';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_documento',
        'codice_articolo',
        'qta_mancante',
        'descrizione'
    ];

    // Map Eloquent timestamps to existing columns (only created_at available)
    const CREATED_AT = 'data_creazione';
    const UPDATED_AT = null; // Disable updated_at

    protected $casts = [
        'qta_mancante' => 'decimal:2',
        'data_creazione' => 'datetime'
    ];

    /**
     * Relationship with document
     */
    public function documento()
    {
        return $this->belongsTo(ExportDocument::class, 'id_documento');
    }

    /**
     * Get formatted quantity
     */
    public function getFormattedQuantityAttribute()
    {
        return number_format($this->qta_mancante, 2);
    }

    /**
     * Get display text
     */
    public function getDisplayTextAttribute()
    {
        $desc = $this->descrizione ? " - {$this->descrizione}" : '';
        return "{$this->codice_articolo}: {$this->formatted_quantity}{$desc}";
    }
}