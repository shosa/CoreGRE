<?php
/**
 * Cron Manager
 * Gestisce lo scheduling e l'esecuzione centralizzata di job cron
 *
 * Usage:
 *   $manager = CronManager::getInstance();
 *   $manager->register(new CleanupTempFilesJob());
 *   $manager->run();
 */

use App\Models\CronLog;

class CronManager
{
    private static $instance = null;
    private $jobs = [];
    private $runningJobs = [];

    /**
     * Singleton pattern
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor per singleton
     */
    private function __construct()
    {
        // Carica job registrati se esistono in cache
        $this->loadRegisteredJobs();
    }

    /**
     * Registra un nuovo job
     *
     * @param CronJob $job Job da registrare
     * @return self
     */
    public function register(CronJob $job)
    {
        $jobClass = get_class($job);
        $this->jobs[$jobClass] = $job;

        $this->log("Job registrato: {$jobClass}", 'info');

        return $this;
    }

    /**
     * Esegue tutti i job che devono essere eseguiti ora
     *
     * @return array Risultati esecuzioni
     */
    public function run()
    {
        $now = new DateTime();
        $results = [];

        $this->log("=== Cron Manager avviato ===", 'info');
        $this->log("Data/Ora: " . $now->format('Y-m-d H:i:s'), 'info');
        $this->log("Job registrati: " . count($this->jobs), 'info');

        foreach ($this->jobs as $jobClass => $job) {
            try {
                // Verifica se il job deve essere eseguito
                if (!$this->shouldRun($job, $now)) {
                    $this->log("Job {$jobClass} saltato (non è il momento)", 'debug');
                    $results[$jobClass] = ['status' => 'skipped', 'reason' => 'schedule'];
                    continue;
                }

                // Verifica se il job è già in esecuzione (lock)
                if ($this->isLocked($job)) {
                    $this->log("Job {$jobClass} saltato (lock attivo)", 'warning');
                    $results[$jobClass] = ['status' => 'skipped', 'reason' => 'locked'];
                    continue;
                }

                // Esegui il job
                $results[$jobClass] = $this->executeJob($job);

            } catch (Exception $e) {
                $this->log("Errore durante esecuzione job {$jobClass}: " . $e->getMessage(), 'error');
                $results[$jobClass] = [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ];
            }
        }

        $this->log("=== Cron Manager terminato ===", 'info');

        return $results;
    }

    /**
     * Verifica se un job deve essere eseguito ora
     *
     * @param CronJob $job
     * @param DateTime $now
     * @return bool
     */
    private function shouldRun(CronJob $job, DateTime $now)
    {
        $schedule = $job->schedule();

        // Se schedule è null, esegui sempre
        if ($schedule === null) {
            return true;
        }

        // Parsing espressione cron
        return CronSchedule::isDue($schedule, $now);
    }

    /**
     * Verifica se un job è già in esecuzione (lock)
     *
     * @param CronJob $job
     * @return bool
     */
    private function isLocked(CronJob $job)
    {
        $jobClass = get_class($job);
        $lockFile = $this->getLockFilePath($jobClass);

        if (!file_exists($lockFile)) {
            return false;
        }

        // Verifica età lock (stale lock dopo 1 ora)
        $lockAge = time() - filemtime($lockFile);
        if ($lockAge > 3600) {
            $this->log("Lock stale rimosso per {$jobClass}", 'warning');
            unlink($lockFile);
            return false;
        }

        return true;
    }

