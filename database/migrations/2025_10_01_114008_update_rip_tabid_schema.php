<?php

/**
 * UpdateRipTabidSchema Migration
 * Auto-generated from model schema differences
 */
class UpdateRipTabidSchema
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE rip_tabid ADD COLUMN created_at DATETIME');
        $connection->statement('ALTER TABLE rip_tabid ADD COLUMN updated_at DATETIME');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE rip_tabid DROP COLUMN created_at');
        $connection->statement('ALTER TABLE rip_tabid DROP COLUMN updated_at');
    }
}
