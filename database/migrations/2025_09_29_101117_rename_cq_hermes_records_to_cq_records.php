<?php

/**
 * RenameCqHermesRecordsToCqRecords Migration
 * Rinomina tabella cq_hermes_records in cq_records
 */
class RenameCqHermesRecordsToCqRecords
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE cq_hermes_records TO cq_records');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE cq_records TO cq_hermes_records');
    }
}
