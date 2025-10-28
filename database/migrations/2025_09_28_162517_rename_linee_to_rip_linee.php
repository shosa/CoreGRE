<?php

/**
 * RenameLineeToRipLinee Migration
 * Rinomina tabella linee in rip_linee
 */
class RenameLineeToRipLinee
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE linee TO rip_linee');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE rip_linee TO linee');
    }
}
