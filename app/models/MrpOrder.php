<?php

namespace App\Models;

/**
 * MRP Order Model - Ordini
 * Tabella: mrp_orders
 */
class MrpOrder extends BaseModel
{
    protected $table = 'mrp_orders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'material_id',
        'size',
        'order_number',
        'order_date',
        'quantity_ordered',
        'notes'
    ];

    protected $casts = [
        'material_id' => 'integer',
        'quantity_ordered' => 'integer',
        'order_date' => 'date',
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
     * Scope by order date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('order_date', [$from, $to]);
    }

    /**
     * Scope recent orders
     */
    public function scopeRecent($query, $days = 30)
    {
        $date = now()->subDays($days);
        return $query->where('order_date', '>=', $date);
    }

    /**
     * Scope by order number
     */
    public function scopeByOrderNumber($query, $orderNumber)
    {
        return $query->where('order_number', 'like', "%{$orderNumber}%");
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
        return $this->order_date ? $this->order_date->format('d/m/Y') : '';
    }

    /**
     * Get formatted quantity
     */
    public function getFormattedQuantityAttribute()
    {
        return number_format($this->quantity_ordered);
    }

    /**
     * Get display text
     */
    public function getDisplayTextAttribute()
    {
        $text = "{$this->order_number}: {$this->formatted_quantity}";
        if ($this->size) {
            $text .= " (Taglia: {$this->size})";
        }
        return $text;
    }

    /**
     * Check if order is recent
     */
    public function isRecent($days = 7)
    {
        return $this->order_date && $this->order_date->isAfter(now()->subDays($days));
    }
}