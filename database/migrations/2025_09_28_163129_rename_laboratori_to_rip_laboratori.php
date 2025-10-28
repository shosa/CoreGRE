<?php

/**
 * RenameLaboratoriToRipLaboratori Migration
 * Rinomina tabella laboratori in rip_laboratori
 */
class RenameLaboratoriToRipLaboratori
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE laboratori TO rip_laboratori');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE rip_laboratori TO laboratori');
    }
}
