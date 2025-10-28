<?php

/**
 * UpdateMrpArrivalsSchema Migration
 * Auto-generated from model schema differences
 */
class UpdateMrpArrivalsSchema
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE mrp_arrivals ADD COLUMN updated_at DATETIME');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE mrp_arrivals DROP COLUMN updated_at');
    }
}
