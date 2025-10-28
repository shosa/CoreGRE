<?php

namespace App\Models;

/**
 * MRP Arrival Model - Arrivi
 * Tabella: mrp_arrivals
 */
class MrpArrival extends BaseModel
{
    protected $table = 'mrp_arrivals';
    protected $primaryKey = 'id';

    protected $fillable = [
        'material_id',
        'size',
        'document_number',
        'arrival_date',
        'quantity_received',
        'notes'
    ];


    protected $casts = [
        'material_id' => 'integer',
        'quantity_received' => 'integer',
        'arrival_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'


    ];

    /**
     * Relationship with material
     */
    public function material()
    {
        return $this->belongsTo(MrpMaterial::class, 'material_id');
    }

    /**
     * Scope by arrival date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('arrival_date', [$from, $to]);
    }

    /**
     * Scope recent arrivals
     */
    public function scopeRecent($query, $days = 30)
    {
        $date = now()->subDays($days);
        return $query->where('arrival_date', '>=', $date);
    }

    /**
     * Scope by document number
     */
    public function scopeByDocumentNumber($query, $documentNumber)
    {
        return $query->where('document_number', 'like', "%{$documentNumber}%");
    }

    /**
     * Scope by size
     */
    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->arrival_date ? $this->arrival_date->format('d/m/Y') : '';
    }

    /**
     * Get formatted quantity
     */
    public function getFormattedQuantityAttribute()
    {
        return number_format($this->quantity_received);
    }

    /**
     * Get display text
     */
    public function getDisplayTextAttribute()
    {
        $text = "{$this->document_number}: {$this->formatted_quantity}";
        if ($this->size) {
            $text .= " (Taglia: {$this->size})";
        }
        return $text;
    }

    /**
     * Check if arrival is recent
     */
    public function isRecent($days = 7)
    {
        return $this->arrival_date && $this->arrival_date->isAfter(now()->subDays($days));
    }

    /**
     * Check if has notes
     */
    public function hasNotes()
    {
        return !empty($this->notes);
    }
}