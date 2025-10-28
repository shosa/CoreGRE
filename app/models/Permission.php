<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Permission Model
 * Gestisce i permessi degli utenti nel sistema WEBGRE
 *
 * @property int $ID
 * @property int $id_utente
 * @property bool $riparazioni
 * @property bool $produzione
 * @property bool $log
 * @property bool $etichette
 * @property bool $dbsql
 * @property bool $utenti
 * @property bool $tracking
 * @property bool $settings
 * @property bool $scm
 * @property bool $export
 * @property bool $admin
 * @property bool $quality
 * @property bool $mrp
 */
class Permission extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'auth_permissions';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'ID';

    /**
     * Disable timestamps as this table doesn't have them
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_utente',
        'riparazioni',
        'produzione',
        'log',
        'etichette',
        'dbsql',
        'utenti',
        'tracking',
        'settings',
        'scm',
        'export',
        'admin',
        'quality',
        'mrp',
        'artisan',
        'cron'

    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'riparazioni' => 'boolean',
        'produzione' => 'boolean',
        'log' => 'boolean',
        'etichette' => 'boolean',
        'dbsql' => 'boolean',
        'utenti' => 'boolean',
        'tracking' => 'boolean',
        'settings' => 'boolean',
        'scm' => 'boolean',
        'export' => 'boolean',
        'admin' => 'boolean',
        'quality' => 'boolean',
        'mrp' => 'boolean',
    ];

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_utente');
    }

    /**
     * Check if user has permission for a specific module
     */
    public function hasPermission(string $module): bool
    {
        return isset($this->attributes[$module]) && (bool) $this->attributes[$module];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }

    /**
     * Get all permission modules
     */
    public function getPermissionModules(): array
    {
        return [
            'riparazioni' => 'Gestione Riparazioni',
            'produzione' => 'Gestione Produzione',
            'log' => 'Activity Log',
            'etichette' => 'Sistema Etichette',
            'dbsql' => 'Database SQL',
            'utenti' => 'Gestione Utenti',
            'tracking' => 'Tracking & Genealogia',
            'settings' => 'Impostazioni Sistema',
            'scm' => 'SCM Terzisti',
            'export' => 'Export/DDT',
            'admin' => 'Amministrazione',
            'quality' => 'Controllo Qualità',
            'mrp' => 'MRP Planning',
            'artisan' => 'Accesso al tool php Artisan',
            'cron'=> 'Schedulazione processi'
        ];
    }

    /**
     * Get enabled permissions as array
     */
    public function getEnabledPermissions(): array
    {
        $modules = $this->getPermissionModules();
        $enabled = [];

        foreach ($modules as $key => $name) {
            if ($this->hasPermission($key)) {
                $enabled[$key] = $name;
            }
        }

        return $enabled;
    }

    /**
     * Set multiple permissions at once
     */
    public function setPermissions(array $permissions): void
    {
        $modules = array_keys($this->getPermissionModules());

        foreach ($modules as $module) {
            $this->$module = isset($permissions[$module]) ? (bool) $permissions[$module] : false;
        }
    }

    /**
     * Grant all permissions
     */
    public function grantAllPermissions(): void
    {
        $modules = array_keys($this->getPermissionModules());

        foreach ($modules as $module) {
            $this->$module = true;
        }
    }

    /**
     * Revoke all permissions
     */
    public function revokeAllPermissions(): void
    {
        $modules = array_keys($this->getPermissionModules());

        foreach ($modules as $module) {
            $this->$module = false;
        }
    }

    /**
     * Scope for admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('admin', 1);
    }

    /**
     * Scope for users with specific permission
     */
    public function scopeWithPermission($query, string $permission)
    {
        return $query->where($permission, 1);
    }
}