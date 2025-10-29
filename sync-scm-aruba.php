<?php
/**
 * Script di Sincronizzazione SCM
 * Da eseguire su CoreGre locale via cron
 *
 * Sincronizza dati tra CoreGre locale e SCM standalone su Aruba
 *
 * Uso: php sync-scm-aruba.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Carica env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configurazione
$ARUBA_API_URL = $_ENV['ARUBA_SCM_API_URL'] ?? 'https://scm.tuodominio.com/api/sync.php';
$API_SECRET = $_ENV['ARUBA_SCM_API_SECRET'] ?? 'change-this-secret';
$SYNC_STATE_FILE = __DIR__ . '/storage/logs/scm-sync-state.json';

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

echo "[" . date('Y-m-d H:i:s') . "] SCM Sync Started\n";

try {
    // Connessione DB locale
    $pdo = new PDO(
        "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Leggi ultimo sync timestamp
    $lastSync = getLastSyncTimestamp();
    echo "Last sync: $lastSync\n";

    // === STEP 1: Pull updates da Aruba → CoreGre ===
    echo "\n[PULL] Downloading updates from Aruba...\n";
    $arubaUpdates = fetchArubaUpdates($lastSync);

    if (!empty($arubaUpdates)) {
        applyUpdatesToLocal($pdo, $arubaUpdates);
        echo "[PULL] Applied " . countRecords($arubaUpdates) . " records from Aruba\n";
    } else {
        echo "[PULL] No updates from Aruba\n";
    }

    // === STEP 2: Push updates da CoreGre → Aruba ===
    echo "\n[PUSH] Uploading updates to Aruba...\n";
    $localUpdates = getLocalUpdates($pdo, $lastSync);

    if (!empty($localUpdates)) {
        pushUpdatesToAruba($localUpdates);
        echo "[PUSH] Pushed " . countRecords($localUpdates) . " records to Aruba\n";
    } else {
        echo "[PUSH] No updates to push\n";
    }

    // Aggiorna timestamp sync
    saveLastSyncTimestamp(date('Y-m-d H:i:s'));

    echo "\n[" . date('Y-m-d H:i:s') . "] SCM Sync Completed Successfully\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

// ========== FUNCTIONS ==========

function getLastSyncTimestamp() {
    global $SYNC_STATE_FILE;
    if (file_exists($SYNC_STATE_FILE)) {
        $state = json_decode(file_get_contents($SYNC_STATE_FILE), true);
        return $state['last_sync'] ?? '1970-01-01 00:00:00';
    }
    return '1970-01-01 00:00:00';
}

function saveLastSyncTimestamp($timestamp) {
    global $SYNC_STATE_FILE;
    $dir = dirname($SYNC_STATE_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($SYNC_STATE_FILE, json_encode([
        'last_sync' => $timestamp,
        'synced_at' => date('Y-m-d H:i:s')
    ]));
}

function fetchArubaUpdates($since) {
    global $ARUBA_API_URL, $API_SECRET;

    $url = $ARUBA_API_URL . '?action=get_updates&since=' . urlencode($since);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-API-Secret: ' . $API_SECRET
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception("Failed to fetch from Aruba: HTTP $httpCode");
    }

    $data = json_decode($response, true);
    if (!$data['success']) {
        throw new Exception("Aruba API error: " . ($data['error'] ?? 'Unknown'));
    }

    return $data['data'];
}

function pushUpdatesToAruba($updates) {
    global $ARUBA_API_URL, $API_SECRET;

    $url = $ARUBA_API_URL . '?action=push_updates';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updates));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-API-Secret: ' . $API_SECRET,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception("Failed to push to Aruba: HTTP $httpCode");
    }

    $data = json_decode($response, true);
    if (!$data['success']) {
        throw new Exception("Aruba API error: " . ($data['error'] ?? 'Unknown'));
    }
}

function getLocalUpdates($pdo, $since) {
    global $SYNC_TABLES;

    $updates = [];
    foreach ($SYNC_TABLES as $table) {
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE updated_at > ?");
        $stmt->execute([$since]);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($records)) {
            $updates[$table] = $records;
        }
    }

    return $updates;
}

function applyUpdatesToLocal($pdo, $updates) {
    foreach ($updates as $table => $records) {
        foreach ($records as $record) {
            // UPSERT
            $columns = array_keys($record);
            $placeholders = array_fill(0, count($columns), '?');

            $updateParts = [];
            foreach ($columns as $col) {
                if ($col !== 'id') {
                    $updateParts[] = "$col = VALUES($col)";
                }
            }

            $sql = "INSERT INTO $table (" . implode(',', $columns) . ")
                    VALUES (" . implode(',', $placeholders) . ")
                    ON DUPLICATE KEY UPDATE " . implode(',', $updateParts);

            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($record));
        }
    }
}

function countRecords($updates) {
    $total = 0;
    foreach ($updates as $records) {
        $total += count($records);
    }
    return $total;
}
