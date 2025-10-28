<?php
/**
 * Cron Job Base Class
 * Classe astratta per creare nuovi job cron
 *
 * Example:
 *   class CleanupTempFilesJob extends CronJob
 *   {
 *       public function name() { return 'Cleanup Temp Files'; }
 *       public function schedule() { return '0 2 * * *'; } // Ogni giorno alle 2:00
 *       public function handle() { // ... logic ... }
 *   }
 */

abstract class CronJob
{
    /**
     * Nome del job (human-readable)
     *
     * @return string
     */
    abstract public function name();

    /**
     * Espressione cron per lo scheduling
     * Format: "minute hour day month weekday"
     *
     * Esempi:
     *   '* * * * *'      - Ogni minuto
     *   '0 * * * *'      - Ogni ora
     *   '0 0 * * *'      - Ogni giorno a mezzanotte
     *   '0 2 * * *'      - Ogni giorno alle 2:00
     *   '0 0 * * 0'      - Ogni domenica a mezzanotte
     *   '0 0 1 * *'      - Primo giorno di ogni mese
     *   '* /5 * * * *'   - Ogni 5 minuti (senza spazio tra * e /)
     *   '0 * /6 * * *'   - Ogni 6 ore (senza spazio tra * e /)
     *
     * @return string|null Espressione cron o null per eseguire sempre
     */
    abstract public function schedule();

    /**
     * Logica principale del job
     * Ritorna qualsiasi dato da loggare (stringa, array, etc.)
     *
     * @return mixed Risultato esecuzione
     * @throws Exception In caso di errore
     */
    abstract public function handle();

    /**
     * Descrizione del job (opzionale)
     *
     * @return string
     */
    public function description()
    {
        return '';
    }

    /**
     * Verifica se il job è abilitato
     *
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Timeout del job in secondi (opzionale)
     * 0 = nessun timeout
     *
     * @return int
     */
    public function timeout()
    {
        return 0;
    }

    /**
     * Numero massimo di tentativi in caso di fallimento
     *
     * @return int
     */
    public function maxAttempts()
    {
        return 1;
    }

    /**
     * Ritardo tra un tentativo e l'altro (secondi)
     *
     * @return int
     */
    public function retryDelay()
    {
        return 60;
    }

    /**
     * Callback eseguito prima del job (opzionale)
     * Utile per setup, validazioni, etc.
     *
     * @return bool Return false per cancellare l'esecuzione
     */
    public function before()
    {
        return true;
    }

    /**
     * Callback eseguito dopo il job (opzionale)
     * Eseguito sia in caso di successo che di fallimento
     *
     * @param mixed $result Risultato del job
     * @param Exception|null $exception Eccezione se fallito
     */
    public function after($result = null, $exception = null)
    {
        // Override in child classes se necessario
    }

    /**
     * Callback eseguito in caso di successo (opzionale)
     *
     * @param mixed $result
     */
    public function onSuccess($result = null)
    {
        // Override in child classes se necessario
    }

    /**
     * Callback eseguito in caso di fallimento (opzionale)
     *
     * @param Exception $exception
     */
    public function onFailure($exception)
    {
        // Override in child classes se necessario
    }

    /**
     * Log helper per i job
     *
     * @param string $message
     * @param string $level
     */
    protected function log($message, $level = 'info')
    {
        $timestamp = date('Y-m-d H:i:s');
        $jobName = $this->name();
        $logMessage = "[{$timestamp}] [{$jobName}] [{$level}] {$message}\n";

        $logFile = APP_ROOT . '/storage/logs/cron-jobs-' . date('Y-m-d') . '.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents($logFile, $logMessage, FILE_APPEND);

        if (php_sapi_name() === 'cli') {
            echo $logMessage;
        }
    }

    /**
     * Helper per inviare notifiche (email, Slack, etc.)
     *
     * @param string $subject
     * @param string $message
     * @param string $channel email|slack|webhook
     */
    protected function notify($subject, $message, $channel = 'email')
    {
        // TODO: Implementare notifiche
        // Placeholder per future implementazioni
        $this->log("Notification: {$subject} - {$message}", 'info');
    }

    /**
     * Helper per verificare se il job può essere eseguito
     * Override in child classes per logica custom
     *
     * @return bool
     */
    protected function canRun()
    {
        return true;
    }

    /**
     * Esegue il job con gestione errori e callbacks
     * Chiamato internamente dal CronManager
     *
     * @return mixed
     * @throws Exception
     */
    final public function execute()
    {
        // Verifica se il job può essere eseguito
        if (!$this->canRun()) {
            $this->log("Job non può essere eseguito (canRun = false)", 'warning');
            return null;
        }

        // Before callback
        if ($this->before() === false) {
            $this->log("Job cancellato (before() returned false)", 'warning');
            return null;
        }

        $result = null;
        $exception = null;

        try {
            // Esegui con timeout se specificato
            if ($this->timeout() > 0) {
                set_time_limit($this->timeout());
            }

            $result = $this->handle();

            // Success callback
            $this->onSuccess($result);

        } catch (Exception $e) {
            $exception = $e;

            // Failure callback
            $this->onFailure($e);

            throw $e;

        } finally {
            // After callback (sempre eseguito)
            $this->after($result, $exception);
        }

        return $result;
    }
}
