<?php

/**
 * UpdateCoreSettingsSchema Migration
 * Auto-generated from model schema differences
 */
class UpdateCoreSettingsSchema
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE core_settings ADD COLUMN created_at DATETIME');
        $connection->statement('ALTER TABLE core_settings ADD COLUMN updated_at DATETIME');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE core_settings DROP COLUMN created_at');
        $connection->statement('ALTER TABLE core_settings DROP COLUMN updated_at');
    }
}
