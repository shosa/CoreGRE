<?php

/**
 * ImportDataFromProd2024Old Migration
 * Imports data from the orphaned prod_2024_old table into the new production_records table.
 */
class ImportDataFromProd2024Old
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        // 1. Create or replace helper function to convert Italian month names
        $connection->statement('
            CREATE OR REPLACE FUNCTION mese_to_number_2024(mese_nome VARCHAR(20))
            RETURNS INT
            READS SQL DATA
            DETERMINISTIC
            BEGIN
                CASE UPPER(mese_nome)
                    WHEN "GENNAIO" THEN RETURN 1;
                    WHEN "FEBBRAIO" THEN RETURN 2;
                    WHEN "MARZO" THEN RETURN 3;
                    WHEN "APRILE" THEN RETURN 4;
                    WHEN "MAGGIO" THEN RETURN 5;
                    WHEN "GIUGNO" THEN RETURN 6;
                    WHEN "LUGLIO" THEN RETURN 7;
                    WHEN "AGOSTO" THEN RETURN 8;
                    WHEN "SETTEMBRE" THEN RETURN 9;
                    WHEN "OTTOBRE" THEN RETURN 10;
                    WHEN "NOVEMBRE" THEN RETURN 11;
                    WHEN "DICEMBRE" THEN RETURN 12;
                    ELSE RETURN NULL;
                END CASE;
            END
        ');

        // 2. Migrate data from prod_2024_old, ignoring duplicates and non-existent columns
        $connection->statement('
            INSERT IGNORE INTO production_records (
                production_date,
                manovia1, manovia1_notes,
                manovia2, manovia2_notes,
                orlatura1, orlatura1_notes,
                orlatura2, orlatura2_notes,
                taglio1, taglio1_notes,
                taglio2, taglio2_notes,
                created_at
            )
            SELECT
                DATE(CONCAT("2024-",
                    LPAD(mese_to_number_2024(MESE), 2, "0"), "-",
                    LPAD(GIORNO, 2, "0")
                )) as production_date,
                CAST(COALESCE(NULLIF(MANOVIA1, ""), "0") AS SIGNED),
                NULLIF(MANOVIA1NOTE, ""),
                CAST(COALESCE(NULLIF(MANOVIA2, ""), "0") AS SIGNED),
                NULLIF(MANOVIA2NOTE, ""),
                CAST(COALESCE(NULLIF(ORLATURA1, ""), "0") AS SIGNED),
                NULLIF(ORLATURA1NOTE, ""),
                CAST(COALESCE(NULLIF(ORLATURA2, ""), "0") AS SIGNED),
                NULLIF(ORLATURA2NOTE, ""),
                CAST(COALESCE(NULLIF(TAGLIO1, ""), "0") AS SIGNED),
                NULLIF(TAGLIO1NOTE, ""),
                CAST(COALESCE(NULLIF(TAGLIO2, ""), "0") AS SIGNED),
                NULLIF(TAGLIO2NOTE, ""),
                NOW()
            FROM prod_2024_old
            WHERE MESE IS NOT NULL
              AND GIORNO IS NOT NULL
              AND GIORNO BETWEEN 1 AND 31
              AND mese_to_number_2024(MESE) IS NOT NULL
        ');

        // 3. Drop the helper function
        $connection->statement('DROP FUNCTION IF EXISTS mese_to_number_2024');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        // Remove the data imported from prod_2024_old
        $connection->statement("DELETE FROM production_records WHERE YEAR(production_date) = 2024");
    }
}