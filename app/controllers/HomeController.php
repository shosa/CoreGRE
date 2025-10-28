<?php
/**
 * Home Controller
 * Gestisce la dashboard principale dell'applicazione
 */

use App\Models\ProductionRecord;
use App\Models\AvailableWidget;
use App\Models\UserWidget;
use App\Models\Repair;
use App\Models\QualityRecord;
use App\Models\ExportDocument;
use App\Models\ActivityLog;
use App\Models\Setting;

class HomeController extends BaseController
{
    /**
     * Pagina principale dashboard
     */
    public function index()
    {
        $this->requireAuth();

        // Ottieni i widget abilitati per l'utente tramite il nuovo sistema
        $widgets = $this->getEnabledWidgets();

        // Ottieni i dati per tutti i widget abilitati
        $widgetData = $this->getWidgetData($widgets);

        $data = [
            'pageTitle' => 'Dashboard - ' . APP_NAME,
            'userName' => $_SESSION['nome'] ?? 'Utente',
            'userType' => $_SESSION['admin_type'] ?? 'user',
            'currentDate' => date('d/m/Y'),
            'widgets' => $widgets,
            'widgetData' => $widgetData,
            'stats' => $this->getDashboardStats() // Manteniamo per retrocompatibilità
        ];

        $this->render('home.index', $data);
    }

