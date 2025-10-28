<?php

namespace App\Models;

/**
 * Quality Record Model (Record CQ)
 * Gestisce i record principali del controllo qualità
 */
class QualityRecord extends BaseModel
{
    protected $table = 'cq_records';

    protected $fillable = [
        'numero_cartellino',
        'reparto',
        'operatore',
        'tipo_cq',
        'paia_totali',
        'cod_articolo',
        'articolo',
        'linea',
        'note',
        'ha_eccezioni'
    ];

    protected $casts = [
        'data_controllo' => 'datetime',
        'paia_totali' => 'integer',
        'ha_eccezioni' => 'boolean'
    ];

    // Disable updated_at since this table only has data_controllo
    public $timestamps = false;

    protected $dates = ['data_controllo'];

    /**
     * Relationship with quality exceptions
     */
    public function qualityExceptions()
    {
        return $this->hasMany(QualityException::class, 'cartellino_id');
    }

    /**
     * Relationship with quality department
     */
    public function department()
    {
        return $this->belongsTo(QualityDepartment::class, 'reparto', 'nome_reparto');
    }

    /**
     * Scope for filtering by department
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('reparto', $department);
    }

    /**
     * Scope for filtering by operator
     */
    public function scopeByOperator($query, $operator)
    {
        return $query->where('operatore', $operator);
    }

    /**
     * Scope for filtering by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('tipo_cq', $type);
    }

    /**
     * Scope for records with exceptions
     */
    public function scopeWithExceptions($query)
    {
        return $query->where('ha_eccezioni', true);
    }

    /**
     * Scope for records without exceptions
     */
    public function scopeWithoutExceptions($query)
    {
        return $query->where('ha_eccezioni', false);
    }

    /**
     * Scope for recent records
     */
    public function scopeRecent($query, $days = 30)
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P' . $days . 'D'));
        return $query->where('data_controllo', '>=', $date);
    }

    /**
     * Scope for today's records
     */
    public function scopeToday($query)
    {
        $today = new \DateTime();
        return $query->whereDate('data_controllo', $today->format('Y-m-d'));
    }

    /**
     * Scope for this week's records
     */
    public function scopeThisWeek($query)
    {
        $startOfWeek = new \DateTime('monday this week');
        return $query->where('data_controllo', '>=', $startOfWeek);
    }

    /**
     * Scope for this month's records
     */
    public function scopeThisMonth($query)
    {
        $startOfMonth = new \DateTime('first day of this month');
        return $query->where('data_controllo', '>=', $startOfMonth);
    }

    /**
     * Get control type display name
     */
    public function getTypeDisplayAttribute()
    {
        return $this->tipo_cq === 'INTERNO' ? 'Controllo Interno' : 'Controllo Griffe';
    }

    /**
     * Get exceptions count
     */
    public function getExceptionsCountAttribute()
    {
        return $this->qualityExceptions()->count();
    }

    /**
     * Get defect rate percentage
     */
    public function getDefectRateAttribute()
    {
        if ($this->paia_totali == 0) return 0;

        $totalDefects = $this->qualityExceptions()->sum('quantita_difetti');
        return round(($totalDefects / $this->paia_totali) * 100, 2);
    }

    /**
     * Check if record has critical defects
     */
    public function hasCriticalDefects()
    {
        return $this->qualityExceptions()
            ->whereHas('defectType', function($query) {
                $query->where('categoria', 'CRITICO');
            })
            ->exists();
    }

    /**
     * Get formatted control date
     */
    public function getFormattedDateAttribute()
    {
        return $this->data_controllo ? $this->data_controllo->format('d/m/Y H:i') : '';
    }

    /**
     * Static method to get available operators
     */
    public static function getOperators()
    {
        return static::distinct()
            ->whereNotNull('operatore')
            ->orderBy('operatore')
            ->pluck('operatore');
    }

    /**
     * Static method to get available control types
     */
    public static function getControlTypes()
    {
        return [
            'INTERNO' => 'Controllo Interno',
            'GRIFFE' => 'Controllo Griffe'
        ];
    }
}