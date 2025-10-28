<?php

/**
 * RenameDatiToCoreDati Migration
 * Rinomina tabella dati in core_dati
 */
class RenameDatiToCoreDati
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE dati TO core_dati');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE core_dati TO dati');
    }
}
