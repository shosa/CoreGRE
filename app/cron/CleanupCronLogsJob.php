<?php
/**
 * Cleanup Cron Logs Job
 * Pulisce vecchi log cron dal database
 */

use App\Models\CronLog;

class CleanupCronLogsJob extends CronJob
{
    private $retentionDays = 60;

    /**
     * Nome del job
     */
    public function name()
    {
        return 'Cleanup Cron Logs';
    }

    /**
     * Descrizione
     */
    public function description()
    {
        return "Elimina log cron più vecchi di {$this->retentionDays} giorni";
    }

    /**
     * Schedule: Ogni domenica alle 4:00 AM
     */
    public function schedule()
    {
        return '0 4 * * 0';
    }

    /**
     * Esecuzione job
     */
    public function handle()
    {
        $this->log("Inizio pulizia vecchi log cron...");

        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$this->retentionDays} days"));

        $deleted = CronLog::where('created_at', '<', $cutoffDate)->delete();

        $this->log("Eliminati {$deleted} record più vecchi di {$this->retentionDays} giorni");

        // Ottimizza tabella
        try {
            CronLog::getConnection()->statement('OPTIMIZE TABLE cron_logs');
            $this->log("Tabella cron_logs ottimizzata");
        } catch (Exception $e) {
            $this->log("Impossibile ottimizzare tabella: " . $e->getMessage(), 'warning');
        }

        return [
            'deleted_records' => $deleted,
            'retention_days' => $this->retentionDays
        ];
    }
}
