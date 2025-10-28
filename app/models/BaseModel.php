<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Base Model per tutti i modelli COREGRE
 * Estende Eloquent Model con funzionalità personalizzate
 */
abstract class BaseModel extends Model
{
    /**
     * Indica se il modello usa i timestamp automatici
     */
    public $timestamps = true;

    /**
     * Format per le date
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * Cast automatici per attributi comuni
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Attributi che devono essere nascosti nelle serializzazioni
     */
    protected $hidden = [];

    /**
     * Attributi che devono essere visibili nelle serializzazioni
     */
    protected $visible = [];

    /**
     * Scope per ottenere record attivi
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope per ottenere record recenti
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Ottiene l'attributo formattato per la data di creazione
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/m/Y H:i') : null;
    }

    /**
     * Ottiene l'attributo formattato per la data di modifica
     */
    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : null;
    }

    /**
     * Metodo helper per validazione semplice
     */
    protected function validateRequired(array $fields)
    {
        foreach ($fields as $field) {
            if (empty($this->attributes[$field])) {
                throw new \InvalidArgumentException("Field {$field} is required");
            }
        }
    }

    /**
     * Metodo helper per sanitizzazione input
     */
    protected function sanitizeString($value)
    {
        return is_string($value) ? trim(strip_tags($value)) : $value;
    }

    /**
     * Override del metodo setAttribute per sanitizzazione automatica
     */
    public function setAttribute($key, $value)
    {
        // Sanitizza automaticamente i campi stringa
        if (is_string($value) && !in_array($key, ['password', 'password_hash', 'remember_token'])) {
            $value = $this->sanitizeString($value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Metodo per ottenere le relazioni caricate
     */
    public function getLoadedRelations()
    {
        return array_keys($this->relations);
    }

    /**
     * Metodo per caricare relazioni in modo sicuro
     */
    public function loadSafe($relations)
    {
        if (is_string($relations)) {
            $relations = [$relations];
        }

        foreach ($relations as $relation) {
            if (method_exists($this, $relation)) {
                $this->load($relation);
            }
        }

        return $this;
    }
}