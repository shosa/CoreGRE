<?php

/**
 * RenameCoreUsersToAuthUsers Migration
 * Rinomina tabella core_users in auth_users
 */
class RenameCoreUsersToAuthUsers
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE core_users TO auth_users');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE auth_users TO core_users');
    }
}
