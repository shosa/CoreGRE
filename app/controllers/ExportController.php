<?php

use App\Models\ExportDocument;
use App\Models\ExportTerzista;
use App\Models\ExportArticle;
use App\Models\ExportDocumentFooter;
use App\Models\ExportMissingData;
use App\Models\ExportLaunchData;
use App\Models\ActivityLog;
use App\Models\Setting;

require_once APP_ROOT . '/app/utils/ExcelProcessor.php';

/**
 * Export Controller
 * Gestisce la creazione di documenti di trasporto personalizzati partendo da schede Excel
 * Migrazione della sezione Export dal sistema legacy
 */
class ExportController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->requirePermission('export'); // Usa permesso export
    }

    /**
     * Dashboard principale export
     */
    public function dashboard()
    {
        try {
            // Statistiche principali con Eloquent
            $stats = [];

            // Documenti totali
            $stats['totalDocuments'] = ExportDocument::count();

            // Documenti aperti
            $stats['openDocuments'] = ExportDocument::open()->count();

            // Terzisti attivi (che hanno almeno un documento)
            $stats['activeTerzisti'] = ExportDocument::distinct('id_terzista')->count();

            // Articoli gestiti totali
            $stats['totalArticles'] = ExportArticle::count();

            // Documenti questa settimana
            $stats['weeklyDocuments'] = ExportDocument::recent(7)->count();

            // Documenti completati questo mese
            $stats['monthlyCompleted'] = ExportDocument::closed()
                ->whereMonth('data', date('n'))
                ->whereYear('data', date('Y'))
                ->count();

            // Media colli per documento
            $avgColli = ExportDocumentFooter::where('n_colli', '>', 0)->avg('n_colli');
            $stats['avgColli'] = $avgColli ?? 0;

            // Documenti recenti (ultimi 5)
            $recentDocuments = ExportDocument::with('terzista')
                ->orderByDesc('id')
                ->limit(5)
                ->get();

            $this->render('export.dashboard', [
                'pageTitle' => 'Dashboard Export',
                'stats' => $stats,
                'recentDocuments' => $recentDocuments
            ]);

        } catch (PDOException $e) {
            error_log("Errore nel recupero statistiche export: " . $e->getMessage());
            $this->render('export.dashboard', [
                'pageTitle' => 'Dashboard Export',
                'stats' => [
                    'totalDocuments' => 0,
                    'openDocuments' => 0,
                    'activeTerzisti' => 0,
                    'totalArticles' => 0,
                    'weeklyDocuments' => 0,
                    'monthlyCompleted' => 0,
                    'avgColli' => 0
                ],
                'recentDocuments' => [],
                'error' => 'Errore nel caricamento delle statistiche'
            ]);
        }
    }

    /**
     * Dashboard/Lista documenti DDT (equivalente a documenti.php)
     */
    public function index()
    {
        // Recupera parametri di paginazione e filtri
        $page = (int) ($this->input('page') ?: 1);
        $pagelimit = Setting::getInt('pagination_export', 15);

        // Recupera filtri
        $numero = $this->input('numero');
        $destinatario = $this->input('destinatario');
        $data = $this->input('data');
        $stato = $this->input('stato');

        try {
            // Query con Eloquent e filtri
            $query = ExportDocument::with(['terzista', 'piede'])
                ->orderByDesc('id');

            // Applica filtri
            if ($numero) {
                $query->where('id', $numero);
            }

            if ($destinatario) {
                $query->whereHas('terzista', function ($q) use ($destinatario) {
                    $q->where('ragione_sociale', 'like', "%{$destinatario}%");
                });
            }

            if ($data) {
                $query->where('data', $data);
            }

            if ($stato) {
                $query->where('stato', $stato);
            }

            // Paginazione
            $total_records = $query->count();
            $total_pages = ceil($total_records / $pagelimit);

            // OTTIMIZZATO: Usa withCount per evitare N+1 query
            $documents = $query->withCount('articoli')
                ->skip(($page - 1) * $pagelimit)
                ->take($pagelimit)
                ->get();

            // Aggiunge informazione articoli per ogni documento usando il count
            foreach ($documents as $doc) {
                $doc->ha_articoli = $doc->articoli_count > 0;
            }

            $this->render('export.index', [
                'pageTitle' => 'Registro DDT - Export',
                'documents' => $documents,
                'currentPage' => $page,
                'totalPages' => $total_pages,
                'totalRecords' => $total_records
            ]);

        } catch (PDOException $e) {
            error_log("Errore nel recupero dei documenti: " . $e->getMessage());
            $this->render('export.index', [
                'pageTitle' => 'Registro DDT - Export',
                'documents' => [],
                'currentPage' => 1,
                'totalPages' => 0,
                'totalRecords' => 0,
                'error' => 'Errore nel caricamento dei documenti'
            ]);
        }
    }

    /**
     * Step 1: Creazione nuovo documento (equivalente a new_step1.php)
     */
    public function create()
    {
        if ($this->isPost()) {
            return $this->handleCreateStep1();
        }

        try {
            // Genera nuovo ID documento con Eloquent
            $lastDocument = ExportDocument::orderBy('id', 'desc')->first();
            $newId = ($lastDocument ? $lastDocument->id + 1 : 1);

            // Recupera lista terzisti con Eloquent
            $terzisti = ExportTerzista::select('id', 'ragione_sociale', 'autorizzazione')
                ->orderBy('ragione_sociale', 'asc')
                ->get();

            $this->render('export.create', [
                'pageTitle' => 'Nuovo Documento DDT - Step 1',
                'newId' => $newId,
                'terzisti' => $terzisti
            ]);

        } catch (PDOException $e) {
            error_log("Errore nella creazione documento: " . $e->getMessage());
            $this->setFlash('error', 'Errore nel caricamento della pagina di creazione');
            $this->redirect($this->url('/export'));
        }
    }

    /**
     * Gestisce la creazione del documento step 1
     */
    private function handleCreateStep1()
    {
        $id_terzista = $this->input('terzista');

        if (!$id_terzista) {
            $this->json(['success' => false, 'message' => 'Seleziona un terzista'], 400);
            return;
        }

        try {
            // Genera nuovo ID documento con Eloquent
            $lastDocument = ExportDocument::orderBy('id', 'desc')->first();
            $newId = ($lastDocument ? $lastDocument->id + 1 : 1);

            // Recupera autorizzazione terzista con Eloquent
            $terzista = ExportTerzista::find($id_terzista);

            if (!$terzista) {
                $this->json(['success' => false, 'message' => 'Terzista non trovato'], 404);
                return;
            }

            // Crea nuovo documento con Eloquent
            $document = ExportDocument::create([
                'id' => $newId,
                'id_terzista' => $id_terzista,
                'data' => date('Y-m-d'),
                'stato' => 'Aperto',
                'autorizzazione' => $terzista->autorizzazione
            ]);

            // Log attività
            $this->logActivity('DDT', 'CREAZIONE', 'Creato nuovo documento', $newId, '');

            // Se è una richiesta AJAX/PJAX, ritorna JSON
            if ($this->isPjax() || $this->isAjax()) {
                $this->json(['success' => true, 'redirect' => $this->url('/export/upload/' . $newId)]);
            } else {
                $this->redirect($this->url('/export/upload/' . $newId));
            }

        } catch (PDOException $e) {
            error_log("Errore nella creazione documento: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore nella creazione del documento'], 500);
        }
    }

    /**
     * Step 2: Upload schede Excel (equivalente a new_step2.php) 
     */
    public function upload($progressivo = null)
    {
        if (!$progressivo) {
            $this->setFlash('error', 'ID documento non valido');
            $this->redirect($this->url('/export'));
            return;
        }

        // Verifica che il documento esista con Eloquent
        $document = ExportDocument::find($progressivo);

        if (!$document) {
            $this->setFlash('error', 'Documento non trovato');
            $this->redirect($this->url('/export'));
            return;
        }

        $this->render('export.upload', [
            'pageTitle' => 'Caricamento Schede - Step 2',
            'progressivo' => $progressivo,
            'document' => $document
        ]);
    }

    /**
     * Step 3: Preview e generazione DDT (equivalente a new_step3.php)
     */
    public function preview($progressivo = null)
    {
        if (!$progressivo) {
            $this->setFlash('error', 'ID documento non valido');
            $this->redirect($this->url('/export'));
            return;
        }

        // Verifica che il documento esista con Eloquent
        $document = ExportDocument::find($progressivo);

        if (!$document) {
            $this->setFlash('error', 'Documento non trovato');
            $this->redirect($this->url('/export'));
            return;
        }

        // Lista dei file temp (simuliamo per ora, da implementare logica file)
        $tempFiles = $this->getTempFiles($progressivo);

        $this->render('export.preview', [
            'pageTitle' => 'Anteprima DDT - Step 3',
            'progressivo' => $progressivo,
            'document' => $document,
            'tempFiles' => $tempFiles
        ]);
    }

    /**
     * Gestione terzisti (equivalente a terzisti.php)
     */


    /**
     * API: Dettagli documento DDT (per modal)
     */
    public function getDdtDetails()
    {
        $id = $this->input('id');

        if (!$id) {
            echo '<div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">ID documento richiesto</div>';
            return;
        }

        try {
            // Recupera dati lanci con Eloquent
            $lanci = ExportLaunchData::where('id_doc', $id)
                ->select('lancio', 'articolo', 'paia')
                ->get();

            $somma = ExportLaunchData::where('id_doc', $id)
                ->sum('paia');

            $somme = [['somma' => $somma]];

            if (empty($lanci)) {
                echo '<div class="bg-blue-100 text-blue-800 px-4 py-2 rounded mb-4">Nessun lancio allegato trovato per questo documento.</div>';
                return;
            }

            echo '<div class="overflow-x-auto">';
            echo '<table class="min-w-full divide-y divide-gray-200 border border-gray-200">';
            echo '<thead class="bg-gray-50">';
            echo '<tr>';
            echo '<th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Lancio</th>';
            echo '<th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Articolo</th>';
            echo '<th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Paia</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody class="bg-white divide-y divide-gray-200">';

            foreach ($lanci as $lancio) {
                echo '<tr class="hover:bg-gray-50">';
                echo '<td class="px-4 py-2 text-left text-sm text-gray-800">' . htmlspecialchars($lancio->lancio) . '</td>';
                echo '<td class="px-4 py-2 text-left text-sm text-gray-800">' . htmlspecialchars($lancio->articolo) . '</td>';
                echo '<td class="px-4 py-2 text-left text-sm text-gray-800">' . htmlspecialchars($lancio->paia) . '</td>';
                echo '</tr>';

            }
            foreach ($somme as $somma) {
                echo '<td colspan="2" class="px-4 py-2 text-right text-sm text-black font-bold">TOTALE</td>';
                echo '<td  class="px-4 py-2 text-left text-sm text-gray-800">' . htmlspecialchars($somma['somma']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table></div>';

        } catch (PDOException $e) {
            error_log("Errore nel recupero dettagli DDT: " . $e->getMessage());
            echo '<div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">Errore nel caricamento dei dettagli</div>';
        }
    }


    /**
     * API: Recupera dettagli terzista per anteprima step1
     */
    public function getTerzistaDetails()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $id = $this->input('id');

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID terzista richiesto'], 400);
            return;
        }

        try {
            // Recupera terzista con Eloquent
            $terzista = ExportTerzista::find($id);

            if (!$terzista) {
                $this->json(['success' => false, 'message' => 'Terzista non trovato'], 404);
                return;
            }

            $this->json([
                'success' => true,
                'data' => $terzista
            ]);

        } catch (PDOException $e) {
            error_log("Errore nel recupero dettagli terzista: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore nel caricamento dei dettagli'], 500);
        }
    }

    /**
     * API: Elimina documento DDT
     */
    public function delete()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $id = $this->input('id');

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID documento richiesto'], 400);
            return;
        }

        try {
            $this->db->beginTransaction();

            // Elimina documento principale (i vincoli CASCADE cancelleranno automaticamente i dati correlati)
            $documento = ExportDocument::find($id);

            if ($documento) {
                $documento->delete();
                $this->db->commit();
                $this->logActivity('DDT', 'ELIMINAZIONE', 'Eliminato documento', $id, '');
                $this->json(['success' => true, 'message' => 'Documento eliminato con successo']);
            } else {
                $this->db->rollback();
                $this->json(['success' => false, 'message' => 'Documento non trovato'], 404);
            }

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Errore nell'eliminazione documento: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore nell\'eliminazione del documento'], 500);
        }
    }

    /**
     * API: Upload file Excel
     */
    public function handleFileUpload()
    {
        try {
            if (!$this->isPost()) {
                $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
                return;
            }

            if (!isset($_FILES['file'])) {
                $this->json(['success' => false, 'error' => 'Nessun file caricato'], 400);
                return;
            }

            $processor = new ExcelProcessor();
            $result = $processor->handleFileUpload($_FILES['file']);

            $this->json($result);
        } catch (Throwable $e) {
            error_log("EXCEPTION in handleFileUpload: " . $e->getMessage());
            error_log("File: " . $e->getFile() . " Line: " . $e->getLine());

            $this->json([
                'success' => false,
                'error' => 'Errore del server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Processa file Excel e restituisce contenuti
     */
    public function processExcelFile()
    {
        try {
            $fileName = $this->input('fileName');
            $progressivo = $this->input('progressivo'); // Parametro opzionale per Step 3

            if (!$fileName) {
                $this->json(['success' => false, 'error' => 'Nome file richiesto'], 400);
                return;
            }

            $processor = new ExcelProcessor();
            $result = $processor->processExcelFile($fileName, $progressivo);

            $this->json($result);
        } catch (Throwable $e) {
            // Catch any PHP errors and return proper JSON
            $this->json([
                'success' => false,
                'error' => 'Errore del server: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'fileName' => $fileName ?? 'N/A',
                    'trace' => APP_ENV === 'development' ? $e->getTraceAsString() : null
                ]
            ], 500);
        }
    }

    /**
     * API: Salva dati Excel processati
     */
    public function saveExcelData()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (!$data) {
            $this->json(['success' => false, 'error' => 'Dati non validi'], 400);
            return;
        }

        $processor = new ExcelProcessor();
        $result = $processor->saveExcelData($data, $this->db);

        $this->json($result);
    }

    /**
     * API: Genera DDT finale
     */
    public function generateDdt()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $progressivo = $this->input('progressivo');

        if (!$progressivo) {
            $this->json(['success' => false, 'message' => 'Progressivo richiesto'], 400);
            return;
        }

        $processor = new ExcelProcessor();
        $result = $processor->generaDDT($progressivo, $this->db);

        $this->json($result);
    }

    /**
     * Continua elaborazione documento esistente
     */
    public function continue($progressivo = null)
    {
        if (!$progressivo) {
            $this->setFlash('error', 'ID documento non valido');
            $this->redirect($this->url('/export'));
            return;
        }

        try {
            // Recupera i dati del documento con Eloquent
            $documento = ExportDocument::with(['terzista', 'piede', 'articoli', 'mancanti'])->find($progressivo);

            if (!$documento) {
                $this->setFlash('error', 'Documento non trovato');
                $this->redirect($this->url('/export'));
                return;
            }

            // Recupera gli articoli ordinati
            $articoli = $documento->articoli()
                ->orderByRaw('is_mancante ASC, voce_doganale ASC, codice_articolo ASC')
                ->get();

            // Recupera terzista
            $terzista = $documento->terzista;

            // Recupera piede documento
            $piede = $documento->piede;

            // Recupera dati mancanti
            $datiMancanti = $documento->mancanti;

            // OTTIMIZZATO: Pre-carica tutti gli articoli in una singola query
            $codiciArticoli = $datiMancanti->pluck('codice_articolo')->unique()->toArray();
            $articoliInfo = ExportArticle::whereIn('codice_articolo', $codiciArticoli)
                ->select('codice_articolo', 'descrizione', 'um', 'voce_doganale')
                ->get()
                ->keyBy('codice_articolo');

            // Arricchisci i dati mancanti con informazioni articolo
            foreach ($datiMancanti as $row) {
                $articoloInfo = $articoliInfo->get($row->codice_articolo);
                if ($articoloInfo) {
                    $row->descrizione = $articoloInfo->descrizione ?? '';
                    $row->um = $articoloInfo->um ?? '';
                    $row->voce_doganale = $articoloInfo->voce_doganale ?? '';
                }
            }

            // Recupera lanci con Eloquent
            $lanci = ExportLaunchData::where('id_doc', $progressivo)
                ->select('lancio', 'articolo', 'paia')
                ->get();

            // Recupera file Excel dalla directory src
            $srcDir = APP_ROOT . '/storage/export/src/' . $progressivo . '/';
            $files = [];
            if (is_dir($srcDir)) {
                $fileList = scandir($srcDir);
                foreach ($fileList as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'xlsx') {
                        $files[] = $file;
                    }
                }
            }

            // Calcola totale
            $total = 0;
            foreach ($articoli as $articolo) {
                $total += round($articolo->qta_reale * ($articolo->prezzo_unitario ?? 0), 2);
            }

            // Conteggio mancanze
            $mancanzeCount = count($datiMancanti);

            // Passa i dati alla view
            $this->render('export.continue', [
                'progressivo' => $progressivo,
                'documento' => $documento,
                'articoli' => $articoli,
                'terzista' => $terzista,
                'piede' => $piede,
                'datiMancanti' => $datiMancanti,
                'lanci' => $lanci,
                'files' => $files,
                'total' => $total,
                'mancanzeCount' => $mancanzeCount
            ]);

        } catch (Exception $e) {
            $this->setFlash('error', 'Errore nel recupero dei dati: ' . $e->getMessage());
            $this->redirect($this->url('/export'));
        }
    }

    /**
     * Visualizza documento chiuso
     */
    public function viewDocument($progressivo = null)
    {
        if (!$progressivo) {
            $this->setFlash('error', 'ID documento non valido');
            $this->redirect($this->url('/export'));
            return;
        }

        try {
            // Recupera dati documento con Eloquent
            $documento = ExportDocument::with(['terzista', 'piede', 'articoli', 'mancanti'])->find($progressivo);

            if (!$documento) {
                $this->setFlash('error', 'Documento non trovato');
                $this->redirect($this->url('/export'));
                return;
            }

            // Recupera articoli ordinati
            $articoli = $documento->articoli()
                ->orderBy('voce_doganale', 'asc')
                ->orderBy('codice_articolo', 'asc')
                ->get();

            // Recupera terzista
            $terzista = $documento->terzista;

            // Recupera piede documento
            $piede = $documento->piede;

            // Recupera dati mancanti
            $datiMancanti = $documento->mancanti;

            // Arricchisci i dati mancanti con informazioni articolo usando Eloquent
            foreach ($datiMancanti as $row) {
                $articolo = ExportArticle::where('codice_articolo', $row->codice_articolo)
                    ->select('descrizione', 'um', 'voce_doganale')
                    ->first();

                if ($articolo) {
                    $row->descrizione = $articolo->descrizione ?? '';
                    $row->um = $articolo->um ?? '';
                    $row->voce_doganale = $articolo->voce_doganale ?? '';
                }
            }

            // Recupera lanci con Eloquent
            $lanci = ExportLaunchData::where('id_doc', $progressivo)
                ->select('lancio', 'articolo', 'paia')
                ->get();

            // Calcola totale
            $total = 0;
            foreach ($articoli as $articolo) {
                if ($articolo->qta_reale > 0) {
                    $total += round($articolo->qta_reale * $articolo->prezzo_unitario, 2);
                }
            }

            // Renderizza senza layout per visualizzazione pulita del DDT
            $this->view('export.document-view', [
                'pageTitle' => 'DDT n° ' . $progressivo,
                'documento' => $documento,
                'articoli' => $articoli,
                'terzista' => $terzista,
                'piede' => $piede,
                'datiMancanti' => $datiMancanti,
                'lanci' => $lanci,
                'total' => $total,
                'progressivo' => $progressivo
            ]);

        } catch (PDOException $e) {
            error_log("Errore nella visualizzazione documento: " . $e->getMessage());
            $this->setFlash('error', 'Errore nel caricamento del documento');
            $this->redirect($this->url('/export'));
        }
    }

    /**
     * Genera segnacolli per documento
     */
    public function generateSegnacolli($progressivo = null)
    {
        if (!$progressivo) {
            $this->setFlash('error', 'ID documento non valido');
            $this->redirect($this->url('/export'));
            return;
        }

        try {
            // Recupera dati del documento e del terzista con Eloquent
            $documento = ExportDocument::with('terzista')->find($progressivo);

            if (!$documento) {
                $this->setFlash('error', 'Documento non trovato');
                $this->redirect($this->url('/export'));
                return;
            }

            // Recupera dati dei colli
            $piede = $documento->piede;

            if (!$piede || !$piede->n_colli) {
                $this->setFlash('error', 'Informazioni sui colli non trovate');
                $this->redirect($this->url('/export'));
                return;
            }

            // Inizializza mPDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'L',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'default_font' => 'dejavusans',
                // Forza directory temporanea scrivibile nel container
                'tempDir' => APP_ROOT . '/storage/cache/mpdf'
            ]);

            $n_colli = (int) $piede->n_colli;
            $aspetto_colli = $piede->aspetto_colli;
            $ragione_sociale = $documento->terzista->ragione_sociale;
            $id_documento = $documento->id;

            $mpdf->SetTitle('Segnacolli - ' . $ragione_sociale);

            // Genera una pagina per ogni collo
            for ($i = 1; $i <= $n_colli; $i++) {
                $logoPath = APP_ROOT . 'assets/small_logo.png';
                $logoSrc = file_exists($logoPath) ? $logoPath : '';

                $html = '
                <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            text-align: center;
                            padding: 20px;
                        }
                        .main-title {
                            font-size: 40pt;
                            font-weight: bold;
                            margin: 20px 0;
                        }
                        .company {
                            font-size: 60pt;
                            font-weight: bold;
                            margin: 20px 0;
                            color: white;
                            background-color: black;
                            padding: 10px;
                        }
                        .footer {
                            text-align: right;
                            font-size: 25pt;
                            margin-top: 160px;
                        }
                    </style>
                </head>
                <body>';

                if ($logoSrc) {
                    $html .= '<img src="' . $logoSrc . '" style="width: 320px; margin-bottom: 20px;">';
                }

                $html .= '
                    <div class="main-title">X</div>
                    <div class="company">' . htmlspecialchars($ragione_sociale) . '</div>
                    <div class="footer">
                        <b>DDT ' . $id_documento . '</b> | 
                        ' . htmlspecialchars($aspetto_colli) . ' ' . $i . ' di ' . $n_colli . '
                    </div>
                </body>
                </html>';

                $mpdf->AddPage();
                $mpdf->WriteHTML($html);
            }

            $filename = 'Segnacolli' . $id_documento . '_' . date('Ymd') . '.pdf';
            $mpdf->Output($filename, 'I');

        } catch (Exception $e) {
            error_log("Errore nella generazione segnacolli: " . $e->getMessage());
            $this->setFlash('error', 'Errore nella generazione dei segnacolli: ' . $e->getMessage());
            $this->redirect($this->url('/export'));
        }
    }

    /**
     * Helper: Recupera file per un documento (da temp o src)
     */
    private function getTempFiles($progressivo)
    {
        // Prima prova temp, poi src se DDT già generato
        $tempDir = APP_ROOT . '/storage/export/temp/';
        $srcDir = APP_ROOT . '/storage/export/src/' . $progressivo . '/';

        $searchDir = $tempDir;
        if ((!is_dir($tempDir) || count(scandir($tempDir)) <= 2) && is_dir($srcDir)) {
            // Se temp è vuoto e src esiste, usa src
            $searchDir = $srcDir;
        }

        if (!is_dir($searchDir)) {
            return [];
        }

        $files = scandir($searchDir);
        $files = array_diff($files, array('.', '..'));

        $result = [];
        foreach ($files as $fileName) {
            if (pathinfo($fileName, PATHINFO_EXTENSION) === 'xlsx') {
                $details = $this->getFileDetailsFromPath($searchDir . $fileName);
                $result[] = [
                    'name' => $fileName,
                    'lancio' => $details['LANCIO'],
                    'paia' => $details['PAIA_DA_PRODURRE']
                ];
            }
        }

        return $result;
    }

    /**
     * Helper: Estrae dettagli da file con percorso completo
     */
    private function getFileDetailsFromPath($filePath)
    {
        if (!file_exists($filePath)) {
            return ['LANCIO' => 'N/A', 'PAIA_DA_PRODURRE' => 'N/A'];
        }

        try {


            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $worksheet = $reader->load($filePath)->getActiveSheet();

            return [
                'LANCIO' => $worksheet->getCell('B2')->getValue() ?: 'N/A',
                'PAIA_DA_PRODURRE' => $worksheet->getCell('B3')->getValue() ?: 'N/A'
            ];

        } catch (Exception $e) {
            error_log("Errore nell'elaborazione del file Excel: " . $e->getMessage());
            return ['LANCIO' => 'N/A', 'PAIA_DA_PRODURRE' => 'N/A'];
        }
    }

    // ==============================================
    // GESTIONE TERZISTI
    // ==============================================

    /**
     * Lista terzisti con filtri e paginazione
     */
    public function terzisti()
    {
        // Recupera parametri di paginazione e filtri
        $page = (int) ($this->input('page') ?: 1);
        $pagelimit = Setting::getInt('pagination_export', 15);
        $offset = ($page - 1) * $pagelimit;

        // Recupera filtri
        $ragione_sociale = $this->input('ragione_sociale');
        $nazione = $this->input('nazione');
        $consegna = $this->input('consegna');

        try {
            // Query con Eloquent e filtri
            $query = ExportTerzista::query();

            // Applica filtri
            if ($ragione_sociale) {
                $query->where('ragione_sociale', 'like', "%{$ragione_sociale}%");
            }

            if ($nazione) {
                $query->where('nazione', 'like', "%{$nazione}%");
            }

            if ($consegna) {
                $query->where('consegna', 'like', "%{$consegna}%");
            }

            // Ordina per ragione sociale
            $query->orderBy('ragione_sociale', 'asc');

            // Paginazione
            $total_records = $query->count();
            $total_pages = ceil($total_records / $pagelimit);

            $terzisti = $query->skip($offset)
                ->take($pagelimit)
                ->get();

            $this->render('export.terzisti', [
                'pageTitle' => 'Gestione Terzisti',
                'terzisti' => $terzisti,
                'currentPage' => $page,
                'totalPages' => $total_pages,
                'totalRecords' => $total_records
            ]);

        } catch (PDOException $e) {
            error_log("Errore nel recupero terzisti: " . $e->getMessage());
            $this->render('export.terzisti', [
                'pageTitle' => 'Gestione Terzisti',
                'terzisti' => [],
                'currentPage' => 1,
                'totalPages' => 0,
                'totalRecords' => 0,
                'error' => 'Errore nel caricamento terzisti'
            ]);
        }
    }

    /**
     * Form creazione nuovo terzista
     */
    public function createTerzista()
    {
        $this->render('export.terzisti-form', [
            'pageTitle' => 'Nuovo Terzista',
            'terzista' => null,
            'isEdit' => false
        ]);
    }

    /**
     * Salva nuovo terzista
     */
    public function storeTerzista()
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('/export/terzisti'));
            return;
        }

        $data = [
            'ragione_sociale' => trim($this->input('ragione_sociale')),
            'indirizzo_1' => trim($this->input('indirizzo_1')),
            'indirizzo_2' => trim($this->input('indirizzo_2')),
            'indirizzo_3' => trim($this->input('indirizzo_3')),
            'nazione' => trim($this->input('nazione')),
            'consegna' => trim($this->input('consegna')),
            'autorizzazione' => trim($this->input('autorizzazione'))
        ];

        // Validazione
        if (empty($data['ragione_sociale'])) {
            $this->setFlash('error', 'La ragione sociale è obbligatoria');
            $this->redirectBack();
            return;
        }

        try {
            // Crea nuovo terzista con Eloquent
            ExportTerzista::create($data);

            $this->logActivity('DDT', 'NUOVO_TERZISTA', 'Creato nuovo terzista', 0, $data['ragione_sociale']);
            $this->setFlash('success', 'Terzista creato con successo');
            $this->redirect($this->url('/export/terzisti'));

        } catch (PDOException $e) {
            error_log("Errore nella creazione terzista: " . $e->getMessage());
            $this->setFlash('error', 'Errore nella creazione del terzista');
            $this->redirectBack();
        }
    }

    /**
     * Form modifica terzista
     */
    public function editTerzista($id)
    {
        try {
            // Recupera terzista con Eloquent
            $terzista = ExportTerzista::find($id);

            if (!$terzista) {
                $this->setFlash('error', 'Terzista non trovato');
                $this->redirect($this->url('/export/terzisti'));
                return;
            }


            $this->render('export.terzisti-form', [
                'pageTitle' => 'Modifica Terzista',
                'terzista' => $terzista,
                'isEdit' => true
            ]);

        } catch (PDOException $e) {
            error_log("Errore nel recupero terzista: " . $e->getMessage());
            $this->setFlash('error', 'Errore nel caricamento del terzista');
            $this->redirect($this->url('/export/terzisti'));
        }
    }

    /**
     * Aggiorna terzista esistente
     */
    public function updateTerzista($id)
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('/export/terzisti'));
            return;
        }

        $data = [
            'ragione_sociale' => trim($this->input('ragione_sociale')),
            'indirizzo_1' => trim($this->input('indirizzo_1')),
            'indirizzo_2' => trim($this->input('indirizzo_2')),
            'indirizzo_3' => trim($this->input('indirizzo_3')),
            'nazione' => trim($this->input('nazione')),
            'consegna' => trim($this->input('consegna')),
            'autorizzazione' => trim($this->input('autorizzazione'))
        ];

        // Validazione
        if (empty($data['ragione_sociale'])) {
            $this->setFlash('error', 'La ragione sociale è obbligatoria');
            $this->redirectBack();
            return;
        }

        try {
            // Aggiorna terzista con Eloquent
            $terzista = ExportTerzista::find($id);
            if ($terzista) {
                $terzista->fill($data);
                $result = $terzista->save();

                if ($result) {
                    $this->logActivity('DDT', 'MODIFICA_TERZISTA', 'Modificato terzista', $id, $data['ragione_sociale']);
                    $this->setFlash('success', 'Terzista aggiornato con successo');
                } else {
                    $this->setFlash('warning', 'Nessuna modifica effettuata');
                }
            } else {
                $this->setFlash('error', 'Terzista non trovato');
            }

            $this->redirect($this->url('/export/terzisti'));

        } catch (PDOException $e) {
            error_log("Errore nell'aggiornamento terzista: " . $e->getMessage());
            $this->setFlash('error', 'Errore nell\'aggiornamento del terzista');
            $this->redirectBack();
        }
    }

    /**
     * Elimina terzista
     */
    public function deleteTerzista()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $id = $this->input('id');

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID terzista richiesto'], 400);
            return;
        }

        try {
            // Verifica se il terzista è utilizzato in documenti con Eloquent
            $usage = ExportDocument::where('id_terzista', $id)->count();

            if ($usage > 0) {
                $this->json(['success' => false, 'message' => 'Impossibile eliminare: terzista utilizzato in ' . $usage . ' documento/i'], 400);
                return;
            }

            // Recupera dati terzista per log
            $terzista = ExportTerzista::find($id);

            if (!$terzista) {
                $this->json(['success' => false, 'message' => 'Terzista non trovato'], 404);
                return;
            }

            // Elimina terzista
            $result = $terzista->delete();

            if ($result) {
                $this->logActivity('DDT', 'ELIMINA_TERZISTA', 'Eliminato terzista', $id, $terzista->ragione_sociale);
                $this->json(['success' => true, 'message' => 'Terzista eliminato con successo']);
            } else {
                $this->json(['success' => false, 'message' => 'Errore durante l\'eliminazione'], 500);
            }

        } catch (PDOException $e) {
            error_log("Errore nell'eliminazione terzista: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore nell\'eliminazione del terzista'], 500);
        }
    }

    // ==============================================
    // NUOVI METODI API PER CONTINUE.PHP
    // ==============================================

    /**
     * API: Completa un DDT
     */
    public function completaDdt()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $progressivo = $this->input('progressivo');

        if (!$progressivo) {
            $this->json(['success' => false, 'message' => 'Progressivo richiesto'], 400);
            return;
        }

        try {
            // Verifica che il documento esista e sia aperto con Eloquent
            $documento = ExportDocument::find($progressivo);

            if (!$documento) {
                $this->json(['success' => false, 'message' => 'Documento non trovato'], 404);
                return;
            }

            if ($documento->stato === 'Chiuso') {
                $this->json(['success' => false, 'message' => 'Il documento è già completato'], 400);
                return;
            }

            // Aggiorna lo stato del documento
            $documento->stato = 'Chiuso';
            $documento->save();

            $this->logActivity('DDT', 'COMPLETAMENTO', 'DDT completato', $progressivo, '');

            $this->json(['success' => true, 'message' => 'DDT completato con successo']);

        } catch (Exception $e) {
            error_log("Errore nel completamento DDT: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore durante il completamento'], 500);
        }
    }

    /**
     * API: Cerca nomenclature e costi
     */
    public function cercaNcECosti()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $progressivo = $this->input('progressivo');

        if (!$progressivo) {
            $this->json(['success' => false, 'message' => 'Progressivo richiesto'], 400);
            return;
        }

        try {
            // Recupera articoli senza voce doganale o prezzo con Eloquent
            $articoliDaAggiornare = ExportArticle::where('id_documento', $progressivo)
                ->where(function ($query) {
                    $query->whereNull('voce_doganale')
                        ->orWhere('voce_doganale', '')
                        ->orWhereNull('prezzo_unitario')
                        ->orWhere('prezzo_unitario', 0);
                })
                ->get();

            $aggiornati = 0;
            foreach ($articoliDaAggiornare as $articolo) {
                // Cerca dati da altri DDT con lo stesso codice articolo (logica legacy)
                $datiArticolo = ExportArticle::where('codice_articolo', $articolo->codice_articolo)
                    ->where('id_documento', '!=', $progressivo)
                    ->whereNotNull('voce_doganale')
                    ->where('voce_doganale', '!=', '')
                    ->where('prezzo_unitario', '>', 0)
                    ->select('voce_doganale', 'prezzo_unitario')
                    ->first();

                if ($datiArticolo) {
                    $updated = false;

                    if (!empty($datiArticolo->voce_doganale) && empty($articolo->voce_doganale)) {
                        $articolo->voce_doganale = $datiArticolo->voce_doganale;
                        $updated = true;
                    }

                    if ($datiArticolo->prezzo_unitario > 0 && ($articolo->prezzo_unitario ?? 0) == 0) {
                        $articolo->prezzo_unitario = $datiArticolo->prezzo_unitario;
                        $updated = true;
                    }

                    if ($updated) {
                        $articolo->save();
                        $aggiornati++;
                    }
                }
            }

            // Aggiorna il campo first_boot come nel sistema legacy
            ExportDocument::where('id', $progressivo)->update(['first_boot' => 0]);

            $this->logActivity('DDT', 'RICERCA_NC_COSTI', 'Ricerca completata', $progressivo, "Aggiornati {$aggiornati} articoli");
            $this->json(['success' => true, 'message' => "Ricerca completata. {$aggiornati} articoli aggiornati."]);

        } catch (Exception $e) {
            error_log("Errore nella ricerca NC e costi: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore durante la ricerca'], 500);
        }
    }

    /**
     * API: Elabora mancanti
     */
    public function elaboraMancanti()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $progressivo = $this->input('progressivo');

        if (!$progressivo) {
            $this->json(['success' => false, 'message' => 'Progressivo richiesto'], 400);
            return;
        }

        try {
            $this->db->beginTransaction();

            // Elimina i mancanti esistenti per questo documento
            ExportMissingData::where('id_documento', $progressivo)->delete();

            // Calcola i nuovi mancanti (articoli con qta_reale < qta_originale)
            $articoliParziali = ExportArticle::where('id_documento', $progressivo)
                ->whereRaw('qta_reale < qta_originale')
                ->get();

            $mancantiInseriti = 0;
            foreach ($articoliParziali as $articolo) {
                $qtaMancante = $articolo->qta_originale - $articolo->qta_reale;

                if ($qtaMancante > 0) {
                    ExportMissingData::create([
                        'id_documento' => $progressivo,
                        'codice_articolo' => $articolo->codice_articolo,
                        'qta_mancante' => $qtaMancante
                    ]);
                    $mancantiInseriti++;
                }
            }

            $this->db->commit();
            $this->logActivity('DDT', 'ELABORA_MANCANTI', 'Mancanti elaborati', $progressivo, "{$mancantiInseriti} mancanti");

            $this->json([
                'success' => true,
                'message' => "Elaborazione completata. {$mancantiInseriti} mancanti identificati."
            ]);

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Errore nell'elaborazione mancanti: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore durante l\'elaborazione'], 500);
        }
    }

    /**
     * API: Aggiorna dati articolo
     */
    public function updateData()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $id = $this->input('id');
        $field = $this->input('field');
        $value = $this->input('value');

        if (!$id || !$field) {
            $this->json(['success' => false, 'message' => 'ID e campo richiesti'], 400);
            return;
        }

        // Validazione extra ID
        $numericId = (int) $id;
        if ($numericId <= 0) {
            error_log("updateData chiamato con ID non valido: '{$id}' (convertito a: {$numericId})");
            $this->json(['success' => false, 'message' => 'ID articolo non valido'], 400);
            return;
        }

        // Campi consentiti per sicurezza
        $allowedFields = ['descrizione', 'voce_doganale', 'qta_originale', 'qta_reale', 'prezzo_unitario'];
        if (!in_array($field, $allowedFields)) {
            $this->json(['success' => false, 'message' => 'Campo non consentito'], 400);
            return;
        }

        try {
            // Prepara il valore in base al tipo
            if (in_array($field, ['qta_originale', 'qta_reale', 'prezzo_unitario'])) {
                $value = str_replace(',', '.', $value); // Converte virgola in punto
                $value = (float) $value;
            }

            // Aggiorna con Eloquent
            $articolo = ExportArticle::find($numericId);
            if ($articolo) {
                $articolo->$field = $value;
                $articolo->save();
                $this->json(['success' => true, 'message' => 'Dato aggiornato con successo']);
            } else {
                $this->json(['success' => false, 'message' => 'Articolo non trovato'], 404);
            }

        } catch (Exception $e) {
            error_log("Errore nell'aggiornamento dati: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore durante l\'aggiornamento'], 500);
        }
    }

    /**
     * API: Salva dati piede documento
     */
    public function savePiedeDocumento()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (!$data || !isset($data['progressivo'])) {
            $this->json(['success' => false, 'message' => 'Dati non validi'], 400);
            return;
        }

        try {
            // Usa updateOrCreate di Eloquent per semplificare
            ExportDocumentFooter::updateOrCreate(
                ['id_documento' => $data['progressivo']],
                [
                    'aspetto_colli' => $data['aspetto_merce'] ?? '',
                    'n_colli' => $data['numero_colli'] ?? 0,
                    'tot_peso_lordo' => $data['peso_lordo'] ?? 0,
                    'tot_peso_netto' => $data['peso_netto'] ?? 0,
                    'trasportatore' => $data['trasportatore'] ?? '',
                    'consegnato_per' => $data['consegnato_per'] ?? ''
                ]
            );

            $this->logActivity('DDT', 'PIEDE_DOCUMENTO', 'Dati piede aggiornati', $data['progressivo'], '');
            $this->json(['success' => true, 'message' => 'Dati salvati con successo']);

        } catch (Exception $e) {
            error_log("Errore nel salvataggio piede documento: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore durante il salvataggio'], 500);
        }
    }

    /**
     * API: Salva commento
     */
    public function saveCommento()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $progressivo = $this->input('progressivo');
        $commento = $this->input('commento');

        if (!$progressivo || !$commento) {
            $this->json(['success' => false, 'message' => 'Progressivo e commento richiesti'], 400);
            return;
        }

        try {
            // Aggiorna commento con Eloquent
            ExportDocument::where('id', $progressivo)->update(['commento' => $commento]);

            $this->logActivity('DDT', 'COMMENTO', 'Commento aggiornato', $progressivo, '');
            $this->json(['success' => true, 'message' => 'Commento salvato con successo']);

        } catch (Exception $e) {
            error_log("Errore nel salvataggio commento: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore durante il salvataggio'], 500);
        }
    }

    /**
     * API: Salva autorizzazione
     */
    public function saveAutorizzazione()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $progressivo = $this->input('progressivo');
        $autorizzazione = $this->input('autorizzazione');

        if (!$progressivo || !$autorizzazione) {
            $this->json(['success' => false, 'message' => 'Progressivo e autorizzazione richiesti'], 400);
            return;
        }

        try {
            // Aggiorna autorizzazione con Eloquent
            ExportDocument::where('id', $progressivo)->update(['autorizzazione' => $autorizzazione]);

            $this->logActivity('DDT', 'AUTORIZZAZIONE', 'Autorizzazione aggiornata', $progressivo, '');
            $this->json(['success' => true, 'message' => 'Autorizzazione salvata con successo']);

        } catch (Exception $e) {
            error_log("Errore nel salvataggio autorizzazione: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore durante il salvataggio'], 500);
        }
    }

    /**
     * API: Aggiungi mancanti selezionati
     */
    public function aggiungiMancanti()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (!$data || !isset($data['progressivo']) || !isset($data['mancanti_ids'])) {
            $this->json(['success' => false, 'message' => 'Dati non validi'], 400);
            return;
        }

        try {
            $this->db->beginTransaction();

            $aggiunti = 0;
            foreach ($data['mancanti_ids'] as $mancanteId) {
                // Recupera i dati del mancante con Eloquent
                $mancante = ExportMissingData::find($mancanteId);

                if ($mancante) {
                    // Aggiungi come nuovo articolo nel DDT corrente
                    ExportArticle::create([
                        'id_documento' => $data['progressivo'],
                        'codice_articolo' => $mancante->codice_articolo,
                        'descrizione' => $mancante->descrizione ?? '',
                        'qta_originale' => $mancante->qta_mancante,
                        'qta_reale' => $mancante->qta_mancante,
                        'is_mancante' => 1,
                        'rif_mancante' => 'DDT ' . $mancante->id_documento
                    ]);

                    // Rimuovi dalla lista mancanti
                    $mancante->delete();

                    $aggiunti++;
                }
            }

            $this->db->commit();
            $this->logActivity('DDT', 'AGGIUNGI_MANCANTI', 'Mancanti aggiunti al DDT', $data['progressivo'], "{$aggiunti} articoli");

            $this->json([
                'success' => true,
                'message' => "{$aggiunti} mancanti aggiunti con successo"
            ]);

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Errore nell'aggiunta mancanti: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore durante l\'aggiunta'], 500);
        }
    }

    /**
     * API: Recupera dati voci doganali
     */
    public function getDoganaleData()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $progressivo = $this->input('progressivo');
        $includeSottopiedi = $this->input('include_sottopiedi') === '1';

        if (!$progressivo) {
            $this->json(['success' => false, 'message' => 'Progressivo richiesto'], 400);
            return;
        }

        try {
            // Sempre includi tutte le voci doganali (non escludere mai 56031480) con Eloquent
            $voci = ExportArticle::where('id_documento', $progressivo)
                ->where('qta_reale', '>', 0)
                ->select('voce_doganale')
                ->selectRaw('SUM(qta_reale) as qta_totale')
                ->selectRaw('0 as peso_netto')
                ->groupBy('voce_doganale')
                ->orderBy('voce_doganale')
                ->get();

            // Se richiesto di includere sottopiedi E ci sono articoli con NC 56031480,
            // aggiungi una riga separata "SOTTOPIEDI" per specificare il peso a parte
            if ($includeSottopiedi) {
                // Controlla se ci sono articoli con voce doganale 56031480
                $hasSottopiedi56031480 = false;
                foreach ($voci as $voce) {
                    if ($voce->voce_doganale === '56031480') {
                        $hasSottopiedi56031480 = true;
                        break;
                    }
                }

                // Se ci sono articoli 56031480, aggiungi la riga separata SOTTOPIEDI
                if ($hasSottopiedi56031480) {
                    // Controlla se SOTTOPIEDI è già presente come riga separata
                    $sottopiedeSeParatoPresente = false;
                    foreach ($voci as $voce) {
                        if ($voce->voce_doganale === 'SOTTOPIEDI') {
                            $sottopiedeSeParatoPresente = true;
                            break;
                        }
                    }

                    // Aggiungi SOTTOPIEDI solo se non è già presente come riga separata
                    if (!$sottopiedeSeParatoPresente) {
                        $voci[] = (object)[
                            'voce_doganale' => 'SOTTOPIEDI',
                            'qta_totale' => 0,
                            'peso_netto' => 0
                        ];
                    }
                }
            }

            // Recupera pesi già salvati se esistono con Eloquent
            $pesi = ExportDocumentFooter::where('id_documento', $progressivo)
                ->select(
                    'voce_1',
                    'peso_1',
                    'voce_2',
                    'peso_2',
                    'voce_3',
                    'peso_3',
                    'voce_4',
                    'peso_4',
                    'voce_5',
                    'peso_5',
                    'voce_6',
                    'peso_6',
                    'voce_7',
                    'peso_7',
                    'voce_8',
                    'peso_8',
                    'voce_9',
                    'peso_9',
                    'voce_10',
                    'peso_10',
                    'voce_11',
                    'peso_11',
                    'voce_12',
                    'peso_12',
                    'voce_13',
                    'peso_13',
                    'voce_14',
                    'peso_14',
                    'voce_15',
                    'peso_15'
                )
                ->first();

            // Associa i pesi alle voci
            if ($pesi) {
                foreach ($voci as $voce) {
                    for ($i = 1; $i <= 15; $i++) {
                        if (
                            $pesi->{"voce_{$i}"} === $voce->voce_doganale ||
                            ($pesi->{"voce_{$i}"} === 'SOTTOPIEDI' && $voce->voce_doganale === 'SOTTOPIEDI')
                        ) {
                            $voce->peso_netto = $pesi->{"peso_{$i}"} ?? 0;
                            break;
                        }
                    }
                }
            }

            $this->json([
                'success' => true,
                'voci' => $voci
            ]);

        } catch (Exception $e) {
            error_log("Errore nel recupero dati doganali: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore nel caricamento dei dati'], 500);
        }
    }

    /**
     * API: Aggiorna peso voce doganale
     */
    public function updateDoganaleWeight()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $progressivo = $this->input('progressivo');
        $voceDoganale = $this->input('voce_doganale');
        $peso = (float) $this->input('peso');

        if (!$progressivo || !$voceDoganale) {
            $this->json(['success' => false, 'message' => 'Progressivo e voce doganale richiesti'], 400);
            return;
        }

        try {
            // Recupera o crea record piede documento con Eloquent
            $piede = ExportDocumentFooter::where('id_documento', $progressivo)->first();

            if (!$piede) {
                // Crea nuovo record
                ExportDocumentFooter::create([
                    'id_documento' => $progressivo,
                    'voce_1' => $voceDoganale,
                    'peso_1' => $peso
                ]);
            } else {
                // Trova slot libero per la voce
                $slot = null;
                for ($i = 1; $i <= 15; $i++) {
                    if (empty($piede->{"voce_{$i}"}) || $piede->{"voce_{$i}"} === $voceDoganale) {
                        $slot = $i;
                        break;
                    }
                }

                if ($slot) {
                    $piede->{"voce_{$slot}"} = $voceDoganale;
                    $piede->{"peso_{$slot}"} = $peso;
                    $piede->save();
                }
            }

            $this->json(['success' => true, 'message' => 'Peso aggiornato con successo']);

        } catch (Exception $e) {
            error_log("Errore nell'aggiornamento peso: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore durante l\'aggiornamento'], 500);
        }
    }

    /**
     * API: Recupera dati piede documento
     */
    public function getPiedeDocumento()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $progressivo = $this->input('progressivo');

        if (!$progressivo) {
            $this->json(['success' => false, 'message' => 'Progressivo richiesto'], 400);
            return;
        }

        try {
            // Recupera piede documento con Eloquent
            $piede = ExportDocumentFooter::where('id_documento', $progressivo)->first();

            $this->json([
                'success' => true,
                'piede' => $piede
            ]);

        } catch (Exception $e) {
            error_log("Errore nel recupero piede documento: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore nel caricamento dei dati'], 500);
        }
    }

    /**
     * API: Recupera lista mancanti
     */
    public function getMancanti()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $progressivo = $this->input('progressivo');

        if (!$progressivo) {
            $this->json(['success' => false, 'message' => 'Progressivo richiesto'], 400);
            return;
        }

        try {
            // Recupera mancanti di tutti i DDT (escludendo quello corrente) con Eloquent
            $mancanti = ExportMissingData::where('exp_dati_mancanti.id_documento', '!=', $progressivo)
                ->leftJoin('exp_dati_articoli as a', function ($join) {
                    $join->on('exp_dati_mancanti.codice_articolo', '=', 'a.codice_articolo')
                        ->on('a.id_documento', '=', 'exp_dati_mancanti.id_documento');
                })
                ->select(
                    'exp_dati_mancanti.id',
                    'exp_dati_mancanti.codice_articolo',
                    'exp_dati_mancanti.qta_mancante',
                    'exp_dati_mancanti.id_documento as rif_ddt',
                    'a.descrizione'
                )
                ->orderBy('exp_dati_mancanti.id_documento')
                ->orderBy('exp_dati_mancanti.codice_articolo')
                ->get()
                ->map(function ($item) {
                    if (empty($item->descrizione)) {
                        $item->descrizione = 'N/A';
                    }
                    return $item;
                });

            $this->json([
                'success' => true,
                'mancanti' => $mancanti
            ]);

        } catch (Exception $e) {
            error_log("Errore nel recupero mancanti: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore nel caricamento dei mancanti'], 500);
        }
    }

    /**
     * API: Reset piede documento
     */
    public function resetPiedeDocumento()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $progressivo = $this->input('progressivo');

        if (!$progressivo) {
            $this->json(['success' => false, 'message' => 'Progressivo richiesto'], 400);
            return;
        }

        try {
            // Elimina il record dal database con Eloquent
            ExportDocumentFooter::where('id_documento', $progressivo)->delete();

            $this->json([
                'success' => true,
                'message' => 'Dati piede documento resettati con successo'
            ]);

        } catch (Exception $e) {
            error_log("Errore nel reset piede documento: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore durante il reset'], 500);
        }
    }

    /**
     * Download singolo file
     */
    public function downloadFile($progressivo, $filename)
    {
        $srcDir = APP_ROOT . '/storage/export/src/' . $progressivo . '/';
        $filePath = $srcDir . $filename;

        if (!file_exists($filePath)) {
            $this->setFlash('error', 'File non trovato');
            $this->redirect($this->url('/export/continue/' . $progressivo));
            return;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
    }

    /**
     * Download tutti i file come ZIP
     */
    public function downloadAllFiles($progressivo)
    {
        $srcDir = APP_ROOT . '/storage/export/src/' . $progressivo . '/';

        if (!is_dir($srcDir)) {
            $this->setFlash('error', 'Directory non trovata');
            $this->redirect($this->url('/export/continue/' . $progressivo));
            return;
        }

        $files = scandir($srcDir);
        $files = array_diff($files, ['.', '..']);
        $excelFiles = array_filter($files, function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'xlsx';
        });

        if (empty($excelFiles)) {
            $this->setFlash('error', 'Nessun file Excel trovato');
            $this->redirect($this->url('/export/continue/' . $progressivo));
            return;
        }

        // Assicurati che la directory temp esista
        $tempDir = APP_ROOT . '/storage/temp';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipPath = $tempDir . '/DDT_' . $progressivo . '_files.zip';
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($excelFiles as $file) {
                $zip->addFile($srcDir . $file, $file);
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="DDT_' . $progressivo . '_files.zip"');
            header('Content-Length: ' . filesize($zipPath));
            readfile($zipPath);
            unlink($zipPath); // Pulisci il file temporaneo
        } else {
            $this->setFlash('error', 'Errore nella creazione del file ZIP');
            $this->redirect($this->url('/export/continue/' . $progressivo));
        }
    }

    /**
     * Genera PDF del DDT
     */
    public function generatePdf($progressivo)
    {
        // Reindirizza alla view del documento per la stampa
        $this->redirect($this->url('/export/view/' . $progressivo));
    }
}
