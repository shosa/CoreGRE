<?php

/**
 * RenameOperatorsToCqOperators Migration
 * Rinomina tabella operators in cq_operators
 */
class RenameOperatorsToCqOperators
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE operators TO cq_operators');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('RENAME TABLE cq_operators TO operators');
    }
}
