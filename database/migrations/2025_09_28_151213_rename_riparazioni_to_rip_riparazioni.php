<?php

/**
 * RenameRiparazioniToRipRiparazioni Migration
 * Rinomina tabella riparazioni in rip_riparazioni
 */
class RenameRiparazioniToRipRiparazioni
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE riparazioni TO rip_riparazioni');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE rip_riparazioni TO riparazioni');
    }
}
