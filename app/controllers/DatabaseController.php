<?php
/**
 * Database Controller
 * Gestisce l'amministrazione database con sistema di migrazioni moderno
 */

use App\Models\Setting;

class DatabaseController extends BaseController
{
    /**
     * Mostra la dashboard database
     */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('dbsql');

        try {
            // Statistiche database
            $stats = $this->getDatabaseStats();

            // Lista tabelle con informazioni
            $tables = $this->getTablesInfo();

            // Migrazioni disponibili e applicate
            // $migrations = $this->getMigrationsStatus(); // Moved to MigrationController

            $data = [
                'pageTitle' => 'Database Manager - ' . APP_NAME,
                'stats' => $stats,
                'tables' => $tables,
                // 'migrations' => $migrations // Moved to MigrationController
            ];

            $this->render('database.index', $data);

        } catch (Exception $e) {
            error_log("Database index error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante il caricamento del database manager.';
            $this->redirect($this->url('/'));
        }
    }

    /**
     * Visualizza dati tabella specifica
     */
    public function table($tableName)
    {
        $this->requireAuth();
        $this->requirePermission('dbsql');

        $page = max(1, (int) ($this->input('page') ?? 1));
        $perPage = Setting::getInt('pagination_database', 50);
        $search = $this->input('search');

        try {
            // Verifica esistenza tabella
            if (!$this->tableExists($tableName)) {
                $_SESSION['alert_error'] = "Tabella '{$tableName}' non trovata.";
                $this->redirect($this->url('/database'));
                return;
            }

            // Struttura tabella
            $structure = $this->getTableStructure($tableName);

            // Dati con paginazione
            $tableData = $this->getTableData($tableName, $page, $perPage, $search);

            // Informazioni tabella
            $tableInfo = $this->getTableInfo($tableName);

            $data = [
                'pageTitle' => "Tabella {$tableName} - Database - " . APP_NAME,
                'tableName' => $tableName,
                'structure' => $structure,
                'tableData' => $tableData['data'],
                'currentPage' => $page,
                'totalPages' => $tableData['totalPages'],
                'totalRecords' => $tableData['total'],
                'perPage' => $perPage,
                'search' => $search,
                'tableInfo' => $tableInfo
            ];

            $this->render('database.table', $data);

        } catch (Exception $e) {
            error_log("Database table error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante il caricamento della tabella.';
            $this->redirect($this->url('/database'));
        }
    }

    /**
     * Console SQL
     */
    public function console()
    {
        $this->requireAuth();
        $this->requirePermission('dbsql');

        // Solo per  Admin
        if (!$this->isAdmin()) {
            $_SESSION['alert_error'] = 'Accesso riservato agli  Admin.';
            $this->redirect($this->url('/database'));
            return;
        }

        $data = [
            'pageTitle' => 'SQL Console - Database - ' . APP_NAME,
            'savedQueries' => $this->getSavedQueries()
        ];

        $this->render('database.console', $data);
    }

    /**
     * Esegue query SQL
     */
    public function executeQuery()
    {
        $this->requireAuth();
        $this->requirePermission('dbsql');

        if (!$this->isAdmin()) {
            $this->json(['error' => 'Accesso riservato agli  Admin'], 403);
            return;
        }

        if (!$this->isPost()) {
            $this->json(['error' => 'Metodo non consentito'], 405);
            return;
        }

        $query = trim($this->input('query'));
        $saveQuery = (bool) $this->input('save_query');
        $queryName = $this->input('query_name');

        if (!$query) {
            $this->json(['error' => 'Query SQL richiesta']);
            return;
        }

        try {
            // Log della query
            $this->logActivity('DATABASE', 'SQL_EXECUTE', 'Query SQL eseguita', $query);

            // Determina tipo query
            $queryType = $this->getQueryType($query);

            if ($queryType === 'SELECT' || $queryType === 'SHOW' || $queryType === 'DESCRIBE') {
                // Query di lettura
                $result = $this->db->fetchAll($query);

                if (empty($result)) {
                    $this->json([
                        'success' => true,
                        'type' => 'select',
                        'message' => 'Query eseguita. Nessun risultato.',
                        'data' => [],
                        'columns' => []
                    ]);
                    return;
                }

                $columns = array_keys($result[0]);

                $this->json([
                    'success' => true,
                    'type' => 'select',
                    'data' => $result,
                    'columns' => $columns,
                    'count' => count($result)
                ]);

            } else {
                // Query di scrittura
                $affectedRows = $this->db->execute($query);

                $this->json([
                    'success' => true,
                    'type' => 'modify',
                    'message' => "Query eseguita. {$affectedRows} righe interessate.",
                    'affected_rows' => $affectedRows
                ]);
            }

            // Salva query se richiesto
            if ($saveQuery && $queryName) {
                $this->saveQuery($queryName, $query);
            }

        } catch (Exception $e) {
            error_log("SQL execution error: " . $e->getMessage());
            $this->json(['error' => 'Errore SQL: ' . $e->getMessage()]);
        }
    }

    /**
     * Crea nuovo record
     */
    public function createRecord()
    {
        $this->requireAuth();
        $this->requirePermission('dbsql');

        if (!$this->isPost()) {
            $this->json(['error' => 'Metodo non consentito'], 405);
            return;
        }

        $tableName = $this->input('table');
        $data = json_decode($this->input('data'), true);

        if (!$tableName || !$this->tableExists($tableName)) {
            $this->json(['error' => 'Tabella non valida']);
            return;
        }

        if (!$data) {
            $this->json(['error' => 'Dati non validi']);
            return;
        }

        try {
            $columns = array_keys($data);
            $placeholders = str_repeat('?,', count($columns) - 1) . '?';

            $sql = "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES ({$placeholders})";

            $this->db->execute($sql, array_values($data));

            $this->logActivity('DATABASE', 'RECORD_CREATE', "Record creato in tabella {$tableName}", json_encode($data));

            $this->json(['success' => true, 'message' => 'Record creato con successo']);

        } catch (Exception $e) {
            error_log("Create record error: " . $e->getMessage());
            $this->json(['error' => 'Errore durante la creazione: ' . $e->getMessage()]);
        }
    }

    /**
     * Aggiorna record esistente
     */
    public function updateRecord()
    {
        $this->requireAuth();
        $this->requirePermission('dbsql');

        if (!$this->isPost()) {
            $this->json(['error' => 'Metodo non consentito'], 405);
            return;
        }

        // Leggi il body JSON
        $inputRaw = file_get_contents('php://input');
        $input = json_decode($inputRaw, true);

        if (!$input || !is_array($input)) {
            $this->json(['error' => 'Formato richiesta non valido: ' . json_last_error_msg()]);
            return;
        }

        $tableName = $input['table'] ?? null;
        $id = $input['id'] ?? null;
        $data = $input['data'] ?? null;

        if (!$tableName || !$this->tableExists($tableName)) {
            $this->json(['error' => 'Tabella non valida']);
            return;
        }

        if (!$id) {
            $this->json(['error' => 'ID richiesto']);
            return;
        }

        if (!$data || !is_array($data) || empty($data)) {
            $this->json(['error' => 'Dati non validi o vuoti']);
            return;
        }

        try {
            // Trova primary key
            $structure = $this->getTableStructure($tableName);
            $primaryKey = null;

            foreach ($structure as $column) {
                if ($column['Key'] === 'PRI') {
                    $primaryKey = $column['Field'];
                    break;
                }
            }

            if (!$primaryKey) {
                $this->json(['error' => 'Tabella senza primary key']);
                return;
            }

            // Escludi primary key e campi auto_increment dall'update
            $setClause = [];
            $values = [];
            $skippedFields = [];

            foreach ($data as $column => $value) {
                // Trova info colonna
                $columnInfo = null;
                foreach ($structure as $col) {
                    if ($col['Field'] === $column) {
                        $columnInfo = $col;
                        break;
                    }
                }

                // Salta primary key e auto_increment
                if ($column === $primaryKey || ($columnInfo && $columnInfo['Extra'] === 'auto_increment')) {
                    $skippedFields[] = $column;
                    continue;
                }

                $setClause[] = "`{$column}` = ?";
                $values[] = $value;
            }

            if (empty($setClause)) {
                $this->json(['error' => 'Nessun campo modificabile trovato']);
                return;
            }

            $values[] = $id;

            $sql = "UPDATE `{$tableName}` SET " . implode(', ', $setClause) . " WHERE `{$primaryKey}` = ?";

            $affectedRows = $this->db->execute($sql, $values);

            $this->logActivity('DATABASE', 'RECORD_UPDATE', "Record aggiornato in tabella {$tableName}", "ID: {$id}");

            if ($affectedRows > 0) {
                $this->json(['success' => true, 'message' => 'Record aggiornato con successo']);
            } else {
                // Nessuna riga modificata può significare che i dati sono identici
                $this->json(['success' => true, 'message' => 'Nessuna modifica necessaria (i dati sono identici)']);
            }

        } catch (Exception $e) {
            error_log("Update record error: " . $e->getMessage());
            $this->json(['error' => 'Errore durante l\'aggiornamento: ' . $e->getMessage()]);
        }
    }

    /**
     * Elimina record
     */
    public function deleteRecord()
    {
        $this->requireAuth();
        $this->requirePermission('dbsql');

        if (!$this->isPost()) {
            $this->json(['error' => 'Metodo non consentito'], 405);
            return;
        }

        $tableName = $this->input('table');
        $id = $this->input('id');

        if (!$tableName || !$this->tableExists($tableName)) {
            $this->json(['error' => 'Tabella non valida']);
            return;
        }

        if (!$id) {
            $this->json(['error' => 'ID richiesto']);
            return;
        }

        try {
            // Trova primary key
            $structure = $this->getTableStructure($tableName);
            $primaryKey = null;

            foreach ($structure as $column) {
                if ($column['Key'] === 'PRI') {
                    $primaryKey = $column['Field'];
                    break;
                }
            }

            if (!$primaryKey) {
                $this->json(['error' => 'Tabella senza primary key']);
                return;
            }

            $sql = "DELETE FROM `{$tableName}` WHERE `{$primaryKey}` = ?";
            $affectedRows = $this->db->execute($sql, [$id]);

            $this->logActivity('DATABASE', 'RECORD_DELETE', "Record eliminato da tabella {$tableName}", "ID: {$id}");

            if ($affectedRows > 0) {
                $this->json(['success' => true, 'message' => 'Record eliminato con successo']);
            } else {
                $this->json(['error' => 'Record non trovato']);
            }

        } catch (Exception $e) {
            error_log("Delete record error: " . $e->getMessage());
            $this->json(['error' => 'Errore durante l\'eliminazione: ' . $e->getMessage()]);
        }
    }

    /**
     * Recupera singolo record
     */
    public function getRecord()
    {
        $this->requireAuth();
        $this->requirePermission('dbsql');

        $tableName = $this->input('table');
        $id = $this->input('id');

        if (!$tableName || !$this->tableExists($tableName)) {
            $this->json(['error' => 'Tabella non valida']);
            return;
        }

        if (!$id) {
            $this->json(['error' => 'ID richiesto']);
            return;
        }

        try {
            // Trova primary key
            $structure = $this->getTableStructure($tableName);
            $primaryKey = null;

            foreach ($structure as $column) {
                if ($column['Key'] === 'PRI') {
                    $primaryKey = $column['Field'];
                    break;
                }
            }

            if (!$primaryKey) {
                $this->json(['error' => 'Tabella senza primary key']);
                return;
            }

            $record = $this->db->fetch("SELECT * FROM `{$tableName}` WHERE `{$primaryKey}` = ?", [$id]);

            if ($record) {
                $this->json(['success' => true, 'data' => $record]);
            } else {
                $this->json(['error' => 'Record non trovato']);
            }

        } catch (Exception $e) {
            error_log("Get record error: " . $e->getMessage());
            $this->json(['error' => 'Errore durante il recupero: ' . $e->getMessage()]);
        }
    }

    /**
     * Preview tabella (API per caricamento AJAX)
     */
    public function tablePreview()
    {
        $this->requireAuth();
        $this->requirePermission('dbsql');

        if (!$this->isPost()) {
            $this->json(['error' => 'Metodo non consentito'], 405);
            return;
        }

        $tableName = $this->input('table');
        $page = max(1, (int) ($this->input('page') ?? 1));
        $perPage = Setting::getInt('pagination_database', 50);
        $search = $this->input('search');

        if (!$tableName || !$this->tableExists($tableName)) {
            $this->json(['error' => 'Tabella non valida']);
            return;
        }

        try {
            // Struttura tabella
            $structure = $this->getTableStructure($tableName);

            // Dati con paginazione
            $tableData = $this->getTableData($tableName, $page, $perPage, $search);

            // Informazioni tabella
            $tableInfo = $this->getTableInfo($tableName);

            $this->json([
                'success' => true,
                'table' => $tableName,
                'structure' => $structure,
                'data' => $tableData['data'],
                'currentPage' => $page,
                'totalPages' => $tableData['totalPages'],
                'totalRecords' => $tableData['total'],
                'perPage' => $perPage,
                'tableInfo' => $tableInfo
            ]);

        } catch (Exception $e) {
            error_log("Table preview error: " . $e->getMessage());
            $this->json(['error' => 'Errore durante il caricamento: ' . $e->getMessage()]);
        }
    }

    /**
     * Operazioni tabella
     */
    public function tableOperation()
    {
        $this->requireAuth();
        $this->requirePermission('dbsql');

        if (!$this->isAdmin()) {
            $this->json(['error' => 'Operazione riservata agli  Admin'], 403);
            return;
        }

        if (!$this->isPost()) {
            $this->json(['error' => 'Metodo non consentito'], 405);
            return;
        }

        $tableName = $this->input('table');
        $operation = $this->input('operation');

        if (!$tableName || !$this->tableExists($tableName)) {
            $this->json(['error' => 'Tabella non valida']);
            return;
        }

        try {
            switch ($operation) {
                case 'optimize':
                    $this->db->execute("OPTIMIZE TABLE `{$tableName}`");
                    $message = "Tabella {$tableName} ottimizzata con successo";
                    break;

                case 'repair':
                    $this->db->execute("REPAIR TABLE `{$tableName}`");
                    $message = "Tabella {$tableName} riparata con successo";
                    break;

                case 'truncate':
                    $this->db->execute("TRUNCATE TABLE `{$tableName}`");
                    $message = "Tabella {$tableName} svuotata con successo";
                    break;

                default:
                    $this->json(['error' => 'Operazione non valida']);
                    return;
            }

            $this->logActivity('DATABASE', 'TABLE_OPERATION', $message, "Operazione: {$operation} su tabella {$tableName}");
            $this->json(['success' => true, 'message' => $message]);

        } catch (Exception $e) {
            error_log("Table operation error: " . $e->getMessage());
            $this->json(['error' => 'Errore durante l\'operazione: ' . $e->getMessage()]);
        }
    }

    /**
     * Export tabella
     */
    public function export()
    {
        $this->requireAuth();
        $this->requirePermission('dbsql');

        $tableName = $this->input('table');
        $format = $this->input('format', 'csv');

        if (!$tableName || !$this->tableExists($tableName)) {
            $_SESSION['alert_error'] = 'Tabella non valida.';
            $this->redirect($this->url('/database'));
            return;
        }

        try {
            $data = $this->db->fetchAll("SELECT * FROM `{$tableName}`");

            $filename = "{$tableName}_" . date('Y-m-d_H-i-s');

            switch ($format) {
                case 'csv':
                    $this->exportCSV($data, $filename);
                    break;
                case 'json':
                    $this->exportJSON($data, $filename);
                    break;
                case 'sql':
                    $this->exportSQL($tableName, $data, $filename);
                    break;
                default:
                    $_SESSION['alert_error'] = 'Formato export non valido.';
                    $this->redirect($this->url('/database'));
                    return;
            }

            $this->logActivity('DATABASE', 'EXPORT_TABLE', "Export tabella {$tableName}", "Formato: {$format}");

        } catch (Exception $e) {
            error_log("Export error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante l\'export.';
            $this->redirect($this->url('/database'));
        }
    }

    // Migration methods moved to MigrationController

    /**
     * Backup database
     */
    public function backup()
    {
        $this->requireAuth();
        $this->requireAdmin();

        try {
            $filename = 'backup_' . DB_NAME . '_' . date('Y-m-d_H-i-s') . '.sql';
            $this->createDatabaseBackup($filename);

            $this->logActivity('DATABASE', 'BACKUP', 'Backup database creato', "File: {$filename}");

        } catch (Exception $e) {
            error_log("Backup error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante la creazione del backup.';
            $this->redirect($this->url('/database'));
        }
    }

    // === METODI PRIVATI ===

    /**
     * Ottiene statistiche database
     */
    private function getDatabaseStats()
    {
        $stats = [];

        // Numero tabelle
        $stats['tables_count'] = $this->db->fetch("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ?", [DB_NAME])['count'];

        // Dimensione database
        $sizeResult = $this->db->fetch("
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size_mb 
            FROM information_schema.tables 
            WHERE table_schema = ?
        ", [DB_NAME]);
        $stats['size'] = $sizeResult['size_mb'] . ' MB';

        // Versione MySQL
        $stats['mysql_version'] = $this->db->fetch("SELECT VERSION() as version")['version'];

        // Engine più usato
        $engineResult = $this->db->fetch("
            SELECT engine, COUNT(*) as count
            FROM information_schema.tables 
            WHERE table_schema = ?
            GROUP BY engine 
            ORDER BY count DESC 
            LIMIT 1
        ", [DB_NAME]);
        $stats['main_engine'] = $engineResult['engine'] ?? 'N/A';

        return $stats;
    }

    /**
     * Ottiene informazioni tabelle
     */
    private function getTablesInfo()
    {
        return $this->db->fetchAll("
            SELECT 
                table_name,
                table_rows,
                ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb,
                engine,
                table_collation,
                create_time,
                update_time
            FROM information_schema.tables 
            WHERE table_schema = ?
            ORDER BY table_name
        ", [DB_NAME]);
    }

    /**
     * Verifica esistenza tabella
     */
    private function tableExists($tableName)
    {
        $result = $this->db->fetch("
            SELECT 1 FROM information_schema.tables 
            WHERE table_schema = ? AND table_name = ?
        ", [DB_NAME, $tableName]);

        return !empty($result);
    }

    /**
     * Ottiene struttura tabella
     */
    private function getTableStructure($tableName)
    {
        return $this->db->fetchAll("DESCRIBE `{$tableName}`");
    }

    /**
     * Ottiene dati tabella con paginazione
     */
    private function getTableData($tableName, $page, $perPage, $search = null)
    {
        $offset = ($page - 1) * $perPage;

        // Query base
        $sql = "SELECT * FROM `{$tableName}`";
        $params = [];

        // Filtro ricerca se presente
        if ($search) {
            $columns = $this->getTableStructure($tableName);
            $searchConditions = [];

            foreach ($columns as $column) {
                $searchConditions[] = "`{$column['Field']}` LIKE ?";
                $params[] = "%{$search}%";
            }

            $sql .= " WHERE " . implode(' OR ', $searchConditions);
        }

        // Count totale
        $countSql = "SELECT COUNT(*) as total FROM `{$tableName}`";
        if ($search) {
            $countSql .= " WHERE " . implode(' OR ', $searchConditions);
        }

        $totalResult = $this->db->fetch($countSql, $params);
        $total = $totalResult['total'];

        // Dati paginati
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        $data = $this->db->fetchAll($sql, $params);

        return [
            'data' => $data,
            'total' => $total,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    /**
     * Ottiene informazioni specifiche tabella
     */
    private function getTableInfo($tableName)
    {
        return $this->db->fetch("
            SELECT 
                table_name,
                table_rows,
                avg_row_length,
                data_length,
                index_length,
                auto_increment,
                engine,
                table_collation,
                create_time,
                update_time,
                table_comment
            FROM information_schema.tables 
            WHERE table_schema = ? AND table_name = ?
        ", [DB_NAME, $tableName]);
    }

    /**
     * Determina tipo di query
     */
    private function getQueryType($query)
    {
        $query = trim(strtoupper($query));

        if (strpos($query, 'SELECT') === 0)
            return 'SELECT';
        if (strpos($query, 'SHOW') === 0)
            return 'SHOW';
        if (strpos($query, 'DESCRIBE') === 0)
            return 'DESCRIBE';
        if (strpos($query, 'INSERT') === 0)
            return 'INSERT';
        if (strpos($query, 'UPDATE') === 0)
            return 'UPDATE';
        if (strpos($query, 'DELETE') === 0)
            return 'DELETE';
        if (strpos($query, 'CREATE') === 0)
            return 'CREATE';
        if (strpos($query, 'DROP') === 0)
            return 'DROP';
        if (strpos($query, 'ALTER') === 0)
            return 'ALTER';

        return 'OTHER';
    }

    // CRUD operations moved to public methods above

    /**
     * Export Methods
     */
    private function exportCSV($data, $filename)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename . '.csv');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM per UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        if (!empty($data)) {
            // Header
            fputcsv($output, array_keys($data[0]));

            // Dati
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;
    }

    private function exportJSON($data, $filename)
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename . '.json');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function exportSQL($tableName, $data, $filename)
    {
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename . '.sql');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Header SQL
        echo "-- Export SQL per tabella {$tableName}\n";
        echo "-- Generato il " . date('Y-m-d H:i:s') . "\n\n";

        if (!empty($data)) {
            $columns = array_keys($data[0]);
            $columnsStr = '`' . implode('`, `', $columns) . '`';

            foreach ($data as $row) {
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = "'" . addslashes($value) . "'";
                    }
                }

                echo "INSERT INTO `{$tableName}` ({$columnsStr}) VALUES (" . implode(', ', $values) . ");\n";
            }
        }

        exit;
    }








    /**
     * Backup Methods
     */
    private function createDatabaseBackup($filename)
    {
        $backupPath = APP_ROOT . '/database/backups';

        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $filePath = $backupPath . '/' . $filename;

        // Get all tables
        $tables = $this->db->fetchAll("SHOW TABLES");

        $output = "-- Database Backup: " . DB_NAME . "\n";
        $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = reset($table);

            // Table structure
            $createTable = $this->db->fetch("SHOW CREATE TABLE `{$tableName}`");
            $output .= "-- Table: {$tableName}\n";
            $output .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $output .= $createTable['Create Table'] . ";\n\n";

            // Table data
            $data = $this->db->fetchAll("SELECT * FROM `{$tableName}`");

            if (!empty($data)) {
                $columns = array_keys($data[0]);
                $columnsStr = '`' . implode('`, `', $columns) . '`';

                $output .= "-- Data for table: {$tableName}\n";

                foreach ($data as $row) {
                    $values = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }

                    $output .= "INSERT INTO `{$tableName}` ({$columnsStr}) VALUES (" . implode(', ', $values) . ");\n";
                }

                $output .= "\n";
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";

        if (file_put_contents($filePath, $output) === false) {
            throw new Exception('Impossibile creare il file di backup');
        }

        // Download del file
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Length: ' . filesize($filePath));
        header('Pragma: no-cache');
        header('Expires: 0');

        readfile($filePath);
        exit;
    }

    /**
     * Query salvate
     */
    private function getSavedQueries()
    {
        // Per ora usa localStorage lato client, in futuro potrebbe essere database
        return [];
    }

    private function saveQuery($name, $query)
    {
        // Implementazione futura per salvare query nel database
        // Per ora gestito lato client
        return true;
    }
}