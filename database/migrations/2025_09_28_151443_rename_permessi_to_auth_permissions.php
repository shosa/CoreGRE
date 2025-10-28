<?php

/**
 * RenamePermessiToAuthPermissions Migration
 * Rinomina tabella permessi in auth_permissions
 */
class RenamePermessiToAuthPermissions
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE permessi TO auth_permissions');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE auth_permissions TO permessi');
    }
}
