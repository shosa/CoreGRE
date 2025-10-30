<?php
/**
 * Test rapido per capire dove fallisce ArtisanController
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('APP_ROOT', __DIR__);
require_once __DIR__ . '/config/config.php';

echo "=== TEST ARTISAN CONTROLLER ===\n\n";

// Test 1: Verifica file artisan
echo "1. Verifica file artisan: ";
$artisanPath = APP_ROOT . '/artisan';
if (file_exists($artisanPath)) {
    echo "OK - file esiste\n";
} else {
    echo "ERRORE - file non trovato: $artisanPath\n";
}

// Test 2: Verifica classe Setting
echo "\n2. Verifica classe Setting: ";
try {
    if (class_exists('App\Models\Setting')) {
        echo "OK - classe esiste\n";

        // Test getValue
        echo "   2a. Test Setting::getValue(): ";
        $value = App\Models\Setting::getValue('php_cli_path');
        echo "OK - valore: " . ($value ?: 'NULL') . "\n";
    } else {
        echo "ERRORE - classe non trovata\n";
    }
} catch (Throwable $e) {
    echo "ERRORE - " . $e->getMessage() . "\n";
}

// Test 3: Verifica detectPhpBinary simulation
echo "\n3. Simula detectPhpBinary(): ";
try {
    $phpPath = PHP_BINARY;
    echo "\n   PHP_BINARY = $phpPath\n";

    $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    echo "   Is Windows: " . ($isWindows ? 'YES' : 'NO') . "\n";

    echo "   PHP_OS: " . PHP_OS . "\n";

} catch (Throwable $e) {
    echo "ERRORE - " . $e->getMessage() . "\n";
}

// Test 4: Verifica permessi admin
echo "\n4. Test permesso admin: ";
try {
    session_start();
    if (isset($_SESSION['user_id'])) {
        echo "User ID: {$_SESSION['user_id']}\n";

        // Controlla permessi
        if (isset($_SESSION['permissions'])) {
            echo "   Permessi: " . implode(', ', $_SESSION['permissions']) . "\n";

            if (in_array('admin', $_SESSION['permissions'])) {
                echo "   Ha permesso ADMIN: SI\n";
            } else {
                echo "   Ha permesso ADMIN: NO (questo causa 500!)\n";
            }
        } else {
            echo "   Nessun permesso in sessione\n";
        }
    } else {
        echo "Non autenticato\n";
    }
} catch (Throwable $e) {
    echo "ERRORE - " . $e->getMessage() . "\n";
}

// Test 5: Prova a instanziare il controller
echo "\n5. Test instanziazione ArtisanController: ";
try {
    require_once APP_ROOT . '/app/controllers/BaseController.php';
    require_once APP_ROOT . '/app/controllers/ArtisanController.php';

    $controller = new ArtisanController();
    echo "OK - controller creato\n";

} catch (Throwable $e) {
    echo "ERRORE - " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== FINE TEST ===\n";
