<?php

namespace App\Models;

/**
 * Cron Log Model
 * Traccia esecuzioni dei job cron
 */
class CronLog extends BaseModel
{
    protected $table = 'cron_logs';

    protected $fillable = [
        'job_class',
        'job_name',
        'status',
        'started_at',
        'completed_at',
        'duration_seconds',
        'schedule',
        'output'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_seconds' => 'float'
    ];

    /**
     * Scope per filtrare per job class
     */
    public function scopeByJob($query, $jobClass)
    {
        return $query->where('job_class', $jobClass);
    }

    /**
     * Scope per filtrare per status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope per job completati con successo
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope per job falliti
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope per job in esecuzione
     */
    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    /**
     * Scope per range di date
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('started_at', [$from, $to]);
    }

    /**
     * Scope per oggi
     */
    public function scopeToday($query)
    {
        $today = date('Y-m-d');
        return $query->whereDate('started_at', $today);
    }

    /**
     * Scope per ultima settimana
     */
    public function scopeLastWeek($query)
    {
        $weekAgo = date('Y-m-d', strtotime('-7 days'));
        return $query->where('started_at', '>=', $weekAgo);
    }

    /**
     * Scope per ultimo mese
     */
    public function scopeLastMonth($query)
    {
        $monthAgo = date('Y-m-d', strtotime('-30 days'));
        return $query->where('started_at', '>=', $monthAgo);
    }

    /**
     * Ottiene durata formattata
     */
    public function getDurationFormattedAttribute()
    {
        if (!$this->duration_seconds) {
            return 'N/A';
        }

        if ($this->duration_seconds < 1) {
            return round($this->duration_seconds * 1000) . 'ms';
        }

        if ($this->duration_seconds < 60) {
            return round($this->duration_seconds, 2) . 's';
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;
        return "{$minutes}m " . round($seconds) . 's';
    }

    /**
     * Ottiene status con badge colore
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'success' => '<span class="badge badge-success">Successo</span>',
            'failed' => '<span class="badge badge-danger">Fallito</span>',
            'running' => '<span class="badge badge-info">In esecuzione</span>',
            'skipped' => '<span class="badge badge-warning">Saltato</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge badge-secondary">' . $this->status . '</span>';
    }

    /**
     * Statistiche generali
     */
    public static function getStats($days = 7)
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));

        return [
            'total' => static::where('started_at', '>=', $since)->count(),
            'successful' => static::successful()->where('started_at', '>=', $since)->count(),
            'failed' => static::failed()->where('started_at', '>=', $since)->count(),
            'running' => static::running()->count(),
            'avg_duration' => static::successful()
                ->where('started_at', '>=', $since)
                ->avg('duration_seconds')
        ];
    }

    /**
     * Statistiche per job specifico
     */
    public static function getJobStats($jobClass, $days = 30)
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));

        $logs = static::byJob($jobClass)
            ->where('started_at', '>=', $since)
            ->get();

        $successful = $logs->where('status', 'success')->count();
        $total = $logs->count();

        return [
            'total_runs' => $total,
            'successful' => $successful,
            'failed' => $logs->where('status', 'failed')->count(),
            'success_rate' => $total > 0 ? round(($successful / $total) * 100, 2) : 0,
            'avg_duration' => $logs->where('status', 'success')->avg('duration_seconds'),
            'last_run' => $logs->sortByDesc('started_at')->first(),
            'last_success' => $logs->where('status', 'success')->sortByDesc('started_at')->first(),
            'last_failure' => $logs->where('status', 'failed')->sortByDesc('started_at')->first()
        ];
    }

    /**
     * Top job per durata
     */
    public static function getSlowestJobs($limit = 10, $days = 7)
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));

        return static::successful()
            ->where('started_at', '>=', $since)
            ->orderBy('duration_seconds', 'DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Job con più fallimenti
     */
    public static function getMostFailedJobs($days = 7)
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));

        return static::failed()
            ->where('started_at', '>=', $since)
            ->selectRaw('job_class, job_name, COUNT(*) as failure_count')
            ->groupBy('job_class', 'job_name')
            ->orderBy('failure_count', 'DESC')
            ->get();
    }
}
