<?php

use App\Models\Setting;

class ArtisanController extends BaseController
{
    private $artisanPath;
    private $phpBinary;

    public function __construct()
    {
        parent::__construct();
        $this->artisanPath = APP_ROOT . '/artisan';
        $this->phpBinary = $this->detectPhpBinary();

        // Solo amministratori possono accedere
        $this->requirePermission('admin');
    }

    /**
     * Rileva il percorso del binario PHP CLI
     */
    private function detectPhpBinary()
    {
        try {
        // Controlla prima se c'è un percorso configurato in settings
        $configuredPath = Setting::getValue('php_cli_path');
        if ($configuredPath && is_executable($configuredPath)) {
            error_log("Using configured PHP CLI path: $configuredPath");
            return $configuredPath;
        }

        // Auto-rilevamento
        $phpPath = PHP_BINARY;
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        // Su Windows, se PHP_BINARY è httpd.exe (Apache), cerca php.exe
        if ($isWindows && (strpos($phpPath, 'httpd.exe') !== false || strpos($phpPath, 'apache') !== false)) {
            // Se siamo in XAMPP, php.exe è in C:/xampp/php/php.exe
            $apacheDir = dirname(dirname($phpPath)); // Risale da apache/bin a xampp
            $phpCliPath = $apacheDir . '/php/php.exe';

            if (file_exists($phpCliPath)) {
                $phpPath = $phpCliPath;
                error_log("Detected PHP CLI from Apache path: $phpPath");
                Setting::setValue('php_cli_path', $phpPath);
                return $phpPath;
            }
        }

        // Controlla se è php-fpm (Linux) e cerca il CLI nella stessa directory
        if (!$isWindows && strpos($phpPath, 'php-fpm') !== false) {
            // Su Aruba, se php-fpm è in /php8.4/sbin/php-fpm, il CLI è in /php8.4/bin/php
            $phpDir = dirname(dirname($phpPath)); // Risale di 2 livelli
            $cliPath = $phpDir . '/bin/php';

            if (is_executable($cliPath)) {
                $phpPath = $cliPath;
                error_log("Detected PHP CLI from php-fpm path: $phpPath");
                Setting::setValue('php_cli_path', $phpPath);
                return $phpPath;
            }
        }

        // Se ancora non è valido, prova percorsi comuni
        if (empty($phpPath) || !is_executable($phpPath) || strpos($phpPath, 'php-fpm') !== false || strpos($phpPath, 'httpd.exe') !== false) {

            if ($isWindows) {
                $commonPaths = [
                    'C:/xampp/php/php.exe',         // XAMPP default
                    'C:/wamp/bin/php/php8.4/php.exe', // WAMP
                    'C:/wamp64/bin/php/php8.4/php.exe',
                    'C:/laragon/bin/php/php84/php.exe', // Laragon
                    PHP_BINARY,                     // Usa il corrente
                    'php'                           // Fallback al PATH
                ];
            } else {
                $commonPaths = [
                    '/php8.4/bin/php',      // Aruba PHP 8.4
                    '/php8.3/bin/php',      // Aruba PHP 8.3
                    '/php8.2/bin/php',      // Aruba PHP 8.2
                    '/php8.1/bin/php',      // Aruba PHP 8.1
                    '/php8.0/bin/php',      // Aruba PHP 8.0
                    '/usr/bin/php',         // Standard Linux
                    '/usr/local/bin/php',   // Alternative
                    '/opt/php/bin/php',     // Custom install
                    'php'                   // Fallback al PATH
                ];
            }

            foreach ($commonPaths as $path) {
                // Prova ad eseguire php --version per verificare che sia CLI
                $testCommand = "$path --version 2>&1";
                @exec($testCommand, $output, $returnCode);

                // Verifica che NON sia php-fpm
                if ($returnCode === 0 && !empty($output)) {
                    $outputStr = implode(' ', $output);
                    if (strpos($outputStr, 'php-fpm') === false && strpos($outputStr, 'PHP') !== false) {
                        $phpPath = $path;
                        error_log("Found PHP CLI at: $phpPath");
                        // Salva il percorso rilevato nelle settings
                        Setting::setValue('php_cli_path', $phpPath);
                        break;
                    }
                }
                $output = []; // Reset per il prossimo test
            }
        }

        return $phpPath ?: 'php';
        } catch (Throwable $e) {
            error_log("ArtisanController detectPhpBinary error: " . $e->getMessage());
            return PHP_BINARY ?: 'php';
        }
    }

    public function index()
    {
        $this->requireAuth();

        $commands = $this->getAvailableCommands();

        $data = [
            'pageTitle' => 'Artisan Console - ' . APP_NAME,
            'commands' => $commands,
        ];

        $this->render('artisan/index', $data);
    }

    private function getAvailableCommands()
    {
        try {
            $artisanContent = file_get_contents($this->artisanPath);

            // Estrai l'array dei comandi con una regex
            if (preg_match('/private \$commands = ([^;]+);/s', $artisanContent, $matches)) {
                // Esegui il codice PHP dell'array per ottenerlo come variabile
                $commandsArray = @ @eval("return {$matches[1]};");
                if (is_array($commandsArray)) {
                    return $commandsArray;
                }
            }
        } catch (Throwable $e) {
            // Fallback in caso di errore
            error_log("ArtisanController: Impossibile parsare i comandi da file artisan: " . $e->getMessage());
        }

        // Fallback a una lista statica se il parsing fallisce
        return [
            'migrate:status' => 'Mostra stato migrazioni',
            'migrate' => 'Esegui migrazioni pendenti',
            'migrate:rollback' => 'Rollback ultima batch migrazioni',
            'cache:clear' => 'Pulisci cache applicazione',
        ];
    }

    public function execute()
    {
        try {
        $input = null;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method Not Allowed'], 405);
            return;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
        } else {
            $input = $_POST;
        }

        $command = $input['command'] ?? '';
        if (!$command) {
            $this->json(['success' => false, 'message' => 'Command is required']);
            return;
        }

        // Validazione dinamica del comando
        $availableCommands = $this->getAvailableCommands();
        $commandParts = explode(' ', $command);
        $baseCommand = $commandParts[0];

        if (!array_key_exists($baseCommand, $availableCommands)) {
            $this->json(['success' => false, 'output' => "Comando non autorizzato: $baseCommand"]);
            return;
        }

        // Esecuzione del comando usando il binario PHP rilevato
        $fullCommand = escapeshellarg($this->phpBinary) . " " . escapeshellarg($this->artisanPath) . " " . $command;

        // Log del comando per debug
        error_log("Artisan executing: $fullCommand");
        error_log("PHP Binary: " . $this->phpBinary);

        // Esegui il comando e cattura l'output
        ob_start();
        passthru($fullCommand . " 2>&1", $exitCode);
        $output = ob_get_clean();

        // Se il comando fallisce, includi informazioni di debug
        if ($exitCode !== 0) {
            $debugInfo = "\n\n--- Debug Info ---\n";
            $debugInfo .= "PHP Binary: " . $this->phpBinary . "\n";
            $debugInfo .= "Exit Code: $exitCode\n";
            $debugInfo .= "Full Command: $fullCommand\n";
            $output .= $debugInfo;
        }

        $this->json([
            'success' => $exitCode === 0,
            'output' => $output ?: "Comando eseguito senza output",
            'command' => $command,
            'exit_code' => $exitCode
        ]);
        } catch (Throwable $e) {
            error_log("ArtisanController execute error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->json(['success' => false, 'message' => 'Errore interno: ' . $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return;
        }
    }
}