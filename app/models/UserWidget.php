<?php

namespace App\Models;

/**
 * UserWidgets Model
 * Table: user_widgets
 * Gestisce le preferenze widget degli utenti
 */
class UserWidget extends BaseModel
{
    protected $table = 'widg_usermap';

    protected $fillable = [
        'user_id',
        'widget_key',
        'is_enabled',
        'position_order',
        'widget_size',
        'custom_settings',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'is_enabled' => 'boolean',
        'position_order' => 'integer',
        'custom_settings' => 'json',
    ];

    /**
     * Relazione con l'utente
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relazione con il widget disponibile
     */
    public function availableWidget()
    {
        return $this->belongsTo(AvailableWidget::class, 'widget_key', 'widget_key');
    }

    /**
     * Scope per widget abilitati
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope per utente specifico
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
