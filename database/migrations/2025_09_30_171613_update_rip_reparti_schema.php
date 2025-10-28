<?php

/**
 * UpdateRipRepartiSchema Migration
 * Auto-generated from model schema differences
 */
class UpdateRipRepartiSchema
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE rip_reparti ADD COLUMN created_at DATETIME');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE rip_reparti DROP COLUMN created_at');
    }
}
