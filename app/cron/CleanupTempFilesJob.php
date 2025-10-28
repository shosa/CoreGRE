<?php
/**
 * Cleanup Temp Files Job
 * Pulisce file temporanei vecchi di più di X giorni
 */

class CleanupTempFilesJob extends CronJob
{
    private $daysOld = 7;
    private $tempDirs = [
        '/storage/tmp',
        '/public/uploads/temp',
        '/storage/cache/old'
    ];

    /**
     * Nome del job
     */
    public function name()
    {
        return 'Cleanup Temp Files';
    }

    /**
     * Descrizione
     */
    public function description()
    {
        return "Elimina file temporanei più vecchi di {$this->daysOld} giorni";
    }

    /**
     * Schedule: Ogni giorno alle 3:00 AM
     */
    public function schedule()
    {
        return '0 3 * * *';
    }

    /**
     * Esecuzione job
     */
    public function handle()
    {
        $deletedFiles = 0;
        $freedSpace = 0;
        $errors = [];

        $this->log("Inizio pulizia file temporanei...");
        $cutoffTime = time() - ($this->daysOld * 24 * 60 * 60);

        foreach ($this->tempDirs as $dir) {
            $fullPath = APP_ROOT . $dir;

            if (!is_dir($fullPath)) {
                $this->log("Directory non trovata: {$fullPath}", 'warning');
                continue;
            }

            $this->log("Scansione directory: {$fullPath}");

            try {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getMTime() < $cutoffTime) {
                        $fileSize = $file->getSize();
                        $filePath = $file->getPathname();

                        if (@unlink($filePath)) {
                            $deletedFiles++;
                            $freedSpace += $fileSize;
                            $this->log("  Eliminato: " . basename($filePath), 'debug');
                        } else {
                            $errors[] = "Impossibile eliminare: {$filePath}";
                        }
                    }
                }

                // Rimuovi directory vuote
                foreach ($iterator as $file) {
                    if ($file->isDir() && $this->isDirEmpty($file->getPathname())) {
                        @rmdir($file->getPathname());
                    }
                }

            } catch (Exception $e) {
                $errors[] = "Errore scansione {$fullPath}: " . $e->getMessage();
                $this->log("Errore: " . $e->getMessage(), 'error');
            }
        }

        $freedSpaceMB = round($freedSpace / 1024 / 1024, 2);

        $this->log("Pulizia completata:");
        $this->log("  File eliminati: {$deletedFiles}");
        $this->log("  Spazio liberato: {$freedSpaceMB} MB");

        if (!empty($errors)) {
            $this->log("  Errori: " . count($errors), 'warning');
        }

        return [
            'deleted_files' => $deletedFiles,
            'freed_space_mb' => $freedSpaceMB,
            'errors' => $errors
        ];
    }

    /**
     * Verifica se directory è vuota
     */
    private function isDirEmpty($dir)
    {
        if (!is_readable($dir)) {
            return false;
        }

        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != '.' && $entry != '..') {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        return true;
    }
}
