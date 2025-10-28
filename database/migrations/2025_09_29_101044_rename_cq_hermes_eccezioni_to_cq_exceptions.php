<?php

/**
 * RenameCqHermesEccezioniToCqExceptions Migration
 * Rinomina tabella cq_hermes_eccezioni in cq_exceptions
 */
class RenameCqHermesEccezioniToCqExceptions
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE cq_hermes_eccezioni TO cq_exceptions');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE cq_exceptions TO cq_hermes_eccezioni');
    }
}
