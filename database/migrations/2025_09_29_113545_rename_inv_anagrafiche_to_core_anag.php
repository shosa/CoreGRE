<?php

/**
 * RenameInvAnagraficheToCoreAnag Migration
 * Rinomina tabella inv_anagrafiche in core_anag
 */
class RenameInvAnagraficheToCoreAnag
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE inv_anagrafiche TO core_anag');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE core_anag TO inv_anagrafiche');
    }
}
