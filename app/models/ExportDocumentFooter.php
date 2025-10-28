<?php

namespace App\Models;

/**
 * Export Document Footer Model - Piede documenti
 * Tabella: exp_piede_documenti
 */
class ExportDocumentFooter extends BaseModel
{
    protected $table = 'exp_piede_documenti';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_documento',
        'aspetto_colli',
        'n_colli',
        'tot_peso_lordo',
        'tot_peso_netto',
        'trasportatore',
        'consegnato_per',
        'voce_1',
        'peso_1',
        'voce_2',
        'peso_2',
        'voce_3',
        'peso_3',
        'voce_4',
        'peso_4',
        'voce_5',
        'peso_5',
        'voce_6',
        'peso_6',
        'voce_7',
        'peso_7',
        'voce_8',
        'peso_8',
        'voce_9',
        'peso_9',
        'voce_10',
        'peso_10',
        'voce_11',
        'peso_11',
        'voce_12',
        'peso_12',
        'voce_13',
        'peso_13',
        'voce_14',
        'peso_14',
        'voce_15',
        'peso_15'
    ];

    // Map Eloquent timestamps to existing columns
    const CREATED_AT = 'data_creazione';
    const UPDATED_AT = 'data_modifica';

    protected $casts = [
        'id_documento' => 'integer',
        'n_colli' => 'integer',
        'tot_peso_lordo' => 'decimal:2',
        'tot_peso_netto' => 'decimal:2',
        'peso_1' => 'decimal:2',
        'peso_2' => 'decimal:2',
        'peso_3' => 'decimal:2',
        'peso_4' => 'decimal:2',
        'peso_5' => 'decimal:2',
        'peso_6' => 'decimal:2',
        'peso_7' => 'decimal:2',
        'peso_8' => 'decimal:2',
        'peso_9' => 'decimal:2',
        'peso_10' => 'decimal:2',
        'peso_11' => 'decimal:2',
        'peso_12' => 'decimal:2',
        'peso_13' => 'decimal:2',
        'peso_14' => 'decimal:2',
        'peso_15' => 'decimal:2',
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
     * Get formatted weight display
     */
    public function getFormattedWeightAttribute()
    {
        if ($this->tot_peso_lordo && $this->tot_peso_netto) {
            return "Lordo: {$this->tot_peso_lordo} kg, Netto: {$this->tot_peso_netto} kg";
        } elseif ($this->tot_peso_lordo) {
            return "Lordo: {$this->tot_peso_lordo} kg";
        } elseif ($this->tot_peso_netto) {
            return "Netto: {$this->tot_peso_netto} kg";
        }
        return 'Peso non specificato';
    }

    /**
     * Get colli display
     */
    public function getColliDisplayAttribute()
    {
        if ($this->n_colli) {
            $aspetto = $this->aspetto_colli ? " ({$this->aspetto_colli})" : '';
            return "{$this->n_colli} colli{$aspetto}";
        }
        return 'Non specificato';
    }

    /**
     * Check if has transport info
     */
    public function hasTransportInfo()
    {
        return !empty($this->trasportatore) || !empty($this->consegnato_per);
    }

    /**
     * Get transport display
     */
    public function getTransportDisplayAttribute()
    {
        $parts = array_filter([
            $this->trasportatore ? "Trasportatore: {$this->trasportatore}" : null,
            $this->consegnato_per ? "Consegnato per: {$this->consegnato_per}" : null
        ]);

        return implode(' - ', $parts) ?: 'Non specificato';
    }
}