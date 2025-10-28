<?php
/**
 * Widget Controller
 * Gestisce i widget della dashboard utente
 */

use App\Models\AvailableWidget;
use App\Models\UserWidget;
use App\Models\ProductionRecord;
use App\Models\Repair;
use App\Models\QualityRecord;
use App\Models\ExportDocument;

class WidgetController extends BaseController
{
    /**
     * Ottiene tutti i widget disponibili per l'utente corrente
     */
    public function getAvailableWidgets()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            $this->json(['error' => 'Method not allowed']);
            return;
        }

        try {
            $userId = $_SESSION['user_id'];

            // Assicurati che l'utente abbia record per tutti i widget disponibili
            $this->ensureUserWidgetRecords($userId);

            // Ottieni tutti i widget disponibili con le preferenze utente usando Eloquent
            $widgets = AvailableWidget::with([
                'userWidgets' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
                ->active()
                ->get()
                ->map(function ($widget) {
                    $userWidget = $widget->userWidgets->first();
                    return [
                        'widget_key' => $widget->widget_key,
                        'widget_name' => $widget->widget_name,
                        'widget_description' => $widget->widget_description,
                        'widget_icon' => $widget->widget_icon,
                        'widget_color' => $widget->widget_color,
                        'required_permission' => $widget->required_permission,
                        'category' => $widget->category,
                        'default_size' => $widget->default_size,
                        'is_enabled' => $userWidget ? $userWidget->is_enabled : true,
                        'position_order' => $userWidget ? $userWidget->position_order : 999,
                        'widget_size' => $userWidget ? $userWidget->widget_size : $widget->default_size,
                        'custom_settings' => $userWidget ? $userWidget->custom_settings : null
                    ];
                })
                ->sortBy('position_order')
                ->values()
                ->toArray();

            // Filtra i widget in base ai permessi dell'utente
            $filteredWidgets = [];
            foreach ($widgets as $widget) {
                // Se il widget richiede un permesso specifico, verificalo
                if ($widget['required_permission'] && !$this->hasPermission($widget['required_permission'])) {
                    continue;
                }

                $filteredWidgets[] = $widget;
            }

            $this->json([
                'success' => true,
                'widgets' => $filteredWidgets
            ]);

        } catch (Exception $e) {
            error_log("Error getting available widgets: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Errore durante il caricamento dei widget'], 500);
        }
    }

    /**
     * Ottiene i widget abilitati per l'utente corrente
     */
    public function getEnabledWidgets()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            $this->json(['error' => 'Method not allowed']);
            return;
        }

        try {
            $userId = $_SESSION['user_id'];

            // Assicurati che l'utente abbia record per tutti i widget disponibili
            $this->ensureUserWidgetRecords($userId);

            // Ottieni widget abilitati usando Eloquent
            $widgets = AvailableWidget::with([
                'userWidgets' => function ($query) use ($userId) {
                    $query->where('user_id', $userId)->where('is_enabled', 1);
                }
            ])
                ->active()
                ->get()
                ->filter(function ($widget) {
                    return $widget->userWidgets->count() > 0; // Solo widget abilitati dall'utente
                })
                ->map(function ($widget) {
                    $userWidget = $widget->userWidgets->first();
                    return [
                        'widget_key' => $widget->widget_key,
                        'widget_name' => $widget->widget_name,
                        'widget_icon' => $widget->widget_icon,
                        'widget_color' => $widget->widget_color,
                        'category' => $widget->category,
                        'required_permission' => $widget->required_permission,
                        'widget_size' => $userWidget->widget_size,
                        'position_order' => $userWidget->position_order,
                        'custom_settings' => $userWidget->custom_settings
                    ];
                })
                ->sortBy('position_order')
                ->values()
                ->toArray();

            // Filtra per permessi e aggiungi i dati del widget
            $enabledWidgets = [];
            foreach ($widgets as $widget) {
                // Verifica permessi
                if ($widget['required_permission'] && !$this->hasPermission($widget['required_permission'])) {
                    continue;
                }

                // Aggiungi i dati specifici del widget
                $widget['data'] = $this->getWidgetData($widget['widget_key']);
                $enabledWidgets[] = $widget;
            }

            $this->json([
                'success' => true,
                'widgets' => $enabledWidgets
            ]);

        } catch (Exception $e) {
            error_log("Error getting enabled widgets: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Errore durante il caricamento dei widget'], 500);
        }
    }

    /**
     * Aggiorna le preferenze di un widget
     */
    public function updateWidget()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->json(['error' => 'Method not allowed']);
            return;
        }

        try {
            $userId = $_SESSION['user_id'];

            // Leggi JSON body
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                // Fallback a form data
                $input = $_POST;
            }

            $widgetKey = $input['widget_key'] ?? null;
            $isEnabled = isset($input['is_enabled']) ? (int) (bool) $input['is_enabled'] : null;
            $positionOrder = isset($input['position_order']) ? (int) $input['position_order'] : null;
            $widgetSize = $input['widget_size'] ?? null;
            $customSettings = $input['custom_settings'] ?? null;

            if (!$widgetKey) {
                $this->json(['success' => false, 'error' => 'Widget key richiesta'], 400);
                return;
            }

            // Verifica che il widget esista usando Eloquent
            $widgetExists = AvailableWidget::where('widget_key', $widgetKey)
                ->active()
                ->exists();

            if (!$widgetExists) {
                $this->json(['success' => false, 'error' => 'Widget non trovato'], 404);
                return;
            }

            // Verifica se esiste già una preferenza
            $existing = UserWidget::where('user_id', $userId)
                ->where('widget_key', $widgetKey)
                ->first();

            if ($existing) {
                // Aggiorna solo i campi inviati usando Eloquent
                $updateData = [];

                if ($isEnabled !== null) {
                    $updateData['is_enabled'] = $isEnabled;
                }

                if ($positionOrder !== null) {
                    $updateData['position_order'] = $positionOrder;
                }

                if ($widgetSize !== null) {
                    $updateData['widget_size'] = $widgetSize;
                }

                if ($customSettings !== null) {
                    $updateData['custom_settings'] = $customSettings;
                }

                if (!empty($updateData)) {
                    $existing->update($updateData);
                }
            } else {
                // Inserisci nuovo con valori di default usando Eloquent
                $defaultEnabled = $isEnabled !== null ? $isEnabled : 1;
                $defaultPosition = $positionOrder !== null ? $positionOrder : 0;
                $defaultSize = $widgetSize !== null ? $widgetSize : 'medium';
                $defaultSettings = $customSettings !== null ? $customSettings : null;

                UserWidget::create([
                    'user_id' => $userId,
                    'widget_key' => $widgetKey,
                    'is_enabled' => $defaultEnabled,
                    'position_order' => $defaultPosition,
                    'widget_size' => $defaultSize,
                    'custom_settings' => $defaultSettings
                ]);
            }

            $this->logActivity('DASHBOARD', 'UPDATE_WIDGET', "Widget $widgetKey " . ($isEnabled ? 'abilitato' : 'disabilitato'));

            $this->json([
                'success' => true,
                'message' => 'Widget aggiornato con successo'
            ]);

        } catch (Exception $e) {
            error_log("Error updating widget: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Errore durante l\'aggiornamento del widget'], 500);
        }
    }

    /**
     * Aggiorna più widget in batch (salvataggio alla chiusura del modale)
     */
    public function batchUpdateWidgets()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->json(['error' => 'Method not allowed']);
            return;
        }

        try {
            $userId = $_SESSION['user_id'];

            // Leggi JSON body
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || !isset($input['changes'])) {
                $this->json(['success' => false, 'error' => 'Dati non validi'], 400);
                return;
            }

            $changes = $input['changes'];
            if (!is_array($changes) || empty($changes)) {
                $this->json(['success' => false, 'error' => 'Nessuna modifica da salvare'], 400);
                return;
            }

            $updatedCount = 0;

            foreach ($changes as $change) {
                if (!isset($change['widget_key'])) {
                    continue;
                }

                $widgetKey = $change['widget_key'];

                // Verifica che il widget esista
                $widgetExists = AvailableWidget::where('widget_key', $widgetKey)
                    ->active()
                    ->exists();

                if (!$widgetExists) {
                    continue;
                }

                // Trova o crea il record
                $userWidget = UserWidget::where('user_id', $userId)
                    ->where('widget_key', $widgetKey)
                    ->first();

                $updateData = [];

                // Raccogli i campi da aggiornare
                if (isset($change['is_enabled'])) {
                    $updateData['is_enabled'] = (int) (bool) $change['is_enabled'];
                }
                if (isset($change['position_order'])) {
                    $updateData['position_order'] = (int) $change['position_order'];
                }
                if (isset($change['widget_size'])) {
                    $updateData['widget_size'] = $change['widget_size'];
                }
                if (isset($change['custom_settings'])) {
                    $updateData['custom_settings'] = $change['custom_settings'];
                }

                if (empty($updateData)) {
                    continue;
                }

                if ($userWidget) {
                    $userWidget->update($updateData);
                } else {
                    $updateData['user_id'] = $userId;
                    $updateData['widget_key'] = $widgetKey;
                    UserWidget::create($updateData);
                }

                $updatedCount++;
            }

            $this->logActivity('DASHBOARD', 'BATCH_UPDATE_WIDGETS', "Aggiornati $updatedCount widget");

            $this->json([
                'success' => true,
                'message' => "Salvate $updatedCount modifiche",
                'updated_count' => $updatedCount
            ]);

        } catch (Exception $e) {
            error_log("Error in batch update widgets: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Errore durante il salvataggio: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Aggiorna l'ordine di più widget
     */
    public function updateWidgetOrder()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->json(['error' => 'Method not allowed']);
            return;
        }

        try {
            $userId = $_SESSION['user_id'];

            // Leggi JSON body
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                // Fallback a form data
                $input = $_POST;
            }

            $widgetOrder = $input['widget_order'] ?? null; // Array di {widget_key, position}

            if (!is_array($widgetOrder)) {
                $this->json(['success' => false, 'error' => 'Ordine widget non valido'], 400);
                return;
            }

            foreach ($widgetOrder as $item) {
                if (!isset($item['widget_key']) || !isset($item['position'])) {
                    continue;
                }

                $widgetKey = $item['widget_key'];
                $position = (int) $item['position'];

                // Aggiorna o inserisci la posizione usando Eloquent
                $existing = UserWidget::where('user_id', $userId)
                    ->where('widget_key', $widgetKey)
                    ->first();

                if ($existing) {
                    $existing->update(['position_order' => $position]);
                } else {
                    UserWidget::create([
                        'user_id' => $userId,
                        'widget_key' => $widgetKey,
                        'position_order' => $position
                    ]);
                }
            }

            $this->logActivity('DASHBOARD', 'REORDER_WIDGETS', 'Riordinati widget dashboard');

            $this->json([
                'success' => true,
                'message' => 'Ordine widget aggiornato con successo'
            ]);

        } catch (Exception $e) {
            error_log("Error updating widget order: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Errore durante l\'aggiornamento dell\'ordine'], 500);
        }
    }

    /**
     * Ottiene i dati specifici per un widget
     */
    private function getWidgetData($widgetKey)
    {
        switch ($widgetKey) {
            case 'riparazioni':
                if (!$this->hasPermission('riparazioni'))
                    return null;
                $count = Repair::where(function ($query) {
                    $query->where('COMPLETA', 0)->orWhereNull('COMPLETA');
                })->count();
                return ['count' => $count, 'label' => 'Riparazioni Aperte'];

            case 'my_riparazioni':
                if (!$this->hasPermission('riparazioni'))
                    return null;
                $username = $_SESSION['username'] ?? $_SESSION['user_name'] ?? '';
                $count = Repair::where('UTENTE', $username)
                    ->where(function ($query) {
                        $query->where('COMPLETA', 0)->orWhereNull('COMPLETA');
                    })->count();
                return ['count' => $count, 'label' => 'Mie Riparazioni'];

            case 'quality_today':
                if (!$this->hasPermission('quality'))
                    return null;
                $today = date('Y-m-d');
                $count = QualityRecord::whereDate('data_controllo', $today)->count();
                return ['count' => $count, 'label' => 'Controlli Oggi'];

            case 'production_week':
                if (!$this->hasPermission('produzione'))
                    return null;
                $currentWeek = date('W');
                $currentYear = date('Y');

                // Usa ProductionRecord con Eloquent
                $total = ProductionRecord::whereRaw('WEEK(production_date) = ? AND YEAR(production_date) = ?', [$currentWeek, $currentYear])
                    ->selectRaw('SUM(COALESCE(taglio1, 0) + COALESCE(taglio2, 0)) + SUM(COALESCE(orlatura1, 0) + COALESCE(orlatura2, 0) + COALESCE(orlatura3, 0) + COALESCE(orlatura4, 0) + COALESCE(orlatura5, 0)) + SUM(COALESCE(manovia1, 0) + COALESCE(manovia2, 0)) as total')
                    ->value('total') ?? 0;
                return ['count' => $total, 'label' => 'Prod. Settimana'];

            case 'production_month':
                if (!$this->hasPermission('produzione'))
                    return null;
                $currentMonth = date('n'); // Numero mese
                $currentYear = date('Y');

                // Usa ProductionRecord con Eloquent
                $total = ProductionRecord::whereMonth('production_date', $currentMonth)
                    ->whereYear('production_date', $currentYear)
                    ->selectRaw('SUM(COALESCE(taglio1, 0) + COALESCE(taglio2, 0)) + SUM(COALESCE(orlatura1, 0) + COALESCE(orlatura2, 0) + COALESCE(orlatura3, 0) + COALESCE(orlatura4, 0) + COALESCE(orlatura5, 0)) + SUM(COALESCE(manovia1, 0) + COALESCE(manovia2, 0)) as total')
                    ->value('total') ?? 0;
                return ['count' => $total, 'label' => 'Prod. Mese'];

            case 'recent_exports':
                if (!$this->hasPermission('export'))
                    return null;
                // Assumendo che ci sia una relazione con terzisti nel modello ExportDocument
                $exports = ExportDocument::with('terzista')
                    ->select(['id as progressivo', 'data as created_at', 'id_terzista'])
                    ->orderBy('data', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($export) {
                        return [
                            'progressivo' => $export->progressivo,
                            'cliente' => $export->terzista->ragione_sociale ?? 'N/A',
                            'created_at' => $export->created_at
                        ];
                    })
                    ->toArray();
                return ['items' => $exports, 'label' => 'Export Recenti'];

            case 'quick_actions':
                $actions = $this->getQuickActions();
                return ['actions' => $actions, 'label' => 'Azioni Rapide'];

            case 'system_health':
                if (!$this->hasPermission('admin'))
                    return null;
                $health = $this->getSystemHealth();
                return array_merge($health, ['label' => 'Sistema']);

            case 'pending_approvals':
                $approvals = $this->getPendingApprovals();
                return ['items' => $approvals, 'label' => 'In Attesa'];

            case 'data_insights':
                $insights = $this->getDataInsights();
                return array_merge($insights, ['label' => 'Analytics']);

            default:
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
                'label' => 'Nuova Riparazione',
                'icon' => 'fas fa-plus',
                'url' => '/riparazioni/create',
                'color' => 'red'
            ];
        }

        if ($this->hasPermission('quality')) {
            $actions[] = [
                'label' => 'Controllo Qualità',
                'icon' => 'fas fa-clipboard-check',
                'url' => '/quality/create',
                'color' => 'green'
            ];
        }

        if ($this->hasPermission('export')) {
            $actions[] = [
                'label' => 'Nuovo Export',
                'icon' => 'fas fa-shipping-fast',
                'url' => '/export/create',
                'color' => 'blue'
            ];
        }

        if ($this->hasPermission('produzione')) {
            $actions[] = [
                'label' => 'Nuova Produzione',
                'icon' => 'fas fa-industry',
                'url' => '/produzione/new',
                'color' => 'yellow'
            ];
        }

        if ($this->hasPermission('scm')) {
            $actions[] = [
                'label' => 'Nuovo Lancio SCM',
                'icon' => 'fas fa-rocket',
                'url' => '/scm-admin/launches/create',
                'color' => 'orange'
            ];
        }

        return $actions;
    }

    /**
     * Ottiene lo stato del sistema
     */
    private function getSystemHealth()
    {
        $health = [
            'status' => 'good',
            'cpu' => 0,
            'memory' => 0,
            'disk' => 0
        ];

        // Simulazione dati sistema (in un ambiente reale useresti sys_getloadavg() etc.)
        $health['cpu'] = rand(10, 90);
        $health['memory'] = rand(30, 80);

        // Verifica spazio disco se possibile
        if (function_exists('disk_free_space')) {
            $bytes = disk_free_space('.');
            $totalBytes = disk_total_space('.');
            if ($bytes !== false && $totalBytes !== false) {
                $health['disk'] = 100 - (($bytes / $totalBytes) * 100);
            }
        }

        // Determina stato generale
        if ($health['cpu'] > 80 || $health['memory'] > 80 || $health['disk'] > 80) {
            $health['status'] = 'warning';
        }
        if ($health['cpu'] > 90 || $health['memory'] > 90 || $health['disk'] > 90) {
            $health['status'] = 'critical';
        }

        return $health;
    }

    /**
     * Ottiene approvazioni pendenti
     */
    private function getPendingApprovals()
    {
        // Placeholder - implementare secondo le necessità business
        return [];
    }

    /**
     * Ottiene insights sui dati
     */
    private function getDataInsights()
    {
        return [
            'charts' => [
                'repairs_trend' => $this->getRepairsTrend(),
                'quality_trend' => $this->getQualityTrend()
            ]
        ];
    }

    /**
     * Trend riparazioni ultime 7 giorni
     */
    private function getRepairsTrend()
    {
        if (!$this->hasPermission('riparazioni'))
            return [];

        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $displayDate = date('d/m/Y', strtotime("-$i days"));
            $count = Repair::whereDate('created_at', $date)->count();

            $data[] = ['date' => $displayDate, 'value' => $count];
        }

        return $data;
    }

    /**
     * Trend qualità ultime 7 giorni  
     */
    private function getQualityTrend()
    {
        if (!$this->hasPermission('quality'))
            return [];

        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $displayDate = date('d/m/Y', strtotime("-$i days"));
            $count = QualityRecord::whereDate('data_controllo', $date)->count();

            $data[] = ['date' => $displayDate, 'value' => $count];
        }

        return $data;
    }

    /**
     * Assicura che l'utente abbia record per tutti i widget disponibili
     * Crea automaticamente i record mancanti in stato disattivato
     */
    private function ensureUserWidgetRecords($userId)
    {
        try {
            // Ottieni tutti i widget disponibili e attivi usando Eloquent
            $availableWidgets = AvailableWidget::active()
                ->select(['widget_key', 'default_size', 'default_enabled'])
                ->get();

            // Ottieni i widget che l'utente ha già configurato
            $existingKeys = UserWidget::where('user_id', $userId)
                ->pluck('widget_key')
                ->toArray();

            // Trova i widget mancanti e creali
            foreach ($availableWidgets as $widget) {
                if (!in_array($widget->widget_key, $existingKeys)) {
                    // Widget mancante - crealo in stato disattivato per default
                    UserWidget::create([
                        'user_id' => $userId,
                        'widget_key' => $widget->widget_key,
                        'is_enabled' => false,
                        'position_order' => 999,
                        'widget_size' => $widget->default_size ?: 'medium',
                        'custom_settings' => null
                    ]);

                    error_log("Created missing widget record for user $userId: " . $widget->widget_key);
                }
            }

        } catch (Exception $e) {
            // Log l'errore ma non bloccare l'esecuzione
            error_log("Error ensuring user widget records for user $userId: " . $e->getMessage());
        }
    }
}