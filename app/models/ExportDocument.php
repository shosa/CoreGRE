<?php

namespace App\Models;

/**
 * Export Document Model - Documenti di trasporto
 * Tabella: exp_documenti
 */
class ExportDocument extends BaseModel
{
    protected $table = 'exp_documenti';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_terzista',
        'data',
        'first_boot',
        'stato',
        'autorizzazione',
        'commento'
    ];

    // Map Eloquent timestamps to existing columns
    const CREATED_AT = 'data_creazione';
    const UPDATED_AT = 'data_modifica';

    protected $casts = [
        'id_terzista'=>'integer',
        'data' => 'date',
        'first_boot' => 'boolean',
        'data_creazione' => 'datetime',
        'data_modifica' => 'datetime'
    ];

    /**
     * Relationship with terzista (supplier)
     */
    public function terzista()
    {
        return $this->belongsTo(ExportTerzista::class, 'id_terzista');
    }

    /**
     * Relationship with document articles
     */
    public function articoli()
    {
        return $this->hasMany(ExportArticle::class, 'id_documento');
    }

    /**
     * Relationship with document footer
     */
    public function piede()
    {
        return $this->hasOne(ExportDocumentFooter::class, 'id_documento');
    }

    /**
     * Relationship with missing data
     */
    public function datiMancanti()
    {
        return $this->hasMany(ExportMissingData::class, 'id_documento');
    }

    /**
     * Alias for missing data relationship
     */
    public function mancanti()
    {
        return $this->hasMany(ExportMissingData::class, 'id_documento');
    }

    /**
     * Relationship with launch data
     */
    public function lanciDdt()
    {
        return $this->hasMany(ExportLaunchData::class, 'id_doc');
    }

    /**
     * Scope for open documents
     */
    public function scopeOpen($query)
    {
        return $query->where('stato', 'Aperto');
    }

    /**
     * Scope for closed documents
     */
    public function scopeClosed($query)
    {
        return $query->where('stato', 'Chiuso');
    }

    /**
     * Scope for documents by date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('data', [$from, $to]);
    }

    /**
     * Scope for recent documents
     */
    public function scopeRecent($query, $days = 30)
    {
        $date = date('Y-m-d', strtotime("-{$days} days"));
        return $query->where('data', '>=', $date);
    }

    /**
     * Check if document is open
     */
    public function isOpen()
    {
        return $this->stato === 'Aperto';
    }

    /**
     * Check if document is closed
     */
    public function isClosed()
    {
        return $this->stato === 'Chiuso';
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->data ? $this->data->format('d/m/Y') : '';
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeAttribute()
    {
        return $this->stato === 'Aperto'
            ? 'bg-green-100 text-green-800'
            : 'bg-gray-100 text-gray-800';
    }
}