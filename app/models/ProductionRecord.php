<?php
namespace App\Models;


/**
 * Modello ProductionRecord
 * Gestisce i record di produzione giornaliera
 */
class ProductionRecord extends BaseModel
{
    protected $table = 'production_records';

    protected $fillable = [
        'production_date',
        'manovia1',
        'manovia1_notes',
        'manovia2',
        'manovia2_notes',
        'orlatura1',
        'orlatura1_notes',
        'orlatura2',
        'orlatura2_notes',
        'orlatura3',
        'orlatura3_notes',
        'orlatura4',
        'orlatura4_notes',
        'orlatura5',
        'orlatura5_notes',
        'taglio1',
        'taglio1_notes',
        'taglio2',
        'taglio2_notes',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'production_date',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'production_date' => 'date',
        'manovia1' => 'integer',
        'manovia2' => 'integer',
        'orlatura1' => 'integer',
        'orlatura2' => 'integer',
        'orlatura3' => 'integer',
        'orlatura4' => 'integer',
        'orlatura5' => 'integer',
        'taglio1' => 'integer',
        'taglio2' => 'integer',
        'total_montaggio' => 'integer',
        'total_orlatura' => 'integer',
        'total_taglio' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Relazione con l'utente che ha creato il record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relazione con l'utente che ha aggiornato il record
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope per filtrare per mese e anno
     */
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('production_date', $month)
                    ->whereYear('production_date', $year);
    }

    /**
     * Scope per filtrare per data specifica
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('production_date', $date);
    }

    /**
     * Scope per filtrare per range di date
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('production_date', [$startDate, $endDate]);
    }

    /**
     * Scope per record con dati (non vuoti)
     */
    public function scopeWithData($query)
    {
        return $query->where(function($q) {
            $q->where('manovia1', '>', 0)
              ->orWhere('manovia2', '>', 0)
              ->orWhere('orlatura1', '>', 0)
              ->orWhere('orlatura2', '>', 0)
              ->orWhere('orlatura3', '>', 0)
              ->orWhere('orlatura4', '>', 0)
              ->orWhere('orlatura5', '>', 0)
              ->orWhere('taglio1', '>', 0)
              ->orWhere('taglio2', '>', 0);
        });
    }

    /**
     * Calcola totale montaggio (per compatibilità se non usa generated column)
     */
    public function getTotalMontaggioAttribute()
    {
        return ($this->manovia1 ?? 0) + ($this->manovia2 ?? 0);
    }

    /**
     * Calcola totale orlatura (per compatibilità se non usa generated column)
     */
    public function getTotalOrlaturaAttribute()
    {
        return ($this->orlatura1 ?? 0) + ($this->orlatura2 ?? 0) +
               ($this->orlatura3 ?? 0) + ($this->orlatura4 ?? 0) +
               ($this->orlatura5 ?? 0);
    }

    /**
     * Calcola totale taglio (per compatibilità se non usa generated column)
     */
    public function getTotalTaglioAttribute()
    {
        return ($this->taglio1 ?? 0) + ($this->taglio2 ?? 0);
    }

    /**
     * Calcola totale produzione giornaliera
     */
    public function getTotalProduzioneAttribute()
    {
        return $this->total_montaggio + $this->total_orlatura + $this->total_taglio;
    }

    /**
     * Verifica se il record ha dati di produzione
     */
    public function hasProductionData()
    {
        return $this->total_produzione > 0;
    }

    /**
     * Ottiene nome mese in italiano per la data
     */
    public function getMonthNameAttribute()
    {
        $months = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo',
            4 => 'Aprile', 5 => 'Maggio', 6 => 'Giugno',
            7 => 'Luglio', 8 => 'Agosto', 9 => 'Settembre',
            10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
        ];

        return $months[$this->production_date->month] ?? '';
    }

    /**
     * Ottiene nome giorno della settimana in italiano
     */
    public function getDayNameAttribute()
    {
        $days = [
            1 => 'Lunedì', 2 => 'Martedì', 3 => 'Mercoledì',
            4 => 'Giovedì', 5 => 'Venerdì', 6 => 'Sabato', 7 => 'Domenica'
        ];

        return $days[$this->production_date->dayOfWeek] ?? '';
    }

    /**
     * Trova o crea record per una data specifica
     */
    public static function findOrCreateByDate($date)
    {
        return static::firstOrCreate(
            ['production_date' => $date],
            [
                'manovia1' => 0,
                'manovia2' => 0,
                'orlatura1' => 0,
                'orlatura2' => 0,
                'orlatura3' => 0,
                'orlatura4' => 0,
                'orlatura5' => 0,
                'taglio1' => 0,
                'taglio2' => 0
            ]
        );
    }

    /**
     * Ottiene statistiche per un periodo
     */
    public static function getStatsForPeriod($startDate, $endDate)
    {
        return static::betweenDates($startDate, $endDate)
            ->selectRaw('
                COUNT(*) as total_days,
                SUM(total_montaggio) as total_montaggio,
                SUM(total_orlatura) as total_orlatura,
                SUM(total_taglio) as total_taglio,
                AVG(total_montaggio) as avg_montaggio,
                AVG(total_orlatura) as avg_orlatura,
                AVG(total_taglio) as avg_taglio
            ')
            ->first();
    }
}