<?php
/**
 * CoreSCM Sync Job
 * Sincronizza dati tra CoreGre locale e CoreSCM su Aruba
 */

class SyncCoreSCMJob extends CronJob
{
    private $apiUrl;
    private $apiSecret;
    private $syncEnabled;
    private $syncStateFile;
    private $syncTables = [
        'scm_laboratories',
        'scm_launches',
        'scm_launch_articles',
        'scm_launch_phases',
        'scm_progress_tracking',
        'scm_standard_phases',
        'scm_settings'
    ];

    /**
     * Nome del job
     */
    public function name()
    {
        return 'CoreSCM Sync';
    }

    /**
     * Descrizione
     */
    public function description()
    {
        return 'Sincronizzazione bidirezionale tra CoreGre locale e CoreSCM su Aruba';
    }

    /**
     * Schedule: Ogni 15 minuti
     */
    public function schedule()
    {
        return '*/15 * * * *';
    }

    /**
     * Timeout: 5 minuti
     */
    public function timeout()
    {
        return 300;
    }

    /**
     * Verifica se il job può essere eseguito
     */
    protected function canRun()
    {
        // Carica configurazione
        $this->apiUrl = $_ENV['CORESCM_API_URL'] ?? '';
        $this->apiSecret = $_ENV['CORESCM_API_SECRET'] ?? '';
        $this->syncEnabled = filter_var($_ENV['CORESCM_SYNC_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->syncStateFile = APP_ROOT . '/storage/logs/corescm-sync-state.json';

        if (!$this->syncEnabled) {
            $this->log("Sync disabilitato (CORESCM_SYNC_ENABLED=false)", 'info');
            return false;
        }

        if (empty($this->apiUrl) || empty($this->apiSecret)) {
            $this->log("Configurazione mancante: CORESCM_API_URL o CORESCM_API_SECRET non impostati", 'error');
            return false;
        }

        return true;
    }

    /**
     * Esecuzione job
     */
    public function handle()
    {
        $this->log("Inizio sincronizzazione CoreSCM...");

        // Connessione DB locale
        $pdo = $this->getDatabaseConnection();

        // Health check CoreSCM
        $this->log("Controllo disponibilità CoreSCM...");
        $health = $this->apiRequest('health', 'GET');

        if (!$health['success']) {
            throw new \Exception("CoreSCM non disponibile (health check failed)");
        }

        $this->log("CoreSCM online (server: {$health['server']})");

        // Leggi ultimo sync timestamp
        $lastSync = $this->getLastSyncTimestamp();
        $this->log("Ultimo sync: $lastSync");

        // === STEP 1: PULL updates da CoreSCM → CoreGre ===
        $this->log("--- PULL FROM CORESCM ---");
        $corescmUpdates = $this->apiRequest('get_updates', 'GET', ['since' => $lastSync]);

        $pulledRecords = 0;
        if ($corescmUpdates['success'] && !empty($corescmUpdates['data'])) {
            $pulledRecords = $corescmUpdates['total_records'];
            $this->log("Ricevuti {$pulledRecords} record da CoreSCM");
            $this->applyUpdatesToLocal($pdo, $corescmUpdates['data']);
            $this->log("Aggiornamenti applicati al DB locale");
        } else {
            $this->log("Nessun aggiornamento da CoreSCM");
        }

        // === STEP 2: PUSH updates da CoreGre → CoreSCM ===
        $this->log("--- PUSH TO CORESCM ---");
        $localUpdates = $this->getLocalUpdates($pdo, $lastSync);
        $totalLocal = $this->countRecords($localUpdates);

        $pushedRecords = 0;
        $pushErrors = [];

        if ($totalLocal > 0) {
            $this->log("Invio di {$totalLocal} record a CoreSCM...");
            $pushResult = $this->apiRequest('push_updates', 'POST', $localUpdates);

            if ($pushResult['success']) {
                $pushedRecords = $pushResult['processed'] ?? 0;
                $this->log("Processati {$pushedRecords} record su CoreSCM");

                if (!empty($pushResult['errors'])) {
                    $pushErrors = $pushResult['errors'];
                    $this->log("Errori durante push: " . count($pushErrors), 'warning');
                    foreach ($pushErrors as $error) {
                        $this->log("  - {$error['table']} #{$error['record_id']}: {$error['error']}", 'warning');
                    }
                }
            } else {
                throw new \Exception("Push fallito: " . ($pushResult['error'] ?? 'Unknown error'));
            }
        } else {
            $this->log("Nessun aggiornamento locale da inviare");
        }

        // Aggiorna timestamp sync
        $newTimestamp = date('Y-m-d H:i:s');
        $this->saveLastSyncTimestamp($newTimestamp);

        // Statistiche finali
        $stats = $this->apiRequest('get_stats', 'GET');
        $statsData = $stats['stats'] ?? [];

        $this->log("Sincronizzazione completata!");

        return [
            'pulled_records' => $pulledRecords,
            'pushed_records' => $pushedRecords,
            'push_errors' => count($pushErrors),
            'last_sync' => $lastSync,
            'new_sync' => $newTimestamp,
            'corescm_stats' => $statsData
        ];
    }

    /**
     * Connessione database locale
     */
    private function getDatabaseConnection()
    {
        try {
            $pdo = new \PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            return $pdo;
        } catch (\PDOException $e) {
            throw new \Exception("Errore connessione DB: " . $e->getMessage());
        }
    }

    /**
     * API Request a CoreSCM
     */
    private function apiRequest($action, $method = 'GET', $data = null)
    {
        $url = $this->apiUrl . '?action=' . $action;

        if ($method === 'GET' && is_array($data)) {
            foreach ($data as $key => $value) {
                $url .= '&' . $key . '=' . urlencode($value);
            }
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-Secret: ' . $this->apiSecret,
            'Content-Type: application/json'
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new \Exception("CURL Error: $curlError");
        }

        if ($httpCode !== 200) {
            throw new \Exception("HTTP Error $httpCode: $response");
        }

        return json_decode($response, true);
    }

    /**
     * Leggi ultimo sync timestamp
     */
    private function getLastSyncTimestamp()
    {
        if (file_exists($this->syncStateFile)) {
            $state = json_decode(file_get_contents($this->syncStateFile), true);
            return $state['last_sync'] ?? '1970-01-01 00:00:00';
        }
        return '1970-01-01 00:00:00';
    }

    /**
     * Salva timestamp sync
     */
    private function saveLastSyncTimestamp($timestamp)
    {
        $dir = dirname($this->syncStateFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->syncStateFile, json_encode([
            'last_sync' => $timestamp,
            'synced_at' => date('Y-m-d H:i:s')
        ]));
    }

    /**
     * Recupera aggiornamenti locali
     */
    private function getLocalUpdates($pdo, $since)
    {
        $updates = [];
        foreach ($this->syncTables as $table) {
            $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE updated_at > ?");
            $stmt->execute([$since]);
            $records = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($records)) {
                $updates[$table] = $records;
            }
        }
        return $updates;
    }

    /**
     * Applica aggiornamenti al DB locale
     */
    private function applyUpdatesToLocal($pdo, $updates)
    {
        foreach ($updates as $table => $records) {
            foreach ($records as $record) {
                $columns = array_keys($record);
                $placeholders = array_fill(0, count($columns), '?');

                $updateParts = [];
                foreach ($columns as $col) {
                    if ($col !== 'id') {
                        $updateParts[] = "`$col` = VALUES(`$col`)";
                    }
                }

                $sql = "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`)
                        VALUES (" . implode(',', $placeholders) . ")
                        ON DUPLICATE KEY UPDATE " . implode(', ', $updateParts);

                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_values($record));
            }
        }
    }

