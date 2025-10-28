<?php

namespace App\Models;

/**
 * Dati Model
 * Table: dati
 *
 * Auto-generated from database table
 */
class CoreData extends BaseModel
{
    public $timestamps = false;
    protected $table = 'core_dati';
    protected $primaryKey = 'id';
    const UPDATED_AT = null;
    const CREATED_AT = null;


    protected $fillable = [
        'St',
        'Ordine',
        'Rg',
        'CCli',
        'Ragione Sociale',
        'Cartel',
        'Commessa Cli',
        'PO',
        'Articolo',
        'Descrizione Articolo',
        'Nu',
        'Marca Etich',
        'Ln',
        'P01',
        'P02',
        'P03',
        'P04',
        'P05',
        'P06',
        'P07',
        'P08',
        'P09',
        'P10',
        'P11',
        'P12',
        'P13',
        'P14',
        'P15',
        'P16',
        'P17',
        'P18',
        'P19',
        'P20',
        'Tot',
    ];

    protected $casts = [
        'Ordine' => 'integer',
        'Rg' => 'integer',
        'CCli' => 'integer',
        'Cartel' => 'integer',
        'P01' => 'integer',
        'P02' => 'integer',
        'P03' => 'integer',
        'P04' => 'integer',
        'P05' => 'integer',
        'P06' => 'integer',
        'P07' => 'integer',
        'P08' => 'integer',
        'P09' => 'integer',
        'P10' => 'integer',
        'P11' => 'integer',
        'P12' => 'integer',
        'P13' => 'integer',
        'P14' => 'integer',
        'P15' => 'integer',
        'P16' => 'integer',
        'P17' => 'integer',
        'P18' => 'integer',
        'P19' => 'integer',
        'P20' => 'integer',
        'Tot' => 'integer',
        'created_at' => 'datetime'
    ];

    /**
     * Relationship with track links
     */
    public function trackLinks()
    {
        return $this->hasMany(TrackLink::class, 'cartel', 'Cartel');
    }

    /**
     * Relationship with order info
     */
    public function orderInfo()
    {
        return $this->belongsTo(TrackOrderInfo::class, 'Ordine', 'ordine');
    }

    /**
     * Relationship with SKU info
     */
    public function skuInfo()
    {
        return $this->belongsTo(TrackSku::class, 'Articolo', 'art');
    }

    /**
     * Scope for specific order
     */
    public function scopeForOrder($query, $ordine)
    {
        return $query->where('Ordine', $ordine);
    }

    /**
     * Scope for specific article
     */
    public function scopeForArticle($query, $articolo)
    {
        return $query->where('Articolo', $articolo);
    }

    /**
     * Scope for specific cartellino
     */
    public function scopeForCartel($query, $cartel)
    {
        return $query->where('Cartel', $cartel);
    }
}
