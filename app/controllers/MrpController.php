<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\MrpArrival;
use App\Models\MrpMaterial;
use App\Models\MrpCategory;
use App\Models\MrpOrder;
use App\Models\ActivityLog;
use App\Models\MrpRequirement;
/**
 * MRP Controller - Material Requirements Planning
 * Gestisce il sistema MRP per la pianificazione dei fabbisogni materiali
 * Funzionalità: Import Excel, Gestione Ordini, Gestione Arrivi, Dashboard situazione
 */
class MrpController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
       $this->requirePermission('mrp');
    }

    /**
     * Dashboard principale MRP
     */
    public function index()
    {

        try {
            $userId = $_SESSION['user_id'];

            // Statistiche generali - Convertito a Eloquent
            $stats = [
                'total_materials' => MrpMaterial::where('user_id', $userId)->count(),
                'materials_with_sizes' => MrpMaterial::where('user_id', $userId)->where('has_sizes', 1)->count(),
                'total_orders' => MrpOrder::whereHas('material', function($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->count(),
                'total_arrivals' => MrpArrival::whereHas('material', function($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->count(),
                'last_import' => MrpRequirement::whereHas('material', function($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->max('import_date')
            ];

            // Materiali con situazione critica - Convertito a Eloquent con eager loading
            $criticalMaterials = MrpMaterial::with(['requirements', 'orders', 'arrivals'])
                ->where('user_id', $userId)
                ->get()
                ->map(function($material) {
                    $fabbisogno = $material->requirements->sum('quantity_needed');
                    $ordinato = $material->orders->sum('quantity_ordered');
                    $ricevuto = $material->arrivals->sum('quantity_received');
                    $mancante = $fabbisogno - $ricevuto;

                    // Solo materiali con mancanze
                    if ($mancante <= 0) return null;

                    $material->fabbisogno = $fabbisogno;
                    $material->ordinato = $ordinato;
                    $material->ricevuto = $ricevuto;
                    $material->da_ordinare = $fabbisogno - $ordinato;
                    $material->da_ricevere = $ordinato - $ricevuto;
                    $material->mancante = $mancante;

                    return $material;
                })
                ->filter() // Rimuove i null
                ->sortByDesc('mancante')
                ->take(10)
                ->values(); // Reindexes the collection

            $data = [
                'pageTitle' => 'MRP - Material Requirements Planning',
                'stats' => $stats,
                'criticalMaterials' => $criticalMaterials,
                'breadcrumb' => [
                    ['title' => 'MRP Dashboard', 'url' => '/mrp', 'current' => true]
                ]
            ];

            $this->render('mrp/index', $data);

        } catch (Exception $e) {
            $this->logActivity('MRP', 'ERROR', 'Errore nel dashboard MRP: ' . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore nel caricamento del dashboard MRP: ' . $e->getMessage();
            $this->redirect($this->url('/'));
        }
    }

    /**
     * Pagina import Excel
     */
    public function import()
    {
        $data = [
            'pageTitle' => 'Import Dati ERP',
            'breadcrumb' => [
                ['title' => 'MRP', 'url' => '/mrp'],
                ['title' => 'Import Dati', 'url' => '/mrp/import', 'current' => true]
            ]
        ];

        $this->render('mrp/import', $data);
    }

    /**
     * Elabora upload Excel
     */
    public function uploadExcel()
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('/mrp/import'));
            return;
        }

        try {
            $userId = $_SESSION['user_id'];

            if (!isset($_FILES['materials_file']) || !isset($_FILES['sizes_file'])) {
                throw new Exception('File mancanti. Caricare entrambi i file Excel.');
            }

            $materialsFile = $_FILES['materials_file'];
            $sizesFile = $_FILES['sizes_file'];

            // Validazione file
            if ($materialsFile['error'] !== UPLOAD_ERR_OK || $sizesFile['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Errore nel caricamento dei file.');
            }

            $this->db->beginTransaction();

            // Pulisci solo i fabbisogni esistenti, mantieni ordini e arrivi - Convertito a Eloquent
            MrpRequirement::whereHas('material', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->delete();

            // Elimina solo i materiali che non hanno ordini o arrivi - Convertito a Eloquent
            MrpMaterial::where('user_id', $userId)
                ->whereDoesntHave('orders')
                ->whereDoesntHave('arrivals')
                ->delete();

            // Elabora file materiali
            $materialsCount = $this->processMaterialsFile($materialsFile['tmp_name'], $userId);

            // Elabora file taglie
            $sizesCount = $this->processSizesFile($sizesFile['tmp_name'], $userId);

            $this->db->commit();

            $this->logActivity('MRP', 'IMPORT', "Import completato: $materialsCount materiali, $sizesCount fabbisogni");
            $_SESSION['alert_success'] = "Import completato con successo! Importati $materialsCount materiali e $sizesCount fabbisogni.";
            $this->redirect($this->url('/mrp/materials'));

        } catch (Exception $e) {
            $this->db->rollback();
            $this->logActivity('MRP', 'ERROR', 'Errore import Excel: ' . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante l\'import: ' . $e->getMessage();
            $this->redirect($this->url('/mrp/import'));
        }
    }

    /**
     * Lista materiali con situazione MRP
     */
    public function materials()
    {
        $userId = $_SESSION['user_id'];

        // Filtri
        $search = $this->input('search', '');
        $hasSize = $this->input('has_size', '');
        $categoryFilter = $this->input('category', '');

        $materialsQuery = MrpMaterial::with(['category_relation', 'requirements', 'orders', 'arrivals'])
            ->where('user_id', $userId);

        if ($search) {
            $materialsQuery->where(function ($query) use ($search) {
                $query->where('material_code', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            });
        }

        if ($hasSize !== '') {
            $materialsQuery->where('has_sizes', (int)$hasSize);
        }

        if (!empty($categoryFilter)) {
            $materialsQuery->where('category', $categoryFilter);
        }

        $materials = $materialsQuery->orderBy('category')->orderBy('material_code')->get()
            ->map(function ($material) {
                $material->fabbisogno = $material->requirements->sum('quantity_needed');
                $material->ordinato = $material->orders->sum('quantity_ordered');
                $material->ricevuto = $material->arrivals->sum('quantity_received');
                $material->da_ordinare = $material->fabbisogno - $material->ordinato;
                $material->da_ricevere = $material->ordinato - $material->ricevuto;
                $material->mancante = $material->fabbisogno - $material->ricevuto;
                $material->category_name = optional($material->category_relation)->name ?? $material->category;
                return $material;
            });

        // Prendi le categorie disponibili con conteggio materiali
        $categories = MrpMaterial::where('user_id', $userId)
            ->leftJoin('mrp_categories', 'mrp_materials.category', '=', 'mrp_categories.code')
            ->selectRaw('mrp_materials.category, mrp_categories.name as category_name, COUNT(mrp_materials.id) as material_count')
            ->groupBy('mrp_materials.category', 'mrp_categories.name')
            ->orderBy('mrp_materials.category')
            ->get();

        // Raggruppa materiali per categoria
        $materialsByCategory = $materials->groupBy(function ($item) {
            return $item->category ?: 'ALTRE';
        });

        $data = [
            'pageTitle' => 'Materiali MRP',
            'materials' => $materials,
            'categories' => $categories,
            'materialsByCategory' => $materialsByCategory,
            'search' => $search,
            'hasSize' => $hasSize,
            'categoryFilter' => $categoryFilter,
            'breadcrumb' => [
                ['title' => 'MRP', 'url' => '/mrp'],
                ['title' => 'Materiali', 'url' => '/mrp/materials', 'current' => true]
            ]
        ];

        $this->render('mrp/materials', $data);
    }

    /**
     * Dettaglio materiale con gestione ordini/arrivi
     */
    public function material($materialId)
    {
        $userId = $_SESSION['user_id'];

        // Verifica proprietà materiale con relazioni
        $material = MrpMaterial::where('id', $materialId)
            ->where('user_id', $userId)
            ->with(['requirements', 'orders', 'arrivals'])
            ->first();

        if (!$material) {
            $_SESSION['alert_error'] = 'Materiale non trovato.';
            $this->redirect($this->url('/mrp/materials'));
            return;
        }

        // Ordina relazioni usando Collection
        $requirements = $material->requirements->sortBy(function($req) {
            return [$req->size === null ? 0 : 1, (int)$req->size, $req->size];
        });

        $orders = $material->orders->sortByDesc('order_date');
        $arrivals = $material->arrivals->sortByDesc('arrival_date');

        $data = [
            'pageTitle' => 'Dettaglio ' . $material->material_code,
            'material' => $material,
            'requirements' => $requirements,
            'orders' => $orders,
            'arrivals' => $arrivals,
            'breadcrumb' => [
                ['title' => 'MRP', 'url' => '/mrp'],
                ['title' => 'Materiali', 'url' => '/mrp/materials'],
                ['title' => $material->material_code, 'url' => '', 'current' => true]
            ]
        ];

        $this->render('mrp/material-detail', $data);
    }

    /**
     * Aggiungi ordine
     */
    public function addOrder($materialId)
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('/mrp/material/' . $materialId));
            return;
        }

        try {
            $userId = $_SESSION['user_id'];

            // Verifica proprietà materiale
            $material = MrpMaterial::where('id', $materialId)
                ->where('user_id', $userId)
                ->first();

            if (!$material) {
                throw new Exception('Materiale non trovato.');
            }

            $this->db->beginTransaction();

            // Gestione diverse tipologie di dati
            if (isset($_POST['order_numbers']) && is_array($_POST['order_numbers'])) {
                // Nuovo formato - ordini multipli
                $orderNumbers = $_POST['order_numbers'];
                $orderDates = $_POST['order_dates'] ?? [];

                if (isset($_POST['size_quantities']) && is_array($_POST['size_quantities'])) {
                    // Materiali con taglie - ordini multipli
                    $rowNotes = $_POST['row_notes'] ?? [];

                    foreach ($_POST['size_quantities'] as $rowIndex => $sizeQuantities) {
                        $orderNumber = trim($orderNumbers[$rowIndex] ?? '');
                        $orderDate = $orderDates[$rowIndex] ?? '';
                        $rowNote = trim($rowNotes[$rowIndex] ?? '') ?: null;

                        if (!$orderNumber || !$orderDate) {
                            continue; // Salta ordini incompleti
                        }

                        foreach ($sizeQuantities as $size => $quantity) {
                            $quantity = (int) $quantity;
                            if ($quantity > 0) {
                                MrpOrder::create([
                                    'material_id' => $materialId,
                                    'size' => $size,
                                    'order_number' => $orderNumber,
                                    'order_date' => $orderDate,
                                    'quantity_ordered' => $quantity,
                                    'notes' => $rowNote
                                ]);
                            }
                        }
                    }
                } else {
                    // Materiali senza taglie - ordini multipli
                    $quantities = $_POST['quantities'] ?? [];
                    $notes = $_POST['notes'] ?? [];

                    foreach ($orderNumbers as $index => $orderNumber) {
                        $orderNumber = trim($orderNumber);
                        $orderDate = $orderDates[$index] ?? '';
                        $quantity = (int) ($quantities[$index] ?? 0);
                        $note = trim($notes[$index] ?? '') ?: null;

                        if ($orderNumber && $orderDate && $quantity > 0) {
                            MrpOrder::create([
                                'material_id' => $materialId,
                                'size' => null,
                                'order_number' => $orderNumber,
                                'order_date' => $orderDate,
                                'quantity_ordered' => $quantity,
                                'notes' => $note
                            ]);
                        }
                    }
                }
            } else {
                // Fallback per formato legacy
                $orderNumber = trim($_POST['order_number'] ?? '');
                $orderDate = $_POST['order_date'] ?? '';

                if (!$orderNumber || !$orderDate) {
                    throw new Exception('Dati ordine incompleti.');
                }

                if (isset($_POST['size_quantities']) && is_array($_POST['size_quantities'])) {
                    // Materiali con taglie - formato legacy tabellare
                    $rowNotes = $_POST['row_notes'] ?? [];

                    foreach ($_POST['size_quantities'] as $rowIndex => $sizeQuantities) {
                        $rowNote = trim($rowNotes[$rowIndex] ?? '') ?: null;

                        foreach ($sizeQuantities as $size => $quantity) {
                            $quantity = (int) $quantity;
                            if ($quantity > 0) {
                                MrpOrder::create([
                                    'material_id' => $materialId,
                                    'size' => $size,
                                    'order_number' => $orderNumber,
                                    'order_date' => $orderDate,
                                    'quantity_ordered' => $quantity,
                                    'notes' => $rowNote
                                ]);
                            }
                        }
                    }
                } elseif (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
                    // Materiali senza taglie - multiple righe legacy
                    $quantities = $_POST['quantities'];
                    $notes = $_POST['notes'] ?? [];

                    foreach ($quantities as $index => $quantity) {
                        $quantity = (int) $quantity;
                        $note = trim($notes[$index] ?? '') ?: null;

                        if ($quantity > 0) {
                            MrpOrder::create([
                                'material_id' => $materialId,
                                'size' => null,
                                'order_number' => $orderNumber,
                                'order_date' => $orderDate,
                                'quantity_ordered' => $quantity,
                                'notes' => $note
                            ]);
                        }
                    }
                } else {
                    // Formato molto legacy - singolo ordine
                    $size = trim($_POST['size'] ?? '') ?: null;
                    $quantity = (int) ($_POST['quantity'] ?? 0);
                    $notes = trim($_POST['notes'] ?? '') ?: null;

                    if ($quantity <= 0) {
                        throw new Exception('Quantità non valida.');
                    }

                    MrpOrder::create([
                        'material_id' => $materialId,
                        'size' => $size,
                        'order_number' => $orderNumber,
                        'order_date' => $orderDate,
                        'quantity_ordered' => $quantity,
                        'notes' => $notes
                    ]);
                }
            }

            $this->db->commit();

            if (method_exists($this, 'logActivity')) {
                $this->logActivity('MRP', 'ORDER_ADD', "Aggiunto ordine per " . $material->material_code);
            }
            $_SESSION['alert_success'] = 'Ordine aggiunto con successo.';

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("ERROR addOrder: " . $e->getMessage());
            if (method_exists($this, 'logActivity')) {
                $this->logActivity('MRP', 'ERROR', 'Errore aggiunta ordine: ' . $e->getMessage());
            }
            $_SESSION['alert_error'] = 'Errore: ' . $e->getMessage();
        }

        $this->redirect($this->url('/mrp/material/' . $materialId));
    }

    /**
     * Aggiungi arrivo
     */
    public function addArrival($materialId)
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('/mrp/material/' . $materialId));
            return;
        }

        try {
            $userId = $_SESSION['user_id'];

            // Verifica proprietà materiale
            $material = MrpMaterial::where('id', $materialId)
                ->where('user_id', $userId)
                ->first();

            if (!$material) {
                throw new Exception('Materiale non trovato.');
            }

            $this->db->beginTransaction();

            // Gestione diverse tipologie di dati
            if (isset($_POST['document_numbers']) && is_array($_POST['document_numbers'])) {
                // Nuovo formato - arrivi multipli
                $documentNumbers = $_POST['document_numbers'];
                $arrivalDates = $_POST['arrival_dates'] ?? [];

                if (isset($_POST['size_quantities']) && is_array($_POST['size_quantities'])) {
                    // Materiali con taglie - arrivi multipli
                    $rowNotes = $_POST['row_notes'] ?? [];

                    foreach ($_POST['size_quantities'] as $rowIndex => $sizeQuantities) {
                        $documentNumber = trim($documentNumbers[$rowIndex] ?? '');
                        $arrivalDate = $arrivalDates[$rowIndex] ?? '';
                        $rowNote = trim($rowNotes[$rowIndex] ?? '') ?: null;

                        if (!$documentNumber || !$arrivalDate) {
                            continue; // Salta arrivi incompleti
                        }

                        foreach ($sizeQuantities as $size => $quantity) {
                            $quantity = (int) $quantity;
                            if ($quantity > 0) {
                                MrpArrival::create([
                                    'material_id' => $materialId,
                                    'size' => $size,
                                    'document_number' => $documentNumber,
                                    'arrival_date' => $arrivalDate,
                                    'quantity_received' => $quantity,
                                    'notes' => $rowNote
                                ]);
                            }
                        }
                    }
                } else {
                    // Materiali senza taglie - arrivi multipli
                    $quantities = $_POST['quantities'] ?? [];
                    $notes = $_POST['notes'] ?? [];

                    foreach ($documentNumbers as $index => $documentNumber) {
                        $documentNumber = trim($documentNumber);
                        $arrivalDate = $arrivalDates[$index] ?? '';
                        $quantity = (int) ($quantities[$index] ?? 0);
                        $note = trim($notes[$index] ?? '') ?: null;

                        if ($documentNumber && $arrivalDate && $quantity > 0) {
                            MrpArrival::create([
                                'material_id' => $materialId,
                                'size' => null,
                                'document_number' => $documentNumber,
                                'arrival_date' => $arrivalDate,
                                'quantity_received' => $quantity,
                                'notes' => $note
                            ]);
                        }
                    }
                }
            } else {
                // Fallback per formato legacy
                $documentNumber = trim($_POST['document_number'] ?? '');
                $arrivalDate = $_POST['arrival_date'] ?? '';

                if (!$documentNumber || !$arrivalDate) {
                    throw new Exception('Dati arrivo incompleti.');
                }

                if (isset($_POST['size_quantities']) && is_array($_POST['size_quantities'])) {
                    // Materiali con taglie - formato legacy tabellare
                    $rowNotes = $_POST['row_notes'] ?? [];

                    foreach ($_POST['size_quantities'] as $rowIndex => $sizeQuantities) {
                        $rowNote = trim($rowNotes[$rowIndex] ?? '') ?: null;

                        foreach ($sizeQuantities as $size => $quantity) {
                            $quantity = (int) $quantity;
                            if ($quantity > 0) {
                                MrpArrival::create([
                                    'material_id' => $materialId,
                                    'size' => $size,
                                    'document_number' => $documentNumber,
                                    'arrival_date' => $arrivalDate,
                                    'quantity_received' => $quantity,
                                    'notes' => $rowNote
                                ]);
                            }
                        }
                    }
                } elseif (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
                    // Materiali senza taglie - multiple righe legacy
                    $quantities = $_POST['quantities'];
                    $notes = $_POST['notes'] ?? [];

                    foreach ($quantities as $index => $quantity) {
                        $quantity = (int) $quantity;
                        $note = trim($notes[$index] ?? '') ?: null;

                        if ($quantity > 0) {
                            MrpArrival::create([
                                'material_id' => $materialId,
                                'size' => null,
                                'document_number' => $documentNumber,
                                'arrival_date' => $arrivalDate,
                                'quantity_received' => $quantity,
                                'notes' => $note
                            ]);
                        }
                    }
                } else {
                    // Formato molto legacy - singolo arrivo
                    $size = trim($_POST['size'] ?? '') ?: null;
                    $quantity = (int) ($_POST['quantity'] ?? 0);
                    $notes = trim($_POST['notes'] ?? '') ?: null;

                    if ($quantity <= 0) {
                        throw new Exception('Quantità non valida.');
                    }

                    MrpArrival::create([
                        'material_id' => $materialId,
                        'size' => $size,
                        'document_number' => $documentNumber,
                        'arrival_date' => $arrivalDate,
                        'quantity_received' => $quantity,
                        'notes' => $notes
                    ]);
                }
            }

            $this->db->commit();


            if (method_exists($this, 'logActivity')) {
                $this->logActivity('MRP', 'ARRIVAL_ADD', "Aggiunto arrivo per " . $material->material_code);
            }
            $_SESSION['alert_success'] = 'Arrivo registrato con successo.';

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("ERROR addArrival: " . $e->getMessage());
            if (method_exists($this, 'logActivity')) {
                $this->logActivity('MRP', 'ERROR', 'Errore registrazione arrivo: ' . $e->getMessage());
            }
            $_SESSION['alert_error'] = 'Errore: ' . $e->getMessage();
        }

        $this->redirect($this->url('/mrp/material/' . $materialId));
    }

    /**
     * Cancella ordine
     */
    public function deleteOrder()
    {
        if (!$this->isPost()) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Metodo non consentito']);
                return;
            }
            $this->redirect($this->url('/mrp'));
            return;
        }

        $materialId = null;

        try {
            $userId = $_SESSION['user_id'];
            $orderId = (int) ($_POST['id'] ?? 0);

            if (!$orderId) {
                throw new Exception('ID ordine non valido.');
            }

            // Verifica proprietà ordine
            $order = MrpOrder::with('material')->where('id', $orderId)
                ->whereHas('material', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->first();

            if (!$order) {
                throw new Exception('Ordine non trovato.');
            }
            
            $materialCode = $order->material->material_code;
            $orderNumber = $order->order_number;
            $materialId = $order->material_id;

            $order->delete();

            if (method_exists($this, 'logActivity')) {
                $this->logActivity('MRP', 'ORDER_DELETE', "Eliminato ordine {$orderNumber} per {$materialCode}");
            }

            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => 'Ordine eliminato con successo']);
                return;
            }

            $_SESSION['alert_success'] = 'Ordine eliminato con successo.';

        } catch (Exception $e) {
            if (method_exists($this, 'logActivity')) {
                $this->logActivity('MRP', 'ERROR', 'Errore eliminazione ordine: ' . $e->getMessage());
            }

            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $e->getMessage()]);
                return;
            }

            $_SESSION['alert_error'] = 'Errore: ' . $e->getMessage();
        }

        if ($materialId) {
            $this->redirect($this->url('/mrp/material/' . $materialId));
        } else {
            $this->redirect($this->url('/mrp/materials'));
        }
    }

    /**
     * Cancella arrivo
     */
    public function deleteArrival()
    {
        if (!$this->isPost()) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Metodo non consentito']);
                return;
            }
            $this->redirect($this->url('/mrp'));
            return;
        }

        $materialId = null;

        try {
            $userId = $_SESSION['user_id'];
            $arrivalId = (int) ($_POST['id'] ?? 0);

            if (!$arrivalId) {
                throw new Exception('ID arrivo non valido.');
            }

            // Verifica proprietà arrivo
            $arrival = MrpArrival::with('material')->where('id', $arrivalId)
                ->whereHas('material', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->first();

            if (!$arrival) {
                throw new Exception('Arrivo non trovato.');
            }
            
            $materialCode = $arrival->material->material_code;
            $documentNumber = $arrival->document_number;
            $materialId = $arrival->material_id;

            $arrival->delete();

            if (method_exists($this, 'logActivity')) {
                $this->logActivity('MRP', 'ARRIVAL_DELETE', "Eliminato arrivo {$documentNumber} per {$materialCode}");
            }

            // Risposta in base al tipo di richiesta
            if ($this->isAjax()) {
                $this->json([
                    'success' => true,
                    'message' => 'Arrivo eliminato con successo'
                ]);
                return;
            }

            $_SESSION['alert_success'] = 'Arrivo eliminato con successo.';

        } catch (Exception $e) {
            if (method_exists($this, 'logActivity')) {
                $this->logActivity('MRP', 'ERROR', 'Errore eliminazione arrivo: ' . $e->getMessage());
            }

            // Risposta in base al tipo di richiesta
            if ($this->isAjax()) {
                $this->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
                return;
            }

            $_SESSION['alert_error'] = 'Errore: ' . $e->getMessage();
        }

        // Redirect to material detail or materials list based on referer
        if ($materialId) {
            $this->redirect($this->url('/mrp/material/' . $materialId));
        } else {
            $this->redirect($this->url('/mrp/materials'));
        }
    }

    /**
     * Elabora file materiali Excel
     */
    private function processMaterialsFile($filePath, $userId)
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Rimuovi header
        $header = array_shift($rows);

        // Mappa colonne (basata sull'analisi Excel)
        $colMap = [
            'material_code' => 1, // PAKAR (colonna B)
            'description' => 2,   // PADESC (colonna C)
            'supplier_code' => 4, // PRKFOR (colonna E)
            'supplier_name' => 5, // RAGSOC (colonna F)
            'qtafab1' => 6,       // QTAFAB1 (colonna G) - fabbisogno per materiali senza taglie
            'unit_measure' => 7,  // UM1 (colonna H)
            'category' => 11,     // PACTME (colonna L)
            'has_sizes' => 18     // NUMERATA (colonna S)
        ];

        // I materiali sono già stati puliti all'inizio dell'import

        $count = 0;
        $importDate = date('Y-m-d');

        foreach ($rows as $rowIndex => $row) {
            if (empty($row[$colMap['material_code']]))
                continue;

            $materialCode = $row[$colMap['material_code']];
            $description = $row[$colMap['description']] ?? '';
            $supplierCode = $row[$colMap['supplier_code']] ?? '';
            $supplierName = $row[$colMap['supplier_name']] ?? '';
            $unitMeasure = $row[$colMap['unit_measure']] ?? '';
            $category = $row[$colMap['category']] ?? '';
            $hasSizes = !empty($row[$colMap['has_sizes']]) && $row[$colMap['has_sizes']] === 'X';
            $qtafab1 = (float) ($row[$colMap['qtafab1']] ?? 0);


            // Inserisci o aggiorna materiale se esiste - Convertito a Eloquent
            $existingMaterial = MrpMaterial::where('user_id', $userId)
                ->where('material_code', $materialCode)
                ->first();

            if ($existingMaterial) {
                // Aggiorna materiale esistente - Convertito a Eloquent
                $existingMaterial->update([
                    'description' => $description,
                    'supplier_code' => $supplierCode,
                    'supplier_name' => $supplierName,
                    'unit_measure' => $unitMeasure,
                    'category' => $category,
                    'has_sizes' => $hasSizes ? 1 : 0
                ]);
                $materialId = $existingMaterial->id;
            } else {
                // Inserisci nuovo materiale - Convertito a Eloquent
                $material = MrpMaterial::create([
                    'user_id' => $userId,
                    'material_code' => $materialCode,
                    'description' => $description,
                    'supplier_code' => $supplierCode,
                    'supplier_name' => $supplierName,
                    'unit_measure' => $unitMeasure,
                    'category' => $category,
                    'has_sizes' => $hasSizes ? 1 : 0
                ]);
                $materialId = $material->id;
            }

            // Per materiali SENZA taglie, aggiungi il fabbisogno dalla colonna QTAFAB1
            if (!$hasSizes && $qtafab1 > 0) {
                if ($materialId > 0) {
                    try {
                        MrpRequirement::create([
                            'material_id' => $materialId,
                            'size' => null,
                            'quantity_needed' => $qtafab1,
                            'import_date' => $importDate
                        ]);
                    } catch (Exception $e) {
                        error_log("ERROR: Failed to insert requirement for $materialCode: " . $e->getMessage());
                    }
                } else {
                    error_log("ERROR: materialId is 0 for $materialCode");
                }
            }

            $count++;
        }

        return $count;
    }

    /**
     * Elabora file taglie Excel
     */
    private function processSizesFile($filePath, $userId)
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Rimuovi header
        $header = array_shift($rows);

        // Mappa colonne (basata sull'analisi Excel)
        $colMap = [
            'material_code' => 1, // PAKAR (colonna B)
            'type' => 2,          // TIPO (colonna C)
            'size' => 5,          // TAGLIA (colonna F)
            'quantity' => 6       // QTA (colonna G)
        ];

        // I dati sono già stati puliti all'inizio dell'import

        $count = 0;
        $importDate = date('Y-m-d');

        foreach ($rows as $row) {
            if (empty($row[$colMap['material_code']]) || $row[$colMap['type']] !== 'FABBISOGNO') {
                continue;
            }

            $materialCode = $row[$colMap['material_code']];
            $size = $row[$colMap['size']] ?? null;
            $quantity = (int) ($row[$colMap['quantity']] ?? 0);

            if ($quantity <= 0)
                continue;

            // Trova material_id
            $material = MrpMaterial::where('user_id', $userId)
                ->where('material_code', $materialCode)
                ->first();

            if ($material) {
                MrpRequirement::create([
                    'material_id' => $material->id,
                    'size' => $size,
                    'quantity_needed' => $quantity,
                    'import_date' => $importDate
                ]);

                $count++;
            }
        }

        return $count;
    }

    /**
     * Lista categorie MRP
     */
    public function categories()
    {
        $categories = MrpCategory::orderBy('code')->get();
        $this->render('mrp/categories', compact('categories'));
    }

    /**
     * Salva categoria
     */
    public function storeCategory()
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('/mrp/categories'));
            return;
        }

        try {
            $code = trim($this->input('code'));
            $name = trim($this->input('name'));
            $description = trim($this->input('description'));

            if (empty($code) || empty($name)) {
                throw new Exception('Codice e nome sono obbligatori.');
            }

            MrpCategory::create([
                'code' => $code,
                'name' => $name,
                'description' => $description
            ]);

            $_SESSION['alert_success'] = 'Categoria creata con successo!';

        } catch (Exception $e) {
            $_SESSION['alert_error'] = 'Errore durante la creazione: ' . $e->getMessage();
        }

        $this->redirect($this->url('/mrp/categories'));
    }

    /**
     * Aggiorna categoria
     */
    public function updateCategory()
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('/mrp/categories'));
            return;
        }

        try {
            $id = (int) $this->input('id');
            $code = trim($this->input('code'));
            $name = trim($this->input('name'));
            $description = trim($this->input('description'));

            if (empty($code) || empty($name)) {
                throw new Exception('Codice e nome sono obbligatori.');
            }

            $category = MrpCategory::find($id);
            if ($category) {
                $category->update([
                    'code' => $code,
                    'name' => $name,
                    'description' => $description
                ]);
                $_SESSION['alert_success'] = 'Categoria aggiornata con successo!';
            } else {
                throw new Exception('Categoria non trovata.');
            }

        } catch (Exception $e) {
            $_SESSION['alert_error'] = 'Errore durante l\'aggiornamento: ' . $e->getMessage();
        }

        $this->redirect($this->url('/mrp/categories'));
    }

    /**
     * Elimina categoria
     */
    public function deleteCategory()
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('/mrp/categories'));
            return;
        }

        try {
            $id = (int) $this->input('id');
            MrpCategory::destroy($id);

            $_SESSION['alert_success'] = 'Categoria eliminata con successo!';

        } catch (Exception $e) {
            $_SESSION['alert_error'] = 'Errore durante l\'eliminazione: ' . $e->getMessage();
        }

        $this->redirect($this->url('/mrp/categories'));
    }

    /**
     * Elimina materiale con cascata
     */
    public function deleteMaterial()
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('/mrp/materials'));
            return;
        }

        try {
            $materialId = (int) $this->input('material_id');
            $reason = trim($this->input('reason'));
            $userId = $_SESSION['user_id'];

            if (empty($materialId)) {
                throw new Exception('ID materiale non valido.');
            }

            // Verifica che il materiale appartenga all'utente
            $material = MrpMaterial::where('id', $materialId)
                ->where('user_id', $userId)
                ->first();

            if (!$material) {
                throw new Exception('Materiale non trovato o non autorizzato.');
            }

            $materialCode = $material->material_code;

            // Avvia transazione per eliminazione cascade
            $this->db->beginTransaction();

            try {
                // Elimina fabbisogni, ordini, arrivi e materiale
                // Assumendo che le relazioni siano definite con onDelete('cascade') nel database,
                // basterebbe $material->delete().
                // Per sicurezza, elimino manualmente le relazioni come nel codice originale.
                $material->requirements()->delete();
                $material->orders()->delete();
                $material->arrivals()->delete();
                $material->delete();

                $this->db->commit();

                $_SESSION['alert_success'] = "Materiale '{$materialCode}' eliminato con successo" .
                                           (!empty($reason) ? " (Motivo: {$reason})" : '');

            } catch (Exception $e) {
                $this->db->rollback();
                throw $e;
            }

        } catch (Exception $e) {
            $_SESSION['alert_error'] = 'Errore durante l\'eliminazione: ' . $e->getMessage();
        }

        $this->redirect($this->url('/mrp/materials'));
    }
}