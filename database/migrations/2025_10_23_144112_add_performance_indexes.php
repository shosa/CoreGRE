<?php

/**
 * AddPerformanceIndexes Migration
 */
class AddPerformanceIndexes
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = EloquentBootstrap::getConnection();

        // ============================================
        // PERFORMANCE INDEXES - COREGRE
        // Miglioramento 10-50x su query filtrate
        // ============================================

        // RIPARAZIONI (rip_riparazioni)
        $this->createIndexIfNotExists($connection, 'rip_riparazioni', 'idx_data_completa', 'DATA, COMPLETA');
        $this->createIndexIfNotExists($connection, 'rip_riparazioni', 'idx_utente_completa', 'UTENTE, COMPLETA');
        $this->createIndexIfNotExists($connection, 'rip_riparazioni', 'idx_data', 'DATA');

        // PRODUZIONE (production_records)
        $this->createIndexIfNotExists($connection, 'production_records', 'idx_production_date', 'production_date');
        $this->createIndexIfNotExists($connection, 'production_records', 'idx_production_created', 'created_at');

        // EXPORT DOCUMENTI (exp_documenti)
        $this->createIndexIfNotExists($connection, 'exp_documenti', 'idx_terzista', 'id_terzista');
        $this->createIndexIfNotExists($connection, 'exp_documenti', 'idx_stato', 'stato');
        $this->createIndexIfNotExists($connection, 'exp_documenti', 'idx_data_creazione', 'data_creazione');
        $this->createIndexIfNotExists($connection, 'exp_documenti', 'idx_stato_data', 'stato, data_creazione');

        // EXPORT ARTICOLI (exp_dati_articoli)
        $this->createIndexIfNotExists($connection, 'exp_dati_articoli', 'idx_id_documento', 'id_documento');
        $this->createIndexIfNotExists($connection, 'exp_dati_articoli', 'idx_codice_articolo', 'codice_articolo');
        $this->createIndexIfNotExists($connection, 'exp_dati_articoli', 'idx_codice_documento', 'codice_articolo, id_documento');

        // EXPORT DATI MANCANTI (exp_dati_mancanti)
        $this->createIndexIfNotExists($connection, 'exp_dati_mancanti', 'idx_mancanti_id_doc', 'id_documento');
        $this->createIndexIfNotExists($connection, 'exp_dati_mancanti', 'idx_mancanti_codice', 'codice_articolo');
        $this->createIndexIfNotExists($connection, 'exp_dati_mancanti', 'idx_mancanti_codice_doc', 'codice_articolo, id_documento');

        // QUALITY RECORDS (cq_records)
        $this->createIndexIfNotExists($connection, 'cq_records', 'idx_quality_data', 'data_controllo');
        $this->createIndexIfNotExists($connection, 'cq_records', 'idx_quality_operatore', 'operatore');
        $this->createIndexIfNotExists($connection, 'cq_records', 'idx_quality_cartellino', 'numero_cartellino');
        $this->createIndexIfNotExists($connection, 'cq_records', 'idx_quality_data_operatore', 'data_controllo, operatore');

        // ACTIVITY LOG (core_log)
        $this->createIndexIfNotExists($connection, 'core_log', 'idx_log_user_date', 'user_id, created_at');
        $this->createIndexIfNotExists($connection, 'core_log', 'idx_log_category', 'category');

        // CORE DATA / TRACKING (core_dati)
        $this->createIndexIfNotExists($connection, 'core_dati', 'idx_cartel', 'Cartel');
        $this->createIndexIfNotExists($connection, 'core_dati', 'idx_commessa', '`Commessa Cli`');
        $this->createIndexIfNotExists($connection, 'core_dati', 'idx_articolo', 'Articolo');
        $this->createIndexIfNotExists($connection, 'core_dati', 'idx_ordine', 'Ordine');

        // NOTIFICATIONS (auth_notifications)
        $this->createIndexIfNotExists($connection, 'auth_notifications', 'idx_notif_user_read', 'user_id, read_at');
        $this->createIndexIfNotExists($connection, 'auth_notifications', 'idx_notif_created', 'created_at');

        // WIDGET USER MAP (widg_usermap)
        $this->createIndexIfNotExists($connection, 'widg_usermap', 'idx_widget_user', 'user_id');
        $this->createIndexIfNotExists($connection, 'widg_usermap', 'idx_widget_user_key', 'user_id, widget_key');

        echo "✓ Performance indexes created successfully!\n";
    }

    /**
     * Helper: Crea indice solo se non esiste già
     */
    private function createIndexIfNotExists($connection, $table, $indexName, $columns)
    {
        try {
            // Verifica se tabella esiste
            $tableExists = $connection->select("SHOW TABLES LIKE '{$table}'");
            if (empty($tableExists)) {
                echo "⚠ Skipping index {$indexName} - table '{$table}' does not exist\n";
                return;
            }

            // Verifica se indice esiste già
            $indexes = $connection->select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");

            if (empty($indexes)) {
                $connection->statement("CREATE INDEX {$indexName} ON {$table} ({$columns})");
                echo "✓ Created index {$indexName} on {$table}\n";
            } else {
                echo "→ Index {$indexName} already exists on {$table}\n";
            }
        } catch (\Exception $e) {
            echo "✗ Error creating index {$indexName} on {$table}: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = EloquentBootstrap::getConnection();

        // ============================================
        // ROLLBACK PERFORMANCE INDEXES
        // ============================================

        // RIPARAZIONI
        $this->dropIndexIfExists($connection, 'rip_riparazioni', 'idx_data_completa');
        $this->dropIndexIfExists($connection, 'rip_riparazioni', 'idx_utente_completa');
        $this->dropIndexIfExists($connection, 'rip_riparazioni', 'idx_data');

        // PRODUZIONE
        $this->dropIndexIfExists($connection, 'production_records', 'idx_production_date');
        $this->dropIndexIfExists($connection, 'production_records', 'idx_production_created');

        // EXPORT DOCUMENTI
        $this->dropIndexIfExists($connection, 'exp_documenti', 'idx_terzista');
        $this->dropIndexIfExists($connection, 'exp_documenti', 'idx_stato');
        $this->dropIndexIfExists($connection, 'exp_documenti', 'idx_data_creazione');
        $this->dropIndexIfExists($connection, 'exp_documenti', 'idx_stato_data');

        // EXPORT ARTICOLI
        $this->dropIndexIfExists($connection, 'exp_dati_articoli', 'idx_id_documento');
        $this->dropIndexIfExists($connection, 'exp_dati_articoli', 'idx_codice_articolo');
        $this->dropIndexIfExists($connection, 'exp_dati_articoli', 'idx_codice_documento');

        // EXPORT DATI MANCANTI
        $this->dropIndexIfExists($connection, 'exp_dati_mancanti', 'idx_mancanti_id_doc');
        $this->dropIndexIfExists($connection, 'exp_dati_mancanti', 'idx_mancanti_codice');
        $this->dropIndexIfExists($connection, 'exp_dati_mancanti', 'idx_mancanti_codice_doc');

        // QUALITY RECORDS
        $this->dropIndexIfExists($connection, 'cq_records', 'idx_quality_data');
        $this->dropIndexIfExists($connection, 'cq_records', 'idx_quality_operatore');
        $this->dropIndexIfExists($connection, 'cq_records', 'idx_quality_cartellino');
        $this->dropIndexIfExists($connection, 'cq_records', 'idx_quality_data_operatore');

        // ACTIVITY LOG
        $this->dropIndexIfExists($connection, 'core_log', 'idx_log_user_date');
        $this->dropIndexIfExists($connection, 'core_log', 'idx_log_category');

        // CORE DATA / TRACKING
        $this->dropIndexIfExists($connection, 'core_dati', 'idx_cartel');
        $this->dropIndexIfExists($connection, 'core_dati', 'idx_commessa');
        $this->dropIndexIfExists($connection, 'core_dati', 'idx_articolo');
        $this->dropIndexIfExists($connection, 'core_dati', 'idx_ordine');

        // NOTIFICATIONS
        $this->dropIndexIfExists($connection, 'auth_notifications', 'idx_notif_user_read');
        $this->dropIndexIfExists($connection, 'auth_notifications', 'idx_notif_created');

        // WIDGET USER MAP
        $this->dropIndexIfExists($connection, 'widg_usermap', 'idx_widget_user');
        $this->dropIndexIfExists($connection, 'widg_usermap', 'idx_widget_user_key');

        echo "✓ Performance indexes removed successfully!\n";
    }

    /**
     * Helper: Rimuove indice solo se esiste
     */
    private function dropIndexIfExists($connection, $table, $indexName)
    {
        try {
            // Verifica se tabella esiste
            $tableExists = $connection->select("SHOW TABLES LIKE '{$table}'");
            if (empty($tableExists)) {
                return;
            }

            // Verifica se indice esiste
            $indexes = $connection->select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");

            if (!empty($indexes)) {
                $connection->statement("DROP INDEX {$indexName} ON {$table}");
                echo "✓ Dropped index {$indexName} from {$table}\n";
            }
        } catch (\Exception $e) {
            echo "✗ Error dropping index {$indexName} from {$table}: " . $e->getMessage() . "\n";
        }
    }
}
