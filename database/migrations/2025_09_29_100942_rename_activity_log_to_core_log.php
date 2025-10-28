<?php

/**
 * RenameActivityLogToCoreLog Migration
 * Rinomina tabella activity_log in core_log
 */
class RenameActivityLogToCoreLog
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE activity_log TO core_log');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE core_log TO activity_log');
    }
}
