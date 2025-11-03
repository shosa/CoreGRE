<?php

/**
 * CreateInWorkSystem Migration
 *
 * Rinomina cq_operators in inwork_operators e crea il sistema di gestione
 * permessi moduli per CoreInWork
 */
class CreateInWorkSystem
{
    /**
     * Esegui le migrazioni.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        // STEP 1: Rinomina tabella operatori
        $connection->statement('RENAME TABLE cq_operators TO inwork_operators');

        // STEP 2: Aggiungi nuove colonne alla tabella operatori
        $connection->statement('
            ALTER TABLE inwork_operators
                ADD COLUMN active TINYINT(1) NOT NULL DEFAULT 1 COMMENT "Operatore attivo/disattivato" AFTER reparto,
                ADD COLUMN email VARCHAR(255) NULL COMMENT "Email operatore (opzionale)" AFTER active,
                ADD COLUMN phone VARCHAR(20) NULL COMMENT "Telefono operatore (opzionale)" AFTER email,
                ADD COLUMN notes TEXT NULL COMMENT "Note amministrative" AFTER phone,
                ADD COLUMN created_at TIMESTAMP NULL DEFAULT NULL COMMENT "Data creazione" AFTER notes,
                ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT "Data ultimo aggiornamento" AFTER created_at
        ');

        // STEP 3: Aggiungi indici per performance
        $connection->statement('
            ALTER TABLE inwork_operators
                ADD INDEX idx_user (user),
                ADD INDEX idx_reparto (reparto),
                ADD INDEX idx_active (active)
        ');

        // STEP 4: Crea tabella permessi moduli
        $connection->statement('
            CREATE TABLE inwork_module_permissions (
                id INT(11) NOT NULL AUTO_INCREMENT,
                operator_id INT(11) NOT NULL COMMENT "ID operatore",
                module ENUM("quality", "repairs") NOT NULL COMMENT "Modulo app mobile",
                enabled TINYINT(1) NOT NULL DEFAULT 1 COMMENT "Modulo abilitato per questo operatore",
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY unique_operator_module (operator_id, module),
                KEY idx_module (module),
                KEY idx_enabled (enabled),
                CONSTRAINT fk_module_perm_operator
                    FOREIGN KEY (operator_id)
                    REFERENCES inwork_operators(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT="Permessi moduli mobile per operatore"
        ');

        // STEP 5: Inserisci permessi di default per operatori esistenti
        // Abilita modulo Quality per tutti (backward compatibility)
        $connection->statement('
            INSERT INTO inwork_module_permissions (operator_id, module, enabled)
            SELECT id, "quality", 1
            FROM inwork_operators
            ON DUPLICATE KEY UPDATE enabled = 1
        ');

        // Abilita modulo Repairs per tutti (backward compatibility)
        $connection->statement('
            INSERT INTO inwork_module_permissions (operator_id, module, enabled)
            SELECT id, "repairs", 1
            FROM inwork_operators
            ON DUPLICATE KEY UPDATE enabled = 1
        ');
    }

    /**
     * Rollback delle migrazioni.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        // Drop tabella permessi
        $connection->statement('DROP TABLE IF EXISTS inwork_module_permissions');

        // Rimuovi colonne aggiunte
        $connection->statement('
            ALTER TABLE inwork_operators
                DROP COLUMN IF EXISTS updated_at,
                DROP COLUMN IF EXISTS notes,
                DROP COLUMN IF EXISTS phone,
                DROP COLUMN IF EXISTS email,
                DROP COLUMN IF EXISTS active,
                DROP INDEX IF EXISTS idx_active,
                DROP INDEX IF EXISTS idx_reparto,
                DROP INDEX IF EXISTS idx_user
        ');

        // Rinomina tabella al nome originale
        $connection->statement('RENAME TABLE inwork_operators TO cq_operators');
    }
}
