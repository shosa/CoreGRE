<?php

/**
 * AddSystemSettings Migration
 */
class AddSystemSettings
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        // Inserisci settings di default
        $settings = [
            // PAGINAZIONE
            ['item' => 'pagination_default', 'value' => '25'],
            ['item' => 'pagination_logs', 'value' => '50'],
            ['item' => 'pagination_database', 'value' => '50'],
            ['item' => 'pagination_export', 'value' => '15'],
            ['item' => 'pagination_treeview', 'value' => '25'],
            ['item' => 'pagination_max_limit', 'value' => '1000'],

            // SISTEMA / PERFORMANCE
            ['item' => 'cache_ttl', 'value' => '3600'],
            ['item' => 'recent_items_limit', 'value' => '5'],
            ['item' => 'max_upload_size_mb', 'value' => '50'],
            ['item' => 'session_timeout_warning', 'value' => '300'],

            // NOTIFICHE / ALERTS
            ['item' => 'alert_timeout', 'value' => '5000'],
            ['item' => 'alert_position', 'value' => 'top-right'],
            ['item' => 'enable_browser_notifications', 'value' => 'false'],
            ['item' => 'enable_sound_notifications', 'value' => 'false'],
        ];

        foreach ($settings as $setting) {
            // Inserisci solo se non esiste già
            $exists = $connection->table('core_settings')
                ->where('item', $setting['item'])
                ->exists();

            if (!$exists) {
                $connection->table('core_settings')->insert([
                    'item' => $setting['item'],
                    'value' => $setting['value'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        // Rimuovi le settings aggiunte
        $settingKeys = [
            'pagination_default', 'pagination_logs', 'pagination_database',
            'pagination_export', 'pagination_treeview', 'pagination_max_limit',
            'cache_ttl', 'recent_items_limit', 'max_upload_size_mb', 'session_timeout_warning',
            'alert_timeout', 'alert_position', 'enable_browser_notifications', 'enable_sound_notifications'
        ];

        $connection->table('core_settings')
            ->whereIn('item', $settingKeys)
            ->delete();
    }
}
