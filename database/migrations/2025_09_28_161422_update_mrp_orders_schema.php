<?php

/**
 * UpdateMrpOrdersSchema Migration
 * Auto-generated from model schema differences
 */
class UpdateMrpOrdersSchema
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE mrp_orders ADD COLUMN updated_at DATETIME');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE mrp_orders DROP COLUMN updated_at');
    }
}
