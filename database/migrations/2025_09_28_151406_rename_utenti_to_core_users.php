<?php

/**
 * RenameUtentiToCoreUsers Migration
 * Rinomina tabella utenti in core_users
 */
class RenameUtentiToCoreUsers
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE utenti TO core_users');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE core_users TO utenti');
    }
}
