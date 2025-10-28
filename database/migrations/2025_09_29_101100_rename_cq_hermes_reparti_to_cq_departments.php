<?php

/**
 * RenameCqHermesRepartiToCqDepartments Migration
 * Rinomina tabella cq_hermes_reparti in cq_departments
 */
class RenameCqHermesRepartiToCqDepartments
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE cq_hermes_reparti TO cq_departments');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE cq_departments TO cq_hermes_reparti');
    }
}
