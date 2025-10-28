<?php
/**
 * Database Backup Job
 * Crea backup automatico del database MySQL
 */

class DatabaseBackupJob extends CronJob
{
    private $backupDir = '/storage/backups/database';
    private $retention = 30; // Giorni di retention backup

    /**
     * Nome del job
     */
    public function name()
    {
        return 'Database Backup';
    }

    /**
     * Descrizione
     */
    public function description()
    {
        return "Backup automatico database MySQL con retention di {$this->retention} giorni";
    }

    /**
     * Schedule: Ogni giorno alle 2:00 AM
     */
    public function schedule()
    {
        return '0 2 * * *';
    }

    /**
     * Esecuzione job
     */
    public function handle()
    {
        $this->log("Inizio backup database...");

        // Verifica directory backup
        $backupPath = APP_ROOT . $this->backupDir;
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
            $this->log("Directory backup creata: {$backupPath}");
        }

        // Nome file backup
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = "{$backupPath}/backup_{$timestamp}.sql";
        $backupFileGz = "{$backupFile}.gz";

        try {
            // Comando mysqldump
            $host = DB_HOST;
            $port = DB_PORT;
            $database = DB_NAME;
            $user = DB_USER;
            $password = DB_PASS;

            // Escape password per shell
            $passwordEscaped = escapeshellarg($password);

            // Costruisci comando mysqldump
            $command = sprintf(
                'mysqldump --host=%s --port=%d --user=%s --password=%s --single-transaction --routines --triggers %s > %s 2>&1',
                escapeshellarg($host),
                $port,
                escapeshellarg($user),
                $passwordEscaped,
                escapeshellarg($database),
                escapeshellarg($backupFile)
            );

            $this->log("Esecuzione mysqldump...");

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new Exception("mysqldump fallito con codice {$returnCode}: " . implode("\n", $output));
            }

            // Verifica file creato
            if (!file_exists($backupFile) || filesize($backupFile) === 0) {
                throw new Exception("File backup non creato o vuoto");
            }

            $fileSize = filesize($backupFile);
            $fileSizeMB = round($fileSize / 1024 / 1024, 2);

            $this->log("Backup SQL creato: {$fileSizeMB} MB");

            // Comprimi con gzip
            $this->log("Compressione backup...");

            exec("gzip -9 " . escapeshellarg($backupFile), $gzipOutput, $gzipReturn);

            if ($gzipReturn === 0 && file_exists($backupFileGz)) {
                $compressedSize = filesize($backupFileGz);
                $compressedSizeMB = round($compressedSize / 1024 / 1024, 2);
                $compressionRatio = round((1 - $compressedSize / $fileSize) * 100, 1);

                $this->log("Backup compresso: {$compressedSizeMB} MB (riduzione {$compressionRatio}%)");

                $finalFile = $backupFileGz;
                $finalSize = $compressedSizeMB;
            } else {
                // Se compressione fallisce, usa file non compresso
                $this->log("Compressione fallita, mantenuto file non compresso", 'warning');
                $finalFile = $backupFile;
                $finalSize = $fileSizeMB;
            }

            // Pulizia backup vecchi
            $this->cleanOldBackups();

            $this->log("Backup completato con successo!");

            return [
                'backup_file' => basename($finalFile),
                'size_mb' => $finalSize,
                'database' => $database,
                'timestamp' => $timestamp
            ];

        } catch (Exception $e) {
            // Pulizia file parziali
            if (file_exists($backupFile)) {
                @unlink($backupFile);
            }
            if (file_exists($backupFileGz)) {
                @unlink($backupFileGz);
            }

            $this->log("Errore backup: " . $e->getMessage(), 'error');
            throw $e;
        }
    }

    /**
     * Pulisce backup vecchi
     */
    private function cleanOldBackups()
    {
        $backupPath = APP_ROOT . $this->backupDir;
        $cutoffTime = time() - ($this->retention * 24 * 60 * 60);
        $deleted = 0;

        $this->log("Pulizia backup più vecchi di {$this->retention} giorni...");

        $files = glob($backupPath . '/backup_*.sql*');

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                if (@unlink($file)) {
                    $deleted++;
                    $this->log("  Eliminato: " . basename($file), 'debug');
                }
            }
        }

        if ($deleted > 0) {
            $this->log("Eliminati {$deleted} backup vecchi");
        }
    }

    /**
     * Callback fallimento - Notifica admin
     */
    public function onFailure($exception)
    {
        $this->notify(
            'Database Backup Fallito',
            "Il backup del database è fallito:\n\n" . $exception->getMessage(),
            'email'
        );
    }
}