    /**
     * Ottiene i widget abilitati per l'utente corrente
     */
    private function getEnabledWidgets()
    {
        $userId = $_SESSION['user_id'];

        try {
            // Query Eloquent per ottenere widget con preferenze utente
            $widgets = AvailableWidget::with(['userWidgets' => function($query) use ($userId) {
                    $query->where('user_id', $userId);
                }])
                ->active()
                ->get()
                ->filter(function($widget) {
                    $userWidget = $widget->userWidgets->first();
                    // Widget è abilitato se: ha preferenza utente abilitata OR (nessuna preferenza E default abilitato)
                    return ($userWidget && $userWidget->is_enabled) || (!$userWidget && $widget->default_enabled);
                })
                ->map(function($widget) {
                    $userWidget = $widget->userWidgets->first();
                    // Aggiungi proprietà personalizzate all'oggetto Eloquent
                    $widget->widget_size = $userWidget ? $userWidget->widget_size : $widget->default_size;
                    $widget->position_order = $userWidget ? $userWidget->position_order : 999;
                    $widget->custom_settings = $userWidget ? $userWidget->custom_settings : null;
                    return $widget;
                })
                ->sortBy('position_order')
                ->values();

            // Filtra per permessi
            $filteredWidgets = collect();
            foreach ($widgets as $widget) {
                // Controlla i permessi se richiesti
                if ($widget->required_permission && !$this->hasPermission($widget->required_permission)) {
                    continue;
                }
                $filteredWidgets->push($widget);
            }

            return $filteredWidgets;
        } catch (Exception $e) {
            error_log("Error getting enabled widgets: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * Ottiene i dati specifici per ogni widget abilitato
     */
    private function getWidgetData($widgets)
    {
        $widgetData = [];

        foreach ($widgets as $widget) {
            $widgetKey = $widget['widget_key'];
            $widgetData[$widgetKey] = $this->getDataForWidget($widgetKey, $widget['custom_settings']);
        }

        return $widgetData;
    }

    /**
     * Ottiene i dati per un singolo widget
     */
    private function getDataForWidget($widgetKey, $customSettings = null)
    {
        $settings = $customSettings ? json_decode($customSettings, true) : [];

        try {
            switch ($widgetKey) {
                case 'riparazioni':
                    if (!$this->hasPermission('riparazioni'))
                        return null;

                    // OTTIMIZZATO: Cache per 5 minuti
                    return SimpleCache::remember(
                        SimpleCache::key('widget', 'riparazioni', date('Y-m-d-H')),
                        function() {
                            // Singola query con aggregazione invece di 3 count separate
                            $stats = Repair::where(function($query) {
                                    $query->where('COMPLETA', 0)->orWhereNull('COMPLETA');
                                })
                                ->selectRaw('
                                    COUNT(*) as totali,
                                    SUM(CASE WHEN DATE(DATA) = CURDATE() THEN 1 ELSE 0 END) as oggi,
                                    SUM(CASE WHEN WEEK(DATA) = WEEK(NOW()) AND YEAR(DATA) = YEAR(NOW()) THEN 1 ELSE 0 END) as questa_settimana
                                ')
                                ->first();

                            return [
                                'totali' => (int)($stats->totali ?? 0),
                                'oggi' => (int)($stats->oggi ?? 0),
                                'questa_settimana' => (int)($stats->questa_settimana ?? 0)
                            ];
                        },
                        300
                    );

                case 'my_riparazioni':
                    if (!$this->hasPermission('riparazioni'))
                        return null;
                    $username = $_SESSION['username'] ?? $_SESSION['user_name'] ?? '';
                    return [
                        'totali' => Repair::where('UTENTE', $username)
                            ->where(function($query) {
                                $query->where('COMPLETA', 0)->orWhereNull('COMPLETA');
                            })->count(),
                        'oggi' => Repair::where('UTENTE', $username)
                            ->whereDate('DATA', date('Y-m-d'))
                            ->where(function($query) {
                                $query->where('COMPLETA', 0)->orWhereNull('COMPLETA');
                            })->count()
                    ];

                case 'production_week':
                    if (!$this->hasPermission('produzione'))
                        return null;
                    $currentWeek = date('W');
                    $currentYear = date('Y');

                    // OTTIMIZZATO: Cache per 10 minuti
                    return SimpleCache::remember(
                        SimpleCache::key('widget', 'production_week', $currentYear, $currentWeek),
                        function() use ($currentWeek, $currentYear) {
                            $total = ProductionRecord::whereRaw('WEEK(production_date) = ? AND YEAR(production_date) = ?', [$currentWeek, $currentYear])
                                ->selectRaw('SUM(COALESCE(taglio1, 0) + COALESCE(taglio2, 0)) + SUM(COALESCE(orlatura1, 0) + COALESCE(orlatura2, 0) + COALESCE(orlatura3, 0) + COALESCE(orlatura4, 0) + COALESCE(orlatura5, 0)) + SUM(COALESCE(manovia1, 0) + COALESCE(manovia2, 0)) as total')
                                ->value('total') ?? 0;

                            return [
                                'totale' => $total,
                                'settimana' => $currentWeek
                            ];
                        },
                        600
                    );

                case 'production_month':
                    if (!$this->hasPermission('produzione'))
                        return null;
                    $currentMonthNum = date('n'); // Numero mese
                    $currentYear = date('Y');
                    $currentMonthIT = $this->getItalianMonth($currentMonthNum); // Nome mese italiano

                    // OTTIMIZZATO: Cache per 15 minuti
                    return SimpleCache::remember(
                        SimpleCache::key('widget', 'production_month', $currentYear, $currentMonthNum),
                        function() use ($currentMonthNum, $currentYear, $currentMonthIT) {
                            $total = ProductionRecord::whereMonth('production_date', $currentMonthNum)
                                ->whereYear('production_date', $currentYear)
                                ->selectRaw('SUM(COALESCE(taglio1, 0) + COALESCE(taglio2, 0)) + SUM(COALESCE(orlatura1, 0) + COALESCE(orlatura2, 0) + COALESCE(orlatura3, 0) + COALESCE(orlatura4, 0) + COALESCE(orlatura5, 0)) + SUM(COALESCE(manovia1, 0) + COALESCE(manovia2, 0)) as total')
                                ->value('total') ?? 0;

                            return [
                                'totale' => $total,
                                'mese' => $currentMonthIT
                            ];
                        },
                        900
                    );

                case 'quality_today':
                    if (!$this->hasPermission('quality'))
                        return null;

                    try {
                        // Usa QualityRecord con Eloquent
                        $today = date('Y-m-d');

                        $controlliCount = QualityRecord::whereDate('data_controllo', $today)->count();
                        $difettiCount = QualityRecord::whereDate('data_controllo', $today)
                            ->where('ha_eccezioni', 1)
                            ->count();

                    } catch (Exception $e) {
                        error_log("CQ tables query error: " . $e->getMessage());
                        $controlliCount = 0;
                        $difettiCount = 0;
                    }

                    return [
                        'controlli' => $controlliCount,
                        'difetti' => $difettiCount
                    ];

                case 'export_recent':
                    if (!$this->hasPermission('export'))
                        return null;

                    try {
                        // Usa ExportDocument con Eloquent
                        $today = date('Y-m-d');

                        $oggiCount = ExportDocument::whereDate('data', $today)->count();
                        $settimanaCount = ExportDocument::whereRaw('WEEK(data) = WEEK(NOW()) AND YEAR(data) = YEAR(NOW())')->count();

                    } catch (Exception $e) {
                        error_log("Export tables query error: " . $e->getMessage());
                        $oggiCount = 0;
                        $settimanaCount = 0;
                    }

                    return [
                        'oggi' => $oggiCount,
                        'settimana' => $settimanaCount
                    ];

                case 'quick_actions':
                    // Azioni rapide sempre disponibili
                    return [
                        'available_actions' => $this->getQuickActions()
                    ];

                case 'recent_activities':
                    // Attività recenti dell'utente
                    return $this->getRecentActivitiesForWidget();


                default:
                    return null;
            }
        } catch (Exception $e) {
            error_log("Error getting data for widget $widgetKey: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ottiene le azioni rapide disponibili per l'utente
     */
    private function getQuickActions()
    {
        $actions = [];

        if ($this->hasPermission('riparazioni')) {
            $actions[] = [
                'name' => 'Nuova Riparazione',
                'url' => BASE_URL . '/riparazioni/create',
                'icon' => 'fas fa-plus',
                'color' => 'blue'
            ];
        }

        if ($this->hasPermission('produzione')) {
            $actions[] = [
                'name' => 'Nuova Produzione',
                'url' => BASE_URL . '/produzione/create',
                'icon' => 'fas fa-calendar',
                'color' => 'yellow'
            ];
        }

        if ($this->hasPermission('export')) {
            $actions[] = [
                'name' => 'Nuovo Export',
                'url' => BASE_URL . '/export/create',
                'icon' => 'fas fa-file-export',
                'color' => 'purple'
            ];
        }

        if ($this->hasPermission('scm')) {
            $actions[] = [
                'name' => 'Nuovo Lancio SCM',
                'url' => BASE_URL . '/scm-admin/launches/create',
                'icon' => 'fas fa-rocket',
                'color' => 'orange'
            ];
        }

        return $actions;
    }

    /**
     * Ottiene le attività recenti per il widget
     */
    private function getRecentActivitiesForWidget()
    {
        $userId = $_SESSION['user_id'];

        try {
            // Usa ActivityLog con Eloquent
            $recentLimit = Setting::getInt('recent_items_limit', 5);
            $activities = ActivityLog::where('user_id', $userId)
                ->select(['category', 'activity_type', 'description', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->limit($recentLimit)
                ->get();

            $formattedActivities = [];
            foreach ($activities as $activity) {
                $icon = $this->getActivityIcon($activity->category, $activity->activity_type);
                $timeAgo = $this->timeAgo($activity->created_at);

                $formattedActivities[] = [
                    'description' => $activity->description,
                    'time' => $timeAgo,
                    'icon' => $icon,
                    'category' => $activity->category
                ];
            }

            return [
                'activities' => $formattedActivities,
                'count' => count($formattedActivities)
            ];

        } catch (Exception $e) {
            error_log("Error getting recent activities: " . $e->getMessage());
            return [
                'activities' => [],
                'count' => 0
            ];
        }
    }

    /**
     * Ottiene il nome del mese in italiano
     */
    private function getItalianMonth($monthNum)
    {
        $months = [
            1 => 'Gennaio',
            2 => 'Febbraio',
            3 => 'Marzo',
            4 => 'Aprile',
            5 => 'Maggio',
            6 => 'Giugno',
            7 => 'Luglio',
            8 => 'Agosto',
            9 => 'Settembre',
            10 => 'Ottobre',
            11 => 'Novembre',
            12 => 'Dicembre'
        ];

        return $months[$monthNum] ?? 'Sconosciuto';
    }


    /**
     * Ottiene le statistiche per la dashboard
     */
    private function getDashboardStats()
    {
        $stats = [];

        try {
            // Statistiche riparazioni se l'utente ha i permessi
            if ($this->hasPermission('riparazioni')) {
                // Usa Repair con Eloquent
                $openRiparazioni = Repair::where(function($query) {
                    $query->where('COMPLETA', 0)->orWhereNull('COMPLETA');
                })->count();

                $userRiparazioni = Repair::where('UTENTE', $_SESSION['username'] ?? $_SESSION['user_name'] ?? '')
                    ->where(function($query) {
                        $query->where('COMPLETA', 0)->orWhereNull('COMPLETA');
                    })->count();

                $stats['riparazioni'] = [
                    'totali' => $openRiparazioni,
                    'mie' => $userRiparazioni
                ];
            }

            // Statistiche qualità se l'utente ha i permessi
            if ($this->hasPermission('quality')) {
                // Usa QualityRecord con Eloquent
                $today = date('Y-m-d');
                $qualityToday = QualityRecord::whereDate('data_controllo', $today)->count();

                $stats['quality'] = [
                    'oggi' => $qualityToday
                ];
            }

            // Statistiche produzione se l'utente ha i permessi
            if ($this->hasPermission('produzione')) {
                $currentWeek = date('W');
                $currentYear = date('Y');
                $currentMonth = date('n');

                // Usa ProductionRecord con Eloquent
                $prodWeek = ProductionRecord::whereRaw('WEEK(production_date) = ? AND YEAR(production_date) = ?', [$currentWeek, $currentYear])
                    ->selectRaw('SUM(COALESCE(taglio1, 0) + COALESCE(taglio2, 0)) + SUM(COALESCE(orlatura1, 0) + COALESCE(orlatura2, 0) + COALESCE(orlatura3, 0) + COALESCE(orlatura4, 0) + COALESCE(orlatura5, 0)) + SUM(COALESCE(manovia1, 0) + COALESCE(manovia2, 0)) as total')
                    ->value('total') ?? 0;

                $prodMonth = ProductionRecord::whereMonth('production_date', $currentMonth)
                    ->whereYear('production_date', $currentYear)
                    ->selectRaw('SUM(COALESCE(taglio1, 0) + COALESCE(taglio2, 0)) + SUM(COALESCE(orlatura1, 0) + COALESCE(orlatura2, 0) + COALESCE(orlatura3, 0) + COALESCE(orlatura4, 0) + COALESCE(orlatura5, 0)) + SUM(COALESCE(manovia1, 0) + COALESCE(manovia2, 0)) as total')
                    ->value('total') ?? 0;

                $stats['produzione'] = [
                    'settimana' => $prodWeek,
                    'mese' => $prodMonth
                ];
            }

        } catch (Exception $e) {
            error_log("Error getting dashboard stats: " . $e->getMessage());
            // Ritorna statistiche vuote in caso di errore
        }

        return $stats;
    }



    /**
     * API endpoint per ottenere le attività recenti dell'utente
     */
    public function getRecentActivities()
    {
        $this->requireAuth();

        $userId = $_SESSION['user_id'];

        try {
            // Usa ActivityLog con Eloquent
            $recentLimit = Setting::getInt('recent_items_limit', 5);
            $activities = ActivityLog::where('user_id', $userId)
                ->select(['category', 'activity_type', 'description', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->limit($recentLimit)
                ->get();

            $formattedActivities = [];
            foreach ($activities as $activity) {
                $icon = $this->getActivityIcon($activity->category, $activity->activity_type);
                $timeAgo = $this->timeAgo($activity->created_at);

                $formattedActivities[] = [
                    'description' => $activity->description,
                    'time' => $timeAgo,
                    'icon' => $icon
                ];
            }

            $this->json($formattedActivities);

        } catch (Exception $e) {
            error_log("Error getting recent activities: " . $e->getMessage());
            $this->json([]);
        }
    }

    /**
     * Ottiene l'icona per un'attività in base al modulo e azione
     */
    private function getActivityIcon($category, $activityType)
    {
        $icons = [
            'RIPARAZIONI' => 'hammer',
            'QUALITY' => 'check-circle',
            'PRODUZIONE' => 'industry',
            'DASHBOARD' => 'tachometer-alt',
            'AUTH' => 'sign-in-alt',
            'EXPORT' => 'file-export',
            'SCM' => 'project-diagram',
            'TRACKING' => 'search-location'
        ];

        return $icons[$category] ?? 'circle';
    }

    /**
     * Calcola il tempo trascorso in formato leggibile
     */
    protected function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);

        if ($time < 60)
            return 'Ora';
        if ($time < 3600)
            return floor($time / 60) . ' min fa';
        if ($time < 86400)
            return floor($time / 3600) . ' ore fa';
        if ($time < 2620800)
            return floor($time / 86400) . ' giorni fa';

        return date('d/m/Y', strtotime($datetime));
    }

    /**
     * API endpoint per ottenere le statistiche aggiornate
     */
    public function getStats()
    {
        $this->requireAuth();

        try {
            $stats = $this->getDashboardStats();
            $this->json($stats);

        } catch (Exception $e) {
            error_log("Error getting dashboard stats: " . $e->getMessage());
            $this->json(['error' => 'Errore durante il caricamento delle statistiche'], 500);
        }
    }
}