<?php

namespace App\Models;

/**
 * Export Terzista Model - Fornitori/Terzisti
 * Tabella: exp_terzisti
 */
class ExportTerzista extends BaseModel
{
    protected $table = 'exp_terzisti';
    protected $primaryKey = 'id';

    protected $fillable = [
        'ragione_sociale',
        'indirizzo_1',
        'indirizzo_2',
        'indirizzo_3',
        'nazione',
        'consegna',
        'autorizzazione'
    ];

    // Map Eloquent timestamps to existing columns
    const CREATED_AT = 'data_creazione';
    const UPDATED_AT = 'data_modifica';

    protected $casts = [
        'data_creazione' => 'datetime',
        'data_modifica' => 'datetime'
    ];

    /**
     * Relationship with documents
     */
    public function documenti()
    {
        return $this->hasMany(ExportDocument::class, 'id_terzista');
    }

    /**
     * Relationship with open documents
     */
    public function documentiAperti()
    {
        return $this->hasMany(ExportDocument::class, 'id_terzista')->open();
    }

    /**
     * Get full address
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->indirizzo_1,
            $this->indirizzo_2,
            $this->indirizzo_3,
            $this->nazione
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get formatted company info
     */
    public function getDisplayNameAttribute()
    {
        return $this->ragione_sociale;
    }

    /**
     * Scope for terzisti with active documents
     */
    public function scopeWithActiveDocuments($query)
    {
        return $query->whereHas('documenti', function($q) {
            $q->open();
        });
    }

    /**
     * Get documents count
     */
    public function getDocumentsCountAttribute()
    {
        return $this->documenti()->count();
    }

    /**
     * Get open documents count
     */
    public function getOpenDocumentsCountAttribute()
    {
        return $this->documentiAperti()->count();
    }
}