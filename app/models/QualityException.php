<?php

namespace App\Models;

/**
 * Quality Exception Model (Eccezioni CQ)
 * Gestisce le eccezioni/difetti del controllo qualità
 */
class QualityException extends BaseModel
{
    protected $table = 'cq_exceptions';

    protected $fillable = [
        'cartellino_id',
        'taglia',
        'tipo_difetto',
        'note_operatore',
        'fotoPath'
    ];

    protected $casts = [
        'cartellino_id' => 'integer',
        'data_creazione' => 'datetime'
    ];

    // Disable updated_at since this table only has data_creazione
    public $timestamps = false;

    protected $dates = ['data_creazione'];

    /**
     * Relationship with quality record
     */
    public function qualityRecord()
    {
        return $this->belongsTo(QualityRecord::class, 'cartellino_id');
    }

    /**
     * Relationship with defect type
     */
    public function defectType()
    {
        return $this->belongsTo(QualityDefectType::class, 'tipo_difetto', 'descrizione');
    }

    /**
     * Scope for filtering by defect type
     */
    public function scopeByDefectType($query, $defectType)
    {
        return $query->where('tipo_difetto', $defectType);
    }

    /**
     * Scope for filtering by size
     */
    public function scopeBySize($query, $size)
    {
        return $query->where('taglia', $size);
    }

    /**
     * Scope for exceptions with photos
     */
    public function scopeWithPhotos($query)
    {
        return $query->whereNotNull('fotoPath');
    }

    /**
     * Scope for exceptions without photos
     */
    public function scopeWithoutPhotos($query)
    {
        return $query->whereNull('fotoPath');
    }

    /**
     * Scope for recent exceptions
     */
    public function scopeRecent($query, $days = 30)
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P' . $days . 'D'));
        return $query->where('data_creazione', '>=', $date);
    }

    /**
     * Scope for today's exceptions
     */
    public function scopeToday($query)
    {
        $today = new \DateTime();
        return $query->whereDate('data_creazione', $today->format('Y-m-d'));
    }

    /**
     * Scope for this month's exceptions
     */
    public function scopeThisMonth($query)
    {
        $startOfMonth = new \DateTime('first day of this month');
        return $query->where('data_creazione', '>=', $startOfMonth);
    }

    /**
     * Check if exception has photo
     */
    public function hasPhoto()
    {
        return !empty($this->fotoPath);
    }

    /**
     * Get photo URL if exists
     */
    public function getPhotoUrlAttribute()
    {
        if (!$this->hasPhoto()) {
            return null;
        }

        // Assuming photos are stored in public/uploads/cq/ directory
        return '/uploads/cq/' . basename($this->fotoPath);
    }

    /**
     * Get defect category from related defect type
     */
    public function getDefectCategoryAttribute()
    {
        return $this->defectType ? $this->defectType->categoria : null;
    }

    /**
     * Check if this is a critical defect
     */
    public function isCritical()
    {
        return $this->defect_category === 'CRITICO';
    }

    /**
     * Get formatted creation date
     */
    public function getFormattedDateAttribute()
    {
        return $this->data_creazione ? $this->data_creazione->format('d/m/Y H:i') : '';
    }

    /**
     * Get time ago for creation date
     */
    public function getTimeAgoAttribute()
    {
        if (!$this->data_creazione) return '';

        $time = time() - strtotime($this->data_creazione);

        if ($time < 60) {
            return $time == 1 ? '1 secondo fa' : $time . ' secondi fa';
        }

        $time = round($time / 60);
        if ($time < 60) {
            return $time == 1 ? '1 minuto fa' : $time . ' minuti fa';
        }

        $time = round($time / 60);
        if ($time < 24) {
            return $time == 1 ? '1 ora fa' : $time . ' ore fa';
        }

        $time = round($time / 24);
        if ($time < 30) {
            return $time == 1 ? '1 giorno fa' : $time . ' giorni fa';
        }

        $time = round($time / 30);
        if ($time < 12) {
            return $time == 1 ? '1 mese fa' : $time . ' mesi fa';
        }

        $time = round($time / 12);
        return $time == 1 ? '1 anno fa' : $time . ' anni fa';
    }

    /**
     * Static method to get available sizes
     */
    public static function getAvailableSizes()
    {
        return static::distinct()
            ->whereNotNull('taglia')
            ->orderBy('taglia')
            ->pluck('taglia');
    }

    /**
     * Static method to create exception with automatic record update
     */
    public static function createWithRecordUpdate($data)
    {
        $exception = static::create($data);

        // Update the quality record to mark it has exceptions
        if ($exception && $exception->cartellino_id) {
            QualityRecord::where('id', $exception->cartellino_id)
                ->update(['ha_eccezioni' => true]);
        }

        return $exception;
    }
}