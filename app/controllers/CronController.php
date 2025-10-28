<?php
/**
 * Cron Controller
 * Gestione e monitoring job cron via web interface
 */

use App\Models\CronLog;

class CronController extends BaseController
{
    /**
     * Dashboard principale cron
     */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('cron');

        // Carica CronManager
        require_once APP_ROOT . '/core/CronManager.php';
        require_once APP_ROOT . '/core/CronJob.php';
        require_once APP_ROOT . '/core/CronSchedule.php';

        // Autoload job
        foreach (glob(APP_ROOT . '/app/cron/*.php') as $jobFile) {
            require_once $jobFile;
        }

        $cronManager = CronManager::getInstance();

        // Carica job registrati
        $configFile = APP_ROOT . '/config/cron.php';
        if (file_exists($configFile)) {
            $jobs = require $configFile;
            if (is_array($jobs)) {
                foreach ($jobs as $jobClass) {
                    if (class_exists($jobClass)) {
                        $cronManager->register(new $jobClass());
                    }
                }
            }
        }

        // Ottieni lista job con info
        $registeredJobs = $cronManager->getRegisteredJobs();

        // Arricchisci con statistiche e prossima esecuzione
        foreach ($registeredJobs as &$job) {
            $stats = CronLog::getJobStats($job['class'], 30);
            $job['stats'] = $stats;

            // Calcola prossima esecuzione
            if ($job['schedule']) {
                $job['next_run'] = CronSchedule::nextRunDate($job['schedule']);
                $job['schedule_description'] = CronSchedule::describe($job['schedule']);
            } else {
                $job['next_run'] = null;
                $job['schedule_description'] = 'Sempre attivo';
            }
        }

        // Statistiche generali ultimi 7 giorni
        $stats = CronLog::getStats(7);

        // Job più lenti
        $slowestJobs = CronLog::getSlowestJobs(5, 7);

        // Job con più fallimenti
        $mostFailedJobs = CronLog::getMostFailedJobs(7);

        // Ultimi log
        $recentLogs = CronLog::orderBy('started_at', 'DESC')
            ->limit(20)
            ->get();

        $data = [
            'pageTitle' => 'Gestione Cron Jobs - ' . APP_NAME,
            'jobs' => $registeredJobs,
            'stats' => $stats,
            'slowest_jobs' => $slowestJobs,
            'most_failed_jobs' => $mostFailedJobs,
            'recent_logs' => $recentLogs
        ];

