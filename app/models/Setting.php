<?php

namespace App\Models;

/**
 * Setting Model
 * Table: settings
 */
class Setting extends BaseModel
{
    protected $table = 'core_settings';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'item',
        'value'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scope per cercare per item
     */
    public function scopeByItem($query, $item)
    {
        return $query->where('item', $item);
    }

    /**
     * Ottieni valore di una setting
     */
    public static function getValue($item, $default = null)
    {
        $setting = static::where('item', $item)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Ottieni valore come intero
     */
    public static function getInt($item, $default = 0)
    {
        return (int) static::getValue($item, $default);
    }

    /**
     * Ottieni valore come boolean
     */
    public static function getBool($item, $default = false)
    {
        $value = static::getValue($item, $default);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Imposta valore di una setting
     */
    public static function setValue($item, $value)
    {
        return static::updateOrCreate(
            ['item' => $item],
            ['value' => $value]
        );
    }

    /**
     * Ottieni tutte le settings raggruppate per categoria
     */
    public static function getAllGrouped()
    {
        $all = static::all()->keyBy('item');

        return [
            'pagination' => [
                'default' => $all->get('pagination_default')->value ?? 25,
                'logs' => $all->get('pagination_logs')->value ?? 50,
                'database' => $all->get('pagination_database')->value ?? 50,
                'export' => $all->get('pagination_export')->value ?? 15,
                'treeview' => $all->get('pagination_treeview')->value ?? 25,
                'max_limit' => $all->get('pagination_max_limit')->value ?? 1000,
            ],
            'system' => [
                'cache_ttl' => $all->get('cache_ttl')->value ?? 3600,
                'recent_items_limit' => $all->get('recent_items_limit')->value ?? 5,
                'max_upload_size_mb' => $all->get('max_upload_size_mb')->value ?? 50,
                'session_timeout_warning' => $all->get('session_timeout_warning')->value ?? 300,
                'php_cli_path' => $all->get('php_cli_path')->value ?? '',
            ],
            'notifications' => [
                'alert_timeout' => $all->get('alert_timeout')->value ?? 5000,
                'alert_position' => $all->get('alert_position')->value ?? 'top-right',
                'enable_browser_notifications' => ($all->get('enable_browser_notifications')->value ?? 'false') === 'true',
                'enable_sound_notifications' => ($all->get('enable_sound_notifications')->value ?? 'false') === 'true',
            ],
        ];
    }
}