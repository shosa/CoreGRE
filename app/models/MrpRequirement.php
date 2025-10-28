<?php

namespace App\Models;

/**
 * MRP Requirement Model - Fabbisogni
 * Tabella: mrp_requirements
 */
class MrpRequirement extends BaseModel
{
    protected $table = 'mrp_requirements';
    protected $primaryKey = 'id';

    protected $fillable = [
        'material_id',
        'size',
        'quantity_needed',
        'import_date'
    ];

    protected $casts = [
        'quantity_needed' => 'integer',
        'import_date' => 'date',
        'created_at' => 'datetime'
    ];

    /**
     * Relationship with material
     */
    public function material()
    {
        return $this->belongsTo(MrpMaterial::class, 'material_id');
    }

    /**
     * Scope by import date
     */
    public function scopeByImportDate($query, $date)
    {
        return $query->where('import_date', $date);
    }

    /**
     * Scope recent imports
     */
    public function scopeRecentImports($query, $days = 30)
    {
        $date = now()->subDays($days);
        return $query->where('import_date', '>=', $date);
    }

    /**
     * Scope by size
     */
    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }

    /**
     * Get formatted quantity
     */
    public function getFormattedQuantityAttribute()
    {
        return number_format($this->quantity_needed);
    }

    /**
     * Get display text
     */
    public function getDisplayTextAttribute()
    {
        $text = $this->material->display_name . ': ' . $this->formatted_quantity;
        if ($this->size) {
            $text .= " (Taglia: {$this->size})";
        }
        return $text;
    }
}