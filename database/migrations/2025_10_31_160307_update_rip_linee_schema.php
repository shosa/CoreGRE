<?php

/**
 * UpdateRipLineeSchema Migration
 * Auto-generated from model schema differences
 */
class UpdateRipLineeSchema
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE rip_linee ADD COLUMN created_at DATETIME');
        $connection->statement('ALTER TABLE rip_linee ADD COLUMN updated_at DATETIME');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE rip_linee DROP COLUMN created_at');
        $connection->statement('ALTER TABLE rip_linee DROP COLUMN updated_at');
    }
}
