<?php

/**
 * RenameCqHermesTipiDifettiToCqDeftypes Migration
 * Rinomina tabella cq_hermes_tipi_difetti in cq_deftypes
 */
class RenameCqHermesTipiDifettiToCqDeftypes
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE cq_hermes_tipi_difetti TO cq_deftypes');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE cq_deftypes TO cq_hermes_tipi_difetti');
    }
}