    /**
     * Conta record negli aggiornamenti
     */
    private function countRecords($updates)
    {
        $total = 0;
        foreach ($updates as $records) {
            $total += count($records);
        }
        return $total;
    }

    /**
     * Callback successo
     */
    public function onSuccess($result = null)
    {
        if ($result) {
            $this->log("Riepilogo sync:");
            $this->log("  - Record ricevuti da CoreSCM: {$result['pulled_records']}");
            $this->log("  - Record inviati a CoreSCM: {$result['pushed_records']}");

            if ($result['push_errors'] > 0) {
                $this->log("  - Errori durante push: {$result['push_errors']}", 'warning');
            }

            if (!empty($result['corescm_stats'])) {
                $stats = $result['corescm_stats'];
                $this->log("  - CoreSCM Launches: {$stats['launches']} ({$stats['launches_active']} attivi)");
            }
        }
    }

    /**
     * Callback fallimento
     */
    public function onFailure($exception)
    {
        $this->log("Sincronizzazione fallita: " . $exception->getMessage(), 'error');

        // Notifica admin solo se l'errore persiste
        $this->notify(
            'CoreSCM Sync Fallito',
            "La sincronizzazione con CoreSCM è fallita:\n\n" . $exception->getMessage(),
            'email'
        );
    }
}
