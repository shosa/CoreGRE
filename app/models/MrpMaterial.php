<?php

namespace App\Models;

/**
 * MRP Material Model - Materiali
 * Tabella: mrp_materials
 */
class MrpMaterial extends BaseModel
{
    protected $table = 'mrp_materials';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'material_code',
        'description',
        'supplier_code',
        'supplier_name',
        'unit_measure',
        'category',
        'has_sizes'
    ];

    protected $casts = [
        'has_sizes' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with requirements
     */
    public function requirements()
    {
        return $this->hasMany(MrpRequirement::class, 'material_id');
    }

    /**
     * Relationship with orders
     */
    public function orders()
    {
        return $this->hasMany(MrpOrder::class, 'material_id');
    }

    /**
     * Relationship with arrivals
     */
    public function arrivals()
    {
        return $this->hasMany(MrpArrival::class, 'material_id');
    }

    /**
     * Relationship with category
     */
    public function category_relation()
    {
        return $this->belongsTo(MrpCategory::class, 'category', 'code');
    }

    /**
     * Scope for user materials
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for materials with sizes
     */
    public function scopeWithSizes($query)
    {
        return $query->where('has_sizes', true);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get total requirement
     */
    public function getTotalRequirementAttribute()
    {
        return $this->requirements()->sum('quantity_needed');
    }

    /**
     * Get total ordered
     */
    public function getTotalOrderedAttribute()
    {
        return $this->orders()->sum('quantity_ordered');
    }

    /**
     * Get total received
     */
    public function getTotalReceivedAttribute()
    {
        return $this->arrivals()->sum('quantity_received');
    }

    /**
     * Get quantity to order
     */
    public function getToOrderAttribute()
    {
        return $this->total_requirement - $this->total_ordered;
    }

    /**
     * Get quantity to receive
     */
    public function getToReceiveAttribute()
    {
        return $this->total_ordered - $this->total_received;
    }

    /**
     * Get missing quantity
     */
    public function getMissingAttribute()
    {
        return $this->total_requirement - $this->total_received;
    }

    /**
     * Check if material is critical
     */
    public function isCritical()
    {
        return $this->missing > 0;
    }

    /**
     * Get display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->material_code . ($this->description ? ' - ' . $this->description : '');
    }
}