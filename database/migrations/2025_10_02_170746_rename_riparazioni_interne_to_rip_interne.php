<?php

/**
 * RenameRiparazioniInterneToRipInterne Migration
 * Rinomina tabella riparazioni_interne in rip_interne
 */
class RenameRiparazioniInterneToRipInterne
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE riparazioni_interne TO rip_interne');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE rip_interne TO riparazioni_interne');
    }
}
