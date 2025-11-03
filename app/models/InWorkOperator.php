<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * InWorkOperator Model
 *
 * Rappresenta un operatore mobile per l'app CoreInWork
 * Separato dal sistema auth_users (utenti web admin)
 */
class InWorkOperator extends Model
{
    protected $table = 'inwork_operators';

    protected $fillable = [
        'user',
        'full_name',
        'pin',
        'reparto',
        'active',
        'email',
        'phone',
        'notes'
    ];

    protected $hidden = [
        'pin' // Nascondi PIN nelle risposte JSON
    ];

    protected $casts = [
        'pin' => 'integer',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relazione con i permessi moduli
     */
    public function modulePermissions()
    {
        return $this->hasMany(InWorkModulePermission::class, 'operator_id');
    }

    /**
     * Ottieni moduli abilitati per questo operatore
     */
    public function getEnabledModulesAttribute()
    {
        return $this->modulePermissions()
            ->where('enabled', 1)
            ->pluck('module')
            ->toArray();
    }

    /**
     * Verifica se operatore ha accesso a un modulo specifico
     */
    public function hasModuleAccess($module)
    {
        return $this->modulePermissions()
            ->where('module', $module)
            ->where('enabled', 1)
            ->exists();
    }

    /**
     * Scope per operatori attivi
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    /**
     * Scope per cercare operatori
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->where('user', 'LIKE', "%{$search}%")
              ->orWhere('full_name', 'LIKE', "%{$search}%")
              ->orWhere('reparto', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Valida credenziali operatore
     */
    public static function validateCredentials($username, $pin)
    {
        return self::where('user', $username)
            ->where('pin', $pin)
            ->where('active', 1)
            ->first();
    }

    /**
     * Ottieni statistiche operatore
     */
    public function getStats()
    {
        // Statistiche da implementare con relazioni future
        return [
            'total_quality_controls' => 0, // Da collegare a cq_hermes_records
            'total_repairs' => 0, // Da collegare a riparazioni_interne
            'enabled_modules' => $this->enabled_modules
        ];
    }
}
