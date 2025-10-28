<?php

/**
 * RenameRepartiToRipReparti Migration
 * Rinomina tabella reparti in rip_reparti
 */
class RenameRepartiToRipReparti
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE reparti TO rip_reparti');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE rip_reparti TO reparti');
    }
}
