<?php

/**
 * RenameIdNumerateToRipIdnumerate Migration
 * Rinomina tabella id_numerate in rip_idnumerate
 */
class RenameIdNumerateToRipIdnumerate
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE id_numerate TO rip_idnumerate');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE rip_idnumerate TO id_numerate');
    }
}
