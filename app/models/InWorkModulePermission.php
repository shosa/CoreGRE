<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * InWorkModulePermission Model
 *
 * Gestisce i permessi per i moduli mobile dell'app CoreInWork
 */
class InWorkModulePermission extends Model
{
    protected $table = 'inwork_module_permissions';

    protected $fillable = [
        'operator_id',
        'module',
        'enabled'
    ];

    protected $casts = [
        'operator_id' => 'integer',
        'enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Moduli disponibili
     */
    const MODULES = [
        'quality' => 'Controllo Qualità',
        'repairs' => 'Riparazioni Interne'
    ];

    /**
     * Relazione con operatore
     */
    public function operator()
    {
        return $this->belongsTo(InWorkOperator::class, 'operator_id');
    }

    /**
     * Ottieni nome modulo leggibile
     */
    public function getModuleNameAttribute()
    {
        return self::MODULES[$this->module] ?? $this->module;
    }

    /**
     * Scope per moduli abilitati
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', 1);
    }

    /**
     * Scope per modulo specifico
     */
    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Sincronizza permessi per un operatore
     *
     * @param int $operatorId
     * @param array $modules Array di moduli da abilitare es: ['quality', 'repairs']
     */
    public static function syncForOperator($operatorId, array $modules)
    {
        // Ottieni permessi attuali
        $currentPermissions = self::where('operator_id', $operatorId)->get();

        foreach (array_keys(self::MODULES) as $module) {
            $shouldBeEnabled = in_array($module, $modules);
            $permission = $currentPermissions->where('module', $module)->first();

            if ($permission) {
                // Aggiorna esistente
                $permission->update(['enabled' => $shouldBeEnabled]);
            } else {
                // Crea nuovo
                self::create([
                    'operator_id' => $operatorId,
                    'module' => $module,
                    'enabled' => $shouldBeEnabled
                ]);
            }
        }
    }

    /**
     * Crea permessi di default per nuovo operatore
     * (Abilita tutti i moduli per backward compatibility)
     */
    public static function createDefaultsForOperator($operatorId)
    {
        foreach (array_keys(self::MODULES) as $module) {
            self::create([
                'operator_id' => $operatorId,
                'module' => $module,
                'enabled' => true // Default: tutti abilitati
            ]);
        }
    }
}
