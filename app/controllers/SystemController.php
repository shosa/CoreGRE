<?php

use App\Models\Setting;

class SystemController extends BaseController
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
        if ($configuredPath && @is_executable($configuredPath)) {
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

            if (@is_executable($cliPath)) {
                $phpPath = $cliPath;
                error_log("Detected PHP CLI from php-fpm path: $phpPath");
                Setting::setValue('php_cli_path', $phpPath);
                return $phpPath;
            }
        }

        // Se ancora non è valido, prova percorsi comuni
        if (empty($phpPath) || !@is_executable($phpPath) || strpos($phpPath, 'php-fpm') !== false || strpos($phpPath, 'httpd.exe') !== false) {

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
            'pageTitle' => 'System Console - ' . APP_NAME,
            'commands' => $commands,
            'sshEnabled' => $this->isSshAvailable(),
        ];

        $this->render('system/index', $data);
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
        exec($fullCommand . " 2>&1", $outputLines, $exitCode);
        $output = implode("\n", $outputLines);

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

    /**
     * Esegue comandi shell SSH
     */
    public function executeShell()
    {
        try {
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
            $type = $input['type'] ?? 'artisan'; // artisan or shell

            if (!$command) {
                $this->json(['success' => false, 'message' => 'Command is required']);
                return;
            }

            // Se è un comando Artisan, usa il metodo esistente
            if ($type === 'artisan') {
                return $this->execute();
            }

            // Altrimenti esegui come comando shell
            $fullCommand = $command . " 2>&1";

            // Whitelist comandi per sicurezza
            $allowedCommands = ['ls', 'pwd', 'whoami', 'date', 'uptime', 'df', 'free', 'top', 'ps', 'netstat', 'ifconfig', 'ip', 'cat', 'tail', 'head', 'grep', 'find', 'du', 'hostname', 'uname'];

            $commandParts = explode(' ', $command);
            $baseCommand = $commandParts[0];

            // Blocca comandi pericolosi
            $blockedCommands = ['rm', 'rmdir', 'mv', 'cp', 'chmod', 'chown', 'kill', 'killall', 'shutdown', 'reboot', 'su', 'sudo', 'passwd'];

            if (in_array($baseCommand, $blockedCommands)) {
                $this->json(['success' => false, 'output' => "Comando bloccato per sicurezza: $baseCommand"]);
                return;
            }

            // Log del comando per sicurezza
            error_log("Shell executing: $fullCommand");

            // Esegui il comando
            exec($fullCommand, $outputLines, $exitCode);
            $output = implode("\n", $outputLines);

            $this->json([
                'success' => $exitCode === 0,
                'output' => $output ?: "Comando eseguito senza output",
                'command' => $command,
                'exit_code' => $exitCode
            ]);

        } catch (Throwable $e) {
            error_log("SystemController executeShell error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore: ' . $e->getMessage()]);
        }
    }

    /**
     * Verifica se SSH/shell è disponibile
     */
    private function isSshAvailable()
    {
        // Su Windows, verifica se PowerShell è disponibile
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true; // PowerShell è sempre disponibile su Windows
        }

        // Su Linux, verifica se shell è disponibile
        return function_exists('exec');
    }

    /**
     * Ritorna le metriche del server in formato JSON
     */
    public function metrics()
    {
        try {
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

            $metrics = [
                'cpu' => $this->getCpuUsage($isWindows),
                'memory' => $this->getMemoryUsage($isWindows),
                'disk' => $this->getDiskUsage(),
                'uptime' => $this->getUptime($isWindows),
                'load' => $this->getLoadAverage($isWindows),
                'php' => [
                    'version' => PHP_VERSION,
                    'memory_limit' => ini_get('memory_limit'),
                    'max_execution_time' => ini_get('max_execution_time'),
                ],
                'timestamp' => time()
            ];

            $this->json(['success' => true, 'data' => $metrics]);
        } catch (Throwable $e) {
            error_log("SystemController metrics error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Error fetching metrics']);
        }
    }

    private function getCpuUsage($isWindows)
    {
        if ($isWindows) {
            // Su Windows usa wmic
            @exec('wmic cpu get loadpercentage /format:value 2>nul', $output);
            foreach ($output as $line) {
                if (strpos($line, 'LoadPercentage=') !== false) {
                    $value = (int)trim(str_replace('LoadPercentage=', '', $line));
                    return max(0, min(100, $value)); // Clamp tra 0 e 100
                }
            }
        } else {
            // Su Linux usa top
            @exec("top -bn1 | grep 'Cpu(s)' | sed 's/.*, *\\([0-9.]*\\)%* id.*/\\1/' | awk '{print 100 - $1}'", $output);
            if (isset($output[0])) {
                $value = round((float)$output[0], 1);
                return max(0, min(100, $value)); // Clamp tra 0 e 100
            }
        }
        return 0;
    }

    private function getMemoryUsage($isWindows)
    {
        if ($isWindows) {
            // Su Windows
            @exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value', $output);
            $data = [];
            foreach ($output as $line) {
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line);
                    $data[trim($key)] = trim($value);
                }
            }
            if (isset($data['TotalVisibleMemorySize']) && isset($data['FreePhysicalMemory'])) {
                $total = (int)$data['TotalVisibleMemorySize'];
                $free = (int)$data['FreePhysicalMemory'];
                $used = $total - $free;
                return [
                    'total' => round($total / 1024, 2), // MB
                    'used' => round($used / 1024, 2),
                    'free' => round($free / 1024, 2),
                    'percent' => round(($used / $total) * 100, 1)
                ];
            }
        } else {
            // Su Linux
            @exec('free -m', $output);
            if (isset($output[1])) {
                $mem = preg_split('/\s+/', $output[1]);
                $total = (int)$mem[1];
                $used = (int)$mem[2];
                $free = (int)$mem[3];
                return [
                    'total' => $total,
                    'used' => $used,
                    'free' => $free,
                    'percent' => round(($used / $total) * 100, 1)
                ];
            }
        }
        return ['total' => 0, 'used' => 0, 'free' => 0, 'percent' => 0];
    }

    private function getDiskUsage()
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        // Su Windows usa C:\ non C:
        $path = $isWindows ? 'C:\\' : '/';

        $total = @disk_total_space($path);
        $free = @disk_free_space($path);

        if ($total === false || $free === false || $total == 0) {
            return ['total' => 0, 'used' => 0, 'free' => 0, 'percent' => 0];
        }

        $used = $total - $free;

        return [
            'total' => round($total / (1024 * 1024 * 1024), 2), // GB
            'used' => round($used / (1024 * 1024 * 1024), 2),
            'free' => round($free / (1024 * 1024 * 1024), 2),
            'percent' => round(($used / $total) * 100, 1)
        ];
    }

    private function getUptime($isWindows)
    {
        if ($isWindows) {
            @exec('net stats workstation | find "Statistics since"', $output);
            if (isset($output[0])) {
                return trim(str_replace('Statistics since', '', $output[0]));
            }
        } else {
            @exec('uptime -p', $output);
            if (isset($output[0])) {
                return $output[0];
            }
        }
        return 'N/A';
    }

    private function getLoadAverage($isWindows)
    {
        if (!$isWindows && function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return [
                '1min' => round($load[0], 2),
                '5min' => round($load[1], 2),
                '15min' => round($load[2], 2)
            ];
        }
        return ['1min' => 0, '5min' => 0, '15min' => 0];
    }
}