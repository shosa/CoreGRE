<?php

use App\Models\ScmLaboratory;
use App\Models\ScmLaunch;
use App\Models\ScmLaunchArticle;
use App\Models\ScmLaunchPhase;
use App\Models\ScmProgressTracking;
use App\Models\ScmStandardPhase;
use App\Models\ScmSettings;

class SCMAdminController extends BaseController
{
    /**
     * Dashboard principale SCM
     */
    public function index()
    {
        try {
            // Statistiche generali
            $stats = [
                'total_laboratories' => ScmLaboratory::where('is_active', 1)->count(),
                'total_launches' => ScmLaunch::count(),
                'pending_launches' => ScmLaunch::where('status', 'IN_PREPARAZIONE')->count(),
                'active_launches' => ScmLaunch::where('status', 'IN_LAVORAZIONE')->count(),
                'blocked_launches' => ScmLaunch::where('status', 'BLOCCATO')->count(),
                'completed_launches' => ScmLaunch::where('status', 'COMPLETATO')->count(),
            ];

            // Ultimi lanci creati
            $recentLaunches = ScmLaunch::with(['laboratory', 'articles'])
                ->withCount('articles')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Aggiungi proprietà calcolate agli oggetti
            $recentLaunches->each(function ($launch) {
                $launch->laboratory_name = $launch->laboratory->name ?? '';
                $launch->total_articles = $launch->articles_count;
                $launch->total_pairs = $launch->articles->sum('total_pairs');
            });

            $data = [
                'pageTitle' => 'SCM Admin Dashboard',
                'stats' => $stats,
                'recentLaunches' => $recentLaunches,
                'breadcrumb' => [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin', 'current' => true]
                ]
            ];

            $this->render('scm-admin/dashboard', $data);

        } catch (Exception $e) {
            error_log("Dashboard error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento della dashboard';
            $this->redirect($this->url('/'));
        }
    }

    /**
     * Gestione Laboratori
     */
    public function laboratories()
    {
        try {
            $search = $_GET['search'] ?? '';
            $status = $_GET['status'] ?? '';

            // Query Eloquent con filtri dinamici
            $query = ScmLaboratory::withCount([
                'launches as total_launches',
                'launches as active_launches' => function ($query) {
                    $query->whereIn('status', ['IN_LAVORAZIONE', 'BLOCCATO']);
                }
            ]);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            if ($status !== '') {
                $query->where('is_active', intval($status));
            }

            $laboratories = $query->orderBy('name')->get();

            $data = [
                'pageTitle' => 'Gestione Laboratori',
                'laboratories' => $laboratories,
                'search' => $search,
                'status' => $status,
                'breadcrumb' => [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                    ['title' => 'Laboratori', 'url' => '/scm-admin/laboratories', 'current' => true]
                ]
            ];

            $this->render('scm-admin/laboratories', $data);

        } catch (Exception $e) {
            error_log("Laboratories error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento dei laboratori';
            $this->redirect($this->url('/scm-admin'));
        }
    }

    public function settings()
    {
        try {
            // Recupero un unico record (id = 1)
            $settings = ScmSettings::find(1);

            // Se non esiste ancora, creo un record di default
            if (!$settings) {
                $settings = ScmSettings::create([
                    'id' => 1,
                    'system_name' => 'SCM Emmegiemme',
                    'company_name' => '',
                    'timezone' => 'Europe/Rome',
                    'language' => 'it',
                    'launch_number_prefix' => 'LAN',
                    'auto_start_phases' => 1,
                    'require_phase_notes' => 0,
                    'max_articles_per_launch' => 50,
                    'notify_launch_completed' => 1,
                    'notify_phase_blocked' => 1,
                    'notify_laboratory_login' => 0,
                    'notification_email' => null,
                    'session_timeout' => 120,
                    'max_login_attempts' => 5,
                    'password_min_length' => 8,
                    'require_password_symbols' => 0,
                ]);
            }

            $data = [
                'pageTitle' => 'Configurazione Sistema',
                'settings' => $settings,
                'breadcrumb' => [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                    ['title' => 'Configurazione', 'url' => '/scm-admin/settings', 'current' => true]
                ]
            ];

            $this->render('scm-admin/settings', $data);

        } catch (Exception $e) {
            error_log("Settings error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento delle impostazioni';
            $this->redirect($this->url('/scm-admin'));
        }
    }
    public function saveSettings()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirect($this->url('/scm-admin/settings'));
                return;
            }

            // Raccolta valori dal form
            $system_name = $_POST['system_name'] ?? 'SCM Emmegiemme';
            $company_name = $_POST['company_name'] ?? '';
            $timezone = $_POST['timezone'] ?? 'Europe/Rome';
            $language = $_POST['language'] ?? 'it';

            $launch_number_prefix = $_POST['launch_number_prefix'] ?? 'LAN';
            $auto_start_phases = $_POST['auto_start_phases'] ?? '1';
            $require_phase_notes = $_POST['require_phase_notes'] ?? '0';
            $max_articles_per_launch = intval($_POST['max_articles_per_launch'] ?? 50);

            $notify_launch_completed = isset($_POST['notify_launch_completed']) ? 1 : 0;
            $notify_phase_blocked = isset($_POST['notify_phase_blocked']) ? 1 : 0;
            $notify_laboratory_login = isset($_POST['notify_laboratory_login']) ? 1 : 0;
            $notification_email = $_POST['notification_email'] ?? null;

            $session_timeout = intval($_POST['session_timeout'] ?? 120);
            $max_login_attempts = intval($_POST['max_login_attempts'] ?? 5);
            $password_min_length = intval($_POST['password_min_length'] ?? 8);
            $require_password_symbols = $_POST['require_password_symbols'] ?? '0';

            // Aggiornamento con Eloquent
            $settings = ScmSettings::find(1);
            if ($settings) {
                $settings->update([
                    'system_name' => $system_name,
                    'company_name' => $company_name,
                    'timezone' => $timezone,
                    'language' => $language,
                    'launch_number_prefix' => $launch_number_prefix,
                    'auto_start_phases' => $auto_start_phases,
                    'require_phase_notes' => $require_phase_notes,
                    'max_articles_per_launch' => $max_articles_per_launch,
                    'notify_launch_completed' => $notify_launch_completed,
                    'notify_phase_blocked' => $notify_phase_blocked,
                    'notify_laboratory_login' => $notify_laboratory_login,
                    'notification_email' => $notification_email,
                    'session_timeout' => $session_timeout,
                    'max_login_attempts' => $max_login_attempts,
                    'password_min_length' => $password_min_length,
                    'require_password_symbols' => $require_password_symbols
                ]);
            }

            $_SESSION['alert_success'] = "Impostazioni salvate correttamente";
            $this->redirect($this->url('/scm-admin/settings'));

        } catch (Exception $e) {
            error_log("SaveSettings error: " . $e->getMessage());
            $_SESSION['alert_error'] = "Errore durante il salvataggio delle impostazioni";
            $this->redirect($this->url('/scm-admin/settings'));
        }
    }


    /**
     * Form creazione laboratorio
     */
    public function createLaboratory()
    {
        $data = [
            'pageTitle' => 'Nuovo Laboratorio',
            'laboratory' => [],
            'isEdit' => false,
            'breadcrumb' => [
                ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                ['title' => 'Laboratori', 'url' => '/scm-admin/laboratories'],
                ['title' => 'Nuovo Laboratorio', 'url' => '/scm-admin/laboratories/create', 'current' => true]
            ]
        ];

        $this->render('scm-admin/laboratory-form', $data);
    }

