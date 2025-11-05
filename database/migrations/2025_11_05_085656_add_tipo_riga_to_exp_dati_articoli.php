<?php

/**
 * AddTipoRigaToExpDatiArticoli Migration
 */
class AddTipoRigaToExpDatiArticoli
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        // Aggiungi campo tipo_riga: 'articolo' (da Excel) o 'libera' (inserita manualmente)
        $connection->statement("
            ALTER TABLE exp_dati_articoli
            ADD COLUMN tipo_riga ENUM('articolo', 'libera') NOT NULL DEFAULT 'articolo' AFTER is_mancante
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        // Rimuovi il campo tipo_riga
        $connection->statement("
            ALTER TABLE exp_dati_articoli
            DROP COLUMN tipo_riga
        ");
    }
}
