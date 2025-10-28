<?php
/**
 * SCM Public Controller
 * Frontend per i laboratori terzisti (area pubblica)
 * Aggiornato per la nuova struttura database
 */

use App\Models\ScmLaboratory;
use App\Models\ScmLaunch;
use App\Models\ScmLaunchArticle;
use App\Models\ScmLaunchPhase;
use App\Models\ScmProgressTracking;
use App\Models\ScmStandardPhase;

class SCMPublicController extends BaseController
{
    /**
     * Login page per laboratori
     */
    public function index()
    {
        // Se già loggato, reindirizza alla dashboard
        if (isset($_SESSION['laboratorio_id'])) {
            $this->redirect($this->url('/scm/dashboard'));
            return;
        }

        $data = [
            'pageTitle' => 'SCM Terzisti - Login'
        ];

        $this->renderStandalone('scm-public.login', $data);
    }

    /**
     * Processo di login
     */
    public function login()
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('/scm'));
            return;
        }

        $username = trim($this->input('username'));
        $password = trim($this->input('password'));
        $error = '';

        if (empty($username) || empty($password)) {
            $error = 'Username e password sono obbligatori';
        } else {
            try {
                // Query per il login laboratorio con Eloquent
                $laboratorio = ScmLaboratory::where('username', $username)
                    ->where('is_active', 1)
                    ->first();

                if ($laboratorio && password_verify($password, $laboratorio->password_hash)) {
                    // Login riuscito - salva in sessione
                    $_SESSION['laboratorio_id'] = $laboratorio->id;
                    $_SESSION['laboratorio_nome'] = $laboratorio->name;
                    $_SESSION['laboratorio_email'] = $laboratorio->email;
                    $_SESSION['laboratorio_username'] = $laboratorio->username;

                    // Aggiorna ultimo accesso
                    $laboratorio->update(['last_login' => date('Y-m-d H:i:s')]);

                    $this->redirect($this->url('/scm/dashboard'));
                    return;
                } else {
                    $error = 'Credenziali non valide o account disattivato';
                }
            } catch (Exception $e) {
                error_log("SCM Login error: " . $e->getMessage());
                $error = 'Errore di connessione al database';
            }
        }

        $_SESSION['scm_login_error'] = $error;
        $_SESSION['scm_login_username'] = $username;
        $this->redirect($this->url('/scm'));
    }

    /**
     * Dashboard laboratorio
     */
    public function dashboard()
    {
        $this->requireScmAuth();

        try {
            $laboratorioId = $_SESSION['laboratorio_id'];
            $laboratorioNome = $_SESSION['laboratorio_nome'];

            // Ottieni i lanci raggruppati per stato
            $lanci = $this->getLanciByStatus($laboratorioId);

            // Calcola statistiche precise
            $stats = $this->calcolaStatistichePrecise($laboratorioId, $lanci);
            
            // Calcola percentuale completamento globale
            $percentualeCompletamento = $stats['total_paia'] > 0 ?
                round(($stats['paia_completi'] / $stats['total_paia']) * 100) : 0;

            $data = [
                'pageTitle' => 'Dashboard - SCM Terzisti',
                'laboratorio_nome' => $laboratorioNome,
                'lanci_preparazione' => $lanci['preparazione'],
                'lanci_lavorazione' => $lanci['lavorazione'],
                'lanci_completi' => $lanci['completi'],
                'stats' => array_merge($stats, [
                    'percentuale_completamento' => $percentualeCompletamento
                ]),
                'breadcrumb' => [
                    ['title' => 'Dashboard', 'url' => '/scm/dashboard', 'current' => true]
                ]
            ];

            $this->renderStandalone('scm-public.dashboard', $data);

        } catch (Exception $e) {
            error_log("SCM Dashboard error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante il caricamento della dashboard.';
            $this->redirect($this->url('/scm'));
        }
    }

    /**
     * Dettagli lancio per lavorazione
     */
    public function workLaunch($launchId)
    {
        $this->requireScmAuth();

        try {
            $laboratorioId = $_SESSION['laboratorio_id'];

            // Verifica che il lancio appartenga al laboratorio
            $lancio = ScmLaunch::with('laboratory')
                ->where('id', $launchId)
                ->where('laboratory_id', $laboratorioId)
                ->first();

            if (!$lancio) {
                $_SESSION['alert_error'] = 'Lancio non trovato o non autorizzato.';
                $this->redirect($this->url('/scm/dashboard'));
                return;
            }

            // Ottieni articoli del lancio
            $articoli = ScmLaunchArticle::where('launch_id', $launchId)
                ->orderBy('article_order')
                ->get();

            // Ottieni fasi del lancio
            $fasiLancio = ScmLaunchPhase::where('launch_id', $launchId)
                ->orderBy('phase_order')
                ->get();

            // Crea mapping fasi
            $fasi = [];
            foreach ($fasiLancio as $fase) {
                $fasi[] = $fase['phase_name'];
            }

            // Calcola percentuale completamento per ogni articolo usando Eloquent
            $totaleFasi = count($fasi);
            foreach ($articoli as &$articolo) {
                if ($totaleFasi > 0) {
                    $numeroFasiCompletate = ScmProgressTracking::where('launch_id', $launchId)
                        ->where('article_id', $articolo['id'])
                        ->where('status', 'COMPLETATA')
                        ->count();

                    $articolo['percentuale'] = round(($numeroFasiCompletate / $totaleFasi) * 100);
                } else {
                    $articolo['percentuale'] = 0;
                }
            }
            unset($articolo);

            // Ottieni stati delle fasi per tutti gli articoli
            $statisFasi = ScmProgressTracking::with(['article', 'phase'])
                ->where('launch_id', $launchId)
                ->get()
                ->each(function($tracking) {
                    // Aggiungi proprietà calcolate agli oggetti Eloquent
                    $tracking->phase_name = $tracking->phase ? $tracking->phase->phase_name : null;
                    $tracking->article_name = $tracking->article ? $tracking->article->article_name : null;
                    $tracking->stato_fase = $tracking->status ?? 'NON_INIZIATA';
                })
                ->sortBy(function($tracking) {
                    return [$tracking->article_id, $tracking->phase_name];
                })
                ->values();

            // Organizza stati fasi per facile accesso nella view
            $matriceStati = [];
            foreach ($statisFasi as $tracking) {
                // Usa l'oggetto Eloquent direttamente
                $tracking->note_fase = $tracking->notes ?? '';
                $tracking->data_completamento = $tracking->completed_at ?? null;

                $matriceStati[$tracking->phase_name][$tracking->article_id] = $tracking;
            }

            // Determina quali articoli sono bloccati (hanno completato l'ultima fase)
            $articoliBloccati = [];
            if (!empty($fasi)) {
                $ultimaFase = end($fasi);
                foreach ($articoli as $articolo) {
                    $ultimaFaseStato = $matriceStati[$ultimaFase][$articolo['id']] ?? null;
                    if ($ultimaFaseStato && $ultimaFaseStato->status === 'COMPLETATA') {
                        $articoliBloccati[$articolo['id']] = true;
                    }
                }
            }

            // Ottieni note del lancio dalla nuova struttura
            $note = $this->buildNotesArray($lancio, $articoli, $launchId);

            $data = [
                'pageTitle' => 'Lavora Lancio: ' . $lancio['launch_number'],
                'lancio' => $lancio,
                'articoli' => $articoli,
                'matriceStati' => $matriceStati,
                'articoli_bloccati' => $articoliBloccati,
                'note' => $note,
                'fasi_ciclo' => $fasi,
                'breadcrumb' => [
                    ['title' => 'Dashboard', 'url' => '/scm/dashboard'],
                    ['title' => 'Lavora Lancio', 'url' => '/scm/lavora/' . $launchId, 'current' => true]
                ]
            ];

            $this->renderStandalone('scm-public.work-launch', $data);

        } catch (Exception $e) {
            error_log("SCM Work Launch error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante il caricamento del lancio.';
            $this->redirect($this->url('/scm/dashboard'));
        }
    }

    /**
     * API - Aggiorna progresso fase con logica sequenziale
     */
    public function updateProgressSequence($launchId)
    {
        // Pulisci qualsiasi output precedente
        if (ob_get_level()) {
            ob_clean();
        }

        error_log("=== SCM updateProgressSequence START ===");
        error_log("LaunchId: " . $launchId);

        $this->requireScmAuth();

        if (!$this->isPost()) {
            error_log("ERROR: Not POST method");
            $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
            return;
        }

        try {
            $laboratorioId = $_SESSION['laboratorio_id'];
            $fase = trim($this->input('nome_fase'));
            $articoloId = (int)$this->input('articolo_id');
            $nuovoStato = trim($this->input('nuovo_stato'));
            $note = trim($this->input('note'));

            error_log("Parametri: fase={$fase}, articolo_id={$articoloId}, nuovo_stato={$nuovoStato}");

            // Verifica parametri obbligatori
            if (empty($fase) || $articoloId <= 0 || empty($nuovoStato)) {
                $this->json(['success' => false, 'error' => 'Parametri mancanti o non validi'], 400);
                return;
            }

            // Verifica autorizzazioni
            $lancio = ScmLaunch::where('id', $launchId)
                ->where('laboratory_id', $laboratorioId)
                ->first();

            if (!$lancio) {
                $this->json(['success' => false, 'error' => 'Lancio non trovato'], 404);
                return;
            }

            // Controlla se il lancio è già completato
            if ($lancio->status === 'COMPLETATO') {
                $this->json(['success' => false, 'error' => 'Non è possibile modificare le fasi di un lancio completato'], 403);
                return;
            }

            // Verifica articolo
            $articolo = ScmLaunchArticle::where('id', $articoloId)
                ->where('launch_id', $launchId)
                ->first();

            if (!$articolo) {
                $this->json(['success' => false, 'error' => 'Articolo non trovato'], 404);
                return;
            }

            // Controlla se l'articolo è bloccato
            if ($this->isArticoloBloccato($launchId, $articoloId)) {
                $this->json(['success' => false, 'error' => 'Non è possibile modificare un articolo che ha completato l\'ultima fase'], 403);
                return;
            }

            // Ottieni fasi in ordine usando Eloquent
            $fasiLancio = ScmLaunchPhase::where('launch_id', $launchId)
                ->orderBy('phase_order')
                ->get(['id', 'phase_name', 'phase_order']);

            $indiceFaseTarget = -1;
            $faseTargetId = null;
            foreach ($fasiLancio as $index => $faseInfo) {
                if ($faseInfo->phase_name === $fase) {
                    $indiceFaseTarget = $index;
                    $faseTargetId = $faseInfo->id;
                    break;
                }
            }

            if ($indiceFaseTarget === -1 || !$faseTargetId) {
                $this->json(['success' => false, 'error' => 'Fase non valida'], 400);
                return;
            }

            // Completa automaticamente tutte le fasi precedenti
            for ($i = 0; $i < $indiceFaseTarget; $i++) {
                $fasePrecedente = $fasiLancio[$i];

                // Verifica se esiste già record di tracking usando Eloquent
                $trackingEsistente = ScmProgressTracking::where('launch_id', $launchId)
                    ->where('article_id', $articoloId)
                    ->where('phase_id', $fasePrecedente->id)
                    ->first(['id', 'status']);

                if ($trackingEsistente) {
                    // Aggiorna solo se non è già completata
                    if ($trackingEsistente->status !== 'COMPLETATA') {
                        $newNote = empty($trackingEsistente->notes)
                            ? 'Completata automaticamente per sequenza'
                            : $trackingEsistente->notes . ' - Completata automaticamente per sequenza';

                        $trackingEsistente->update([
                            'status' => 'COMPLETATA',
                            'completed_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'notes' => $newNote
                        ]);
                    }
                } else {
                    // Crea nuovo record completato
                    ScmProgressTracking::create([
                        'launch_id' => $launchId,
                        'article_id' => $articoloId,
                        'phase_id' => $fasePrecedente->id,
                        'status' => 'COMPLETATA',
                        'notes' => 'Completata automaticamente per sequenza',
                        'started_at' => date('Y-m-d H:i:s'),
                        'completed_at' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            // Ora aggiorna la fase target usando Eloquent
            $trackingTarget = ScmProgressTracking::where('launch_id', $launchId)
                ->where('article_id', $articoloId)
                ->where('phase_id', $faseTargetId)
                ->first();

            if ($trackingTarget) {
                $updateData = [
                    'status' => $nuovoStato,
                    'notes' => $note,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($nuovoStato === 'COMPLETATA') {
                    $updateData['completed_at'] = date('Y-m-d H:i:s');
                }

                if ($nuovoStato !== 'NON_INIZIATA' && empty($trackingTarget->started_at)) {
                    $updateData['started_at'] = date('Y-m-d H:i:s');
                }

                $trackingTarget->update($updateData);
            } else {
                $createData = [
                    'launch_id' => $launchId,
                    'article_id' => $articoloId,
                    'phase_id' => $faseTargetId,
                    'status' => $nuovoStato,
                    'notes' => $note,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($nuovoStato === 'COMPLETATA') {
                    $createData['completed_at'] = date('Y-m-d H:i:s');
                }

                if ($nuovoStato !== 'NON_INIZIATA') {
                    $createData['started_at'] = date('Y-m-d H:i:s');
                }

                ScmProgressTracking::create($createData);
            }

            // Aggiorna stato generale del lancio
            $this->aggiornaStatoLancio($launchId);

            // Log dell'attività
            $launch = ScmLaunch::find($launchId);
            $article = ScmLaunchArticle::find($articoloId);
            $phase = ScmLaunchPhase::find($faseTargetId);

            $this->logActivity('SCM', 'UPDATE_PROGRESS',
                "Aggiornato progresso lancio #{$launch->launch_number}",
                "Articolo: {$article->article_name}, Fase: {$phase->phase_name}, Stato: $nuovoStato"
            );

            $this->json(['success' => true, 'message' => 'Fasi aggiornate con sequenza']);

        } catch (Exception $e) {
            error_log("SCM Update Progress Sequence error: " . $e->getMessage());
            error_log("SCM Update Progress Sequence trace: " . $e->getTraceAsString());
            $this->json(['success' => false, 'error' => 'Errore durante l\'aggiornamento sequenziale: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API - Aggiungi nota (salva nelle note del progress tracking)
     */
    public function addNote($launchId)
    {
        $this->requireScmAuth();

        if (!$this->isPost()) {
            $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
            return;
        }

        try {
            $laboratorioId = $_SESSION['laboratorio_id'];
            $nota = trim($this->input('nota'));

            if (empty($nota)) {
                $this->json(['success' => false, 'error' => 'La nota non può essere vuota'], 400);
                return;
            }

            // Verifica che il lancio appartenga al laboratorio usando Eloquent
            $lancio = ScmLaunch::where('id', $launchId)
                ->where('laboratory_id', $laboratorioId)
                ->first(['id', 'notes', 'launch_number']);

            if (!$lancio) {
                $this->json(['success' => false, 'error' => 'Lancio non trovato'], 404);
                return;
            }

            // Aggiungi nota alle note generali del lancio
            $newNotes = empty($lancio->notes)
                ? $nota
                : $lancio->notes . "\n---\n" . $nota;

            $lancio->update([
                'notes' => $newNotes,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Log dell'attività
            $this->logActivity('SCM', 'ADD_NOTE', "Aggiunta nota al lancio #{$lancio->launch_number}", substr($nota, 0, 100) . (strlen($nota) > 100 ? '...' : ''));

            $this->json(['success' => true, 'message' => 'Nota aggiunta']);

        } catch (Exception $e) {
            error_log("SCM Add Note error: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Errore durante l\'inserimento della nota'], 500);
        }
    }

    /**
     * Logout laboratorio
     */
    public function logout()
    {
        // Pulisci sessione laboratorio
        unset($_SESSION['laboratorio_id']);
        unset($_SESSION['laboratorio_nome']);
        unset($_SESSION['laboratorio_email']);
        unset($_SESSION['laboratorio_username']);

        $this->redirect($this->url('/scm'));
    }

    /**
     * Verifica autenticazione SCM
     */
    private function requireScmAuth()
    {
        if (!isset($_SESSION['laboratorio_id'])) {
            // Se è una richiesta AJAX, restituisci JSON
            if ($this->isPost() || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')) {
                $this->json(['success' => false, 'error' => 'Sessione scaduta'], 401);
                exit;
            }
            $this->redirect($this->url('/scm'));
            exit;
        }
    }

    /**
     * Ottiene lanci raggruppati per stato con nuova struttura
     */
    private function getLanciByStatus($laboratorioId)
    {
        $gruppi = [
            'preparazione' => ScmLaunch::with('articles')
                ->where('laboratory_id', $laboratorioId)
                ->whereIn('status', ['IN_PREPARAZIONE'])
                ->orderBy('launch_number', 'ASC')
                ->get(),
            'lavorazione' => ScmLaunch::with('articles')
                ->where('laboratory_id', $laboratorioId)
                ->whereIn('status', ['IN_LAVORAZIONE', 'BLOCCATO'])
                ->orderBy('launch_number', 'ASC')
                ->get(),
            'completi' => ScmLaunch::with('articles')
                ->where('laboratory_id', $laboratorioId)
                ->where('status', 'COMPLETATO')
                ->orderBy('launch_number', 'ASC')
                ->get()
        ];

        // Aggiungi proprietà calcolate agli oggetti
        foreach ($gruppi as &$gruppo) {
            $gruppo->each(function($lancio) {
                // Aggiungi proprietà calcolate
                $lancio->totale_articoli = $lancio->articles->count();
                $lancio->totale_paia = $lancio->articles->sum('total_pairs');

                // Articoli come oggetti Eloquent
                $lancio->articoli = $lancio->articles->sortBy('article_order');

                // Calcola percentuale completamento
                $lancio->percentuale = $this->calcolaPercentualeLancio($lancio->id);
                $lancio->percentuale_completamento = $lancio->percentuale;
            });
        }

        return $gruppi;
    }

    /**
     * Calcola statistiche precise per il laboratorio
     */
    private function calcolaStatistichePrecise($laboratorioId, $lanci)
    {
        // 1. Volume totale: somma delle paia di ogni articolo
        $totalPaia = ScmLaunch::where('laboratory_id', $laboratorioId)
            ->with('articles')
            ->get()
            ->sum(function($launch) {
                return $launch->articles->sum('total_pairs');
            });

        // 2. In preparazione
        $paiaInPreparazione = ScmLaunch::where('laboratory_id', $laboratorioId)
            ->where('status', 'IN_PREPARAZIONE')
            ->with('articles')
            ->get()
            ->sum(function($launch) {
                return $launch->articles->sum('total_pairs');
            });

        // 3. In lavorazione attiva
        $paiaInLavorazione = $this->calcolaPaiaInLavorazioneAttiva($laboratorioId);

        // 4. Completati
        $paiaCompleti = $this->calcolaPaiaTerminate($laboratorioId);

        return [
            'total_paia' => (int)$totalPaia,
            'paia_preparazione' => (int)$paiaInPreparazione,
            'paia_lavorazione' => (int)$paiaInLavorazione,
            'paia_completi' => (int)$paiaCompleti
        ];
    }

    /**
     * Calcola paia in lavorazione attiva (non ancora tutte le fasi completate)
     */
    private function calcolaPaiaInLavorazioneAttiva($laboratorioId)
    {
        $lanci = ScmLaunch::where('laboratory_id', $laboratorioId)
            ->where('status', 'IN_LAVORAZIONE')
            ->with(['articles', 'phases', 'progressTracking'])
            ->get();

        $totalePaiaInLavorazione = 0;

        foreach ($lanci as $lancio) {
            $fasiTotali = $lancio->phases->count();

            if ($fasiTotali == 0) continue;

            // Per ogni articolo, verifica se ha completato tutte le fasi
            foreach ($lancio->articles as $articolo) {
                $fasiCompletate = $lancio->progressTracking
                    ->where('article_id', $articolo->id)
                    ->where('status', 'COMPLETATA')
                    ->count();

                // Se l'articolo NON ha completato tutte le fasi, è ancora in lavorazione
                if ($fasiCompletate < $fasiTotali) {
                    $totalePaiaInLavorazione += $articolo->total_pairs;
                }
            }
        }

        return $totalePaiaInLavorazione;
    }

    /**
     * Calcola paia terminate (completate o con ultima fase completata)
     */
    private function calcolaPaiaTerminate($laboratorioId)
    {
        // 1. Paia di lanci completati
        $paiaCompleti = ScmLaunch::where('laboratory_id', $laboratorioId)
            ->where('status', 'COMPLETATO')
            ->with('articles')
            ->get()
            ->sum(function($launch) {
                return $launch->articles->sum('total_pairs');
            });

        // 2. Paia di lanci in lavorazione che hanno completato tutte le fasi
        $lanci = ScmLaunch::where('laboratory_id', $laboratorioId)
            ->where('status', 'IN_LAVORAZIONE')
            ->with(['articles', 'phases', 'progressTracking'])
            ->get();

        $paiaUltimaFaseCompleta = 0;

        foreach ($lanci as $lancio) {
            $fasiTotali = $lancio->phases->count();

            if ($fasiTotali == 0) continue;

            // Per ogni articolo, verifica se ha completato tutte le fasi
            foreach ($lancio->articles as $articolo) {
                $fasiCompletate = $lancio->progressTracking
                    ->where('article_id', $articolo->id)
                    ->where('status', 'COMPLETATA')
                    ->count();

                if ($fasiCompletate >= $fasiTotali) {
                    $paiaUltimaFaseCompleta += $articolo->total_pairs;
                }
            }
        }

        return $paiaCompleti + $paiaUltimaFaseCompleta;
    }

    /**
     * Verifica se un articolo è bloccato (ha completato l'ultima fase)
     */
    private function isArticoloBloccato($lancioId, $articoloId)
    {
        // Ottieni l'ultima fase del lancio usando Eloquent
        $ultimaFase = ScmLaunchPhase::where('launch_id', $lancioId)
            ->orderBy('phase_order', 'desc')
            ->first(['id']);

        if (!$ultimaFase) return false;

        // Verifica se l'articolo ha completato l'ultima fase usando Eloquent
        $ultimaFaseCompletata = ScmProgressTracking::where('launch_id', $lancioId)
            ->where('article_id', $articoloId)
            ->where('phase_id', $ultimaFase->id)
            ->where('status', 'COMPLETATA')
            ->count();

        return $ultimaFaseCompletata > 0;
    }

    /**
     * Calcola la percentuale di completamento di un lancio
     */
    private function calcolaPercentualeLancio($lancioId)
    {
        $lancio = ScmLaunch::with(['articles', 'phases', 'progressTracking'])
            ->find($lancioId);

        if (!$lancio) return 0;

        // Conta fasi totali necessarie
        $fasiTotali = $lancio->phases->count();

        if ($fasiTotali == 0) return 0;

        // Conta articoli
        $totaleArticoli = $lancio->articles->count();
        if ($totaleArticoli == 0) return 0;

        // Fasi totali necessarie = articoli × fasi
        $fasiTotaliNecessarie = $totaleArticoli * $fasiTotali;

        // Conta fasi completate
        $numeroFasiCompletate = $lancio->progressTracking
            ->where('status', 'COMPLETATA')
            ->count();

        return $fasiTotaliNecessarie > 0 ? round(($numeroFasiCompletate / $fasiTotaliNecessarie) * 100) : 0;
    }

    /**
     * Aggiorna lo stato generale di un lancio
     */
    private function aggiornaStatoLancio($lancioId)
    {
        $lancio = ScmLaunch::with(['phases', 'articles', 'progressTracking'])->find($lancioId);

        if (!$lancio) return;

        // Conta fasi totali necessarie per questo lancio
        $fasiTotali = $lancio->phases->count();
        $totaleArticoli = $lancio->articles->count();
        $fasiTotaliNecessarie = $fasiTotali * $totaleArticoli;

        if ($fasiTotaliNecessarie == 0) return;

        // Conta fasi per stato usando Collection methods
        $progressTracking = $lancio->progressTracking;

        $fasiCompletate = $progressTracking->where('status', 'COMPLETATA')->count();
        $fasiInCorso = $progressTracking->where('status', 'IN_CORSO')->count();
        $fasiBloccate = $progressTracking->where(function($item) {
            return $item->status === 'BLOCCATA' || $item->is_blocked == 1;
        })->count();

        // Determina nuovo stato (priorità: BLOCCATO > COMPLETATO > IN_LAVORAZIONE > IN_PREPARAZIONE)
        $nuovoStato = 'IN_PREPARAZIONE';

        if ($fasiBloccate > 0) {
            // Se ci sono fasi bloccate, il lancio è bloccato
            $nuovoStato = 'BLOCCATO';
        } elseif ($fasiCompletate == $fasiTotaliNecessarie) {
            // Se tutte le fasi sono completate, il lancio è completato
            $nuovoStato = 'COMPLETATO';
        } elseif ($fasiCompletate > 0 || $fasiInCorso > 0) {
            // Se ci sono fasi completate o in corso, è in lavorazione
            $nuovoStato = 'IN_LAVORAZIONE';
        }

        // Aggiorna usando Eloquent
        $lancio->update([
            'status' => $nuovoStato,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Costruisce array delle note dalla nuova struttura
     */
    private function buildNotesArray($lancio, $articoli, $launchId)
    {
        $note = [];
        
        // Note generali del lancio
        if (!empty($lancio['notes'])) {
            $noteGenerali = explode('---', $lancio['notes']);
            foreach ($noteGenerali as $index => $notaGenerale) {
                $notaGenerale = trim($notaGenerale);
                if (!empty($notaGenerale)) {
                    $note[] = [
                        'nota' => $notaGenerale,
                        'data_nota' => $lancio['created_at'] ?? null,
                        'mittente' => 'Sistema',
                        'titolo' => 'Note Generali' . ($index > 0 ? ' #' . ($index + 1) : ''),
                        'tipo_nota' => 'GENERALE',
                        'priorita' => 'MEDIA'
                    ];
                }
            }
        }
        
        // Note degli articoli
        foreach ($articoli as $art) {
            if (!empty($art['notes'])) {
                $note[] = [
                    'nota' => $art['notes'],
                    'data_nota' => null,
                    'mittente' => 'Sistema',
                    'titolo' => 'Note Articolo: ' . $art['article_name'],
                    'tipo_nota' => 'ARTICOLO',
                    'priorita' => 'MEDIA'
                ];
            }
        }
        
        // Note del progress tracking usando Eloquent
        $noteTracking = ScmProgressTracking::with(['phase', 'article'])
            ->where('launch_id', $launchId)
            ->whereNotNull('notes')
            ->where('notes', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->each(function($progress) {
                // Aggiungi proprietà calcolate agli oggetti Eloquent
                $progress->nota = $progress->notes;
                $progress->data_nota = $progress->updated_at;
                $progress->mittente = 'Laboratorio';
                $progress->titolo = 'Fase: ' . ($progress->phase ? $progress->phase->phase_name : 'N/A') .
                                   ' - Articolo: ' . ($progress->article ? $progress->article->article_name : 'N/A');
                $progress->tipo_nota = 'PROGRESSO';
                $progress->priorita = 'MEDIA';
            });

        // Unisci le note mantenendo oggetti Eloquent
        foreach ($noteTracking as $tracking) {
            $note[] = $tracking;
        }

        return $note;
    }

    /**
     * Alias per workLaunch - compatibilità URL /scm/lavora/ID
     */
    public function lavora($launchId)
    {
        return $this->workLaunch($launchId);
    }

    /**
     * Renderizza una view standalone senza layout WEBGRE
     */
    protected function renderStandalone($viewName, $data = [])
    {
        // Imposta le variabili disponibili nella view
        extract($data);
        
        // Converti il nome della view nel percorso del file
        $viewPath = str_replace('.', DIRECTORY_SEPARATOR, $viewName);
        $viewFile = VIEW_PATH . DIRECTORY_SEPARATOR . $viewPath . '.php';
        
        if (!file_exists($viewFile)) {
            throw new Exception("View file not found: $viewFile");
        }
        
        // Include e renderizza la view direttamente
        include $viewFile;
    }
}