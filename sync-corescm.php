<?php
/**
 * CoreGre → CoreSCM Sync Script
 * Sincronizza dati tra CoreGre locale e CoreSCM su Aruba
 *
 * Uso: php sync-corescm.php
 * Cron: 15 * * * * cd /path/to/CoreGre && php sync-corescm.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Carica env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configurazione
$CORESCM_API_URL = $_ENV['CORESCM_API_URL'] ?? '';
$API_SECRET = $_ENV['CORESCM_API_SECRET'] ?? '';
$SYNC_ENABLED = filter_var($_ENV['CORESCM_SYNC_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN);
$SYNC_STATE_FILE = __DIR__ . '/storage/logs/corescm-sync-state.json';

// Tabelle da sincronizzare
$SYNC_TABLES = [
    'scm_laboratories',
    'scm_launches',
    'scm_launch_articles',
    'scm_launch_phases',
    'scm_progress_tracking',
    'scm_standard_phases',
    'scm_settings'
];

// Check se sincronizzazione abilitata
if (!$SYNC_ENABLED) {
    echo "CoreSCM Sync is disabled (CORESCM_SYNC_ENABLED=false)\n";
    exit(0);
}

if (empty($CORESCM_API_URL) || empty($API_SECRET)) {
    echo "ERROR: Missing CORESCM_API_URL or CORESCM_API_SECRET in .env\n";
    exit(1);
}

echo "===============================================\n";
echo "CoreGre → CoreSCM Sync\n";
echo "Started: " . date('Y-m-d H:i:s') . "\n";
echo "===============================================\n";

try {
    // Connessione DB locale
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Health check CoreSCM
    echo "\n[HEALTH] Checking CoreSCM availability...\n";
    $health = apiRequest($CORESCM_API_URL, 'health', 'GET', null, $API_SECRET);
    if ($health['success']) {
        echo "[HEALTH] ✓ CoreSCM is online (server: {$health['server']})\n";
    } else {
        throw new Exception("CoreSCM health check failed");
    }

    // Leggi ultimo sync timestamp
    $lastSync = getLastSyncTimestamp($SYNC_STATE_FILE);
    echo "\n[INFO] Last sync: $lastSync\n";

    // === STEP 1: PULL updates da CoreSCM → CoreGre ===
    echo "\n========== PULL FROM CORESCM ==========\n";
    $corescmUpdates = apiRequest($CORESCM_API_URL, 'get_updates', 'GET', ['since' => $lastSync], $API_SECRET);

    if ($corescmUpdates['success'] && !empty($corescmUpdates['data'])) {
        echo "[PULL] Received {$corescmUpdates['total_records']} records from CoreSCM\n";
        applyUpdatesToLocal($pdo, $corescmUpdates['data']);
        echo "[PULL] ✓ Applied updates to CoreGre local DB\n";
    } else {
        echo "[PULL] No new updates from CoreSCM\n";
    }

    // === STEP 2: PUSH updates da CoreGre → CoreSCM ===
    echo "\n========== PUSH TO CORESCM ==========\n";
    $localUpdates = getLocalUpdates($pdo, $SYNC_TABLES, $lastSync);
    $totalLocal = countRecords($localUpdates);

    if ($totalLocal > 0) {
        echo "[PUSH] Sending $totalLocal records to CoreSCM...\n";
        $pushResult = apiRequest($CORESCM_API_URL, 'push_updates', 'POST', $localUpdates, $API_SECRET);

        if ($pushResult['success']) {
            echo "[PUSH] ✓ Processed {$pushResult['processed']} records on CoreSCM\n";
            if (!empty($pushResult['errors'])) {
                echo "[PUSH] ⚠ Errors: " . count($pushResult['errors']) . "\n";
                foreach ($pushResult['errors'] as $error) {
                    echo "  - {$error['table']} #{$error['record_id']}: {$error['error']}\n";
                }
            }
        } else {
            throw new Exception("Push failed: " . ($pushResult['error'] ?? 'Unknown error'));
        }
    } else {
        echo "[PUSH] No local updates to push\n";
    }

    // Aggiorna timestamp sync
    $newTimestamp = date('Y-m-d H:i:s');
    saveLastSyncTimestamp($SYNC_STATE_FILE, $newTimestamp);

    // Statistiche finali
    echo "\n========== STATS ==========\n";
    $stats = apiRequest($CORESCM_API_URL, 'get_stats', 'GET', null, $API_SECRET);
    if ($stats['success']) {
        echo "CoreSCM Database:\n";
        echo "  - Laboratories: {$stats['stats']['laboratories']}\n";
        echo "  - Launches: {$stats['stats']['launches']} ({$stats['stats']['launches_active']} active)\n";
        echo "  - Progress Updates: {$stats['stats']['progress_updates']}\n";
        echo "  - Last Update: {$stats['stats']['last_update']}\n";
    }

    echo "\n===============================================\n";
    echo "CoreGre → CoreSCM Sync Completed\n";
    echo "Finished: " . date('Y-m-d H:i:s') . "\n";
    echo "===============================================\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    logError($e);
    exit(1);
}

// ========== FUNCTIONS ==========

function apiRequest($baseUrl, $action, $method = 'GET', $data = null, $apiSecret = '')
{
    $url = $baseUrl . '?action=' . $action;

    if ($method === 'GET' && is_array($data)) {
        foreach ($data as $key => $value) {
            $url .= '&' . $key . '=' . urlencode($value);
        }
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-API-Secret: ' . $apiSecret,
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
        throw new Exception("CURL Error: $curlError");
    }

    if ($httpCode !== 200) {
        throw new Exception("HTTP Error $httpCode: $response");
    }

    return json_decode($response, true);
}

function getLastSyncTimestamp($file)
{
    if (file_exists($file)) {
        $state = json_decode(file_get_contents($file), true);
        return $state['last_sync'] ?? '1970-01-01 00:00:00';
    }
    return '1970-01-01 00:00:00';
}

function saveLastSyncTimestamp($file, $timestamp)
{
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($file, json_encode([
        'last_sync' => $timestamp,
        'synced_at' => date('Y-m-d H:i:s')
    ]));
}

function getLocalUpdates($pdo, $tables, $since)
{
    $updates = [];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE updated_at > ?");
        $stmt->execute([$since]);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($records)) {
            $updates[$table] = $records;
        }
    }
    return $updates;
}

function applyUpdatesToLocal($pdo, $updates)
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

function countRecords($updates)
{
    $total = 0;
    foreach ($updates as $records) {
        $total += count($records);
    }
    return $total;
}

function logError($exception)
{
    $logFile = __DIR__ . '/storage/logs/corescm-sync-errors.log';
    $timestamp = date('Y-m-d H:i:s');
    $message = "[$timestamp] ERROR: " . $exception->getMessage() . "\n";
    $message .= $exception->getTraceAsString() . "\n\n";
    file_put_contents($logFile, $message, FILE_APPEND);
}
