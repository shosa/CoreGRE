<?php

/**
 * UpdateCoreAnagSchema Migration
 * Auto-generated from model schema differences
 */
class UpdateCoreAnagSchema
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE core_anag ADD COLUMN created_at DATETIME');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE core_anag DROP COLUMN created_at');
    }
}
