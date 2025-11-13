<?php

/**
 * IncreaseDatiVarcharLengths Migration
 * Aumenta la dimensione delle colonne VARCHAR in core_dati per evitare errori di troncamento
 */
class IncreaseCoreDatiVarcharLengths
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        // Aumenta la dimensione delle colonne problematiche
        $connection->statement('ALTER TABLE core_dati MODIFY COLUMN `Articolo` VARCHAR(255) NOT NULL');
        $connection->statement('ALTER TABLE core_dati MODIFY COLUMN `Descrizione Articolo` VARCHAR(255) NOT NULL');

        echo "✓ Colonne 'Articolo' e 'Descrizione Articolo' estese a VARCHAR(255)\n";
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        // Riporta le colonne alla dimensione originale (assumi VARCHAR(50))
        // ATTENZIONE: Questo potrebbe causare perdita di dati se ci sono valori lunghi
        $connection->statement('ALTER TABLE core_dati MODIFY COLUMN `Articolo` VARCHAR(50) NOT NULL');
        $connection->statement('ALTER TABLE core_dati MODIFY COLUMN `Descrizione Articolo` VARCHAR(50) NOT NULL');

        echo "✓ Colonne 'Articolo' e 'Descrizione Articolo' riportate a VARCHAR(50)\n";
    }
}
