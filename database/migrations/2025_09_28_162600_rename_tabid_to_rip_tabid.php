<?php

/**
 * RenameTabidToRipTabid Migration
 * Rinomina tabella tabid in rip_tabid
 */
class RenameTabidToRipTabid
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE tabid TO rip_tabid');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE rip_tabid TO tabid');
    }
}