    /**
     * Acquisisce lock per un job
     *
     * @param CronJob $job
     * @return bool
     */
    private function acquireLock(CronJob $job)
    {
        $jobClass = get_class($job);
        $lockFile = $this->getLockFilePath($jobClass);

        try {
            $lockDir = dirname($lockFile);
            if (!is_dir($lockDir)) {
                mkdir($lockDir, 0755, true);
            }

            file_put_contents($lockFile, json_encode([
                'job' => $jobClass,
                'started_at' => date('Y-m-d H:i:s'),
                'pid' => getmypid()
            ]));

            return true;
        } catch (Exception $e) {
            $this->log("Impossibile acquisire lock per {$jobClass}: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Rilascia lock per un job
     *
     * @param CronJob $job
     */
    private function releaseLock(CronJob $job)
    {
        $jobClass = get_class($job);
        $lockFile = $this->getLockFilePath($jobClass);

        if (file_exists($lockFile)) {
            unlink($lockFile);
        }
    }

    /**
     * Esegue un singolo job
     *
     * @param CronJob $job
     * @return array Risultato esecuzione
     */
    private function executeJob(CronJob $job)
    {
        $jobClass = get_class($job);
        $startTime = microtime(true);
        $logId = null;

        $this->log("Esecuzione job: {$jobClass}", 'info');

        // Acquisisce lock
        if (!$this->acquireLock($job)) {
            return ['status' => 'error', 'message' => 'Impossibile acquisire lock'];
        }

        try {
            // Crea log entry nel database
            $logId = $this->createLogEntry($job, 'running');

            // Esegui il job
            $result = $job->handle();

            // Calcola durata
            $duration = round(microtime(true) - $startTime, 2);

            // Aggiorna log entry
            $this->updateLogEntry($logId, 'success', $result, $duration);

            $this->log("Job {$jobClass} completato con successo in {$duration}s", 'info');

            return [
                'status' => 'success',
                'duration' => $duration,
                'result' => $result,
                'log_id' => $logId
            ];

        } catch (Exception $e) {
            $duration = round(microtime(true) - $startTime, 2);

            $this->log("Job {$jobClass} fallito: " . $e->getMessage(), 'error');

            // Aggiorna log entry con errore
            $this->updateLogEntry($logId, 'failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], $duration);

            // Rilascia sempre il lock
            $this->releaseLock($job);

            return [
                'status' => 'failed',
                'duration' => $duration,
                'error' => $e->getMessage(),
                'log_id' => $logId
            ];

        } finally {
            // Rilascia lock
            $this->releaseLock($job);
        }
    }

    /**
     * Crea entry nel log database
     *
     * @param CronJob $job
     * @param string $status
     * @return int Log ID
     */
    private function createLogEntry(CronJob $job, $status = 'running')
    {
        try {
            $log = CronLog::create([
                'job_class' => get_class($job),
                'job_name' => $job->name(),
                'status' => $status,
                'started_at' => date('Y-m-d H:i:s'),
                'schedule' => $job->schedule()
            ]);

            return $log->id;
        } catch (Exception $e) {
            // Se fallisce il log, non bloccare l'esecuzione
            $this->log("Impossibile creare log entry: " . $e->getMessage(), 'warning');
            return null;
        }
    }

    /**
     * Aggiorna entry nel log database
     *
     * @param int|null $logId
     * @param string $status
     * @param mixed $result
     * @param float $duration
     */
    private function updateLogEntry($logId, $status, $result = null, $duration = null)
    {
        if ($logId === null) {
            return;
        }

        try {
            $log = CronLog::find($logId);
            if ($log) {
                $log->update([
                    'status' => $status,
                    'completed_at' => date('Y-m-d H:i:s'),
                    'duration_seconds' => $duration,
                    'output' => is_array($result) || is_object($result)
                        ? json_encode($result, JSON_PRETTY_PRINT)
                        : (string)$result
                ]);
            }
        } catch (Exception $e) {
            $this->log("Impossibile aggiornare log entry: " . $e->getMessage(), 'warning');
        }
    }

    /**
     * Ottiene path file lock per un job
     *
     * @param string $jobClass
     * @return string
     */
    private function getLockFilePath($jobClass)
    {
        $lockDir = APP_ROOT . '/storage/cron/locks';
        $sanitizedClass = str_replace('\\', '_', $jobClass);
        return "{$lockDir}/{$sanitizedClass}.lock";
    }

    /**
     * Carica job registrati da configurazione
     */
    private function loadRegisteredJobs()
    {
        $configFile = APP_ROOT . '/config/cron.php';

        if (file_exists($configFile)) {
            $jobs = require $configFile;

            if (is_array($jobs)) {
                foreach ($jobs as $jobClass) {
                    if (class_exists($jobClass)) {
                        $this->register(new $jobClass());
                    }
                }
            }
        }
    }

    /**
     * Ottiene lista job registrati
     *
     * @return array
     */
    public function getRegisteredJobs()
    {
        return array_map(function($job) {
            return [
                'class' => get_class($job),
                'name' => $job->name(),
                'description' => $job->description(),
                'schedule' => $job->schedule(),
                'enabled' => $job->isEnabled()
            ];
        }, $this->jobs);
    }

    /**
     * Ottiene statistiche esecuzioni
     *
     * @param string|null $jobClass
     * @param int $limit
     * @return array
     */
    public function getJobStats($jobClass = null, $limit = 10)
    {
        try {
            $query = CronLog::orderBy('started_at', 'DESC')->limit($limit);

            if ($jobClass) {
                $query->where('job_class', $jobClass);
            }

            return $query->get()->toArray();
        } catch (Exception $e) {
            $this->log("Errore recupero statistiche: " . $e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * Logging interno
     *
     * @param string $message
     * @param string $level
     */
    private function log($message, $level = 'info')
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] {$message}\n";

        // Log su file
        $logFile = APP_ROOT . '/storage/logs/cron-' . date('Y-m-d') . '.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents($logFile, $logMessage, FILE_APPEND);

        // Log anche su stdout in modalità CLI
        if (php_sapi_name() === 'cli') {
            echo $logMessage;
        }
    }

    /**
     * Pulisce vecchi log (chiamato manualmente o via job)
     *
     * @param int $days Giorni di retention
     * @return int Numero record eliminati
     */
    public function cleanOldLogs($days = 30)
    {
        try {
            $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

            $deleted = CronLog::where('created_at', '<', $cutoffDate)->delete();

            $this->log("Pulizia vecchi log: {$deleted} record eliminati (>{$days} giorni)", 'info');

            return $deleted;
        } catch (Exception $e) {
            $this->log("Errore pulizia log: " . $e->getMessage(), 'error');
            return 0;
        }
    }
}
