<?php

/**
 * RefactorProductionTables Migration
 * Rifattorizza le tabelle produzione sostituendo MESE+GIORNO con date vere
 */
class RefactorProductionTables
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        // 1. Crea nuova tabella production_records con struttura ottimizzata
        $connection->statement('
            CREATE TABLE production_records (
                id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                production_date DATE NOT NULL,

                -- Dati montaggio
                manovia1 INT DEFAULT 0,
                manovia1_notes TEXT NULL,
                manovia2 INT DEFAULT 0,
                manovia2_notes TEXT NULL,

                -- Dati orlatura
                orlatura1 INT DEFAULT 0,
                orlatura1_notes TEXT NULL,
                orlatura2 INT DEFAULT 0,
                orlatura2_notes TEXT NULL,
                orlatura3 INT DEFAULT 0,
                orlatura3_notes TEXT NULL,
                orlatura4 INT DEFAULT 0,
                orlatura4_notes TEXT NULL,
                orlatura5 INT DEFAULT 0,
                orlatura5_notes TEXT NULL,

                -- Dati taglio
                taglio1 INT DEFAULT 0,
                taglio1_notes TEXT NULL,
                taglio2 INT DEFAULT 0,
                taglio2_notes TEXT NULL,

                -- Totali calcolati (generated columns)
                total_montaggio INT GENERATED ALWAYS AS (manovia1 + manovia2) STORED,
                total_orlatura INT GENERATED ALWAYS AS (orlatura1 + orlatura2 + orlatura3 + orlatura4 + orlatura5) STORED,
                total_taglio INT GENERATED ALWAYS AS (taglio1 + taglio2) STORED,

                -- Metadati
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_by INT(11) NULL,
                updated_by INT(11) NULL,

                -- Indici
                UNIQUE KEY unique_production_date (production_date),
                KEY idx_production_year_month (production_date),
                KEY idx_created_by (created_by)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // 2. Crea funzione helper per convertire nomi mesi italiani
        $connection->statement('
            CREATE FUNCTION mese_to_number(mese_nome VARCHAR(20))
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

        // 3. Migra i dati esistenti da prod_mesi
        $connection->statement('
            INSERT INTO production_records (
                production_date,
                manovia1, manovia1_notes,
                manovia2, manovia2_notes,
                orlatura1, orlatura1_notes,
                orlatura2, orlatura2_notes,
                orlatura3, orlatura3_notes,
                orlatura4, orlatura4_notes,
                orlatura5, orlatura5_notes,
                taglio1, taglio1_notes,
                taglio2, taglio2_notes,
                created_at
            )
            SELECT
                DATE(CONCAT("2025-",
                    LPAD(mese_to_number(MESE), 2, "0"), "-",
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
                CAST(COALESCE(NULLIF(ORLATURA3, ""), "0") AS SIGNED),
                NULLIF(ORLATURA3NOTE, ""),
                CAST(COALESCE(NULLIF(ORLATURA4, ""), "0") AS SIGNED),
                NULLIF(ORLATURA4NOTE, ""),
                CAST(COALESCE(NULLIF(ORLATURA5, ""), "0") AS SIGNED),
                NULLIF(ORLATURA5NOTE, ""),
                CAST(COALESCE(NULLIF(TAGLIO1, ""), "0") AS SIGNED),
                NULLIF(TAGLIO1NOTE, ""),
                CAST(COALESCE(NULLIF(TAGLIO2, ""), "0") AS SIGNED),
                NULLIF(TAGLIO2NOTE, ""),
                NOW()
            FROM prod_mesi
            WHERE MESE IS NOT NULL
              AND GIORNO IS NOT NULL
              AND GIORNO BETWEEN 1 AND 31
              AND mese_to_number(MESE) IS NOT NULL
        ');

        // 4. Rimuovi funzione helper
        $connection->statement('DROP FUNCTION mese_to_number');

        // 5. Rinomina vecchia tabella (backup)
        $connection->statement('RENAME TABLE prod_mesi TO prod_mesi_backup');

        // 6. Rimuovi sped_mesi se non più necessaria
        $connection->statement('DROP TABLE IF EXISTS sped_mesi');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        // Ripristina la tabella originale
        $connection->statement('RENAME TABLE prod_mesi_backup TO prod_mesi');

        // Rimuovi la nuova tabella
        $connection->statement('DROP TABLE IF EXISTS production_records');
    }
}