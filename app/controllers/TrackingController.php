<?php
/**
 * Tracking Controller
 * Gestione completa del sistema di monitoraggio lotti di produzione
 * 
 * Funzionalità:
 * - Dashboard tracking principale
 * - Associazione cartellini per ricerca
 * - Associazione cartellini manuali
 * - Gestione lotti e dettagli
 * - Albero dettagli e visualizzazioni
 * - Report PDF/Excel completi
 * - Packing Lists e gestione SKU
 */

// Autoloader già incluso in config.php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\TrackType;
use App\Models\TrackLink;
use App\Models\TrackOrderInfo;
use App\Models\TrackSku;
use App\Models\TrackLotInfo;
use App\Models\CoreData;
use App\Models\ActivityLog;
use App\Models\Setting;

class TrackingController extends BaseController
{
    /**
     * Dashboard principale tracking
     */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        // Carica statistiche dashboard
        $stats = $this->getTrackingStats();

        $data = [
            'pageTitle' => 'Tracking - Monitoraggio Lotti',
            'stats' => $stats,
            'pageScripts' => $this->getIndexScripts()
        ];

        $this->render('tracking.index', $data);
    }

    /**
     * Associazione cartellini per ricerca multipla
     */
    public function multiSearch()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        // Carica tipi di tracking disponibili usando Eloquent
        $trackTypes = TrackType::ordered()->get();

        $data = [
            'pageTitle' => 'Tracking - Associa per Ricerca',
            'trackTypes' => $trackTypes,
            'pageScripts' => $this->getMultiSearchScripts()
        ];

        $this->render('tracking.multisearch', $data);
    }

    /**
     * Associazione cartellini manuali
     */
    public function orderSearch()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        // Carica tipi di tracking disponibili usando Eloquent
        $trackTypes = TrackType::ordered()->get();

        $data = [
            'pageTitle' => 'Tracking - Associa Cartellini',
            'trackTypes' => $trackTypes,
            'pageScripts' => $this->getOrderSearchScripts()
        ];

        $this->render('tracking.ordersearch', $data);
    }

    /**
     * Vista albero dettagli
     */
    public function treeView()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        // Carica statistiche per la vista
        $stats = $this->getTreeViewStats();

        $data = [
            'pageTitle' => 'Tracking - Albero Dettagli',
            'stats' => $stats,
            'pageScripts' => $this->getTreeViewScripts()
        ];

        $this->render('tracking.treeview', $data);
    }

    /**
     * Gestione dettagli lotti
     */
    public function lotDetailManager()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        // Gestione salvataggio riferimenti lotti
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_references'])) {
            $this->saveReferences();
        }

        // Gestione aggiornamento dettagli lotto
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_lot_details'])) {
            $this->updateLotDetails();
        }

        // Gestione salvataggio date ordini
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_order_dates'])) {
            $this->saveOrderDates();
        }

        // Gestione aggiornamento dettagli ordine
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_details'])) {
            $this->updateOrderDetails();
        }

        // Gestione salvataggio SKU
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_sku'])) {
            $this->saveSkuCodes();
        }

        // Gestione aggiornamento dettagli articolo
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_articolo_details'])) {
            $this->updateArticoloDetails();
        }

        // Carica dati per la vista
        $lotsWithoutReferences = $this->getLotsWithoutReferences();
        $allLots = $this->getAllLots();
        $ordersWithoutDate = $this->getOrdersWithoutDate();
        $allOrders = $this->getAllOrders();
        $articoliWithoutSku = $this->getArticoliWithoutSku();
        $allArticoli = $this->getAllArticoli();

        $data = [
            'pageTitle' => 'Tracking - Dettagli Lotti',
            'lotsWithoutReferences' => $lotsWithoutReferences,
            'allLots' => $allLots,
            'ordersWithoutDate' => $ordersWithoutDate,
            'allOrders' => $allOrders,
            'articoliWithoutSku' => $articoliWithoutSku,
            'allArticoli' => $allArticoli,
            'pageScripts' => $this->getLotManagerScripts()
        ];

        $this->render('tracking.lotdetail', $data);
    }

    /**
     * Packing List - Generazione report PDF ed Excel
     */
    public function packingList()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        // Carica statistiche per la dashboard (opzionale)
        $stats = $this->getPackingListStats();

        $data = [
            'pageTitle' => 'Tracking - Packing List',
            'stats' => $stats,
            'pageScripts' => $this->getPackingListScripts()
        ];

        $this->render('tracking.packinglist', $data);
    }

    /**
     * Interfaccia per generazione fiches cartellini
     */
    public function makeFiches()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        $data = [
            'pageTitle' => 'Tracking - Fiches Cartellini',
            'pageScripts' => $this->getMakeFichesScripts()
        ];

        $this->render('tracking.makefiches', $data);
    }

    /**
     * Genera report PDF fiches per cartellini
     */
    public function generateReportFiches()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        // Leggi i dati inviati dalla richiesta POST
        $input = file_get_contents("php://input");
        $request = json_decode($input, true);

        // Verifica se sono stati ricevuti i dati dei cartellini
        if (!isset($request['cartellini']) || empty($request['cartellini'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Dati non ricevuti correttamente.']);
            return;
        }

        $cartellini = $request['cartellini'];

        try {
            // Query usando Eloquent con eager loading per evitare N+1
            $results = TrackLink::with(['trackType', 'coreData'])
                ->whereIn('cartel', $cartellini)
                ->orderBy('cartel')
                ->orderBy(function($query) {
                    $query->select('name')
                        ->from('track_types')
                        ->whereColumn('track_types.id', 'track_links.type_id');
                })
                ->orderBy('lot')
                ->get();
            
            // Raccogli i dati in una struttura raggruppata per cartellino e tipo
            $data = [];
            foreach ($results as $row) {
                $cartel = $row->cartel;
                $type_name = $row->trackType->name;

                if (!isset($data[$cartel])) {
                    $data[$cartel] = [
                        'Commessa Cli' => $row->coreData->{'Commessa Cli'},
                        'types' => []
                    ];
                }

                if (!isset($data[$cartel]['types'][$type_name])) {
                    $data[$cartel]['types'][$type_name] = [];
                }

                $data[$cartel]['types'][$type_name][] = $row->lot;
            }

            // Creazione del documento PDF utilizzando TCPDF
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            // Impostazioni del documento PDF
            $pdf->SetCreator('WebGRE');
            $pdf->SetAuthor('Emmegiemme');
            $pdf->SetTitle('Fiches Cartellini');
            $pdf->SetSubject('Report');
            $pdf->SetKeywords('TCPDF, PDF, fiches, cartellini');
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetHeaderMargin(0);
            $pdf->SetFooterMargin(0);
            
            // Itera sui cartellini per costruire le pagine PDF
            foreach ($data as $cartel => $details) {
                $pdf->AddPage();
                
                // Titolo della pagina
                $pdf->SetFont('helvetica', '', 15);
                $pdf->Cell(0, 10, "DETTAGLIO LOTTI", 0, 1, 'C');
                $pdf->Ln(8);
                
                // Dettagli del cartellino
                $pdf->SetFont('helvetica', 'B', 18);
                $pdf->Cell(0, 10, $cartel . " / " . $details['Commessa Cli'], 0, 1, 'C');
                $pdf->Ln(8);
                
                // Itera sui tipi di lotto
                foreach ($details['types'] as $type_name => $lots) {
                    // Stampa il nome del tipo di lotto con testo bianco su sfondo nero
                    $pdf->SetFont('helvetica', 'B', 12);
                    $pdf->SetTextColor(255, 255, 255); // Testo bianco
                    $pdf->SetFillColor(0, 0, 0); // Sfondo nero
                    $pdf->Cell(0, 10, $type_name, 0, 1, 'C', 1); // '1' abilita il riempimento
                    $pdf->Ln(4);
                    
                    // Ripristina il colore del testo per i lotti
                    $pdf->SetTextColor(0, 0, 0); // Testo nero
                    
                    // Stampa i lotti per il tipo di lotto corrente
                    $pdf->SetFont('helvetica', '', 10);
                    foreach ($lots as $lot) {
                        $pdf->Cell(0, 10, $lot, 0, 1, 'C');
                        $pdf->Ln(2);
                    }
                    $pdf->Ln(4); // Aggiungi spazio tra i tipi di lotti
                }
            }

            // Output del documento PDF
            $pdfContent = $pdf->Output('', 'S');
            
            // Invia il contenuto del PDF come risposta
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="fiches_report.pdf"');
            echo $pdfContent;
            
        } catch (Exception $e) {
            // Gestione degli errori
            http_response_code(500);
            echo json_encode(['message' => 'Errore del server: ' . $e->getMessage()]);
        }
    }

    /**
     * Processamento cartellini selezionati
     */
    public function processLinks()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/tracking/multisearch'));
        }

        if (!isset($_POST['selectedCartels']) || empty($_POST['selectedCartels'])) {
            $this->setFlash('error', 'Nessun cartellino selezionato');
            $this->redirect($this->url('/tracking/multisearch'));
        }

        // Verifica CSRF token
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token CSRF non valido');
            $this->redirect($this->url('/tracking/multisearch'));
        }

        $selectedCartels = json_decode($_POST['selectedCartels'], true);

        if (!is_array($selectedCartels) || empty($selectedCartels)) {
            $this->setFlash('error', 'Dati non validi');
            $this->redirect($this->url('/tracking/multisearch'));
        }

        // Carica tipi di tracking disponibili usando Eloquent
        $trackTypes = TrackType::ordered()->get();

        $data = [
            'pageTitle' => 'Tracking - Associazione Cartellini',
            'selectedCartels' => $selectedCartels,
            'trackTypes' => $trackTypes,
            'pageScripts' => $this->getProcessLinksScripts($selectedCartels)
        ];

        $this->render('tracking.processlinks', $data);
    }

    /**
     * API: Salva collegamenti tracking
     */
    public function saveLinks()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Metodo non valido']);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        try {
            $this->db->beginTransaction();

            $type_id = $input['type_id'] ?? null;
            $lotNumbers = $input['lotNumbers'] ?? [];
            $cartelli = $input['cartelli'] ?? [];

            if (empty($type_id) || empty($lotNumbers) || empty($cartelli)) {
                throw new Exception('Dati mancanti');
            }

            // Inserisce i collegamenti prevenendo duplicati
            foreach ($cartelli as $cartel) {
                foreach ($lotNumbers as $lot) {
                    if (!empty(trim($lot))) {
                        $lotTrimmed = trim($lot);

                        // Verifica se il collegamento esiste già usando Eloquent
                        $existing = TrackLink::forCartel($cartel)
                            ->forType($type_id)
                            ->forLot($lotTrimmed)
                            ->first();

                        if (!$existing) {
                            TrackLink::create([
                                'cartel' => $cartel,
                                'type_id' => $type_id,
                                'lot' => $lotTrimmed,
                                'timestamp' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
            }

            $this->db->commit();

            $this->logActivity('TRACKING', 'SAVE_LINKS', 'Collegamenti tracking salvati');
            $this->json(['success' => true, 'message' => 'Collegamenti salvati con successo']);

        } catch (Exception $e) {
            $this->db->rollback();
            error_log('Error saving tracking links: ' . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Errore durante il salvataggio']);
        }
    }

    /**
     * API: Cerca dati per tracking
     */
    public function searchData()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Metodo non valido']);
        }

        $cartel = $_POST['cartel'] ?? '';
        $commessa = $_POST['commessa'] ?? '';
        $articolo = $_POST['articolo'] ?? '';
        $descrizioneArticolo = $_POST['descrizioneArticolo'] ?? '';
        $ln = $_POST['ln'] ?? '';
        $ragioneSociale = $_POST['ragioneSociale'] ?? '';
        $ordine = $_POST['ordine'] ?? '';

        try {
            // Query Eloquent con filtri dinamici
            $query = CoreData::select('Cartel', 'Commessa Cli', 'Articolo', 'Descrizione Articolo', 'Ln', 'Ragione Sociale', 'Tot');

            if (!empty($cartel)) {
                $query->where('Cartel', 'LIKE', "$cartel%");
            }
            if (!empty($commessa)) {
                $query->where('Commessa Cli', 'LIKE', "$commessa%");
            }
            if (!empty($articolo)) {
                $query->where('Articolo', 'LIKE', "%$articolo%");
            }
            if (!empty($descrizioneArticolo)) {
                $query->where('Descrizione Articolo', 'LIKE', "%$descrizioneArticolo%");
            }
            if (!empty($ln)) {
                $query->where('Ln', 'LIKE', "%$ln%");
            }
            if (!empty($ragioneSociale)) {
                $query->where('Ragione Sociale', 'LIKE', "%$ragioneSociale%");
            }
            if (!empty($ordine)) {
                $query->where('Ordine', 'LIKE', "%$ordine%");
            }

            $results = $query->orderBy('Cartel')->get();

            header('Content-Type: application/json');
            echo json_encode($results);
            exit;

        } catch (Exception $e) {
            error_log('Error searching tracking data: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * API: Ottiene dati albero per treeview
     */
    public function getTreeData()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        if (!isset($_GET['search_query'])) {
            echo '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg">Inserisci una query di ricerca.</div>';
            exit;
        }

        $searchQuery = $_GET['search_query'];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = Setting::getInt('pagination_treeview', 25);
        $maxResults = Setting::getInt('pagination_max_limit', 1000);

        try {
            $query = TrackLink::with(['trackType', 'coreData'])
                ->select('id', 'lot', 'cartel', 'type_id', 'note', 'timestamp');

            if ($searchQuery === '*') {
                // Limita a 1000 record massimo per query *
                $totalCount = $query->count();
                $isLimited = $totalCount > $maxResults;

                $results = $query->orderBy('cartel')
                    ->orderBy(function($q) {
                        $q->select('name')
                          ->from('track_types')
                          ->whereColumn('track_types.id', 'track_links.type_id');
                    })
                    ->orderBy('lot')
                    ->take($maxResults)
                    ->skip(($page - 1) * $perPage)
                    ->limit($perPage)
                    ->get();

                $totalResults = min($totalCount, $maxResults);
            } else {
                // Ricerca con filtri
                $queryWithFilters = $query->where(function($q) use ($searchQuery) {
                        $q->where('cartel', 'LIKE', $searchQuery . '%')
                          ->orWhere('lot', 'LIKE', '%' . $searchQuery . '%')
                          ->orWhereHas('coreData', function($subQ) use ($searchQuery) {
                              $subQ->where('Commessa Cli', 'LIKE', '%' . $searchQuery . '%');
                          });
                    });

                $totalResults = $queryWithFilters->count();
                $isLimited = false;

                $results = $queryWithFilters->orderBy('cartel')
                    ->orderBy(function($q) {
                        $q->select('name')
                          ->from('track_types')
                          ->whereColumn('track_types.id', 'track_links.type_id');
                    })
                    ->orderBy('lot')
                    ->skip(($page - 1) * $perPage)
                    ->limit($perPage)
                    ->get();
            }

            if ($results && $results->count() > 0) {
                // Calcola informazioni paginazione
                $totalPages = ceil($totalResults / $perPage);
                $hasMore = $page < $totalPages;
                $hasPrev = $page > 1;

                // Genera HTML con informazioni paginazione
                $html = $this->renderTreeView($results);

                // Aggiungi informazioni su limitazione per query *
                if ($searchQuery === '*' && isset($isLimited) && $isLimited) {
                    $html = '<div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Risultati limitati a ' . $maxResults . ' record per performance. Usa filtri più specifici per accedere a tutti i dati.
                    </div>' . $html;
                }

                // Aggiungi controlli paginazione
                if ($totalPages > 1) {
                    $html .= $this->renderPagination($page, $totalPages, $totalResults, $searchQuery);
                }

                echo $html;
            } else {
                echo '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mt-4">Nessun risultato per i dati inseriti.</div>';
            }

        } catch (Exception $e) {
            error_log('Error getting tree data: ' . $e->getMessage());
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mt-4">Errore durante la ricerca.</div>';
        }

        exit;
    }

    /**
     * API: Aggiorna lotto
     */
    public function updateLot()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo 'Metodo non valido';
            exit;
        }

        $id = $_POST['id'] ?? null;
        $newLotValue = $_POST['lot'] ?? null;

        if (!$id || !$newLotValue) {
            echo 'Dati non validi';
            exit;
        }

        try {
            $trackLink = TrackLink::find($id);
            if ($trackLink) {
                $trackLink->update(['lot' => $newLotValue]);
                $this->logActivity('TRACKING', 'UPDATE_LOT', "Lotto aggiornato ID: $id");
                echo 'Lotto aggiornato con successo.';
            } else {
                echo 'Track link non trovato.';
            }
        } catch (Exception $e) {
            error_log('Error updating lot: ' . $e->getMessage());
            echo 'Errore durante l\'aggiornamento del lotto.';
        }

        exit;
    }

    /**
     * API: Elimina lotto
     */
    public function deleteLot()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo 'Metodo non valido';
            exit;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo 'Dati non validi';
            exit;
        }

        try {
            $trackLink = TrackLink::find($id);
            if ($trackLink) {
                $trackLink->delete();
                $this->logActivity('TRACKING', 'DELETE_LOT', "Lotto eliminato ID: $id");
                echo 'Lotto cancellato con successo.';
            } else {
                echo 'Track link non trovato.';
            }
        } catch (Exception $e) {
            error_log('Error deleting lot: ' . $e->getMessage());
            echo 'Errore durante la cancellazione del lotto.';
        }

        exit;
    }

    /**
     * API: Ricerca dettagli ordine
     */
    public function searchOrderDetails()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        $ordine = $_GET['ordine'] ?? '';

        if (empty($ordine)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Numero ordine mancante']);
            exit;
        }

        try {
            // Prima verifica se l'ordine esiste nei dati usando Eloquent
            $orderExists = CoreData::forOrder($ordine)->exists();
            
            if (!$orderExists) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Ordine ' . htmlspecialchars($ordine) . ' non trovato nei dati']);
                exit;
            }

            // Cerca i dettagli dell'ordine usando Eloquent
            $details = TrackOrderInfo::find($ordine);

            if ($details) {
                // Formatta la data per l'input date HTML (Y-m-d)
                if (!empty($details['date']) && $details['date'] != '0000-00-00') {
                    $details['date'] = date('Y-m-d', strtotime($details['date']));
                } else {
                    $details['date'] = '';
                }
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'data' => $details]);
            } else {
                // Restituisci un record vuoto per permettere l'inserimento
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'data' => [
                    'ordine' => $ordine,
                    'date' => ''
                ]]);
            }

        } catch (Exception $e) {
            error_log('Error searching order details: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Errore durante la ricerca']);
        }

        exit;
    }

    /**
     * API: Ricerca dettagli articolo
     */
    public function searchArticoloDetails()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        $articolo = $_GET['articolo'] ?? '';

        if (empty($articolo)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Codice articolo mancante']);
            exit;
        }

        try {
            // Prima verifica se l'articolo esiste nei dati usando Eloquent
            $articleExists = CoreData::forArticle($articolo)->exists();

            if (!$articleExists) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Articolo ' . htmlspecialchars($articolo) . ' non trovato nei dati']);
                exit;
            }

            // Cerca i dettagli dell'articolo usando Eloquent
            $details = TrackSku::find($articolo);

            if ($details) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'data' => $details]);
            } else {
                // Restituisci un record vuoto per permettere l'inserimento
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'data' => [
                    'art' => $articolo,
                    'sku' => ''
                ]]);
            }

        } catch (Exception $e) {
            error_log('Error searching articolo details: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Errore durante la ricerca']);
        }

        exit;
    }

    /**
     * API: Ricerca dettagli lotto
     */
    public function searchLotDetails()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        $lot = $_GET['lot'] ?? '';

        if (empty($lot)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Numero lotto mancante']);
            exit;
        }

        try {
            // Prima verifica se il lotto esiste in track_links usando Eloquent
            $lotExists = TrackLink::forLot($lot)->exists();

            if (!$lotExists) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Lotto ' . htmlspecialchars($lot) . ' non trovato nei collegamenti']);
                exit;
            }

            // Cerca i dettagli del lotto usando Eloquent
            $details = TrackLotInfo::find($lot);

            if ($details) {
                // Formatta la data per l'input date HTML (Y-m-d)
                if (!empty($details['date']) && $details['date'] != '0000-00-00') {
                    $details['date'] = date('Y-m-d', strtotime($details['date']));
                } else {
                    $details['date'] = '';
                }
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'data' => $details]);
            } else {
                // Restituisci un record vuoto per permettere l'inserimento di nuovi dati
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'data' => [
                    'lot' => $lot,
                    'doc' => '',
                    'date' => '',
                    'note' => ''
                ]]);
            }

        } catch (Exception $e) {
            error_log('Error searching lot details: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Errore durante la ricerca']);
        }

        exit;
    }

    /**
     * API: Verifica esistenza cartellino
     */
    public function checkCartel()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Metodo non valido']);
            exit;
        }

        $commessa = $_POST['commessa'] ?? null;

        if (!$commessa) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Parametro commessa non ricevuto']);
            exit;
        }

        try {
            $results = CoreData::forCartel($commessa)
                ->select('Descrizione Articolo', 'Tot')
                ->get();

            if ($results->count() > 0) {
                $cartelDetails = [];
                foreach ($results as $row) {
                    $cartelDetails[] = [
                        'Descrizione Articolo' => $row->{'Descrizione Articolo'},
                        'Dati' => [
                            'Tot' => $row->Tot
                        ]
                    ];
                }

                header('Content-Type: application/json');
                echo json_encode([
                    'exists' => true,
                    'cartel' => $cartelDetails
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['exists' => false]);
            }

        } catch (Exception $e) {
            error_log('Error checking cartel: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }

        exit;
    }

    /**
     * API: Carica riepilogo cartellini
     */
    public function loadSummary()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Metodo non valido']);
            exit;
        }

        try {
            $commessas = json_decode($_POST['commessas'] ?? '[]', true);
            $data = [];

            foreach ($commessas as $commessa) {
                $results = CoreData::forCartel($commessa)
                    ->select('Articolo', 'Descrizione Articolo', 'Tot')
                    ->get();

                foreach ($results as $row) {
                    $data[] = $row;
                }
            }

            // Raggruppa per Articolo e calcola il totale per ogni articolo
            $groupedData = [];
            foreach ($data as $row) {
                $articolo = $row['Articolo'];
                if (!isset($groupedData[$articolo])) {
                    $groupedData[$articolo] = [
                        'Articolo' => $row['Articolo'],
                        'Descrizione Articolo' => $row['Descrizione Articolo'],
                        'Tot' => 0
                    ];
                }
                $groupedData[$articolo]['Tot'] += $row['Tot'];
            }

            // Calcola il totale generale
            $total = array_sum(array_column($data, 'Tot'));

            header('Content-Type: application/json');
            echo json_encode(['data' => array_values($groupedData), 'total' => $total]);

        } catch (Exception $e) {
            error_log('Error loading summary: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }

        exit;
    }

    /**
     * Report PDF per lotti (usa codice legacy)
     */
    public function generateReportLot()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['lotti']) || empty($input['lotti'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Nessun lotto specificato']);
            return;
        }

        $lotti = $input['lotti'];

        try {
            // Query Eloquent per report per lotto
            $results = TrackLink::with(['trackType', 'coreData'])
                ->whereIn('lot', $lotti)
                ->orderBy('cartel')
                ->get();

            // Raggruppa i risultati usando oggetti Eloquent
            $groupedResults = [];
            foreach ($results as $row) {
                $descrizioneArticolo = $row->coreData->{'Descrizione Articolo'};
                $typeName = $row->trackType->name;

                $groupedResults[$descrizioneArticolo][$typeName][$row->lot][] = [
                    'cartel' => $row->cartel,
                    'commessa' => $row->coreData->{'Commessa Cli'}
                ];
            }

            // Genera PDF usando TCPDF
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('WebGre System');
            $pdf->SetAuthor('Emmegiemme');
            $pdf->SetTitle('Packing List - Per Lotto');
            $pdf->SetSubject('Report');
            $pdf->SetKeywords('PDF');
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetHeaderMargin(0);
            $pdf->SetFooterMargin(0);
            $pdf->AddPage();
            $pdf->SetCellHeightRatio(1.5);
            
            $coloreSfondo = array(204, 228, 255);
            $coloreIntestazione = array(119, 119, 119);
            $coloreTesto = array(0, 0, 0);
            
            $pdf->SetFont('helvetica', '', 12);
            $pdf->SetFillColor($coloreSfondo[0], $coloreSfondo[1], $coloreSfondo[2]);
            $pdf->SetTextColor($coloreTesto[0], $coloreTesto[1], $coloreTesto[2]);
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, "PACKING LIST - Dettaglio per Lotto", 0, 1, 'L', true);
            $pdf->Ln(1);

            foreach ($groupedResults as $descrizioneArticolo => $tipi) {
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->Cell(0, 10, "$descrizioneArticolo", 0, 1, 'L', true);
                $pdf->Ln(1);
                
                foreach ($tipi as $type_name => $lotti_data) {
                    $pdf->SetFont('helvetica', '', 10);
                    $pdf->Cell(0, 10, "$type_name", 0, 1, 'L', true);
                    $pdf->Ln(1);
                    $colWidth = ($pdf->GetPageWidth() - 20) / 3;
                    $pdf->SetFont('helvetica', '', 8);
                    
                    foreach ($lotti_data as $lot => $details) {
                        $pdf->Cell($colWidth, 10, "Lotto", 0, 0, 'C', false);
                        $pdf->Cell($colWidth, 10, $lot, 0, 0, 'C', false);
                        $pdf->Cell($colWidth, 10, '', 0, 1, 'C', false);
                        
                        for ($i = 0; $i < count($details); $i += 3) {
                            $pdf->Cell($colWidth, 10, "{$details[$i]['cartel']} / {$details[$i]['commessa']}", 0, 0, 'C');
                            if ($i + 1 < count($details)) {
                                $pdf->Cell($colWidth, 10, "{$details[$i + 1]['cartel']} / {$details[$i + 1]['commessa']}", 0, 0, 'C');
                            } else {
                                $pdf->Cell($colWidth, 10, '', 0, 0, 'C');
                            }
                            if ($i + 2 < count($details)) {
                                $pdf->Cell($colWidth, 10, "{$details[$i + 2]['cartel']} / {$details[$i + 2]['commessa']}", 0, 1, 'C');
                            } else {
                                $pdf->Cell($colWidth, 10, '', 0, 1, 'C');
                            }
                        }
                        $pdf->Ln(4);
                    }
                    $pdf->Ln(4);
                }
                $pdf->Ln(6);
            }

            // Log dell'attività
            $this->logActivity(
                'TRACKING',
                'GENERATE_LOT_PDF',
                'Generato report PDF lotti',
                'Lotti: ' . implode(', ', $lotti)
            );

            // Output PDF
            $pdfContent = $pdf->Output('', 'S');
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="packing_list_lotti_' . date('Y-m-d') . '.pdf"');
            echo $pdfContent;
            exit;

        } catch (Exception $e) {
            error_log('Error generating lot PDF report: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Errore del server: ' . $e->getMessage()]);
        }
    }

    /**
     * Report PDF per cartellini (usa codice legacy)
     */
    public function generateReportCartel()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['cartellini']) || empty($input['cartellini'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Nessun cartellino specificato']);
            return;
        }

        $cartellini = $input['cartellini'];

        try {
            // Query Eloquent per report per cartellino
            $results = TrackLink::with(['trackType', 'coreData'])
                ->whereIn('cartel', $cartellini)
                ->orderBy('cartel')
                ->orderBy('id')
                ->get();

            // Raggruppa i risultati usando oggetti Eloquent
            $groupedResults = [];
            foreach ($results as $row) {
                $descrizioneArticolo = $row->coreData->{'Descrizione Articolo'};
                $commessaCli = $row->coreData->{'Commessa Cli'};
                $typeName = $row->trackType->name;

                $groupedResults[$descrizioneArticolo][$commessaCli][$row->cartel][$typeName][] = ['lot' => $row->lot];
            }

            // Genera PDF usando TCPDF
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('WebGre System');
            $pdf->SetAuthor('Emmegiemme');
            $pdf->SetTitle('Packing List - Per Cartellino');
            $pdf->SetSubject('Report');
            $pdf->SetKeywords('PDF');
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetHeaderMargin(0);
            $pdf->SetFooterMargin(0);
            $pdf->AddPage();
            $pdf->SetCellHeightRatio(0.5);
            $pdf->SetFont('helvetica', '', 15);
            $pdf->SetFillColor(204, 228, 255);
            $pdf->Cell(0, 10, "PACKING LIST - Dettaglio lotti di produzione per Cartellini", 0, 1, 'L', true);
            $pdf->SetFillColor(204, 228, 255);

            foreach ($groupedResults as $descrizioneArticolo => $commesse) {
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->Cell(0, 10, $descrizioneArticolo, 0, 1, 'L');
                $pdf->Ln(1);
                foreach ($commesse as $commessa => $cartellini_data) {
                    $pdf->SetFont('helvetica', '', 10);
                    foreach ($cartellini_data as $cartel => $types) {
                        $pdf->SetFillColor(240, 240, 240);
                        $pdf->Cell(0, 10, "Cartellino: $cartel / Commessa: $commessa", 0, 1, 'L', true);
                        $colWidth = 25;
                        $pdf->SetFont('helvetica', 'B', 8);
                        foreach ($types as $type_name => $lots) {
                            $pdf->Cell($colWidth, 5, $type_name, 0, 0, 'C', false);
                        }
                        $pdf->Ln();
                        $pdf->SetFont('helvetica', '', 8);
                        $maxRows = 0;
                        foreach ($types as $type_name => $lots) {
                            $rows = count($lots);
                            if ($rows > $maxRows) {
                                $maxRows = $rows;
                            }
                        }
                        for ($row = 0; $row < $maxRows; $row++) {
                            foreach ($types as $type_name => $lots) {
                                if (isset($lots[$row])) {
                                    $pdf->Cell($colWidth, 5, $lots[$row]['lot'], 0, 0, 'C');
                                } else {
                                    $pdf->Cell($colWidth, 5, '', 0, 0, 'C');
                                }
                            }
                            $pdf->Ln();
                        }
                        $pdf->Ln(2);
                    }
                    $pdf->Ln(4);
                }
                $pdf->Ln(6);
            }

            // Log dell'attività
            $this->logActivity(
                'TRACKING',
                'GENERATE_CARTEL_PDF',
                'Generato report PDF cartellini',
                'Cartellini: ' . implode(', ', $cartellini)
            );

            // Output PDF
            $pdfContent = $pdf->Output('', 'S');
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="packing_list_cartellini_' . date('Y-m-d') . '.pdf"');
            echo $pdfContent;
            exit;

        } catch (Exception $e) {
            error_log('Error generating cartel PDF report: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Errore del server: ' . $e->getMessage()]);
        }
    }

    /**
     * Report Excel per lotti (usa codice legacy)
     */
    public function generateExcelLot()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['lotti']) || empty($input['lotti'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Nessun lotto specificato']);
            return;
        }

        $lotti = $input['lotti'];

        try {
            // Codice dalla funzione legacy generateXlsxLot.php - convertito a Eloquent
            $results = TrackLink::with(['trackType', 'coreData.orderInfo', 'coreData.skuInfo'])
                ->whereIn('lot', $lotti)
                ->get()
                ->map(function($trackLink) {
                    $coreData = $trackLink->coreData;
                    return [
                        'Descrizione Articolo' => $coreData ? $coreData->{'Descrizione Articolo'} : null,
                        'Commessa Cli' => $coreData ? $coreData->{'Commessa Cli'} : null,
                        'Tot' => $coreData ? $coreData->Tot : null,
                        'cartel' => $trackLink->cartel,
                        'lot' => $trackLink->lot,
                        'type_name' => $trackLink->trackType ? $trackLink->trackType->name : null,
                        'data_inserimento' => $coreData && $coreData->orderInfo ? $coreData->orderInfo->date : null,
                        'codice_articolo' => $coreData && $coreData->skuInfo ? $coreData->skuInfo->sku : null
                    ];
                })
                ->sortBy(function($item) {
                    return $item['cartel'];
                })
                ->toArray();

            // Raggruppa i risultati per cartellino
            $groupedResults = [];
            foreach ($results as $row) {
                $cartel = $row['cartel'];
                if (!isset($groupedResults[$cartel])) {
                    $groupedResults[$cartel] = [
                        'data_inserimento' => $row['data_inserimento'],
                        'riferimento_originale' => $row['Commessa Cli'],
                        'codice_articolo' => $row['codice_articolo'],
                        'paia' => $row['Tot'],
                        'types' => []
                    ];
                }
                $groupedResults[$cartel]['types'][$row['type_name']][] = $row['lot'];
            }

            // Crea Excel usando PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Packing List Lotti');

            // Header
            $headers = ['Cartellino', 'Data Inserimento', 'Riferimento Originale', 'Codice Articolo', 'Paia'];
            $typeNames = [];
            
            // Trova tutti i type_name unici per creare le colonne
            foreach ($groupedResults as $cartel => $data) {
                foreach ($data['types'] as $typeName => $lots) {
                    if (!in_array($typeName, $typeNames)) {
                        $typeNames[] = $typeName;
                    }
                }
            }
            
            $headers = array_merge($headers, $typeNames);

            // Scrivi gli header
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValueByColumnAndRow($col, 1, $header);
                $col++;
            }

            // Scrivi i dati
            $row = 2;
            foreach ($groupedResults as $cartel => $data) {
                $sheet->setCellValue("A$row", $cartel);
                $sheet->setCellValue("B$row", $data['data_inserimento']);
                $sheet->setCellValue("C$row", $data['riferimento_originale']);
                $sheet->setCellValue("D$row", $data['codice_articolo']);
                $sheet->setCellValue("E$row", $data['paia']);

                $col = 6; // Inizia dopo le colonne fisse
                foreach ($typeNames as $typeName) {
                    if (isset($data['types'][$typeName])) {
                        $lots = implode(', ', $data['types'][$typeName]);
                        $sheet->setCellValueByColumnAndRow($col, $row, $lots);
                    }
                    $col++;
                }
                $row++;
            }

            // Stile header
            $headerRange = "A1:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . "1";
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            $sheet->getStyle($headerRange)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('CCCCCC');

            // Auto-size columns
            foreach (range(1, count($headers)) as $col) {
                $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
            }

            // Log dell'attività
            $this->logActivity(
                'TRACKING',
                'GENERATE_LOT_EXCEL',
                'Generato report Excel lotti',
                'Lotti: ' . implode(', ', $lotti)
            );

            // Output Excel
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="packing_list_lotti_' . date('Y-m-d') . '.xlsx"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (Exception $e) {
            error_log('Error generating lot Excel report: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Errore del server: ' . $e->getMessage()]);
        }
    }

    /**
     * Report Excel per cartellini (usa codice legacy)
     */
    public function generateExcelCartel()
    {
        $this->requireAuth();
        $this->requirePermission('tracking');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['cartellini']) || empty($input['cartellini'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Nessun cartellino specificato']);
            return;
        }

        $cartellini = $input['cartellini'];

        try {
            // Codice dalla funzione legacy generateXlsxCartel.php - convertito a Eloquent
            $results = TrackLink::with(['trackType', 'coreData.orderInfo', 'coreData.skuInfo'])
                ->distinct()
                ->whereIn('cartel', $cartellini)
                ->get()
                ->map(function($trackLink) {
                    $coreData = $trackLink->coreData;
                    return [
                        'id' => $trackLink->id,
                        'Descrizione Articolo' => $coreData ? $coreData->{'Descrizione Articolo'} : null,
                        'Commessa Cli' => $coreData ? $coreData->{'Commessa Cli'} : null,
                        'Tot' => $coreData ? $coreData->Tot : null,
                        'cartel' => $trackLink->cartel,
                        'lot' => $trackLink->lot,
                        'type_name' => $trackLink->trackType ? $trackLink->trackType->name : null,
                        'data_inserimento' => $coreData && $coreData->orderInfo ? $coreData->orderInfo->date : null,
                        'codice_articolo' => $coreData && $coreData->skuInfo ? $coreData->skuInfo->sku : null
                    ];
                })
                ->sortBy(function($item) {
                    return $item['cartel'];
                })
                ->toArray();

            // Raggruppa i risultati per cartellino
            $groupedResults = [];
            foreach ($results as $row) {
                $cartel = $row['cartel'];
                if (!isset($groupedResults[$cartel])) {
                    $groupedResults[$cartel] = [
                        'data_inserimento' => $row['data_inserimento'],
                        'riferimento_originale' => $row['Commessa Cli'],
                        'codice_articolo' => $row['codice_articolo'],
                        'paia' => $row['Tot'],
                        'types' => []
                    ];
                }
                $groupedResults[$cartel]['types'][$row['type_name']][] = $row['lot'];
            }

            // Crea Excel usando PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Packing List Cartellini');

            // Header
            $headers = ['Cartellino', 'Data Inserimento', 'Riferimento Originale', 'Codice Articolo', 'Paia'];
            $typeNames = [];
            
            // Trova tutti i type_name unici per creare le colonne
            foreach ($groupedResults as $cartel => $data) {
                foreach ($data['types'] as $typeName => $lots) {
                    if (!in_array($typeName, $typeNames)) {
                        $typeNames[] = $typeName;
                    }
                }
            }
            
            $headers = array_merge($headers, $typeNames);

            // Scrivi gli header
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValueByColumnAndRow($col, 1, $header);
                $col++;
            }

            // Scrivi i dati
            $row = 2;
            foreach ($groupedResults as $cartel => $data) {
                $sheet->setCellValue("A$row", $cartel);
                $sheet->setCellValue("B$row", $data['data_inserimento']);
                $sheet->setCellValue("C$row", $data['riferimento_originale']);
                $sheet->setCellValue("D$row", $data['codice_articolo']);
                $sheet->setCellValue("E$row", $data['paia']);

                $col = 6; // Inizia dopo le colonne fisse
                foreach ($typeNames as $typeName) {
                    if (isset($data['types'][$typeName])) {
                        $lots = implode(', ', $data['types'][$typeName]);
                        $sheet->setCellValueByColumnAndRow($col, $row, $lots);
                    }
                    $col++;
                }
                $row++;
            }

            // Stile header
            $headerRange = "A1:" . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . "1";
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            $sheet->getStyle($headerRange)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('CCCCCC');

            // Auto-size columns
            foreach (range(1, count($headers)) as $col) {
                $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
            }

            // Log dell'attività
            $this->logActivity(
                'TRACKING',
                'GENERATE_CARTEL_EXCEL',
                'Generato report Excel cartellini',
                'Cartellini: ' . implode(', ', $cartellini)
            );

            // Output Excel
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="packing_list_cartellini_' . date('Y-m-d') . '.xlsx"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (Exception $e) {
            error_log('Error generating cartel Excel report: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['message' => 'Errore del server: ' . $e->getMessage()]);
        }
    }

    /**
     * ========================================
     * METODI PRIVATI DI SUPPORTO
     * ========================================
     */

    /**
     * Statistiche dashboard
     */
    private function getTrackingStats()
    {
        $stats = [];

        try {
            // Total links usando Eloquent
            $stats['totalLinks'] = TrackLink::count();

            // Total lots usando Eloquent
            $stats['totalLots'] = TrackLink::distinct('lot')->count();

            // Total types usando Eloquent
            $stats['totalTypes'] = TrackType::count();

            // Recent activity (last 7 days) usando Eloquent
            $stats['recentActivity'] = TrackLink::recent(7)->count();

        } catch (Exception $e) {
            error_log('Error getting tracking stats: ' . $e->getMessage());
            $stats = ['totalLinks' => 0, 'totalLots' => 0, 'totalTypes' => 0, 'recentActivity' => 0];
        }

        return $stats;
    }

    /**
     * Statistiche per treeview
     */
    private function getTreeViewStats()
    {
        $stats = [];

        try {
            // Total links usando Eloquent
            $stats['totalLinks'] = TrackLink::count();

            // Total unique cartels usando Eloquent
            $stats['totalCartels'] = TrackLink::distinct('cartel')->count();

        } catch (Exception $e) {
            error_log('Error getting treeview stats: ' . $e->getMessage());
            $stats = ['totalLinks' => 0, 'totalCartels' => 0];
        }

        return $stats;
    }

    /**
     * Renderizza la vista ad albero
     */
    private function renderTreeView($results)
    {
        // Costruisce la struttura ad albero usando oggetti Eloquent
        $tree = [];
        foreach ($results as $result) {
            $tree[$result->cartel]['cartel'] = $result->cartel;
            $tree[$result->cartel]['Commessa Cli'] = $result->coreData->{'Commessa Cli'};
            $tree[$result->cartel]['Articolo'] = $result->coreData->Articolo;
            $tree[$result->cartel]['children'][$result->type_id]['type_name'] = $result->trackType->name;
            $tree[$result->cartel]['children'][$result->type_id]['lots'][] = [
                'id' => $result->id,
                'lot' => $result->lot,
                'timestamp' => $result->timestamp
            ];
        }

        // Genera HTML con stile dev-friendly
        $html = '<ul>';

        foreach ($tree as $cartellino) {
            $html .= '<li data-type="cartel" class="collapsed">';
            $html .= htmlspecialchars($cartellino['cartel']) . ' (' . htmlspecialchars($cartellino['Commessa Cli']) . ')';
            $html .= '<span class="timestamp">' . htmlspecialchars($cartellino['Articolo']) . '</span>';
            $html .= '<ul>';

            foreach ($cartellino['children'] as $typeId => $type) {
                $html .= '<li data-type="type" class="collapsed">';
                $html .= htmlspecialchars($type['type_name']) . ' (' . count($type['lots']) . ' lotti)';
                $html .= '<ul>';

                foreach ($type['lots'] as $lot) {
                    $html .= '<li data-type="lot" class="leaf" data-id="' . $lot['id'] . '">';
                    $html .= '<span>' . htmlspecialchars($lot['lot']) . '</span>';
                    $html .= '<div class="lot-actions">';
                    $html .= '<button class="edit-lot-btn" data-id="' . $lot['id'] . '" data-lot="' . htmlspecialchars($lot['lot']) . '"><i class="fas fa-pencil-alt"></i></button>';
                    $html .= '<button class="delete-lot-btn" data-id="' . $lot['id'] . '"><i class="fas fa-trash-alt"></i></button>';
                    $html .= '</div>';
                    $html .= '<span class="timestamp">' . htmlspecialchars($lot['timestamp']) . '</span>';
                    $html .= '</li>';
                }

                $html .= '</ul></li>';
            }

            $html .= '</ul></li>';
        }

        $html .= '</ul>';
        return $html;
    }

    /**
     * Renderizza i controlli di paginazione
     */
    private function renderPagination($currentPage, $totalPages, $totalResults, $searchQuery)
    {
        $html = '<div class="mt-6 border-t border-gray-200 pt-4">';

        // Informazioni risultati
        $html .= '<div class="flex items-center justify-between mb-4">';
        $html .= '<div class="text-sm text-gray-600">';
        $html .= 'Pagina ' . $currentPage . ' di ' . $totalPages . ' (' . $totalResults . ' risultati totali)';
        $html .= '</div>';
        $html .= '</div>';

        // Controlli paginazione
        $html .= '<div class="flex items-center justify-center space-x-2">';

        // Pulsante Previous
        if ($currentPage > 1) {
            $html .= '<button onclick="loadPage(' . ($currentPage - 1) . ')" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">';
            $html .= '<i class="fas fa-chevron-left mr-1"></i> Precedente';
            $html .= '</button>';
        } else {
            $html .= '<button disabled class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">';
            $html .= '<i class="fas fa-chevron-left mr-1"></i> Precedente';
            $html .= '</button>';
        }

        // Numeri pagina (mostra max 5 pagine)
        $start = max(1, $currentPage - 2);
        $end = min($totalPages, $start + 4);

        if ($end - $start < 4) {
            $start = max(1, $end - 4);
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $currentPage) {
                $html .= '<button class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-md">';
                $html .= $i;
                $html .= '</button>';
            } else {
                $html .= '<button onclick="loadPage(' . $i . ')" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">';
                $html .= $i;
                $html .= '</button>';
            }
        }

        // Pulsante Next
        if ($currentPage < $totalPages) {
            $html .= '<button onclick="loadPage(' . ($currentPage + 1) . ')" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">';
            $html .= 'Successiva <i class="fas fa-chevron-right ml-1"></i>';
            $html .= '</button>';
        } else {
            $html .= '<button disabled class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">';
            $html .= 'Successiva <i class="fas fa-chevron-right ml-1"></i>';
            $html .= '</button>';
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Ottiene lotti senza riferimenti completi
     */
    private function getLotsWithoutReferences()
    {
        try {
            $results = collect();

            // Prima parte: lotti in track_links che non hanno info complete in track_lots_info
            $lotsWithoutInfo = TrackLink::with('trackType')
                ->whereNotIn('lot', function($query) {
                    $query->select('lot')
                          ->from('track_lots_info')
                          ->whereNotNull('doc')
                          ->where('doc', '!=', '')
                          ->whereNotNull('date')
                          ->where('date', '!=', '0000-00-00');
                })
                ->get()
                ->map(function($link) {
                    return [
                        'lot' => $link->lot,
                        'type_name' => $link->trackType->name,
                        'doc' => null,
                        'date' => null
                    ];
                });

            // Seconda parte: lotti in track_lots_info con dati mancanti
            $lotsWithMissingData = TrackLotInfo::with(['trackLinks.trackType'])
                ->where(function($query) {
                    $query->whereNull('doc')
                          ->orWhere('doc', '')
                          ->orWhereNull('date')
                          ->orWhere('date', '0000-00-00');
                })
                ->get()
                ->map(function($lotInfo) {
                    return [
                        'lot' => $lotInfo->lot,
                        'type_name' => $lotInfo->trackLinks->first()?->trackType?->name,
                        'doc' => $lotInfo->doc,
                        'date' => $lotInfo->date
                    ];
                });

            return $results->merge($lotsWithoutInfo)->merge($lotsWithMissingData)->unique('lot')->values()->toArray();
        } catch (Exception $e) {
            error_log('Error getting lots without references: ' . $e->getMessage());
            return [];
        }
    }

    private function getOrdersWithoutDate()
    {

        try {
            $results = collect();

            // Prima parte: ordini in core_data che non hanno date in track_order_info
            $ordersWithoutDate = TrackLink::with('coreData')
                ->whereHas('coreData', function($query) {
                    $query->whereNotIn('Ordine', function($subQuery) {
                        $subQuery->select('ordine')
                                ->from('track_order_info')
                                ->whereNotNull('date');
                    });
                })
                ->get()
                ->map(function($link) {
                    return [
                        'Ordine' => $link->coreData->Ordine,
                        'date' => null
                    ];
                })
                ->unique('Ordine');

            // Seconda parte: ordini in track_order_info con date mancanti/invalide
            $ordersWithMissingDate = TrackOrderInfo::where(function($query) {
                    $query->whereNull('date')
                          ->orWhere('date', '')
                          ->orWhere('date', '0000-00-00');
                })
                ->get()
                ->map(function($orderInfo) {
                    return [
                        'Ordine' => $orderInfo->ordine,
                        'date' => $orderInfo->date
                    ];
                });

            return $results->merge($ordersWithoutDate)->merge($ordersWithMissingDate)->toArray();
        } catch (Exception $e) {
            error_log('Error getting orders without date: ' . $e->getMessage());
            return [];
        }
    }

    private function getArticoliWithoutSku()
    {

        try {
            $results = collect();

            // Prima parte: articoli in core_data che non hanno SKU in track_sku
            $articlesWithoutSku = TrackLink::with('coreData')
                ->whereHas('coreData', function($query) {
                    $query->whereNotIn('Articolo', function($subQuery) {
                        $subQuery->select('art')
                                ->from('track_sku')
                                ->whereNotNull('sku');
                    });
                })
                ->get()
                ->map(function($link) {
                    return [
                        'Articolo' => $link->coreData->Articolo,
                        'sku' => null
                    ];
                })
                ->unique('Articolo');

            // Seconda parte: articoli in track_sku con SKU mancanti/vuoti
            $articlesWithMissingSku = TrackSku::where(function($query) {
                    $query->whereNull('sku')
                          ->orWhere('sku', '');
                })
                ->get()
                ->map(function($skuInfo) {
                    return [
                        'Articolo' => $skuInfo->art,
                        'sku' => $skuInfo->sku
                    ];
                });

            return $results->merge($articlesWithoutSku)->merge($articlesWithMissingSku)->toArray();
        } catch (Exception $e) {
            error_log('Error getting articles without sku: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ottiene tutti gli ordini che hanno associazioni di tracking
     */
    private function getAllOrders()
    {
        try {
            return TrackOrderInfo::select('ordine as Ordine', 'date')
                ->whereNotNull('ordine')
                ->where('ordine', '!=', '')
                ->orderBy('ordine')
                ->get()
                ->toArray();
        } catch (Exception $e) {
            error_log('Error getting all tracked orders: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ottiene tutti gli articoli che hanno associazioni di tracking
     */
    private function getAllArticoli()
    {
        try {
            return TrackSku::select('art as Articolo', 'sku')
                ->whereNotNull('art')
                ->where('art', '!=', '')
                ->orderBy('art')
                ->get()
                ->toArray();
        } catch (Exception $e) {
            error_log('Error getting all tracked articoli: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ottiene tutti i lotti con dettagli
     */
    private function getAllLots()
    {
        try {
            return TrackLotInfo::with(['trackLinks.trackType'])
                ->get()
                ->flatMap(function($lotInfo) {
                    return $lotInfo->trackLinks->map(function($trackLink) use ($lotInfo) {
                        return [
                            'lot' => $trackLink->lot,
                            'type_name' => $trackLink->trackType ? $trackLink->trackType->name : null,
                            'doc' => $lotInfo->doc ?? null,
                            'date' => $lotInfo->date ?? null
                        ];
                    });
                })
                ->unique('lot')
                ->sortBy('lot')
                ->values()
                ->toArray();
        } catch (Exception $e) {
            error_log('Error getting all lots: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Salva riferimenti per lotti
     */
    private function saveReferences()
    {
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token CSRF non valido');
            $this->redirect($this->url('/tracking/lotdetail'));
        }

        try {
            if (isset($_POST['lots']) && is_array($_POST['lots'])) {
                foreach ($_POST['lots'] as $lot) {
                    $lotNumber = $lot['number'] ?? '';
                    $doc = $lot['doc'] ?? '';
                    $date = $lot['date'] ?? '';

                    if (empty($lotNumber))
                        continue;

                    // Usa updateOrCreate per gestire insert/update
                    TrackLotInfo::updateOrCreate(
                        ['lot' => $lotNumber],
                        [
                            'doc' => $doc,
                            'date' => $date
                        ]
                    );
                }
            }
            $this->logActivity('TRACKING', 'SAVE_REFERENCES', 'Riferimenti lotti salvati');
            $this->setFlash('success', 'Riferimenti salvati con successo');

        } catch (Exception $e) {
            error_log('Error saving references: ' . $e->getMessage());
            $this->setFlash('error', 'Errore durante il salvataggio dei riferimenti');
        }

        $this->redirect($this->url('/tracking/lotdetail'));
    }

    /**
     * Aggiorna dettagli di un lotto specifico
     */
    private function updateLotDetails()
    {
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token CSRF non valido');
            $this->redirect($this->url('/tracking/lotdetail'));
        }

        $lot = $_POST['lot'] ?? '';
        $doc = $_POST['doc'] ?? '';
        $date = $_POST['date'] ?? '';
        $note = $_POST['note'] ?? '';

        if (empty($lot)) {
            $this->setFlash('error', 'Numero lotto mancante');
            $this->redirect($this->url('/tracking/lotdetail'));
        }

        try {
            // Usa updateOrCreate per gestire insert/update
            TrackLotInfo::updateOrCreate(
                ['lot' => $lot],
                [
                    'doc' => $doc,
                    'date' => $date,
                    'note' => $note
                ]
            );
            $this->logActivity('TRACKING', 'UPDATE_LOT_DETAILS', "Dettagli lotto aggiornati: $lot");
            $this->setFlash('success', 'Dettagli lotto aggiornati con successo');

        } catch (Exception $e) {
            error_log('Error updating lot details: ' . $e->getMessage());
            $this->setFlash('error', 'Errore durante l\'aggiornamento del lotto');
        }

        $this->redirect($this->url('/tracking/lotdetail'));
    }

    /**
     * Salva date ordini
     */
    private function saveOrderDates()
    {
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token CSRF non valido');
            $this->redirect($this->url('/tracking/lotdetail'));
        }

        try {
            if (isset($_POST['orders']) && is_array($_POST['orders'])) {
                foreach ($_POST['orders'] as $order) {
                    $ordineNumber = $order['ordine'] ?? '';
                    $date = $order['date'] ?? '';

                    if (empty($ordineNumber))
                        continue;

                    // Usa updateOrCreate per gestire insert/update
                    TrackOrderInfo::updateOrCreate(
                        ['ordine' => $ordineNumber],
                        ['date' => $date]
                    );
                }
            }
            $this->logActivity('TRACKING', 'SAVE_ORDER_DATES', 'Date ordini salvate');
            $this->setFlash('success', 'Date ordini salvate con successo');

        } catch (Exception $e) {
            error_log('Error saving order dates: ' . $e->getMessage());
            $this->setFlash('error', 'Errore durante il salvataggio delle date ordini');
        }

        $this->redirect($this->url('/tracking/lotdetail'));
    }

    /**
     * Aggiorna dettagli ordine
     */
    private function updateOrderDetails()
    {
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token CSRF non valido');
            $this->redirect($this->url('/tracking/lotdetail'));
        }

        $ordine = $_POST['ordine'] ?? '';
        $date = $_POST['date'] ?? '';

        if (empty($ordine)) {
            $this->setFlash('error', 'Numero ordine mancante');
            $this->redirect($this->url('/tracking/lotdetail'));
        }

        try {
            // Usa updateOrCreate per gestire insert/update
            TrackOrderInfo::updateOrCreate(
                ['ordine' => $ordine],
                ['date' => $date]
            );
            $this->logActivity('TRACKING', 'UPDATE_ORDER_DETAILS', "Dettagli ordine aggiornati: $ordine");
            $this->setFlash('success', 'Dettagli ordine aggiornati con successo');

        } catch (Exception $e) {
            error_log('Error updating order details: ' . $e->getMessage());
            $this->setFlash('error', 'Errore durante l\'aggiornamento dell\'ordine');
        }

        $this->redirect($this->url('/tracking/lotdetail'));
    }

    /**
     * Salva codici SKU
     */
    private function saveSkuCodes()
    {
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token CSRF non valido');
            $this->redirect($this->url('/tracking/lotdetail'));
        }

        try {
            if (isset($_POST['arts']) && is_array($_POST['arts'])) {
                foreach ($_POST['arts'] as $art) {
                    $articolo = $art['articolo'] ?? '';
                    $sku = $art['sku'] ?? '';

                    if (empty($articolo))
                        continue;

                    // Usa updateOrCreate per gestire insert/update
                    TrackSku::updateOrCreate(
                        ['art' => $articolo],
                        ['sku' => $sku]
                    );
                }
            }
            $this->logActivity('TRACKING', 'SAVE_SKU_CODES', 'Codici SKU salvati');
            $this->setFlash('success', 'Codici SKU salvati con successo');

        } catch (Exception $e) {
            error_log('Error saving SKU codes: ' . $e->getMessage());
            $this->setFlash('error', 'Errore durante il salvataggio dei codici SKU');
        }

        $this->redirect($this->url('/tracking/lotdetail'));
    }

    /**
     * Aggiorna dettagli articolo
     */
    private function updateArticoloDetails()
    {
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->setFlash('error', 'Token CSRF non valido');
            $this->redirect($this->url('/tracking/lotdetail'));
        }

        $articolo = $_POST['articolo'] ?? '';
        $sku = $_POST['sku'] ?? '';

        if (empty($articolo)) {
            $this->setFlash('error', 'Codice articolo mancante');
            $this->redirect($this->url('/tracking/lotdetail'));
        }

        try {
            // Usa updateOrCreate per gestire insert/update
            TrackSku::updateOrCreate(
                ['art' => $articolo],
                ['sku' => $sku]
            );
            $this->logActivity('TRACKING', 'UPDATE_ARTICOLO_DETAILS', "Dettagli articolo aggiornati: $articolo");
            $this->setFlash('success', 'Dettagli articolo aggiornati con successo');

        } catch (Exception $e) {
            error_log('Error updating articolo details: ' . $e->getMessage());
            $this->setFlash('error', 'Errore durante l\'aggiornamento dell\'articolo');
        }

        $this->redirect($this->url('/tracking/lotdetail'));
    }

    /**
     * Genera PDF report lotti (mantiene codice legacy)
     */
    private function generateLotPdfReport($lotti)
    {
        // Include TCPDF
        

        // Convertito a Eloquent per ottenere i dati per il PDF
        $results = TrackLink::with(['trackType', 'coreData'])
            ->whereIn('lot', $lotti)
            ->get()
            ->map(function($trackLink) {
                $coreData = $trackLink->coreData;
                return [
                    'Descrizione Articolo' => $coreData ? $coreData->{'Descrizione Articolo'} : '',
                    'Commessa Cli' => $coreData ? $coreData->{'Commessa Cli'} : '',
                    'cartel' => $trackLink->cartel,
                    'type_name' => $trackLink->trackType ? $trackLink->trackType->name : '',
                    'lot' => $trackLink->lot
                ];
            })
            ->sortBy('cartel')
            ->toArray();

        // Raggruppa risultati come nel legacy
        $groupedResults = [];
        foreach ($results as $row) {
            $groupedResults[$row['Descrizione Articolo']][$row['type_name']][$row['lot']][] = [
                'cartel' => $row['cartel'],
                'commessa' => $row['Commessa Cli']
            ];
        }

        // Crea PDF con stessa formattazione del legacy
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Emmegiemme');
        $pdf->SetTitle('Packing List - Per Lotto');
        $pdf->SetSubject('Report');
        $pdf->SetKeywords('PDF');
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);
        $pdf->AddPage();
        $pdf->SetCellHeightRatio(1.5);

        $coloreSfondo = array(204, 228, 255);
        $coloreIntestazione = array(119, 119, 119);
        $coloreTesto = array(0, 0, 0);

        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetFillColor($coloreSfondo[0], $coloreSfondo[1], $coloreSfondo[2]);
        $pdf->SetTextColor($coloreTesto[0], $coloreTesto[1], $coloreTesto[2]);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, "PACKING LIST - Dettaglio per Lotto", 0, 1, 'L', true);
        $pdf->Ln(1);

        foreach ($groupedResults as $descrizioneArticolo => $tipi) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(0, 10, "$descrizioneArticolo", 0, 1, 'L', true);
            $pdf->Ln(1);

            foreach ($tipi as $type_name => $lotti) {
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell(0, 10, "$type_name", 0, 1, 'L', true);
                $pdf->Ln(1);
                $colWidth = ($pdf->GetPageWidth() - 20) / 3;
                $pdf->SetFont('helvetica', '', 8);

                foreach ($lotti as $lot => $details) {
                    $pdf->Cell($colWidth, 10, "Lotto", 0, 0, 'C', false);
                    $pdf->Cell($colWidth, 10, $lot, 0, 0, 'C', false);
                    $pdf->Cell($colWidth, 10, '', 0, 1, 'C', false);

                    for ($i = 0; $i < count($details); $i += 3) {
                        $pdf->Cell($colWidth, 10, "{$details[$i]['cartel']} / {$details[$i]['commessa']}", 0, 0, 'C');

                        if ($i + 1 < count($details)) {
                            $pdf->Cell($colWidth, 10, "{$details[$i + 1]['cartel']} / {$details[$i + 1]['commessa']}", 0, 0, 'C');
                        } else {
                            $pdf->Cell($colWidth, 10, '', 0, 0, 'C');
                        }

                        if ($i + 2 < count($details)) {
                            $pdf->Cell($colWidth, 10, "{$details[$i + 2]['cartel']} / {$details[$i + 2]['commessa']}", 0, 1, 'C');
                        } else {
                            $pdf->Cell($colWidth, 10, '', 0, 1, 'C');
                        }
                    }
                    $pdf->Ln(4);
                }
            }
            $pdf->Ln(4);
        }

        // Output PDF
        $pdf->Output("packing_list_lotti_" . date('Y-m-d') . ".pdf", 'D');
        exit;
    }

    /**
     * Genera Excel report lotti (mantiene codice legacy)
     */
    private function generateLotExcelReport($lotti)
    {
        // Include il codice originale da generateXlsxLot.php
        // mantenendo la stessa logica e formattazione

        // TODO: Implementare generazione Excel completa
        // usando lo stesso codice del file legacy
    }

    /**
     * Scripts per pagina index
     */
    private function getIndexScripts()
    {
        return "
        // Inizializzazione dashboard tracking
        function initTrackingIndex() {
            // TODO: Implementare JavaScript dashboard
        }
        
        // Registra per PJAX
        if (window.WEBGRE && window.WEBGRE.onPageLoad) {
            window.WEBGRE.onPageLoad(initTrackingIndex);
        }
        ";
    }

    /**
     * Scripts per multi search
     */
    private function getMultiSearchScripts()
    {
        return "
        // TODO: Migrare JavaScript da multiSearch.php
        function initMultiSearch() {
            // Implementazione ricerca multipla
        }
        
        if (window.WEBGRE && window.WEBGRE.onPageLoad) {
            window.WEBGRE.onPageLoad(initMultiSearch);
        }
        ";
    }

    /**
     * Scripts per order search
     */
    private function getOrderSearchScripts()
    {
        return "
        function addField() {
            var container = document.getElementById('commessa-fields');
            var inputField = document.createElement('div');
            inputField.className = 'input-item';
            inputField.innerHTML = '<input type=\"text\" class=\"commessa-input border-solid w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 text-sm\" placeholder=\"\" onchange=\"verifyCommessa(this)\">';
            container.appendChild(inputField);
            showUpdateMessage();
            checkAvantiButton();
        }

        function verifyCommessa(input) {
            var commessa = input.value;
            if (commessa.length === 0) {
                input.style.borderColor = '';
                checkLoadSummaryButton();
                checkAvantiButton();
                hideErrorMessage();
                showUpdateMessage();
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '" . $this->url('/tracking/check-cartel') . "', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.exists) {
                            input.style.borderColor = 'green';
                            input.classList.remove('border-red-500');
                           input.classList.add('border');
                            input.classList.add('border-green-500');
                        } else {
                            input.style.borderColor = 'red';
                            input.classList.remove('border-green-500');
                           input.classList.add('border');
                            input.classList.add('border-red-500');
                        }
                    } else {
                        console.error('Errore nella verifica della commessa');
                        input.style.borderColor = 'red';
                        input.classList.remove('border-green-500');
                        input.classList.add('border-red-500');
                    }
                    checkLoadSummaryButton();
                    checkAvantiButton();
                    hideErrorMessage();
                    showUpdateMessage();
                }
            };
            xhr.send('commessa=' + encodeURIComponent(commessa));
        }

        function checkLoadSummaryButton() {
            var inputs = document.querySelectorAll('.commessa-input');
            var isValid = false;
            var hasInvalidInput = false;

            inputs.forEach(function (input) {
                if (input.value.trim().length > 0 && (input.style.borderColor === 'green' || input.classList.contains('border-green-500'))) {
                    isValid = true;
                }
                if (input.value.trim().length > 0 && (input.style.borderColor === 'red' || input.classList.contains('border-red-500'))) {
                    hasInvalidInput = true;
                }
            });

            var loadSummaryBtn = document.getElementById('loadSummaryBtn');
            loadSummaryBtn.disabled = !isValid;

            var errorMessage = document.getElementById('error-message');
            var updateMessage = document.getElementById('update-message');

            if (hasInvalidInput && !isValid) {
                errorMessage.classList.remove('hidden');
                updateMessage.classList.add('hidden');
            } else {
                errorMessage.classList.add('hidden');
                updateMessage.classList.remove('hidden');
            }
        }

        function checkAvantiButton() {
            var inputs = document.querySelectorAll('.commessa-input');
            var isValid = false;

            inputs.forEach(function (input) {
                if (input.value.trim().length > 0 && (input.style.borderColor === 'green' || input.classList.contains('border-green-500'))) {
                    isValid = true;
                }
            });

            var avantiBtn = document.getElementById('avantiBtn');
            avantiBtn.disabled = !isValid;
        }

        function loadSummary() {
            var commessas = [];
            var inputs = document.querySelectorAll('.commessa-input');
            var invalidInput = false;

            inputs.forEach(function (input) {
                if (input.value.trim().length > 0) {
                    if (input.style.borderColor === 'red' || input.classList.contains('border-red-500')) {
                        invalidInput = true;
                    } else if (input.style.borderColor === 'green' || input.classList.contains('border-green-500')) {
                        commessas.push(input.value.trim());
                    }
                }
            });

            if (invalidInput) {
                WebgreModals.alert('Errore', 'Uno o più campi non sono validi. Correggi i campi evidenziati in rosso.');
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '" . $this->url('/tracking/load-summary') . "', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        updateSummaryList(response.data, response.total);
                    } else {
                        console.error('Errore nel caricamento del riepilogo');
                    }
                }
            };
            xhr.send('commessas=' + encodeURIComponent(JSON.stringify(commessas)));
        }

        function updateSummaryList(data, total) {
            var summaryList = document.getElementById('summary-list');
            var summaryTotal = document.getElementById('summary-total');
            summaryList.innerHTML = '';
            summaryTotal.innerHTML = '';

            data.forEach(function (cartel) {
                var summaryItem = document.createElement('div');
                summaryItem.className = 'bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600';
                summaryItem.innerHTML = 
                    '<h6 class=\"font-bold text-gray-900 dark:text-white mb-2\">' + cartel['Articolo'] + '</h6>' +
                    '<p class=\"text-sm text-gray-600 dark:text-gray-400 mb-2\">' + cartel['Descrizione Articolo'] + '</p>' +
                    '<p class=\"text-sm font-medium text-orange-600 dark:text-orange-400\">PA: ' + cartel['Tot'] + '</p>';
                summaryList.appendChild(summaryItem);
            });

            if (total > 0) {
                summaryTotal.innerHTML = '<div class=\"text-lg font-bold text-gray-900 dark:text-white\">Totale: ' + total + '</div>';
            }

            hideUpdateMessage();
        }

        function showUpdateMessage() {
            var updateMessage = document.getElementById('update-message');
            updateMessage.classList.remove('hidden');
        }

        function hideUpdateMessage() {
            var updateMessage = document.getElementById('update-message');
            updateMessage.classList.add('hidden');
        }

        function showErrorMessage() {
            var errorMessage = document.getElementById('error-message');
            errorMessage.classList.remove('hidden');
        }

        function hideErrorMessage() {
            var errorMessage = document.getElementById('error-message');
            errorMessage.classList.add('hidden');
        }

        function inviaDati() {
            var commessas = [];
            var inputs = document.querySelectorAll('.commessa-input');
            
            inputs.forEach(function (input) {
                var commessa = input.value.trim();
                if (commessa.length > 0 && (input.style.borderColor === 'green' || input.classList.contains('border-green-500'))) {
                    commessas.push(commessa);
                }
            });

            if (commessas.length === 0) {
                WebgreModals.alert('Errore', 'Nessun cartellino valido da inviare.');
                return;
            }

            document.getElementById('selectedCartelsInput').value = JSON.stringify(commessas);
            document.getElementById('invioForm').submit();
        }

        // Rendi funzioni globali
        window.addField = addField;
        window.verifyCommessa = verifyCommessa;
        window.loadSummary = loadSummary;
        window.inviaDati = inviaDati;

        if (window.WEBGRE && window.WEBGRE.onPageLoad) {
            window.WEBGRE.onPageLoad(function() {
                // Inizializzazione completata
            });
        }
        ";
    }

    /**
     * Scripts per tree view
     */
    private function getTreeViewScripts()
    {
        return "
        var currentSearchQuery = '';

        function loadTreeData(searchQuery, page = 1) {
            if (searchQuery.trim() === '') {
                searchQuery = '*';
            }
            currentSearchQuery = searchQuery;
            var loader = document.getElementById('loader');
            var placeholder = document.getElementById('treeViewPlaceholder');
            loader.classList.remove('hidden');
            var url = '" . $this->url('/tracking/get-tree-data') . "?search_query=' + encodeURIComponent(searchQuery) + '&page=' + page;
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    placeholder.innerHTML = html;
                    initializeTreeView();
                    attachEventHandlers();
                    loader.classList.add('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    placeholder.innerHTML = '<div class=\"bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mt-4\">Errore durante la ricerca.</div>';
                    loader.classList.add('hidden');
                });
        }

        function loadPage(page) {
            loadTreeData(currentSearchQuery, page);
        }

        document.getElementById('searchForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var searchQuery = document.querySelector('input[name=\"search_query\"]').value;
            loadTreeData(searchQuery, 1);
        });

        function initializeTreeView() {
            var subTrees = document.querySelectorAll('#treeViewPlaceholder ul ul');
            subTrees.forEach(function(tree) {
                tree.style.display = 'none';
            });
            var parentNodes = document.querySelectorAll('#treeViewPlaceholder li');
            parentNodes.forEach(function(node) {
                var childUl = node.querySelector('ul');
                if (childUl) {
                    node.classList.add('collapsed');
                    node.addEventListener('click', function(event) {
                        if (event.target.closest('.lot-actions')) {
                            return;
                        }
                        event.stopPropagation();
                        if (!node.classList.contains('leaf')) {
                            node.classList.toggle('collapsed');
                            node.classList.toggle('expanded');
                            var childUl = node.querySelector('ul');
                            if (childUl) {
                                childUl.style.display = childUl.style.display === 'none' ? 'block' : 'none';
                            }
                        }
                    });
                } else {
                    node.classList.add('leaf');
                }
            });
        }

        function attachEventHandlers() {
            const placeholder = document.getElementById('treeViewPlaceholder');
            if (!placeholder) return;

            placeholder.addEventListener('click', function(event) {
                const deleteBtn = event.target.closest('.delete-lot-btn');
                if (deleteBtn) {
                    const id = deleteBtn.dataset.id;
                    WebgreModals.confirmDelete('Sei sicuro di voler eliminare questa associazione?', function() {
                        fetch('" . $this->url('/tracking/delete-lot') . "', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-CSRF-TOKEN': '" . $this->generateCsrfToken() . "'
                            },
                            body: 'id=' + id
                        })
                        .then(response => response.text())
                        .then(text => {
                            deleteBtn.closest('li[data-type=\"lot\"]').remove();
                        });
                    });
                }

                const editBtn = event.target.closest('.edit-lot-btn');
                if (editBtn) {
                    const id = editBtn.dataset.id;
                    const lot = editBtn.dataset.lot;
                    openEditModal(id, lot);
                }
            });

            document.getElementById('saveLotBtn').addEventListener('click', function() {
                const id = document.getElementById('editLotId').value;
                const newLot = document.getElementById('editLotInput').value;

                fetch('" . $this->url('/tracking/update-lot') . "', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': '" . $this->generateCsrfToken() . "'
                    },
                    body: 'id=' + id + '&lot=' + newLot
                })
                .then(response => response.text())
                .then(text => {
                    const lotLi = document.querySelector(`li[data-id='\${id}'] > span`);
                    if(lotLi) {
                        lotLi.textContent = newLot;
                    }
                    closeEditModal();
                });
            });

            document.getElementById('cancelEditLotBtn').addEventListener('click', closeEditModal);
        }

        function openEditModal(id, lot) {
            const modal = document.getElementById('editLotModal');
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            document.getElementById('editLotId').value = id;
            document.getElementById('editLotInput').value = lot;
            modal.classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editLotModal').classList.add('hidden');
        }
        
        if (window.WEBGRE && window.WEBGRE.onPageLoad) {
            window.WEBGRE.onPageLoad(function() {
                // Inizializzazione completata
            });
        }
        ";
    }

    /**
     * Scripts per lot manager
     */
    private function getLotManagerScripts()
    {
        return "
        // Form ricerca lotto
        document.getElementById('searchLotForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            var searchLot = document.getElementById('search_lot').value.trim();
            if (!searchLot) {
                WebgreModals.alert('Errore', 'Inserisci un numero di lotto');
                return;
            }
            
            // Fetch dettagli lotto
            fetch('" . $this->url('/tracking/search-lot-details') . "?lot=' + encodeURIComponent(searchLot))
                .then(response => response.json())
                .then(data => {
                    var container = document.getElementById('lotDetailsContainer');
                    var noResults = document.getElementById('noResultsMessage');
                    
                    if (data.success && data.data) {
                        // Popola il form con i dati
                        document.getElementById('lot_number').value = data.data.lot || '';
                        document.getElementById('lot_doc').value = data.data.doc || '';
                        document.getElementById('lot_date').value = data.data.date || '';
                        document.getElementById('lot_note').value = data.data.note || '';
                        
                        container.style.display = 'block';
                        noResults.classList.add('hidden');
                    } else {
                        // Mostra messaggio di errore
                        document.getElementById('noResultsText').textContent = data.error || 'Nessun risultato trovato';
                        noResults.classList.remove('hidden');
                        container.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    var noResults = document.getElementById('noResultsMessage');
                    document.getElementById('noResultsText').textContent = 'Errore durante la ricerca';
                    noResults.classList.remove('hidden');
                    document.getElementById('lotDetailsContainer').style.display = 'none';
                });
        });
        
        // Modal tutti i lotti
        function showAllLotsModal() {
            const modal = document.getElementById('allLotsModal-new');
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            modal.classList.remove('hidden');
        }
        
        function hideAllLotsModal() {
            document.getElementById('allLotsModal-new').classList.add('hidden');
        }
        
        // Modal tutti gli ordini
        function showAllOrdersModal() {
            const modal = document.getElementById('allOrdersModal-new');
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            modal.classList.remove('hidden');
        }
        
        function hideAllOrdersModal() {
            document.getElementById('allOrdersModal-new').classList.add('hidden');
        }
        
        // Modal tutti gli articoli
        function showAllArticoliModal() {
            const modal = document.getElementById('allArticoliModal-new');
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            modal.classList.remove('hidden');
        }
        
        function hideAllArticoliModal() {
            document.getElementById('allArticoliModal-new').classList.add('hidden');
        }
        
        // Rendi funzioni globali
        window.showAllLotsModal = showAllLotsModal;
        window.hideAllLotsModal = hideAllLotsModal;
        window.showAllOrdersModal = showAllOrdersModal;
        window.hideAllOrdersModal = hideAllOrdersModal;
        window.showAllArticoliModal = showAllArticoliModal;
        window.hideAllArticoliModal = hideAllArticoliModal;
        
        // Chiudi modal cliccando fuori
        document.getElementById('allLotsModal-new').addEventListener('click', function(e) {
            if (e.target === this) {
                hideAllLotsModal();
            }
        });
        
        document.getElementById('allOrdersModal-new').addEventListener('click', function(e) {
            if (e.target === this) {
                hideAllOrdersModal();
            }
        });
        
        document.getElementById('allArticoliModal-new').addEventListener('click', function(e) {
            if (e.target === this) {
                hideAllArticoliModal();
            }
        });
        
        // Form salvataggio riferimenti con conferma
        document.getElementById('saveReferencesForm').addEventListener('submit', function(event) {
            var emptyFields = [];
            var inputs = this.querySelectorAll('input[type=\"text\"], input[type=\"date\"]');
            
            inputs.forEach(function(input) {
                if (!input.value.trim()) {
                    emptyFields.push(input);
                }
            });
            
            if (emptyFields.length > 0) {
                event.preventDefault();
                var message = 'Alcuni campi sono vuoti. Continuare comunque?';
                WebgreModals.confirm({
                    title: 'Campi vuoti',
                    message: message,
                    confirmText: 'Continua',
                    cancelText: 'Annulla',
                    type: 'warning',
                    onConfirm: function() {
                        event.target.submit();
                    }
                });
            }
        });
        
        // Form aggiornamento dettagli con conferma
        document.getElementById('updateLotForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            var lotNumber = document.getElementById('lot_number').value;
            var message = 'Confermi di voler aggiornare i dettagli del lotto ' + lotNumber + '?';
            
            WebgreModals.confirm({
                title: 'Conferma aggiornamento',
                message: message,
                confirmText: 'Aggiorna',
                cancelText: 'Annulla',
                type: 'info',
                onConfirm: function() {
                    event.target.submit();
                }
            });
        });
        
        if (window.WEBGRE && window.WEBGRE.onPageLoad) {
            window.WEBGRE.onPageLoad(function() {
                // Inizializzazione completata
            });
        }
        ";
    }

    /**
     * Scripts per packing list
     */
    private function getPackingListScripts()
    {
        return "
        // TODO: Migrare JavaScript da makePackingList.php
        function initPackingList() {
            // Implementazione packing list
        }
        
        if (window.WEBGRE && window.WEBGRE.onPageLoad) {
            window.WEBGRE.onPageLoad(initPackingList);
        }
        ";
    }

    /**
     * Scripts per make fiches
     */
    private function getMakeFichesScripts()
    {
        return "
        function initMakeFiches() {
            // Implementazione make fiches
        }
        
        if (window.WEBGRE && window.WEBGRE.onPageLoad) {
            window.WEBGRE.onPageLoad(initMakeFiches);
        }
        ";
    }

    /**
     * Statistiche per Packing List
     */
    private function getPackingListStats()
    {
        $stats = [];

        try {
            // Totale cartellini tracciati
            $stats['totalCartels'] = TrackLink::distinct('cartel')->count('cartel');

            // Totale lotti
            $stats['totalLots'] = TrackLink::distinct('lot')->count('lot');

            // Tipi di tracking attivi
            $stats['totalTypes'] = TrackType::count();

            // Report generati nell'ultimo mese - convertito a Eloquent
            $stats['recentReports'] = ActivityLog::recentReports(30)->count();

        } catch (Exception $e) {
            error_log('Error getting packing list stats: ' . $e->getMessage());
            $stats = ['totalCartels' => 0, 'totalLots' => 0, 'totalTypes' => 0, 'recentReports' => 0];
        }

        return $stats;
    }

    /**
     * Scripts per process links
     */
    private function getProcessLinksScripts($selectedCartels)
    {
        $cartelsJson = json_encode($selectedCartels);
        return "
        function toggleLotInput() {
            var typeSelect = document.getElementById('type_id');
            var lotInput = document.getElementById('lotNumbers');
            lotInput.disabled = !typeSelect.value;
        }
        
        var isSubmitting = false; // Previene doppi submit
        
        document.getElementById('associazioneForm').addEventListener('submit', function (event) {
            event.preventDefault();
            
            if (isSubmitting) {
                return; // Blocca se già in submit
            }

            var type_id = document.getElementById('type_id').value;
            var lotNumbers = document.getElementById('lotNumbers').value.trim();
            var cartelli = $cartelsJson;

            if (!type_id) {
                WebgreModals.alert('Errore', 'Seleziona un tipo di tracking');
                return;
            }
            
            if (!lotNumbers) {
                WebgreModals.alert('Errore', 'Inserisci almeno un numero di lotto');
                return;
            }

            // Mostra conferma
            var confirmMessage = 'Confermi di voler associare ' + cartelli.length + ' cartellini a ' + lotNumbers.split('\\n').length + ' lotti?';
            
            WebgreModals.confirm({
                title: 'Conferma Associazione',
                message: confirmMessage,
                confirmText: 'Associa',
                cancelText: 'Annulla',
                type: 'info',
                onConfirm: function() {
                    processAssociation();
                }
            });
            return;
            
            function processAssociation() {
            
            isSubmitting = true;
            var submitBtn = document.querySelector('button[type=\"submit\"]');
            var originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class=\"fas fa-spinner fa-spin mr-2\"></i>Salvando...';

            // Trasformare i lotNumbers in un array dividendo per le righe
            lotNumbers = lotNumbers.split('\\n').map(function (line) { return line.trim(); }).filter(function(line) { return line.length > 0; });

            // Invio tramite AJAX per l'inserimento dei dati
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '" . $this->url('/tracking/save-links') . "', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    isSubmitting = false;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);

                        if (response.success) {
                            WebgreModals.alert('Successo', 'Associazione salvata con successo!', function() {
                                if (window.WEBGRE && window.WEBGRE.navigateTo) {
                                    window.WEBGRE.navigateTo('" . $this->url('/tracking') . "');
                                } else {
                                    window.location.href = '" . $this->url('/tracking') . "';
                                }
                            });
                        } else {
                            var errorMsg = 'Errore durante il salvataggio: ' + (response.error || 'Errore sconosciuto');
                            WebgreModals.alert('Errore', errorMsg);
                        }
                    } else {
                        WebgreModals.alert('Errore', 'Errore durante il salvataggio');
                    }
                }
            };

            var data = {
                type_id: type_id,
                lotNumbers: lotNumbers,
                cartelli: cartelli
            };

            xhr.send(JSON.stringify(data));
            } // Fine processAssociation
        });
        
        if (window.WEBGRE && window.WEBGRE.onPageLoad) {
            window.WEBGRE.onPageLoad(function() {
                // Inizializzazione pagina
            });
        }
        ";
    }
}