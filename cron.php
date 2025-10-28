<?php
/**
 * Cron Entry Point
 * File unico da configurare nel cronjob del server
 *
 * Setup webserver cronjob:
 *   * * * * * php /path/to/webgre3/cron.php >> /dev/null 2>&1
 *
 * Oppure con log:
 *   * * * * * php /path/to/webgre3/cron.php >> /path/to/logs/cron.log 2>&1
 */

// Bootstrap applicazione
define('APP_ROOT', __DIR__);
require_once APP_ROOT . '/config/config.php';

// Carica CronManager e dipendenze
require_once APP_ROOT . '/core/CronManager.php';
require_once APP_ROOT . '/core/CronJob.php';
require_once APP_ROOT . '/core/CronSchedule.php';

// Carica modello CronLog
require_once APP_ROOT . '/app/models/CronLog.php';

// Autoload job cron
foreach (glob(APP_ROOT . '/app/cron/*.php') as $jobFile) {
    require_once $jobFile;
}

// Header output
echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║              WEBGRE3 CRON MANAGER - " . date('Y-m-d H:i:s') . "          ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n\n";

try {
    // Ottieni istanza CronManager
    $cronManager = CronManager::getInstance();

    // Carica configurazione job da file
    $configFile = APP_ROOT . '/config/cron.php';
    if (file_exists($configFile)) {
        $jobs = require $configFile;

        if (is_array($jobs)) {
            echo "📋 Caricamento job da configurazione...\n";

            foreach ($jobs as $jobClass) {
                if (class_exists($jobClass)) {
                    $job = new $jobClass();
                    $cronManager->register($job);
                    echo "  ✓ Registrato: {$jobClass}\n";
                } else {
                    echo "  ⚠ Classe non trovata: {$jobClass}\n";
                }
            }

            echo "\n";
        }
    } else {
        echo "⚠ File configurazione cron non trovato: {$configFile}\n";
        echo "  Creare il file per registrare job automaticamente.\n\n";
    }

    // Esegui tutti i job
    $results = $cronManager->run();

    // Riepilogo risultati
    echo "\n";
    echo "╔═══════════════════════════════════════════════════════════╗\n";
    echo "║                    RIEPILOGO ESECUZIONE                    ║\n";
    echo "╚═══════════════════════════════════════════════════════════╝\n";

    $totalJobs = count($results);
    $successfulJobs = count(array_filter($results, function($r) { return $r['status'] === 'success'; }));
    $failedJobs = count(array_filter($results, function($r) { return $r['status'] === 'failed'; }));
    $skippedJobs = count(array_filter($results, function($r) { return $r['status'] === 'skipped'; }));

    echo "Job totali:      {$totalJobs}\n";
    echo "✓ Successi:      {$successfulJobs}\n";
    echo "✗ Falliti:       {$failedJobs}\n";
    echo "⊝ Saltati:       {$skippedJobs}\n\n";

    if ($failedJobs > 0) {
        echo "❌ JOB FALLITI:\n";
        foreach ($results as $jobClass => $result) {
            if ($result['status'] === 'failed') {
                echo "  • {$jobClass}\n";
                echo "    Errore: " . ($result['error'] ?? 'Unknown') . "\n";
            }
        }
        echo "\n";
    }

    // Exit code: 0 se tutti i job sono ok, 1 se ci sono fallimenti
    exit($failedJobs > 0 ? 1 : 0);

} catch (Exception $e) {
    echo "\n";
    echo "╔═══════════════════════════════════════════════════════════╗\n";
    echo "║                     ERRORE CRITICO                         ║\n";
    echo "╚═══════════════════════════════════════════════════════════╝\n";
    echo "Messaggio: " . $e->getMessage() . "\n";
    echo "File:      " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";

    exit(1);
}
