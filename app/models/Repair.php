<?php

namespace App\Models;

/**
 * Repair Model (Riparazioni)
 * Gestisce le riparazioni esterne
 */
class Repair extends BaseModel
{
    protected $table = 'rip_riparazioni';
    protected $primaryKey = 'IDRIP';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'IDRIP',
        'ARTICOLO',
        'CODICE',
        'P01', 'P02', 'P03', 'P04', 'P05', 'P06', 'P07', 'P08', 'P09', 'P10',
        'P11', 'P12', 'P13', 'P14', 'P15', 'P16', 'P17', 'P18', 'P19', 'P20',
        'QTA',
        'CARTELLINO',
        'REPARTO',
        'CAUSALE',
        'LABORATORIO',
        'URGENZA',
        'DATA',
        'NU',
        'UTENTE',
        'CLIENTE',
        'COMMESSA',
        'LINEA',
        'COMPLETA'
    ];

    protected $casts = [
        'P01' => 'integer', 'P02' => 'integer', 'P03' => 'integer', 'P04' => 'integer', 'P05' => 'integer',
        'P06' => 'integer', 'P07' => 'integer', 'P08' => 'integer', 'P09' => 'integer', 'P10' => 'integer',
        'P11' => 'integer', 'P12' => 'integer', 'P13' => 'integer', 'P14' => 'integer', 'P15' => 'integer',
        'P16' => 'integer', 'P17' => 'integer', 'P18' => 'integer', 'P19' => 'integer', 'P20' => 'integer',
        'COMPLETA' => 'boolean', 'created_at' => 'datetime', 'updated_at' => 'datetime'
    ];

    // Disable timestamps
    public $timestamps = false;

    /**
     * Relationship with user who created the repair
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'UTENTE', 'user_name');
    }

    /**
     * Scope for completed repairs
     */
    public function scopeCompleted($query)
    {
        return $query->where('COMPLETA', true);
    }

    /**
     * Scope for pending repairs
     */
    public function scopePending($query)
    {
        return $query->where('COMPLETA', false)->orWhereNull('COMPLETA');
    }

    /**
     * Scope for filtering by urgency
     */
    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('URGENZA', $urgency);
    }

    /**
     * Scope for filtering by department
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('REPARTO', $department);
    }

    /**
     * Scope for filtering by laboratory
     */
    public function scopeByLaboratory($query, $laboratory)
    {
        return $query->where('LABORATORIO', $laboratory);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('DATA', [$from, $to]);
    }

    /**
     * Scope for recent repairs
     */
    public function scopeRecent($query, $days = 30)
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P' . $days . 'D'));
        return $query->where('DATA', '>=', $date->format('Y-m-d'));
    }

    /**
     * Get total quantity from all sizes
     */
    public function getTotalQuantityAttribute()
    {
        $total = 0;
        for ($i = 1; $i <= 20; $i++) {
            $size = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $total += $this->$size ?? 0;
        }
        return $total;
    }

    /**
     * Get sizes with quantities
     */
    public function getSizesWithQuantitiesAttribute()
    {
        $sizes = [];
        for ($i = 1; $i <= 20; $i++) {
            $size = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
            if (($this->$size ?? 0) > 0) {
                $sizes[$size] = $this->$size;
            }
        }
        return $sizes;
    }

    /**
     * Get urgency display name
     */
    public function getUrgencyDisplayAttribute()
    {
        $urgencies = [
            'BASSA' => 'Bassa',
            'MEDIA' => 'Media',
            'ALTA' => 'Alta',
            'CRITICA' => 'Critica'
        ];

        return $urgencies[$this->URGENZA] ?? ucfirst(strtolower($this->URGENZA ?? 'Bassa'));
    }

    /**
     * Get status display
     */
    public function getStatusDisplayAttribute()
    {
        return $this->COMPLETA ? 'Completata' : 'In Lavorazione';
    }

    /**
     * Get urgency color for UI
     */
    public function getUrgencyColorAttribute()
    {
        $colors = [
            'BASSA' => 'green',
            'MEDIA' => 'yellow',
            'ALTA' => 'orange',
            'CRITICA' => 'red'
        ];

        return $colors[$this->URGENZA] ?? 'gray';
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        if (!$this->DATA) return '';

        try {
            // Handle different date formats
            if (strlen($this->DATA) === 10) {
                return date('d/m/Y', strtotime($this->DATA));
            }
            return $this->DATA;
        } catch (Exception $e) {
            return $this->DATA;
        }
    }

    /**
     * Check if repair is overdue (more than 30 days old and not completed)
     */
    public function isOverdue()
    {
        if ($this->COMPLETA) return false;
        if (!$this->DATA) return false;

        $createdAt = strtotime($this->DATA);
        $thirtyDaysAgo = strtotime('-30 days');

        return $createdAt < $thirtyDaysAgo;
    }

    /**
     * Get available urgency levels
     */
    public static function getUrgencyLevels()
    {
        return [
            'BASSA' => 'Bassa',
            'MEDIA' => 'Media',
            'ALTA' => 'Alta',
            'CRITICA' => 'Critica'
        ];
    }

    /**
     * Get available departments
     */
    public static function getDepartments()
    {
        return static::distinct()
            ->whereNotNull('REPARTO')
            ->orderBy('REPARTO')
            ->pluck('REPARTO');
    }

    /**
     * Get available laboratories
     */
    public static function getLaboratories()
    {
        return static::distinct()
            ->whereNotNull('LABORATORIO')
            ->orderBy('LABORATORIO')
            ->pluck('LABORATORIO');
    }
}