<?php

/**
 * RenameNotificationsToAuthNotifications Migration
 * Rinomina tabella notifications in auth_notifications
 */
class RenameNotificationsToAuthNotifications
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE notifications TO auth_notifications');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE auth_notifications TO notifications');
    }
}
