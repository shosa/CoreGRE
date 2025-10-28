<?php

/**
 * RenameAvailableWidgetsToWidgAvailable Migration
 * Rinomina tabella available_widgets in widg_available
 */
class RenameAvailableWidgetsToWidgAvailable
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE available_widgets TO widg_available');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE widg_available TO available_widgets');
    }
}
