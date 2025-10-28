<?php

namespace App\Models;

/**
 * AvailableWidgets Model
 * Table: available_widgets
 * Gestisce i widget disponibili nel sistema
 */
class AvailableWidget extends BaseModel
{
    protected $table = 'widg_available';
    protected $primaryKey = 'widget_key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'widget_key',
        'widget_name',
        'widget_description',
        'widget_icon',
        'widget_color',
        'required_permission',
        'is_active',
        'default_size',
        'default_enabled',
        'category',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_enabled' => 'boolean',
    ];

    /**
     * Relazione con le preferenze utente
     */
    public function userWidgets()
    {
        return $this->hasMany(UserWidget::class, 'widget_key', 'widget_key');
    }


    /**
     * Scope per widget attivi
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope per widget con permesso specifico
     */
    public function scopeWithPermission($query, $permission)
    {
        return $query->where('required_permission', $permission);
    }

    /**
     * Scope per widget abilitati di default
     */
    public function scopeDefaultEnabled($query)
    {
        return $query->where('default_enabled', true);
    }

    /**
     * Scope per categoria
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
