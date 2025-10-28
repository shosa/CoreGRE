<?php

/**
 * UpdateScmLaunchPhasesSchema Migration
 * Auto-generated from model schema differences
 */
class UpdateScmLaunchPhasesSchema
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE scm_launch_phases ADD COLUMN updated_at DATETIME');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE scm_launch_phases DROP COLUMN updated_at');
    }
}