        $this->render('cron.index', $data);
    }

    /**
     * Dettaglio singolo job
     */
    public function show()
    {
        $this->requireAuth();
        $this->requirePermission('admin');

        $jobClass = $_GET['job'] ?? null;

        if (!$jobClass) {
            $this->setFlash('error', 'Job non specificato');
            $this->redirect($this->url('/cron'));
            return;
        }

        // Verifica che il job esista
        $jobFile = APP_ROOT . '/app/cron/' . $jobClass . '.php';
        if (!file_exists($jobFile)) {
            $this->setFlash('error', 'Job non trovato');
            $this->redirect($this->url('/cron'));
            return;
        }

        require_once APP_ROOT . '/core/CronJob.php';
        require_once APP_ROOT . '/core/CronSchedule.php';
        require_once $jobFile;

        $job = new $jobClass();

        // Statistiche dettagliate
        $stats = CronLog::getJobStats($jobClass, 30);

        // Log recenti
        $logs = CronLog::byJob($jobClass)
            ->orderBy('started_at', 'DESC')
            ->limit(50)
            ->get();

        // Grafico esecuzioni ultimi 30 giorni
        $chartData = CronLog::byJob($jobClass)
            ->where('started_at', '>=', date('Y-m-d', strtotime('-30 days')))
            ->selectRaw('DATE(started_at) as date, COUNT(*) as total,
                         SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as successful,
                         AVG(duration_seconds) as avg_duration')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $data = [
            'pageTitle' => $job->name() . ' - Cron Jobs',
            'job' => $job,
            'job_class' => $jobClass,
            'stats' => $stats,
            'logs' => $logs,
            'chart_data' => $chartData,
            'schedule_description' => $job->schedule() ? CronSchedule::describe($job->schedule()) : 'Sempre attivo',
            'next_run' => $job->schedule() ? CronSchedule::nextRunDate($job->schedule()) : null
        ];

        $this->render('cron.show', $data);
    }

    /**
     * Esegui manualmente un job (AJAX)
     */
    public function run()
    {
        $this->requireAuth();
        $this->requirePermission('admin');

        if (!$this->isPost()) {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $jobClass = $this->input('job_class');

        if (!$jobClass) {
            $this->json(['error' => 'Job non specificato'], 400);
            return;
        }

        try {
            // Carica job
            $jobFile = APP_ROOT . '/app/cron/' . $jobClass . '.php';
            if (!file_exists($jobFile)) {
                $this->json(['error' => 'Job non trovato'], 404);
                return;
            }

            require_once APP_ROOT . '/core/CronJob.php';
            require_once APP_ROOT . '/core/CronManager.php';
            require_once $jobFile;

            $job = new $jobClass();
            $cronManager = CronManager::getInstance();
            $cronManager->register($job);

            // Esegui job
            $startTime = microtime(true);
            $result = $job->handle();
            $duration = round(microtime(true) - $startTime, 2);

            // Log nel database
            CronLog::create([
                'job_class' => $jobClass,
                'job_name' => $job->name(),
                'status' => 'success',
                'started_at' => date('Y-m-d H:i:s', $startTime),
                'completed_at' => date('Y-m-d H:i:s'),
                'duration_seconds' => $duration,
                'schedule' => $job->schedule(),
                'output' => json_encode($result, JSON_PRETTY_PRINT)
            ]);

            $this->logActivity('CRON', 'MANUAL_RUN', "Esecuzione manuale job: {$jobClass}");

            $this->json([
                'success' => true,
                'message' => 'Job eseguito con successo',
                'result' => $result,
                'duration' => $duration
            ]);

        } catch (Exception $e) {
            // Log errore
            CronLog::create([
                'job_class' => $jobClass,
                'job_name' => $job->name() ?? $jobClass,
                'status' => 'failed',
                'started_at' => date('Y-m-d H:i:s'),
                'completed_at' => date('Y-m-d H:i:s'),
                'duration_seconds' => 0,
                'schedule' => $job->schedule() ?? null,
                'output' => json_encode([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ])
            ]);

            $this->json([
                'success' => false,
                'message' => 'Errore durante esecuzione job',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Visualizza dettaglio log singolo (AJAX)
     */
    public function logDetail()
    {
        $this->requireAuth();
        $this->requirePermission('admin');

        $logId = $_GET['id'] ?? null;

        if (!$logId) {
            $this->json(['error' => 'Log ID non specificato'], 400);
            return;
        }

        $log = CronLog::find($logId);

        if (!$log) {
            $this->json(['error' => 'Log non trovato'], 404);
            return;
        }

        $this->json([
            'success' => true,
            'log' => [
                'id' => $log->id,
                'job_class' => $log->job_class,
                'job_name' => $log->job_name,
                'status' => $log->status,
                'started_at' => $log->started_at->format('Y-m-d H:i:s'),
                'completed_at' => $log->completed_at ? $log->completed_at->format('Y-m-d H:i:s') : null,
                'duration_seconds' => $log->duration_seconds,
                'duration_formatted' => $log->duration_formatted,
                'output' => $log->output,
                'schedule' => $log->schedule
            ]
        ]);
    }

    /**
     * Elimina vecchi log (AJAX)
     */
    public function cleanLogs()
    {
        $this->requireAuth();
        $this->requirePermission('admin');

        if (!$this->isPost()) {
            $this->json(['error' => 'Method not allowed'], 405);
            return;
        }

        $days = (int)($this->input('days') ?? 30);

        if ($days < 1 || $days > 365) {
            $this->json(['error' => 'Giorni non validi (1-365)'], 400);
            return;
        }

        try {
            $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
            $deleted = CronLog::where('created_at', '<', $cutoffDate)->delete();

            $this->logActivity('CRON', 'CLEAN_LOGS', "Pulizia log cron: {$deleted} record eliminati (>{$days} giorni)");

            $this->json([
                'success' => true,
                'message' => "Eliminati {$deleted} record",
                'deleted' => $deleted
            ]);

        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Errore durante pulizia: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ottieni statistiche per grafico (AJAX)
     */
    public function stats()
    {
        $this->requireAuth();
        $this->requirePermission('admin');

        $days = (int)($_GET['days'] ?? 7);
        $jobClass = $_GET['job'] ?? null;

        try {
            $since = date('Y-m-d', strtotime("-{$days} days"));

            $query = CronLog::where('started_at', '>=', $since);

            if ($jobClass) {
                $query->where('job_class', $jobClass);
            }

            // Raggruppa per giorno
            $dailyStats = $query
                ->selectRaw('DATE(started_at) as date,
                             COUNT(*) as total,
                             SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as successful,
                             SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
                             AVG(duration_seconds) as avg_duration')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $this->json([
                'success' => true,
                'stats' => $dailyStats
            ]);

        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test connessione cron system
     */
    public function test()
    {
        $this->requireAuth();
        $this->requirePermission('admin');

        $data = [
            'pageTitle' => 'Test Cron System - ' . APP_NAME,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/'],
                ['title' => 'Cron Jobs', 'url' => '/cron'],
                ['title' => 'Test', 'url' => '/cron/test']
            ]
        ];

        $this->render('cron.test', $data);
    }
}
