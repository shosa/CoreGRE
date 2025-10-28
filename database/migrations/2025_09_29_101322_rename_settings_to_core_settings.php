<?php

/**
 * RenameSettingsToCoreSettings Migration
 * Rinomina tabella settings in core_settings
 */
class RenameSettingsToCoreSettings
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE settings TO core_settings');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE core_settings TO settings');
    }
}
