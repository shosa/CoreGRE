<?php

namespace App\Models;

/**
 * MRP Category Model - Categorie materiali
 * Tabella: mrp_categories
 */
class MrpCategory extends BaseModel
{
    protected $table = 'mrp_categories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'code',
        'name',
        'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship with materials
     */
    public function materials()
    {
        return $this->hasMany(MrpMaterial::class, 'category', 'code');
    }

    /**
     * Scope by code
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Get display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->code . ' - ' . $this->name;
    }

    /**
     * Get materials count
     */
    public function getMaterialsCountAttribute()
    {
        return $this->materials()->count();
    }
}