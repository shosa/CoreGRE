<?php

/**
 * UpdateProductionRecordsSchema Migration
 * Auto-generated from model schema differences
 */
class UpdateProductionRecordsSchema
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE production_records MODIFY COLUMN manovia1 INT(11)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN manovia2 INT(11)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN orlatura1 INT(11)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN orlatura2 INT(11)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN orlatura3 INT(11)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN orlatura4 INT(11)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN orlatura5 INT(11)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN taglio1 INT(11)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN taglio2 INT(11)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN total_montaggio INT(11)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN total_orlatura INT(11)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN total_taglio INT(11)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        $connection->statement('ALTER TABLE production_records MODIFY COLUMN manovia1 decimal(8,2)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN manovia2 decimal(8,2)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN orlatura1 decimal(8,2)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN orlatura2 decimal(8,2)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN orlatura3 decimal(8,2)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN orlatura4 decimal(8,2)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN orlatura5 decimal(8,2)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN taglio1 decimal(8,2)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN taglio2 decimal(8,2)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN total_montaggio decimal(8,2)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN total_orlatura decimal(8,2)');
        $connection->statement('ALTER TABLE production_records MODIFY COLUMN total_taglio decimal(8,2)');
    }
}