    /**
     * Salvataggio nuovo laboratorio
     */
    public function storeLaboratory()
    {
        try {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Validazione
            if (empty($name) || empty($email) || empty($username) || empty($password)) {
                $_SESSION['alert_error'] = 'Tutti i campi sono obbligatori';
                $this->redirect($this->url('/scm-admin/laboratories/create'));
                return;
            }

            // Controlla email univoca
            if (ScmLaboratory::where('email', $email)->exists()) {
                $_SESSION['alert_error'] = 'Email già utilizzata da un altro laboratorio';
                $this->redirect($this->url('/scm-admin/laboratories/create'));
                return;
            }

            // Controlla username univoco
            if (ScmLaboratory::where('username', $username)->exists()) {
                $_SESSION['alert_error'] = 'Username già utilizzato da un altro laboratorio';
                $this->redirect($this->url('/scm-admin/laboratories/create'));
                return;
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            ScmLaboratory::create([
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'password_hash' => $passwordHash,
                'is_active' => 1
            ]);

            $_SESSION['alert_success'] = 'Laboratorio creato con successo';
            $this->redirect($this->url('/scm-admin/laboratories'));

        } catch (Exception $e) {
            error_log("Store laboratory error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante la creazione del laboratorio';
            $this->redirect($this->url('/scm-admin/laboratories/create'));
        }
    }

    /**
     * Form modifica laboratorio
     */
    public function editLaboratory($laboratoryId)
    {
        try {
            $laboratory = ScmLaboratory::find($laboratoryId);

            if (!$laboratory) {
                $_SESSION['alert_error'] = 'Laboratorio non trovato';
                $this->redirect($this->url('/scm-admin/laboratories'));
                return;
            }

            $data = [
                'pageTitle' => 'Modifica Laboratorio',
                'laboratory' => $laboratory,
                'isEdit' => true,
                'breadcrumb' => [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                    ['title' => 'Laboratori', 'url' => '/scm-admin/laboratories'],
                    ['title' => 'Modifica Laboratorio', 'url' => '/scm-admin/laboratories/' . $laboratoryId . '/edit', 'current' => true]
                ]
            ];

            $this->render('scm-admin/laboratory-form', $data);

        } catch (Exception $e) {
            error_log("Edit laboratory error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento del laboratorio';
            $this->redirect($this->url('/scm-admin/laboratories'));
        }
    }

    /**
     * Aggiornamento laboratorio
     */
    public function updateLaboratory($laboratoryId)
    {
        try {
            $laboratory = ScmLaboratory::find($laboratoryId);
            $name = trim(string: $_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($name) || empty($email) || empty($username)) {
                $_SESSION['alert_error'] = 'Nome, email e username sono obbligatori';
                $this->redirect($this->url('/scm-admin/laboratories/' . $laboratoryId . '/edit'));
                return;
            }

            // Controlla email univoca (escluso il corrente)
            if (ScmLaboratory::where('email', $email)->where('id', '!=', $laboratoryId)->exists()) {
                $_SESSION['alert_error'] = 'Email già utilizzata da un altro laboratorio';
                $this->redirect($this->url('/scm-admin/laboratories/' . $laboratoryId . '/edit'));
                return;
            }

            // Controlla username univoco (escluso il corrente)
            if (ScmLaboratory::where('username', $username)->where('id', '!=', $laboratoryId)->exists()) {
                $_SESSION['alert_error'] = 'Username già utilizzato da un altro laboratorio';
                $this->redirect($this->url('/scm-admin/laboratories/' . $laboratoryId . '/edit'));
                return;
            }

            // Aggiorna con o senza password
            $updateData = [
                'name' => $name,
                'email' => $email,
                'username' => $username
            ];

            if (!empty($password)) {
                $updateData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $laboratory->update($updateData);

            // Log dell'attività
            $this->logActivity('SCM', 'UPDATE_LABORATORY', "Aggiornato laboratorio $name", "ID: $laboratoryId, Email: $email, Username: $username" . ($password ? ', Password modificata' : ''));

            $_SESSION['alert_success'] = 'Laboratorio aggiornato con successo';
            $this->redirect($this->url('/scm-admin/laboratories'));

        } catch (Exception $e) {
            error_log("Update laboratory error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante l\'aggiornamento del laboratorio';
            $this->redirect($this->url('/scm-admin/laboratories/' . $laboratoryId . '/edit'));
        }
    }

    /**
     * Toggle attivo/inattivo laboratorio
     */
    public function toggleLaboratory($laboratoryId)
    {
        try {
            $laboratory = ScmLaboratory::find($laboratoryId);

            if (!$laboratory) {
                $_SESSION['alert_error'] = 'Laboratorio non trovato';
                $this->redirect($this->url('/scm-admin/laboratories'));
                return;
            }

            $newStatus = $laboratory['is_active'] ? 0 : 1;

            ScmLaboratory::where('id', $laboratoryId)->update([
                'is_active' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $statusText = $newStatus ? 'attivato' : 'disattivato';

            // Log dell'attività
            $this->logActivity('SCM', 'TOGGLE_LABORATORY', "Laboratorio {$laboratory['name']} $statusText", "Stato: " . ($laboratory['is_active'] ? 'attivo' : 'inattivo') . " → " . ($newStatus ? 'attivo' : 'inattivo'));

            $_SESSION['alert_success'] = "Laboratorio {$statusText} con successo";
            $this->redirect($this->url('/scm-admin/laboratories'));

        } catch (Exception $e) {
            error_log("Toggle laboratory error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante la modifica dello stato del laboratorio';
            $this->redirect($this->url('/scm-admin/laboratories'));
        }
    }

    /**
     * Gestione Lanci
     */
    public function launches()
    {
        try {
            $search = $_GET['search'] ?? '';
            $laboratory = $_GET['laboratory_id'] ?? '';
            $status = $_GET['status'] ?? '';

            // Query Eloquent con relazioni
            $query = ScmLaunch::with(['laboratory', 'articles', 'phases', 'progressTracking']);

            if (!empty($search)) {
                $query->where('launch_number', 'LIKE', "%{$search}%");
            }

            if (!empty($laboratory)) {
                $query->where('laboratory_id', $laboratory);
            }

            if (!empty($status)) {
                $query->where('status', $status);
            }

            $launches = $query->orderBy('launch_number', 'desc')->get();

            // Aggiungi proprietà calcolate agli oggetti
            $launches->each(function ($launch) {
                // Calcoli basati su relazioni reali
                $totalArticles = $launch->articles->count();
                $totalPairs = $launch->articles->sum('total_pairs');
                $totalPhases = $launch->phases->count();
                $totalTasks = $totalArticles * $totalPhases;
                $completedTasks = $launch->progressTracking->where('status', 'COMPLETATA')->count();
                $completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

                // Aggiungi proprietà calcolate all'oggetto
                $launch->laboratory_name = $launch->laboratory ? $launch->laboratory->name : 'N/A';
                $launch->total_articles = $totalArticles;
                $launch->total_pairs = $totalPairs;
                $launch->total_phases = $totalPhases;
                $launch->completed_tasks = $completedTasks;
                $launch->total_tasks = $totalTasks;
                $launch->completed_phases = $launch->phases->filter(function ($phase) use ($launch) {
                    return $launch->progressTracking
                        ->where('phase_id', $phase->id)
                        ->where('status', 'COMPLETATA')
                        ->count() === $launch->articles->count();
                })->count();
                $launch->completion_percentage = $completionPercentage;
            });

            $laboratories = ScmLaboratory::where('is_active', 1)
                ->orderBy('name')
                ->select('id', 'name')
                ->get();

            // Calcola statistiche per le card (senza filtri applicati)
            $allLaunches = ScmLaunch::with('articles')->get();
            $stats = [
                'total_launches' => $allLaunches->count(),
                'preparation_launches' => $allLaunches->where('status', 'IN_PREPARAZIONE')->count(),
                'processing_launches' => $allLaunches->where('status', 'IN_LAVORAZIONE')->count(),
                'completed_launches' => $allLaunches->where('status', 'COMPLETATO')->count(),
                'blocked_launches' => $allLaunches->where('status', 'BLOCCATO')->count(),
                'preparation_pairs' => $allLaunches->where('status', 'IN_PREPARAZIONE')->sum(function ($launch) {
                    return $launch->articles->sum('total_pairs');
                }),
                'processing_pairs' => $allLaunches->where('status', 'IN_LAVORAZIONE')->sum(function ($launch) {
                    return $launch->articles->sum('total_pairs');
                }),
                'completed_pairs' => $allLaunches->where('status', 'COMPLETATO')->sum(function ($launch) {
                    return $launch->articles->sum('total_pairs');
                }),
                'total_pairs' => $allLaunches->sum(function ($launch) {
                    return $launch->articles->sum('total_pairs');
                })
            ];

            $data = [
                'pageTitle' => 'Gestione Lanci',
                'launches' => $launches,
                'laboratories' => $laboratories,
                'stats' => $stats,
                'search' => $search,
                'laboratory' => $laboratory,
                'status' => $status,
                'breadcrumb' => [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                    ['title' => 'Lanci', 'url' => '/scm-admin/launches', 'current' => true]
                ]
            ];

            $this->render('scm-admin/launches', $data);

        } catch (Exception $e) {
            error_log("Launches error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento dei lanci';
            $this->redirect($this->url('/scm-admin'));
        }
    }


    /**
     * Vista lanci in preparazione (pending)
     */
    public function pendingLaunches()
    {
        try {
            $launches = ScmLaunch::with(['laboratory', 'articles', 'phases', 'progressTracking'])
                ->where('status', 'IN_PREPARAZIONE')
                ->orderBy('created_at', 'desc')
                ->get();

            // Aggiungi proprietà calcolate agli oggetti
            $launches->each(function ($launch) {
                $totalArticles = $launch->articles->count();
                $totalPairs = $launch->articles->sum('total_pairs');
                $totalPhases = $launch->phases->count();
                $totalTasks = $launch->progressTracking->count();
                $completedTasks = $launch->progressTracking->where('status', 'COMPLETATA')->count();
                $completedPhases = $launch->phases->filter(function ($phase) use ($launch) {
                    return $launch->progressTracking
                        ->where('phase_id', $phase->id)
                        ->where('status', 'COMPLETATA')
                        ->count() === $launch->articles->count();
                })->count();

                $completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

                // Aggiungi proprietà calcolate all'oggetto
                $launch->laboratory_name = $launch->laboratory ? $launch->laboratory->name : null;
                $launch->total_articles = $totalArticles;
                $launch->total_pairs = $totalPairs;
                $launch->total_phases = $totalPhases;
                $launch->completed_tasks = $completedTasks;
                $launch->total_tasks = $totalTasks;
                $launch->completed_phases = $completedPhases;
                $launch->completion_percentage = $completionPercentage;
            });

            $data = [
                'pageTitle' => 'Lanci in Preparazione',
                'launches' => $launches,
                'breadcrumb' => [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                    ['title' => 'Lanci Pending', 'url' => '/scm-admin/launches/pending', 'current' => true]
                ]
            ];

            $this->render('scm-admin/launches', $data);

        } catch (Exception $e) {
            error_log("Pending launches error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento dei lanci in preparazione';
            $this->redirect($this->url('/scm-admin'));
        }
    }

    /**
     * Passa lancio da IN_PREPARAZIONE a IN_LAVORAZIONE
     */
    public function startLaunch($launchId)
    {
        try {
            $launch = ScmLaunch::where('id', $launchId)
                ->where('status', 'IN_PREPARAZIONE')
                ->first();

            if (!$launch) {
                $_SESSION['alert_error'] = 'Lancio non trovato o non in preparazione';
                $this->redirect($this->url('/scm-admin/launches/pending'));
                return;
            }

            $launch->update([
                'status' => 'IN_LAVORAZIONE',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Log dell'attività
            $this->logActivity('SCM', 'START_LAUNCH', "Avviato lancio #{$launch->launch_number}", "Stato: IN_PREPARAZIONE → IN_LAVORAZIONE");

            $_SESSION['alert_success'] = 'Lancio avviato e in lavorazione';
            $this->redirect($this->url('/scm-admin/launches/pending'));

        } catch (Exception $e) {
            error_log("Start launch error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante l\'avvio del lancio';
            $this->redirect($this->url('/scm-admin/launches/pending'));
        }
    }

    /**
     * Dashboard principale monitoring SCM
     */
    public function monitoring()
    {
        try {
            // Statistiche generali immediate
            $launches = ScmLaunch::with(['articles', 'progressTracking'])->get();

            $generalStats = [
                'active_launches' => $launches->whereIn('status', ['IN_LAVORAZIONE', 'BLOCCATO'])->count(),
                'completed_launches' => $launches->where('status', 'COMPLETATO')->count(),
                'pending_launches' => $launches->where('status', 'IN_PREPARAZIONE')->count(),
                'launches_today' => $launches->filter(function ($launch) {
                    return $launch->created_at->isToday();
                })->count(),
                'total_laboratories' => $launches->pluck('laboratory_id')->unique()->count(),
                'active_laboratories' => $launches->whereIn('status', ['IN_LAVORAZIONE', 'BLOCCATO'])
                    ->pluck('laboratory_id')->unique()->count(),
                'total_pairs_in_production' => $launches->sum(function ($launch) {
                    return $launch->articles->sum('total_pairs');
                }),
                'completed_tasks' => $launches->sum(function ($launch) {
                    return $launch->progressTracking->where('status', 'COMPLETATA')->count();
                }),
                'total_tasks' => $launches->sum(function ($launch) {
                    return $launch->progressTracking->count();
                })
            ];

            // Top 5 laboratori per attività
            $topLaboratories = ScmLaboratory::where('is_active', 1)
                ->with(['launches.articles', 'launches.progressTracking'])
                ->get();

            // Aggiungi proprietà calcolate agli oggetti
            $topLaboratories->each(function ($lab) {
                $activeLaunches = $lab->launches->whereIn('status', ['IN_LAVORAZIONE', 'BLOCCATO'])->count();
                $totalPairs = $lab->launches->sum(function ($launch) {
                    return $launch->articles->sum('total_pairs');
                });

                $allProgressTracking = $lab->launches->flatMap(function ($launch) {
                    return $launch->progressTracking;
                });

                $avgCompletion = 0;
                if ($allProgressTracking->count() > 0) {
                    $completedTasks = $allProgressTracking->where('status', 'COMPLETATA')->count();
                    $avgCompletion = round(($completedTasks / $allProgressTracking->count()) * 100, 1);
                }

                // Aggiungi proprietà calcolate all'oggetto
                $lab->active_launches = $activeLaunches;
                $lab->total_pairs = $totalPairs;
                $lab->avg_completion = $avgCompletion;
            });

            // Ordina e prendi i primi 5
            $topLaboratories = $topLaboratories
                ->sortByDesc('active_launches')
                ->sortByDesc('total_pairs')
                ->take(5)
                ->values();

            // Lanci critici (bloccati o con ritardo)
            $weekAgo = date('Y-m-d', strtotime('-7 days'));
            $criticalLaunches = ScmLaunch::with(['laboratory', 'progressTracking'])
                ->where(function ($query) use ($weekAgo) {
                    $query->where('status', 'BLOCCATO')
                        ->orWhere(function ($subQuery) use ($weekAgo) {
                            $subQuery->where('status', 'IN_LAVORAZIONE')
                                ->where('launch_date', '<', $weekAgo);
                        });
                })
                ->get();

            // Aggiungi proprietà calcolate agli oggetti
            $criticalLaunches->each(function ($launch) {
                $launch->laboratory_name = $launch->laboratory->name;
                $launch->total_tasks = $launch->progressTracking->count();
                $launch->completed_tasks = $launch->progressTracking->where('status', 'COMPLETATA')->count();
            });

            // Ordina
            $criticalLaunches = $criticalLaunches->sortBy(function ($launch) {
                return [$launch->status === 'BLOCCATO' ? 1 : 2, $launch->launch_date];
            })
                ->take(10)
                ->values();

            // Attività recenti (ultime 24h)
            $yesterday = date('Y-m-d H:i:s', strtotime('-1 day'));
            $recentActivity = ScmLaunch::with(['laboratory', 'progressTracking'])
                ->whereHas('progressTracking', function ($query) use ($yesterday) {
                    $query->where('completed_at', '>=', $yesterday);
                })
                ->get();

            // Aggiungi proprietà calcolate agli oggetti
            $recentActivity->each(function ($launch) use ($yesterday) {
                $launch->laboratory_name = $launch->laboratory->name;
                $launch->tasks_completed_24h = $launch->progressTracking
                    ->where('completed_at', '>=', $yesterday)
                    ->count();
            });

            // Ordina e limita
            $recentActivity = $recentActivity
                ->sortByDesc('tasks_completed_24h')
                ->take(10)
                ->values();

            $data = [
                'pageTitle' => 'Monitoring SCM - Dashboard',
                'generalStats' => $generalStats,
                'topLaboratories' => $topLaboratories,
                'criticalLaunches' => $criticalLaunches,
                'recentActivity' => $recentActivity,
                'viewType' => 'dashboard',
                'breadcrumb' => [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                    ['title' => 'Monitoring Dashboard', 'url' => '/scm-admin/monitoring', 'current' => true]
                ]
            ];

            $this->render('scm-admin/monitoring-dashboard', $data);

        } catch (Exception $e) {
            error_log("Monitoring dashboard error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento della dashboard di monitoring';
            $this->redirect($this->url('/scm-admin'));
        }
    }

    /**
     * Monitor lanci attivi - Vista dettagliata
     */
    public function monitoringLaunches()
    {
        try {
            // Gestisci filtri da parametri GET
            $search = $_GET['search'] ?? '';
            $laboratory = $_GET['laboratory_id'] ?? '';
            $status = $_GET['status'] ?? '';

            // Se è specificato un laboratorio, questa è una vista dedicata al laboratorio
            $isLaboratoryView = !empty($laboratory);
            $laboratoryDetails = null;

            if ($isLaboratoryView) {
                // Ottieni dettagli del laboratorio specifico
                $laboratoryDetails = ScmLaboratory::where('id', $laboratory)
                    ->where('is_active', 1)
                    ->first();

                if (!$laboratoryDetails) {
                    $_SESSION['alert_error'] = 'Laboratorio non trovato';
                    $this->redirect($this->url('/scm-admin/monitoring/launches'));
                    return;
                }
            }

            // Costruiamo la query Eloquent con le condizioni
            $query = ScmLaunch::with(['laboratory', 'articles', 'phases', 'progressTracking'])
                ->whereIn('status', ['IN_LAVORAZIONE', 'BLOCCATO', 'COMPLETATO']);

            if (!empty($search)) {
                $query->where('launch_number', 'LIKE', "%{$search}%");
            }

            if (!empty($laboratory)) {
                $query->where('laboratory_id', $laboratory);
            }

            if (!empty($status)) {
                $query->where('status', $status);
            }

            $launches = $query->get()
                ->each(function ($launch) {
                    $totalArticles = $launch->articles->count();
                    $totalPairs = $launch->articles->sum('total_pairs');
                    $totalPhases = $launch->phases->count();
                    $totalTasks = $launch->progressTracking->count();
                    $completedTasks = $launch->progressTracking->where('status', 'COMPLETATA')->count();

                    // Calcola fasi completate (fasi dove tutti gli articoli sono completati)
                    $completedPhases = $launch->phases->filter(function ($phase) use ($launch) {
                        $phaseTasksTotal = $launch->progressTracking->where('phase_id', $phase->id)->count();
                        $phaseTasksCompleted = $launch->progressTracking
                            ->where('phase_id', $phase->id)
                            ->where('status', 'COMPLETATA')
                            ->count();
                        return $phaseTasksTotal > 0 && $phaseTasksTotal === $phaseTasksCompleted;
                    })->count();

                    $completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

                    // Aggiungi proprietà calcolate all'oggetto
                    $launch->laboratory_name = $launch->laboratory ? $launch->laboratory->name : null;
                    $launch->total_articles = $totalArticles;
                    $launch->total_pairs = $totalPairs;
                    $launch->total_phases = $totalPhases;
                    $launch->completed_tasks = $completedTasks;
                    $launch->total_tasks = $totalTasks;
                    $launch->completed_phases = $completedPhases;
                    $launch->completion_percentage = $completionPercentage;
                })
                ->sortBy(function ($launch) {
                    $statusOrder = [
                        'BLOCCATO' => 1,
                        'IN_LAVORAZIONE' => 2,
                        'COMPLETATO' => 3
                    ];
                    return [$statusOrder[$launch->status] ?? 4, -strtotime($launch->launch_date)];
                })
                ->values();

            // Calcola statistiche specifiche
            if ($isLaboratoryView) {
                // Statistiche base per lanci e paia del laboratorio
                $labLaunches = ScmLaunch::with('articles')
                    ->where('laboratory_id', $laboratory)
                    ->whereIn('status', ['IN_LAVORAZIONE', 'BLOCCATO', 'COMPLETATO'])
                    ->get();

                $labStats = [
                    'lanci_in_corso' => $labLaunches->where('status', 'IN_LAVORAZIONE')->count(),
                    'lanci_bloccati' => $labLaunches->where('status', 'BLOCCATO')->count(),
                    'lanci_completati' => $labLaunches->where('status', 'COMPLETATO')->count(),
                    'paia_in_lavorazione' => $labLaunches->where('status', 'IN_LAVORAZIONE')->sum(function ($launch) {
                        return $launch->articles->sum('total_pairs');
                    }),
                    'paia_bloccate' => $labLaunches->where('status', 'BLOCCATO')->sum(function ($launch) {
                        return $launch->articles->sum('total_pairs');
                    }),
                    'paia_completate' => $labLaunches->where('status', 'COMPLETATO')->sum(function ($launch) {
                        return $launch->articles->sum('total_pairs');
                    }),
                    'paia_totali' => $labLaunches->sum(function ($launch) {
                        return $launch->articles->sum('total_pairs');
                    })
                ];

                // Calcola paia di articoli che hanno completato l'ultima fase
                $pairsFinalPhase = ['paia_ultima_fase' => 0];

                foreach ($labLaunches as $launch) {
                    $maxPhaseOrder = $launch->phases->max('phase_order');
                    if ($maxPhaseOrder) {
                        $finalPhase = $launch->phases->where('phase_order', $maxPhaseOrder)->first();
                        if ($finalPhase) {
                            $completedArticles = $launch->articles->filter(function ($article) use ($launch, $finalPhase) {
                                return $launch->progressTracking
                                    ->where('article_id', $article->id)
                                    ->where('phase_id', $finalPhase->id)
                                    ->where('status', 'COMPLETATA')
                                    ->count() > 0;
                            });
                            $pairsFinalPhase['paia_ultima_fase'] += $completedArticles->sum('total_pairs');
                        }
                    }
                }

                $stats = [
                    'in_lavorazione' => $labStats['paia_in_lavorazione'] ?? 0,
                    'bloccate' => $labStats['paia_bloccate'] ?? 0,
                    'completate' => $labStats['paia_completate'] ?? 0,
                    'ultima_fase' => $pairsFinalPhase['paia_ultima_fase'] ?? 0,
                    'totali' => $labStats['paia_totali'] ?? 0,
                    'lanci_in_corso' => $labStats['lanci_in_corso'] ?? 0,
                    'lanci_bloccati' => $labStats['lanci_bloccati'] ?? 0,
                    'lanci_completati' => $labStats['lanci_completati'] ?? 0
                ];
            } else {
                // Statistiche generali per i lanci (vista globale)
                $stats = [
                    // lanci attivi (in lavorazione o bloccati)
                    'active_launches' => $launches->filter(fn($l) => in_array($l->status, ['IN_LAVORAZIONE', 'BLOCCATO']))->count(),

                    // lanci creati oggi
                    'launches_today' => $launches->filter(fn($l) => $l->created_at->format('Y-m-d') === date('Y-m-d'))->count(),

                    // laboratori online (distinti e non nulli)
                    'online_laboratories' => $launches->pluck('laboratory_id')->filter()->unique()->count(),

                    // laboratori totali (stesso calcolo, forse online e total coincidono nel tuo caso)
                    'total_laboratories' => $launches->pluck('laboratory_id')->unique()->count(),

                    // fasi attive = tutte le task - quelle completate
                    'active_phases' => $launches->sum('total_tasks') - $launches->sum('completed_tasks'),

                    // fasi completate oggi (qui serve capire se hai un campo tipo completed_at per calcolarle)
                    'phases_completed_today' => 0,

                    // paia in lavorazione
                    'pairs_processing' => $launches->filter(fn($l) => $l->status === 'IN_LAVORAZIONE')->sum('total_pairs'),

                    // paia totali
                    'total_pairs' => $launches->sum('total_pairs'),
                ];
            }

            // Ottieni tutti i laboratori attivi per il filtro
            $activeLaboratories = ScmLaboratory::where('is_active', 1)
                ->orderBy('name')
                ->select('id', 'name')
                ->get();

            // Prepara il titolo e breadcrumb dinamici
            if ($isLaboratoryView) {
                $pageTitle = 'Monitoring ' . htmlspecialchars($laboratoryDetails['name']);
                $breadcrumb = [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                    ['title' => 'Monitoring', 'url' => '/scm-admin/monitoring'],
                    ['title' => 'Laboratori', 'url' => '/scm-admin/monitoring/laboratories'],
                    ['title' => htmlspecialchars($laboratoryDetails['name']), 'url' => '/scm-admin/monitoring/launches?laboratory_id=' . $laboratory, 'current' => true]
                ];
            } else {
                $pageTitle = 'Monitoring Lanci - Dettaglio';
                $breadcrumb = [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                    ['title' => 'Monitoring', 'url' => '/scm-admin/monitoring'],
                    ['title' => 'Lanci', 'url' => '/scm-admin/monitoring/launches', 'current' => true]
                ];
            }

            $data = [
                'pageTitle' => $pageTitle,
                'launches' => $launches,
                'activeLaboratories' => $activeLaboratories,
                'stats' => $stats,
                'search' => $search,
                'laboratory' => $laboratory,
                'status' => $status,
                'viewType' => 'launches',
                'isLaboratoryView' => $isLaboratoryView,
                'laboratoryDetails' => $laboratoryDetails,
                'breadcrumb' => $breadcrumb
            ];

            $this->render('scm-admin/monitoring-launches', $data);

        } catch (Exception $e) {
            error_log("Monitoring launches error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento del monitoraggio lanci';
            $this->redirect($this->url('/scm-admin'));
        }
    }

    /**
     * Monitor laboratori
     */
    public function monitoringLaboratories()
    {
        try {
            $laboratories = ScmLaboratory::where('is_active', 1)
                ->with(['launches.articles', 'launches.progressTracking'])
                ->orderBy('name')
                ->get();

            // Aggiungi proprietà calcolate agli oggetti
            $laboratories->each(function ($lab) {
                $launches = $lab->launches;

                $totalLaunches = $launches->count();
                $activeLaunches = $launches->whereIn('status', ['IN_LAVORAZIONE', 'BLOCCATO'])->count();
                $completedLaunches = $launches->where('status', 'COMPLETATO')->count();

                $totalPairs = $launches->sum(function ($launch) {
                    return $launch->articles->sum('total_pairs');
                });

                $allTasks = $launches->flatMap(function ($launch) {
                    return $launch->progressTracking;
                });
                $completedTasks = $allTasks->where('status', 'COMPLETATA')->count();
                $totalTasks = $allTasks->count();

                $completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

                // Aggiungi proprietà calcolate all'oggetto
                $lab->total_launches = $totalLaunches;
                $lab->active_launches = $activeLaunches;
                $lab->completed_launches = $completedLaunches;
                $lab->total_pairs = $totalPairs;
                $lab->completed_tasks = $completedTasks;
                $lab->total_tasks = $totalTasks;
                $lab->completion_percentage = $completionPercentage;
            });

            // Calcola statistiche generali per i laboratori
            $stats = [
                'active_launches' => $laboratories->sum('active_launches'),
                'launches_today' => 0, // Would need additional query for today's launches
                'online_laboratories' => $laboratories->filter(function ($lab) {
                    return $lab->active_launches > 0;
                })->count(),
                'total_laboratories' => $laboratories->count(),
                'active_phases' => $laboratories->sum('total_tasks') - $laboratories->sum('completed_tasks'),
                'phases_completed_today' => 0, // Would need additional query for today's completions
                'pairs_processing' => $laboratories->sum('total_pairs'),
                'total_pairs' => $laboratories->sum('total_pairs')
            ];

            // Per il monitoring laboratori, usiamo la lista dei laboratori come "activeLaboratories"
            // e aggiungiamo il campo last_activity
            $laboratories->each(function ($lab) {
                $lastUpdate = ScmLaunch::where('laboratory_id', $lab->id)
                    ->max('updated_at');
                $lab->last_activity = $lastUpdate ?? date('Y-m-d H:i:s');
            });

            $data = [
                'pageTitle' => 'Monitoring Laboratori - Dettaglio',
                'laboratories' => $laboratories,
                'activeLaboratories' => $laboratories, // Per compatibilità con la view
                'stats' => $stats,
                'viewType' => 'laboratories',
                'breadcrumb' => [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                    ['title' => 'Monitoring', 'url' => '/scm-admin/monitoring'],
                    ['title' => 'Laboratori', 'url' => '/scm-admin/monitoring/laboratories', 'current' => true]
                ]
            ];

            $this->render('scm-admin/monitoring-laboratories', $data);

        } catch (Exception $e) {
            error_log("Monitoring laboratories error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento del monitoraggio laboratori';
            $this->redirect($this->url('/scm-admin'));
        }
    }

    /**
     * Carica fasi standard per i form
     */
    public function loadStandardPhases()
    {
        header('Content-Type: application/json');

        try {
            $standardPhases = ScmStandardPhase::orderBy('phase_order')
                ->select('id', 'phase_name', 'phase_order')
                ->get();

            echo json_encode([
                'success' => true,
                'phases' => $standardPhases
            ]);

        } catch (Exception $e) {
            error_log("Load standard phases error: " . $e->getMessage());

            echo json_encode([
                'success' => false,
                'error' => 'Errore nel caricamento delle fasi standard'
            ]);
        }
        exit;
    }

    /**
     * Gestione fasi standard
     */
    public function standardPhases()
    {
        try {
            $standardPhases = ScmStandardPhase::orderBy('phase_order')->get();

            $data = [
                'pageTitle' => 'Fasi Standard',
                'standardPhases' => $standardPhases,
                'breadcrumb' => [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                    ['title' => 'Fasi Standard', 'url' => '/scm-admin/standard-phases', 'current' => true]
                ]
            ];

            $this->render('scm-admin/standard-phases', $data);

        } catch (Exception $e) {
            error_log("Standard phases error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento delle fasi standard';
            $this->redirect($this->url('/scm-admin'));
        }
    }

    /**
     * Crea nuova fase standard
     */
    public function createStandardPhase()
    {
        header('Content-Type: application/json');

        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $phaseName = trim($input['phase_name'] ?? '');
            $description = trim($input['description'] ?? '');
            $category = trim($input['category'] ?? 'production');
            $phaseOrder = (int) ($input['phase_order'] ?? 1);

            ScmStandardPhase::create([
                'phase_name' => $phaseName,
                'description' => $description,
                'category' => $category,
                'phase_order' => $phaseOrder
            ]);

            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            error_log("Create standard phase error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Errore nella creazione della fase']);
        }
        exit;
    }

    /**
     * Gestione form lanci - CREATE
     */
    public function createLaunch()
    {
        try {
            // Ottieni laboratori attivi
            $laboratories = ScmLaboratory::where('is_active', 1)
                ->orderBy('name')
                ->select('id', 'name')
                ->get();

            $data = [
                'pageTitle' => 'Nuovo Lancio',
                'isEdit' => false,
                'launch' => [],
                'laboratories' => $laboratories,
                'breadcrumb' => [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                    ['title' => 'Lanci', 'url' => '/scm-admin/launches'],
                    ['title' => 'Nuovo Lancio', 'url' => '/scm-admin/launches/create', 'current' => true]
                ]
            ];

            $this->render('scm-admin/launch-form', $data);

        } catch (Exception $e) {
            error_log("Create launch form error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento del form';
            $this->redirect($this->url('/scm-admin/launches'));
        }
    }

    /**
     * Salva nuovo lancio
     */
    public function storeLaunch()
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('/scm-admin/launches'));
            return;
        }

        try {
            // Validazione base
            $launchNumber = trim($this->input('launch_number'));
            $launchDate = trim($this->input('launch_date'));
            $laboratoryId = (int) $this->input('laboratory_id');
            $status = trim($this->input('status'));
            $notes = trim($this->input('notes'));

            // Validazione campi obbligatori
            $errors = [];
            if (empty($launchNumber))
                $errors[] = 'Numero lancio obbligatorio';
            if (empty($launchDate))
                $errors[] = 'Data lancio obbligatoria';
            if ($laboratoryId <= 0)
                $errors[] = 'Laboratorio obbligatorio';

            // Valida fasi
            $phases = $this->input('phases') ?? [];
            $phases = array_filter($phases, function ($phase) {
                return !empty(trim($phase));
            });
            if (empty($phases))
                $errors[] = 'Almeno una fase è obbligatoria';

            // Valida articoli
            $articles = $this->input('articles') ?? [];
            $validArticles = [];
            foreach ($articles as $article) {
                $articleName = trim($article['article_name'] ?? '');
                $totalPairs = (int) ($article['total_pairs'] ?? 0);

                if (!empty($articleName) && $totalPairs > 0) {
                    $validArticles[] = [
                        'article_name' => $articleName,
                        'total_pairs' => $totalPairs,
                        'notes' => trim($article['notes'] ?? '')
                    ];
                }
            }
            if (empty($validArticles))
                $errors[] = 'Almeno un articolo con nome e quantità è obbligatorio';

            if (!empty($errors)) {
                $_SESSION['alert_error'] = 'Errori di validazione: ' . implode(', ', $errors);
                $this->redirect($this->url('/scm-admin/launches/create'));
                return;
            }

            // Verifica unicità numero lancio
            if (ScmLaunch::where('launch_number', $launchNumber)->exists()) {
                $_SESSION['alert_error'] = 'Numero lancio già esistente';
                $this->redirect($this->url('/scm-admin/launches/create'));
                return;
            }

            // Crea lancio
            $launch = ScmLaunch::create([
                'launch_number' => $launchNumber,
                'launch_date' => $launchDate,
                'laboratory_id' => $laboratoryId,
                'status' => $status,
                'phases_cycle' => implode(';', $phases),
                'notes' => $notes
            ]);

            // Crea fasi del lancio e mantieni gli ID
            $phaseIds = [];
            foreach ($phases as $index => $phaseName) {
                $phase = ScmLaunchPhase::create([
                    'launch_id' => $launch->id,
                    'phase_name' => trim($phaseName),
                    'phase_order' => $index + 1
                ]);
                $phaseIds[] = $phase->id;
            }

            // Crea articoli del lancio e mantieni gli ID
            $articleIds = [];
            foreach ($validArticles as $index => $article) {
                $articleModel = ScmLaunchArticle::create([
                    'launch_id' => $launch->id,
                    'article_name' => $article['article_name'],
                    'total_pairs' => $article['total_pairs'],
                    'notes' => $article['notes'],
                    'article_order' => $index + 1
                ]);
                $articleIds[] = $articleModel->id;
            }

            // Crea record di progress tracking per ogni combinazione articolo/fase
            foreach ($articleIds as $articleId) {
                foreach ($phaseIds as $phaseId) {
                    ScmProgressTracking::create([
                        'launch_id' => $launch->id,
                        'article_id' => $articleId,
                        'phase_id' => $phaseId,
                        'status' => 'NON_INIZIATA'
                    ]);
                }
            }

            // Log dell'attività
            $this->logActivity('SCM', 'CREATE_LAUNCH', "Creato lancio #$launchNumber", "Laboratorio: $laboratoryId, Articoli: " . count($articleIds) . ", Fasi: " . count($phaseIds));

            $_SESSION['alert_success'] = 'Lancio creato con successo';
            $this->redirect($this->url('/scm-admin/launches/' . $launch->id));

        } catch (Exception $e) {
            error_log("Store launch error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante la creazione del lancio: ' . $e->getMessage();
            $this->redirect($this->url('/scm-admin/launches/create'));
        }
    }

    /**
     * Mostra dettagli di un lancio
     */
    public function showLaunch($id)
    {
        try {
            $launch = ScmLaunch::with('laboratory')->find($id);

            if (!$launch) {
                $_SESSION['alert_error'] = 'Lancio non trovato';
                $this->redirect($this->url('/scm-admin/launches'));
                return;
            }

            // Ottieni articoli del lancio
            $articles = ScmLaunchArticle::where('launch_id', $id)
                ->orderBy('article_order')
                ->get();

            // Ottieni fasi del lancio
            $phases = ScmLaunchPhase::where('launch_id', $id)
                ->orderBy('phase_order')
                ->get();

            // Ottieni il progresso per ogni combinazione articolo/fase
            $progress = ScmProgressTracking::with(['article', 'phase'])
                ->where('launch_id', $id)
                ->get()
                ->sortBy(function ($item) {
                    return [$item->article->article_order, $item->phase->phase_order];
                });

            // Calcola statistiche
            $totalPhases = $phases->count() * $articles->count();
            $totalPairs = $articles->sum('total_pairs');
            $articleStats = [];
            $completedPhases = 0;

            // Organizza progresso per articolo e calcola completamento
            $articles->each(function ($article) use ($progress, $phases, &$articleStats, &$completedPhases) {
                $articleProgress = $progress->where('article_id', $article->id);
                $articleCompletedPhases = $articleProgress->where('status', 'COMPLETATA')->count();
                $completionPercentage = $phases->count() > 0 ? round(($articleCompletedPhases / $phases->count()) * 100) : 0;

                $articleStats[$article->id] = [
                    'completed_phases' => $articleCompletedPhases,
                    'total_phases' => $phases->count(),
                    'completion_percentage' => $completionPercentage
                ];

                $completedPhases += $articleCompletedPhases;

                // Aggiungi proprietà calcolata all'oggetto
                $article->completion_percentage = $completionPercentage;
            });

            $stats = [
                'completion_percentage' => $totalPhases > 0 ? round(($completedPhases / $totalPhases) * 100) : 0,
                'completed_phases' => $completedPhases,
                'total_phases' => $totalPhases,
                'total_pairs' => $totalPairs
            ];

            // Aggiungi proprietà calcolata al lancio
            $launch->laboratory_name = $launch->laboratory ? $launch->laboratory->name : '';

            $data = [
                'pageTitle' => 'Dettagli Lancio #' . $launch->launch_number,
                'launch' => $launch,
                'articles' => $articles,
                'phases' => $phases,
                'progress' => $progress,
                'stats' => $stats,
                'articleStats' => $articleStats,
                'breadcrumb' => [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                    ['title' => 'Lanci', 'url' => '/scm-admin/launches'],
                    ['title' => 'Lancio #' . $launch->launch_number, 'url' => '/scm-admin/launches/' . $id, 'current' => true]
                ]
            ];

            $this->render('scm-admin/launch-detail', $data);

        } catch (Exception $e) {
            error_log("Show launch error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento del lancio';
            $this->redirect($this->url('/scm-admin/launches'));
        }
    }

    /**
     * Modifica lancio
     */
    public function editLaunch($id)
    {
        try {
            $launch = ScmLaunch::find($id);

            if (!$launch) {
                $_SESSION['alert_error'] = 'Lancio non trovato';
                $this->redirect($this->url('/scm-admin/launches'));
                return;
            }

            // Ottieni laboratori attivi
            $laboratories = ScmLaboratory::where('is_active', 1)
                ->orderBy('name')
                ->select('id', 'name')
                ->get();

            // Ottieni articoli del lancio
            $articles = ScmLaunchArticle::where('launch_id', $id)
                ->orderBy('article_order')
                ->get();

            // Ottieni fasi del lancio
            $phases = ScmLaunchPhase::where('launch_id', $id)
                ->orderBy('phase_order')
                ->pluck('phase_name')
                ->toArray();

            // Converti il lancio per compatibilità con la view
            $launchArray = $launch->toArray();
            $launchArray['articles'] = $articles->toArray();

            $data = [
                'pageTitle' => 'Modifica Lancio #' . $launch->launch_number,
                'isEdit' => true,
                'launch' => $launchArray,
                'laboratories' => $laboratories,
                'articles' => $articles,
                'phases' => $phases,
                'breadcrumb' => [
                    ['title' => 'SCM Admin', 'url' => '/scm-admin'],
                    ['title' => 'Lanci', 'url' => '/scm-admin/launches'],
                    ['title' => 'Modifica Lancio #' . $launch->launch_number, 'url' => '/scm-admin/launches/' . $id . '/edit', 'current' => true]
                ]
            ];

            $this->render('scm-admin/launch-form', $data);

        } catch (Exception $e) {
            error_log("Edit launch error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento del lancio per la modifica';
            $this->redirect($this->url('/scm-admin/launches'));
        }
    }

    /**
     * Aggiorna lancio
     */
    public function updateLaunch($id)
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('/scm-admin/launches'));
            return;
        }

        try {
            // Validazione base
            $launchNumber = trim($this->input('launch_number'));
            $launchDate = trim($this->input('launch_date'));
            $laboratoryId = (int) $this->input('laboratory_id');
            $status = trim($this->input('status'));
            $notes = trim($this->input('notes'));

            // Validazione campi obbligatori
            $errors = [];
            if (empty($launchNumber))
                $errors[] = 'Numero lancio obbligatorio';
            if (empty($launchDate))
                $errors[] = 'Data lancio obbligatoria';
            if ($laboratoryId <= 0)
                $errors[] = 'Laboratorio obbligatorio';

            if (!empty($errors)) {
                $_SESSION['alert_error'] = 'Errori di validazione: ' . implode(', ', $errors);
                $this->redirect($this->url('/scm-admin/launches/' . $id . '/edit'));
                return;
            }

            // Verifica che il lancio esista
            $existingLaunch = ScmLaunch::find($id);
            if (!$existingLaunch) {
                $_SESSION['alert_error'] = 'Lancio non trovato';
                $this->redirect($this->url('/scm-admin/launches'));
                return;
            }

            // Verifica unicità numero lancio (escludendo il lancio corrente)
            $existing = ScmLaunch::where('launch_number', $launchNumber)
                ->where('id', '!=', $id)
                ->exists();
            if ($existing) {
                $_SESSION['alert_error'] = 'Numero lancio già esistente';
                $this->redirect($this->url('/scm-admin/launches/' . $id . '/edit'));
                return;
            }

            // Aggiorna lancio
            $existingLaunch->update([
                'launch_number' => $launchNumber,
                'launch_date' => $launchDate,
                'laboratory_id' => $laboratoryId,
                'status' => $status,
                'notes' => $notes
            ]);

            $_SESSION['alert_success'] = 'Lancio aggiornato con successo';
            $this->redirect($this->url('/scm-admin/launches/' . $id));

        } catch (Exception $e) {
            // Rollback removed - Eloquent handles transactions automatically
            error_log("Update launch error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante l\'aggiornamento del lancio: ' . $e->getMessage();
            $this->redirect($this->url('/scm-admin/launches/' . $id . '/edit'));
        }
    }

    /**
     * Elimina lancio (solo se in preparazione)
     */
    public function deleteLaunch($id)
    {
        try {
            $launch = ScmLaunch::find($id);

            if (!$launch) {
                $_SESSION['alert_error'] = 'Lancio non trovato';
                $this->redirect($this->url('/scm-admin/launches'));
                return;
            }

            // Permetti eliminazione solo se il lancio è in preparazione
            if ($launch->status !== 'IN_PREPARAZIONE') {
                $_SESSION['alert_error'] = 'È possibile eliminare solo lanci in preparazione';
                $this->redirect($this->url('/scm-admin/launches'));
                return;
            }

            // Elimina dati correlati (le foreign key CASCADE si occupano della rimozione automatica)
            ScmProgressTracking::where('launch_id', $id)->delete();
            ScmLaunchPhase::where('launch_id', $id)->delete();
            ScmLaunchArticle::where('launch_id', $id)->delete();
            $launch->delete();

            // Log dell'attività
            $this->logActivity('SCM', 'DELETE_LAUNCH', "Eliminato lancio #{$launch->launch_number}", "Lancio eliminato definitivamente");

            $_SESSION['alert_success'] = 'Lancio eliminato con successo';
            $this->redirect($this->url('/scm-admin/launches'));

        } catch (Exception $e) {
            // Rollback removed - Eloquent handles transactions automatically
            error_log("Delete launch error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante l\'eliminazione del lancio';
            $this->redirect($this->url('/scm-admin/launches'));
        }
    }

    /**
     * Aggiorna fase standard
     */
    public function updateStandardPhase($phaseId)
    {
        header('Content-Type: application/json');

        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $phase = ScmStandardPhase::find($phaseId);
            if (!$phase) {
                echo json_encode(['success' => false, 'error' => 'Fase non trovata']);
                exit;
            }

            // Aggiorna solo i campi forniti
            $updateData = [];

            if (isset($input['phase_name'])) {
                $updateData['phase_name'] = trim($input['phase_name']);
            }

            if (isset($input['description'])) {
                $updateData['description'] = trim($input['description']);
            }

            if (isset($input['category'])) {
                $updateData['category'] = trim($input['category']);
            }

            if (empty($updateData)) {
                echo json_encode(['success' => false, 'error' => 'Nessun campo da aggiornare']);
                exit;
            }

            $phase->update($updateData);

            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            error_log("Update standard phase error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Errore durante l\'aggiornamento']);
        }
        exit;
    }

    /**
     * Duplica fase standard
     */
    public function duplicateStandardPhase($phaseId)
    {
        header('Content-Type: application/json');

        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        try {
            $phase = ScmStandardPhase::find($phaseId);
            if (!$phase) {
                echo json_encode(['success' => false, 'error' => 'Fase non trovata']);
                exit;
            }

            // Trova il prossimo ordine disponibile
            $maxOrder = ScmStandardPhase::max('phase_order') ?? 0;

            ScmStandardPhase::create([
                'phase_name' => $phase->phase_name . ' (Copia)',
                'description' => $phase->description,
                'category' => $phase->category,
                'phase_order' => $maxOrder + 1
            ]);

            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            error_log("Duplicate standard phase error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Errore durante la duplicazione']);
        }
        exit;
    }

    /**
     * Elimina fase standard
     */
    public function deleteStandardPhase($phaseId)
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        try {
            $phase = ScmStandardPhase::find($phaseId);
            if (!$phase) {
                echo json_encode(['success' => false, 'error' => 'Fase non trovata']);
                exit;
            }

            $phaseOrder = $phase->phase_order;
            $phase->delete();

            // Riordina le fasi rimanenti
            ScmStandardPhase::where('phase_order', '>', $phaseOrder)
                ->decrement('phase_order', 1);

            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            error_log("Delete standard phase error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Errore durante l\'eliminazione']);
        }
        exit;
    }

    /**
     * Riordina fasi standard
     */
    public function reorderStandardPhases()
    {
        header('Content-Type: application/json');

        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $order = $input['order'] ?? [];

            if (empty($order)) {
                echo json_encode(['success' => false, 'error' => 'Ordine non specificato']);
                exit;
            }

            foreach ($order as $item) {
                ScmStandardPhase::where('id', $item['id'])
                    ->update(['phase_order' => $item['order']]);
            }

            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            error_log("Reorder standard phases error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Errore durante il riordinamento']);
        }
        exit;
    }

    /**
     * Carica template predefinito di fasi
     */
    public function loadTemplate($templateName)
    {
        header('Content-Type: application/json');

        if (!$this->isPost()) {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        try {
            $templates = [
                'basic' => [
                    ['phase_name' => 'Taglio', 'description' => 'Taglio delle tomaie', 'category' => 'production'],
                    ['phase_name' => 'Montaggio', 'description' => 'Assemblaggio delle parti', 'category' => 'production'],
                    ['phase_name' => 'Controllo', 'description' => 'Controllo qualità', 'category' => 'quality'],
                    ['phase_name' => 'Confezionamento', 'description' => 'Confezionamento finale', 'category' => 'packaging']
                ],
                'advanced' => [
                    ['phase_name' => 'Preparazione', 'description' => 'Preparazione materiali', 'category' => 'production'],
                    ['phase_name' => 'Taglio', 'description' => 'Taglio delle tomaie', 'category' => 'production'],
                    ['phase_name' => 'Pre-assemblaggio', 'description' => 'Pre-assemblaggio componenti', 'category' => 'production'],
                    ['phase_name' => 'Montaggio', 'description' => 'Assemblaggio finale', 'category' => 'production'],
                    ['phase_name' => 'Controllo Qualità', 'description' => 'Controllo qualità completo', 'category' => 'quality'],
                    ['phase_name' => 'Finitura', 'description' => 'Operazioni di finitura', 'category' => 'finishing'],
                    ['phase_name' => 'Confezionamento', 'description' => 'Confezionamento e etichettatura', 'category' => 'packaging']
                ],
                'quality' => [
                    ['phase_name' => 'Taglio', 'description' => 'Taglio con controllo qualità', 'category' => 'production'],
                    ['phase_name' => 'Controllo Intermedio', 'description' => 'Controllo parti tagliate', 'category' => 'quality'],
                    ['phase_name' => 'Montaggio', 'description' => 'Assemblaggio controllato', 'category' => 'production'],
                    ['phase_name' => 'Controllo Finale', 'description' => 'Controllo finale prodotto', 'category' => 'quality'],
                    ['phase_name' => 'Test', 'description' => 'Test di resistenza e qualità', 'category' => 'quality'],
                    ['phase_name' => 'Confezionamento', 'description' => 'Confezionamento certificato', 'category' => 'packaging']
                ]
            ];

            if (!isset($templates[$templateName])) {
                echo json_encode(['success' => false, 'error' => 'Template non trovato']);
                exit;
            }

            // Elimina fasi esistenti
            ScmStandardPhase::truncate();

            // Inserisci nuove fasi
            foreach ($templates[$templateName] as $index => $phase) {
                ScmStandardPhase::create([
                    'phase_name' => $phase['phase_name'],
                    'description' => $phase['description'],
                    'category' => $phase['category'],
                    'phase_order' => $index + 1
                ]);
            }

            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            error_log("Load template error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Errore durante il caricamento del template']);
        }
        exit;
    }

}