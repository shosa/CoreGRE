<?php
/**
 * Activity Log Controller
 * Gestisce il sistema di log delle attività utente
 */

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Setting;

class ActivityLogController extends BaseController
{
    /**
     * Mostra l'index dei log con filtri avanzati
     */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('log');

        // Parametri di filtro e paginazione
        $page = max(1, (int) ($this->input('page') ?? 1));
        $perPage = Setting::getInt('pagination_logs', 50);
        $search = $this->input('search');
        $category = $this->input('category');
        $activityType = $this->input('activity_type');
        $userId = $this->input('user_id');
        $dateFrom = $this->input('date_from');
        $dateTo = $this->input('date_to');

        try {
            // Query base con Eloquent
            $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

            // Filtri di ricerca
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('category', 'like', "%{$search}%")
                        ->orWhere('activity_type', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhere('text_query', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('user_name', 'like', "%{$search}%")
                                ->orWhere('nome', 'like', "%{$search}%");
                        });
                });
            }

            if ($category) {
                $query->byCategory($category);
            }

            if ($activityType) {
                $query->byType($activityType);
            }

            if ($userId) {
                $query->byUser($userId);
            }

            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            // Conteggio totale per paginazione
            $totalCount = $query->count();
            $totalPages = ceil($totalCount / $perPage);

            // Query finale con paginazione
            $logs = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

            // Ottieni dati per i filtri con Eloquent
            $categories = ActivityLog::select('category')
                ->distinct()
                ->whereNotNull('category')
                ->orderBy('category')
                ->pluck('category');

            $activityTypes = ActivityLog::select('activity_type')
                ->distinct()
                ->whereNotNull('activity_type')
                ->orderBy('activity_type')
                ->pluck('activity_type');

            $users = User::select('id', 'user_name', 'nome')
                ->orderBy('user_name')
                ->get();

            // Statistiche rapide
            $stats = $this->getActivityStats();

            $data = [
                'pageTitle' => 'Log Attività - ' . APP_NAME,
                'logs' => $logs,
                'categories' => $categories,
                'activityTypes' => $activityTypes,
                'users' => $users,
                'stats' => $stats,
                'currentSearch' => $search,
                'currentCategory' => $category,
                'currentActivityType' => $activityType,
                'currentUserId' => $userId,
                'currentDateFrom' => $dateFrom,
                'currentDateTo' => $dateTo,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalCount' => $totalCount,
                'perPage' => $perPage
            ];

            $this->render('logs.index', $data);

        } catch (Exception $e) {
            error_log("Errore Activity Log index: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante il caricamento dei log attività.';
            $this->redirect($this->url('/'));
        }
    }

    /**
     * Mostra i dettagli di un log specifico
     */
    public function show($id)
    {
        $this->requireAuth();
        $this->requirePermission('log');

        try {
            $log = ActivityLog::with('user')->find($id);

            if (!$log) {
                $_SESSION['alert_error'] = 'Log non trovato.';
                $this->redirect($this->url('/logs'));
                return;
            }

            // Calcola statistiche utente
            $userStats = [
                'total' => 0,
                'today' => 0,
                'week' => 0
            ];

            if ($log->user_id) {
                $userStats['total'] = ActivityLog::where('user_id', $log->user_id)->count();
                $userStats['today'] = ActivityLog::where('user_id', $log->user_id)
                    ->whereDate('created_at', date('Y-m-d'))
                    ->count();
                $userStats['week'] = ActivityLog::where('user_id', $log->user_id)
                    ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-7 days')))
                    ->count();
            }

            $data = [
                'pageTitle' => 'Dettaglio Log #' . $id . ' - ' . APP_NAME,
                'log' => $log,
                'userStats' => $userStats
            ];

            $this->render('logs.show', $data);

        } catch (Exception $e) {
            error_log("Errore Activity Log show: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante il caricamento del log.';
            $this->redirect($this->url('/logs'));
        }
    }

    /**
     * Elimina log selezionati (solo per admin)
     */
    public function delete()
    {
        $this->requireAuth();
        $this->requirePermission('log');

        if (!$this->isAdmin()) {
            $this->json(['error' => 'Operazione non autorizzata'], 403);
            return;
        }

        if (!$this->isPost()) {
            $this->json(['error' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $ids = $input['ids'] ?? [];

            if (empty($ids) || !is_array($ids)) {
                $this->json(['error' => 'Nessun ID fornito']);
                return;
            }

            // Sanitizza gli ID
            $ids = array_filter(array_map('intval', $ids));
            if (empty($ids)) {
                $this->json(['error' => 'ID non validi']);
                return;
            }

            $deleted = ActivityLog::whereIn('id', $ids)->delete();

            // Log dell'eliminazione
            $this->logActivity(
                'SYSTEM',
                'DELETE_LOGS',
                'Eliminati ' . count($ids) . ' log',
                'IDs: ' . implode(', ', $ids)
            );

            $this->json([
                'success' => true,
                'message' => count($ids) === 1 ? 'Log eliminato con successo' : count($ids) . ' log eliminati con successo'
            ]);

        } catch (Exception $e) {
            error_log("Errore eliminazione log: " . $e->getMessage());
            $this->json(['error' => 'Errore durante l\'eliminazione: ' . $e->getMessage()]);
        }
    }

    /**
     * Esporta i log in formato CSV
     */
    public function export()
    {
        $this->requireAuth();
        $this->requirePermission('log');

        // Applica gli stessi filtri dell'index
        $search = $this->input('search');
        $category = $this->input('category');
        $activityType = $this->input('activity_type');
        $userId = $this->input('user_id');
        $dateFrom = $this->input('date_from');
        $dateTo = $this->input('date_to');

        try {
            // Query con Eloquent
            $query = ActivityLog::with('user')->orderByDesc('created_at');

            // Applica gli stessi filtri
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('category', 'like', "%{$search}%")
                        ->orWhere('activity_type', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhere('text_query', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('user_name', 'like', "%{$search}%")
                                ->orWhere('nome', 'like', "%{$search}%");
                        });
                });
            }

            if ($category) {
                $query->where('category', $category);
            }

            if ($activityType) {
                $query->where('activity_type', $activityType);
            }

            if ($userId) {
                $query->where('user_id', $userId);
            }

            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            $logs = $query->get();

            // Genera CSV
            $filename = 'activity_log_' . date('Y-m-d_H-i-s') . '.csv';

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Pragma: no-cache');
            header('Expires: 0');

            $output = fopen('php://output', 'w');

            // BOM per UTF-8
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header CSV
            fputcsv($output, [
                'ID',
                'Data/Ora',
                'Utente',
                'Nome Utente',
                'Categoria',
                'Tipo Attività',
                'Descrizione',
                'Note',
                'Query'
            ]);

            // Dati
            foreach ($logs as $log) {
                fputcsv($output, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user ? $log->user->user_name : 'SYSTEM',
                    $log->user ? $log->user->nome : 'System User',
                    $log->category ?? '',
                    $log->activity_type ?? '',
                    $log->description ?? '',
                    $log->note ?? '',
                    $log->text_query ?? ''
                ]);
            }

            fclose($output);

            // Log dell'esportazione
            $this->logActivity(
                'SYSTEM',
                'EXPORT_LOGS',
                'Esportati ' . count($logs) . ' log in CSV',
                'File: ' . $filename
            );

            exit;

        } catch (Exception $e) {
            error_log("Errore export log: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante l\'esportazione.';
            $this->redirect($this->url('/logs'));
        }
    }

    /**
     * Pulisce i log più vecchi di X giorni
     */
    public function cleanup()
    {
        $this->requireAuth();

        if (!$this->isAdmin()) {
            $this->json(['error' => 'Operazione non autorizzata'], 403);
            return;
        }

        if (!$this->isPost()) {
            $this->json(['error' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $days = (int) ($input['days'] ?? 90);

            if ($days < 30) {
                $this->json(['error' => 'Non è possibile eliminare log più recenti di 30 giorni']);
                return;
            }

            // Elimina i log più vecchi di X giorni e ottiene il conteggio
            $deletedCount = ActivityLog::where('created_at', '< ', now()->subDays($days))->delete();

            if ($deletedCount === 0) {
                $this->json(['message' => 'Nessun log da eliminare']);
                return;
            }

            // Log della pulizia
            $this->logActivity(
                'SYSTEM',
                'CLEANUP_LOGS',
                "Pulizia log completata: eliminati {$deletedCount} record",
                "Log più vecchi di {$days} giorni"
            );

            $this->json([
                'success' => true,
                'message' => "Eliminati {$deletedCount} log più vecchi di {$days} giorni"
            ]);

        } catch (Exception $e) {
            error_log("Errore cleanup log: " . $e->getMessage());
            $this->json(['error' => 'Errore durante la pulizia: ' . $e->getMessage()]);
        }
    }

    /**
     * Ottiene statistiche sulle attività
     */
    private function getActivityStats()
    {
        try {
            $stats = [];

            // Attività oggi
            $stats['today'] = ActivityLog::today()->count();

            // Attività questa settimana
            $stats['week'] = ActivityLog::recent(7)->count();

            // Attività questo mese
            $stats['month'] = ActivityLog::recent(30)->count();

            // Categorie più attive
            $stats['top_categories'] = ActivityLog::recent(30)
                ->whereNotNull('category')
                ->groupBy('category')
                ->selectRaw('category, COUNT(*) as count')
                ->orderByDesc('count')
                ->limit(5)
                ->get();

            // Utenti più attivi
            $stats['top_users'] = ActivityLog::with('user')
                ->recent(30)
                ->groupBy('user_id')
                ->selectRaw('user_id, COUNT(*) as count')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'user_name' => $item->user ? $item->user->user_name : 'Sconosciuto',
                        'nome' => $item->user ? $item->user->nome : 'Sconosciuto',
                        'count' => $item->count
                    ];
                });

            return $stats;


        } catch (Exception $e) {
            error_log("Errore stats log: " . $e->getMessage());
            return [];
        }


    }
}