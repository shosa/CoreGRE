<?php

/**
 * UpdateCoreAnagSchema Migration
 * Auto-generated from model schema differences
 */
class UpdateAuthPermissionsSchema
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE auth_permissions ADD COLUMN artisan Tinyint(1) DEFAULT 0');
        $connection->statement('ALTER TABLE auth_permissions ADD COLUMN cron Tinyint(1) DEFAULT 0');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE auth_permissions DROP COLUMN artisan');
        $connection->statement('ALTER TABLE auth_permissions DROP COLUMN artisan');
    }
}
