<?php
/**
 * Configurazione principale dell'applicazione WEBGRE MVC
 */

// Impedisce accesso diretto al file
defined('APP_ROOT') or die('Access denied');

// Carica la classe Env
require_once APP_ROOT . '/core/Env.php';

// Carica il file .env
Env::load();

// Environment settings
define('APP_ENV', Env::get('APP_ENV', 'development'));
define('APP_DEBUG', Env::getBool('APP_DEBUG', true));
define('VER_NO', Env::get('VER_NO', '3.0.0'));
define('BUILD_NO', Env::get('BUILD_NO', '2025.09'));

// Database configuration
define('DB_HOST', Env::get('DB_HOST', 'localhost'));
define('DB_NAME', Env::get('DB_NAME', 'my_webgre'));
define('DB_USER', Env::get('DB_USER', 'root'));
define('DB_PASS', Env::get('DB_PASS', ''));
define('DB_CHARSET', Env::get('DB_CHARSET', 'utf8mb4'));
define('DB_PORT', Env::getInt('DB_PORT', 3306));

// Path configuration
define('APP_SUBDIRECTORY', Env::get('APP_SUBDIRECTORY', ''));

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

// Calcola il path base dell'applicazione
$scriptDir = dirname($scriptName);

// Se è definita una subdirectory specifica
if (APP_SUBDIRECTORY !== '') {
    // Verifica se siamo già nella subdirectory configurata
    if (basename($scriptDir) === APP_SUBDIRECTORY) {
        $basePath = $scriptDir;
    } else {
        // Cerca la subdirectory nel path o aggiungila
        $pathParts = explode('/', trim($scriptDir, '/'));
        $subIndex = array_search(APP_SUBDIRECTORY, $pathParts);

        if ($subIndex !== false) {
            $basePath = '/' . implode('/', array_slice($pathParts, 0, $subIndex + 1));
        } else {
            $basePath = $scriptDir . '/' . APP_SUBDIRECTORY;
        }
    }
} else {
    // Nessuna subdirectory configurata, usa il path dello script
    $basePath = $scriptDir;
}

// Normalizza il path
$basePath = rtrim($basePath, '/');
if (empty($basePath)) {
    $basePath = '';
}

// Definisce le costanti di path
define('BASE_URL', $protocol . '://' . $host . $basePath);
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}
define('PUBLIC_PATH', APP_ROOT . '/public');
define('VIEW_PATH', APP_ROOT . '/app/views');
define('CONTROLLER_PATH', APP_ROOT . '/app/controllers');
define('MODEL_PATH', APP_ROOT . '/app/models');

// Session configuration
define('SESSION_LIFETIME', Env::getInt('SESSION_LIFETIME', 7200));
define('REMEMBER_TOKEN_LIFETIME', Env::getInt('REMEMBER_TOKEN_LIFETIME', 2592000));

// Security settings
define('HASH_COST', Env::getInt('HASH_COST', 12));
define('CSRF_TOKEN_LENGTH', Env::getInt('CSRF_TOKEN_LENGTH', 32));

// Application settings
define('APP_NAME', Env::get('APP_NAME', 'WEBGRE'));
define('APP_VERSION', Env::get('APP_VERSION', '2.0.0'));
define('DEFAULT_CONTROLLER', Env::get('DEFAULT_CONTROLLER', 'Home'));
define('DEFAULT_ACTION', Env::get('DEFAULT_ACTION', 'index'));

// Timezone
date_default_timezone_set(Env::get('APP_TIMEZONE', 'Europe/Rome'));

// Upload settings
define('MAX_UPLOAD_SIZE', Env::get('MAX_UPLOAD_SIZE', '50M'));
define('ALLOWED_EXTENSIONS', Env::get('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx'));

// Cache settings
define('CACHE_DRIVER', Env::get('CACHE_DRIVER', 'file'));
define('CACHE_TTL', Env::getInt('CACHE_TTL', 3600));

// Logging settings
define('LOG_LEVEL', Env::get('LOG_LEVEL', 'debug'));
define('LOG_MAX_FILES', Env::getInt('LOG_MAX_FILES', 30));



// Error reporting in base all'environment
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Include Composer autoloader
if (file_exists(APP_ROOT . '/vendor/autoload.php')) {
    require_once APP_ROOT . '/vendor/autoload.php';
} else {
    // Fallback autoloader per le classi core
    spl_autoload_register(function ($className) {
        $paths = [
            APP_ROOT . '/core/',
            APP_ROOT . '/app/controllers/',
            APP_ROOT . '/app/models/',
            APP_ROOT . '/app/utils/'
        ];

        foreach ($paths as $path) {
            $file = $path . $className . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    });
}

// Inizializza Eloquent ORM
require_once APP_ROOT . '/core/EloquentBootstrap.php';
EloquentBootstrap::init();