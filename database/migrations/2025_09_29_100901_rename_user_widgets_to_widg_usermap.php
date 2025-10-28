<?php

/**
 * RenameUserWidgetsToWidgUsermap Migration
 * Rinomina tabella user_widgets in widg_usermap
 */
class RenameUserWidgetsToWidgUsermap
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE user_widgets TO widg_usermap');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE widg_usermap TO user_widgets');
    }
}
