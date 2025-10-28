<?php
/**
 * Riparazioni Controller
 * Gestisce il sistema riparazioni con Eloquent ORM
 */

use App\Models\Repair;
use App\Models\InternalRepair;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\CoreData;
use App\Models\Department;
use App\Models\Laboratory;
use App\Models\IdSize;
use App\Models\Tabid;
use App\Models\Line;

class RiparazioniController extends BaseController
{
    /**
     * Lista riparazioni
     */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('riparazioni');
        
        // Parametri di paginazione e filtro
        $page = (int)($this->input('page') ?? 1);
        $search = $this->input('search');
        $status = $this->input('status');
        $urgency = $this->input('urgency');
        
        try {
            // Query base con Eloquent
            $query = Repair::query();

            // Filtri
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('IDRIP', 'like', "%{$search}%")
                      ->orWhere('CODICE', 'like', "%{$search}%")
                      ->orWhere('ARTICOLO', 'like', "%{$search}%")
                      ->orWhere('CARTELLINO', 'like', "%{$search}%");
                });
            }

            if ($status) {
                if ($status === 'complete') {
                    $query->completed();
                } elseif ($status === 'open') {
                    $query->pending();
                }
            }

            if ($urgency) {
                $query->byUrgency($urgency);
            }

            // Ordina per data e ID
            $query->orderByDesc('DATA')->orderByDesc('IDRIP');

            // Esegue la query
            $riparazioni = $query->get();
            
            $data = [
                'pageTitle' => 'Riparazioni - ' . APP_NAME,
                'riparazioni' => $riparazioni,
                'currentSearch' => $search,
                'currentStatus' => $status,
                'currentUrgency' => $urgency
            ];
            
            $this->render('riparazioni.index', $data);
            
        } catch (Exception $e) {
            error_log("Errore riparazioni index: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante il caricamento delle riparazioni.';
            $this->redirect($this->url('/'));
        }
    }
    
    /**
     * Mostra il form per creare una nuova riparazione - Step 1: inserimento cartellino/commessa
     */
    public function create()
    {
        $this->requireAuth();
        $this->requirePermission('riparazioni');
        
        $data = [
            'pageTitle' => 'Nuova Riparazione - Step 1 - ' . APP_NAME
        ];
        
        $this->render('riparazioni.create_step1', $data);
    }
    
    /**
     * Step 2: Form con dati precompilati dal cartellino
     */
    public function createStep2()
    {
        $this->requireAuth();
        $this->requirePermission('riparazioni');
        
        $cartellino = $this->input('cartellino');
        
        if (!$cartellino) {
            $_SESSION['alert_error'] = 'Cartellino non specificato.';
            $this->redirect($this->url('/riparazioni/create'));
        }
        
        try {
            // Cerca il cartellino nella tabella dati
            $datiCartellino = CoreData::where('Cartel', $cartellino)->first();

            if (!$datiCartellino) {
                $_SESSION['alert_error'] = 'Cartellino non trovato nel database.';
                $this->redirect($this->url('/riparazioni/create'));
            }

            // Carica dati supplementari
            $reparti = Department::orderBy('Nome')->get();
            $laboratori = Laboratory::select('Nome')->distinct()->orderBy('Nome')->get();

            // Carica la numerata corrispondente
            $numerata = null;
            if (!empty($datiCartellino->Nu)) {
                $numerata = IdSize::where('ID', $datiCartellino->Nu)->first();
            }

            // Ottiene il prossimo ID riparazione
            $maxTabid = Tabid::max('ID') ?? 0;
            $nextId = $maxTabid + 1;

            $data = [
                'pageTitle' => 'Nuova Riparazione - Step 2 - ' . APP_NAME,
                'datiCartellino' => $datiCartellino,
                'numerata' => $numerata,
                'reparti' => $reparti,
                'laboratori' => $laboratori,
                'nextId' => $nextId,
                'currentUser' => $_SESSION['username'] ?? '',
                'currentDate' => date('d/m/Y')
            ];
            
            $this->render('riparazioni.create_step2', $data);
            
        } catch (Exception $e) {
            error_log("Errore riparazioni create step2: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante il caricamento dei dati del cartellino.';
            $this->redirect($this->url('/riparazioni/create'));
        }
    }
    
    /**
     * Salva una nuova riparazione
     */
    public function store()
    {
        $this->requireAuth();
        $this->requirePermission('riparazioni');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/riparazioni'));
        }
        
        // Valida CSRF token
        if (!$this->validateCsrfToken($this->input('csrf_token'))) {
            $_SESSION['alert_error'] = 'Token di sicurezza non valido.';
            $this->redirect($this->url('/riparazioni/create'));
        }
        
        try {
            // Ottiene il prossimo ID dalla tabella tabid
            $newId = (Tabid::max('ID') ?? 0) + 1;

            // Prepara i dati per l'inserimento
            $data = [
                'IDRIP' => $newId,
                'ARTICOLO' => $this->input('ARTICOLO'),
                'CODICE' => $this->input('CODICE'),
                'CARTELLINO' => $this->input('cartellino'),
                'REPARTO' => $this->input('reparto'),
                'CAUSALE' => $this->input('causale'),
                'LABORATORIO' => $this->input('laboratorio'),
                'DATA' => $this->input('data') ?: date('Y-m-d'),
                'NU' => $this->input('nu'),
                'UTENTE' => $this->input('utente') ?: $_SESSION['username'],
                'CLIENTE' => $this->input('cliente'),
                'COMMESSA' => $this->input('commessa'),
                'LINEA' => $this->input('linea'),
                'URGENZA' => $this->input('urgenza') ?: 'BASSA',
                'COMPLETA' => 0
            ];

            // Gestione quantità P01-P20
            $totalQty = 0;
            for ($i = 1; $i <= 20; $i++) {
                $pField = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $qty = (int)($this->input($pField) ?? 0);
                $data[$pField] = $qty;
                $totalQty += $qty;
            }
            $data['QTA'] = $totalQty;

            // Validazione
            $errors = [];
            if (empty($data['ARTICOLO'])) $errors[] = 'Articolo obbligatorio';
            if (empty($data['CODICE'])) $errors[] = 'Codice obbligatorio';
            if (empty($data['CARTELLINO'])) $errors[] = 'Cartellino obbligatorio';
            if (empty($data['CAUSALE'])) $errors[] = 'Causale obbligatoria';
            if ($totalQty == 0) $errors[] = 'Deve essere specificata almeno una quantità';

            if (!empty($errors)) {
                $_SESSION['alert_error'] = implode('. ', $errors);
                $this->redirect($this->url('/riparazioni/create'));
            }

            // Crea riparazione con Eloquent
            $repair = Repair::create($data);

            if ($repair) {
                // Aggiorna la tabella tabid con il nuovo ID usato
                Tabid::create(['ID' => $newId]);

                // Log attività
                ActivityLog::create([
                    'user_id' => $_SESSION['user_id'] ?? null,
                    'action' => 'CREATE',
                    'module' => 'REPAIRS',
                    'description' => "Creata riparazione {$newId}",
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
                ]);

                $_SESSION['alert_success'] = "Riparazione {$newId} creata con successo!";
                $this->redirect($this->url('/riparazioni/' . $newId));
            } else {
                $_SESSION['alert_error'] = 'Errore durante il salvataggio della riparazione.';
                $this->redirect($this->url('/riparazioni/create'));
            }
            
        } catch (Exception $e) {
            error_log("Errore creazione riparazione: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante la creazione della riparazione.';
            $this->redirect($this->url('/riparazioni/create'));
        }
    }
    
    /**
     * Mostra i dettagli di una riparazione
     */
    public function show($id)
    {
        $this->requireAuth();
        $this->requirePermission('riparazioni');

        try {
            // Trova la riparazione con Eloquent
            $riparazione = Repair::find($id);

            if (!$riparazione) {
                $_SESSION['alert_error'] = 'Riparazione non trovata.';
                $this->redirect($this->url('/riparazioni'));
            }

            // Per ora, aggiungiamo le proprietà join manualmente
            // TODO: Creare modelli per id_numerate e linee se necessario
            if ($riparazione->NU) {
                $numerata = IdSize::where('ID', $riparazione->NU)->first();
                if ($numerata) {
                    foreach ($numerata->toArray() as $key => $value) {
                        $riparazione->$key = $value;
                    }
                }
            }

            if ($riparazione->LINEA) {
                $linea = Line::where('Sigla', $riparazione->LINEA)->first();
                if ($linea) {
                    $riparazione->linea_descrizione = $linea->descrizione;
                }
            }
            
            // Prepara dati taglie per la view
            $taglie = [];
            for ($i = 1; $i <= 20; $i++) {
                $pField = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $taglie[$pField] = $riparazione->$pField ?? 0;
            }

            $data = [
                'pageTitle' => "Riparazione {$id} - " . APP_NAME,
                'riparazione' => $riparazione,
                'taglie' => $taglie,
                'sizesWithQuantities' => $riparazione->sizes_with_quantities
            ];
            
            $this->render('riparazioni.show', $data);
            
        } catch (Exception $e) {
            error_log("Errore riparazione show: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante il caricamento della riparazione.';
            $this->redirect($this->url('/riparazioni'));
        }
    }
    
    /**
     * Mostra il form per modificare una riparazione
     */
    public function edit($id)
    {
        $this->requireAuth();
        $this->requirePermission('riparazioni');
        
        try {
            $riparazione = Repair::find($id);

            if (!$riparazione) {
                $_SESSION['alert_error'] = 'Riparazione non trovata.';
                $this->redirect($this->url('/riparazioni'));
            }
            
            // Carica reparti
            $reparti = Department::orderBy('Nome')->get();

            // Carica laboratori
            $laboratori = Laboratory::select('Nome')->distinct()->orderBy('Nome')->get();

            // Carica numerazioni
            $numerazioni = IdSize::orderBy('ID')->get();

            // Carica numerata specifica per questa riparazione
            $numerata = null;
            if (!empty($riparazione->NU)) {
                $numerata = IdSize::where('ID', $riparazione->NU)->first();
            }
            
            // Prepara dati taglie per la view
            $taglie = [];
            for ($i = 1; $i <= 20; $i++) {
                $pField = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $taglie[$pField] = $riparazione->$pField ?? 0;
            }

            $data = [
                'pageTitle' => "Modifica Riparazione {$id} - " . APP_NAME,
                'riparazione' => $riparazione,
                'reparti' => $reparti,
                'laboratori' => $laboratori,
                'numerazioni' => $numerazioni,
                'numerata' => $numerata,
                'taglie' => $taglie,
                'sizesWithQuantities' => $riparazione->sizes_with_quantities,
                'edit' => true
            ];
            
            $this->render('riparazioni.edit', $data);
            
        } catch (Exception $e) {
            error_log("Errore riparazioni edit: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante il caricamento del form.';
            $this->redirect($this->url('/riparazioni'));
        }
    }
    
    /**
     * Aggiorna una riparazione
     */
    public function update($id)
    {
        $this->requireAuth();
        $this->requirePermission('riparazioni');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/riparazioni/' . $id));
        }
        
        // Valida CSRF token
        if (!$this->validateCsrfToken($this->input('csrf_token'))) {
            $_SESSION['alert_error'] = 'Token di sicurezza non valido.';
            $this->redirect($this->url('/riparazioni/' . $id . '/edit'));
        }
        
        try {
            // Trova la riparazione con Eloquent
            $repair = Repair::find($id);

            if (!$repair) {
                $_SESSION['alert_error'] = 'Riparazione non trovata.';
                $this->redirect($this->url('/riparazioni'));
            }

            // Prepara i dati per l'aggiornamento
            $data = [
                'ARTICOLO' => $this->input('ARTICOLO'),
                'CODICE' => $this->input('CODICE'),
                'CARTELLINO' => $this->input('cartellino'),
                'REPARTO' => $this->input('reparto'),
                'CAUSALE' => $this->input('causale'),
                'LABORATORIO' => $this->input('laboratorio'),
                'DATA' => $this->input('data'),
                'NU' => $this->input('numerata'),
                'UTENTE' => $this->input('utente'),
                'CLIENTE' => $this->input('cliente'),
                'COMMESSA' => $this->input('commessa'),
                'LINEA' => $this->input('linea'),
                'URGENZA' => $this->input('urgenza') ?: 'BASSA'
            ];

            // Gestione quantità P01-P20
            $totalQty = 0;
            for ($i = 1; $i <= 20; $i++) {
                $pField = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $qty = (int)($this->input($pField) ?? 0);
                $data[$pField] = $qty;
                $totalQty += $qty;
            }
            $data['QTA'] = $totalQty;

            // Rimuove campi vuoti dal data
            $filteredData = array_filter($data, function($value) {
                return $value !== null && $value !== '';
            });

            if (empty($filteredData)) {
                $_SESSION['alert_warning'] = 'Nessuna modifica specificata.';
                $this->redirect($this->url('/riparazioni/' . $id));
            }

            // Aggiorna con Eloquent
            $repair->fill($filteredData);

            if ($repair->save()) {
                // Log attività
                ActivityLog::create([
                    'user_id' => $_SESSION['user_id'] ?? null,
                    'action' => 'UPDATE',
                    'module' => 'REPAIRS',
                    'description' => "Aggiornata riparazione {$id}",
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
                ]);

                $_SESSION['alert_success'] = 'Riparazione aggiornata con successo!';
                $this->redirect($this->url('/riparazioni/' . $id));
            } else {
                $_SESSION['alert_error'] = 'Errore durante l\'aggiornamento.';
                $this->redirect($this->url('/riparazioni/' . $id . '/edit'));
            }
            
        } catch (Exception $e) {
            error_log("Errore aggiornamento riparazione: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante l\'aggiornamento della riparazione.';
            $this->redirect($this->url('/riparazioni/' . $id . '/edit'));
        }
    }
    
    /**
     * Completa una riparazione
     */
    public function complete($id)
    {
        $this->requireAuth();
        $this->requirePermission('riparazioni');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/riparazioni/' . $id));
        }
        
        // Valida CSRF token
        if (!$this->validateCsrfToken($this->input('csrf_token'))) {
            $_SESSION['alert_error'] = 'Token di sicurezza non valido.';
            $this->redirect($this->url('/riparazioni/' . $id));
        }
        
        try {
            $repair = Repair::find($id);

            if (!$repair) {
                $_SESSION['alert_error'] = 'Riparazione non trovata.';
                $this->redirect($this->url('/riparazioni'));
            }

            $repair->COMPLETA = 1;

            if ($repair->save()) {
                // Log attività
                ActivityLog::create([
                    'user_id' => $_SESSION['user_id'] ?? null,
                    'action' => 'COMPLETE',
                    'module' => 'REPAIRS',
                    'description' => "Completata riparazione {$id}",
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
                ]);

                $_SESSION['alert_success'] = 'Riparazione completata con successo!';
            } else {
                $_SESSION['alert_error'] = 'Errore durante il completamento.';
            }
            
        } catch (Exception $e) {
            error_log("Errore completamento riparazione: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante il completamento della riparazione.';
        }
        
        $this->redirect($this->url('/riparazioni/' . $id));
    }
    
    /**
     * Ricerca riparazioni (AJAX)
     */
    public function search()
    {
        $this->requireAuth();
        $this->requirePermission('riparazioni');
        
        $searchTerm = $this->input('q');
        if (empty($searchTerm)) {
            $this->json(['items' => []]);
        }
        
        try {
            $riparazioni = Repair::where(function($q) use ($searchTerm) {
                $q->where('IDRIP', 'like', "%{$searchTerm}%")
                  ->orWhere('CODICE', 'like', "%{$searchTerm}%")
                  ->orWhere('ARTICOLO', 'like', "%{$searchTerm}%")
                  ->orWhere('CARTELLINO', 'like', "%{$searchTerm}%");
            })
            ->orderByDesc('DATA')
            ->orderByDesc('IDRIP')
            ->limit(10)
            ->get(['IDRIP', 'CODICE', 'ARTICOLO', 'CARTELLINO', 'URGENZA', 'COMPLETA']);

            $items = $riparazioni->map(function($rip) {
                return [
                    'id' => $rip->IDRIP,
                    'text' => "{$rip->IDRIP} - {$rip->CODICE} - {$rip->ARTICOLO}",
                    'codice' => $rip->CODICE,
                    'urgenza' => $rip->URGENZA,
                    'completa' => $rip->COMPLETA
                ];
            });

            $this->json(['items' => $items]);
            
        } catch (Exception $e) {
            $this->json(['error' => 'Errore durante la ricerca']);
        }
    }
    
    /**
     * API: Verifica se un cartellino esiste nella tabella dati
     */
    public function checkCartellino()
    {
        $this->requireAuth();
        $this->requirePermission('riparazioni');
        
        $cartellino = $this->input('cartellino');
        
        if (empty($cartellino)) {
            $this->json(['exists' => false, 'error' => 'Cartellino non specificato']);
        }
        
        try {
            $exists = CoreData::where('Cartel', $cartellino)->exists();
            $this->json(['exists' => $exists]);
            
        } catch (Exception $e) {
            error_log("Errore check cartellino: " . $e->getMessage());
            $this->json(['exists' => false, 'error' => 'Errore durante la verifica']);
        }
    }
    
    /**
     * API: Verifica se una commessa esiste e restituisce il cartellino corrispondente
     */
    public function checkCommessa()
    {
        $this->requireAuth();
        $this->requirePermission('riparazioni');
        
        $commessa = $this->input('commessa');
        
        if (empty($commessa)) {
            $this->json(['exists' => false, 'error' => 'Commessa non specificata']);
        }
        
        try {
            $data = CoreData::where('Commessa Cli', $commessa)->first();

            if ($data) {
                $this->json([
                    'exists' => true,
                    'cartellino' => $data->Cartel
                ]);
            } else {
                $this->json(['exists' => false]);
            }
            
        } catch (Exception $e) {
            error_log("Errore check commessa: " . $e->getMessage());
            $this->json(['exists' => false, 'error' => 'Errore durante la verifica']);
        }
    }
    
    /**
     * API: Elimina riparazioni
     */
    public function delete()
    {
        $this->requireAuth();
        $this->requirePermission('riparazioni');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Metodo non consentito']);
        }
        
        // Legge i dati JSON dal body della richiesta
        $input = json_decode(file_get_contents('php://input'), true);
        $ids = $input['ids'] ?? [];
        
        if (empty($ids) || !is_array($ids)) {
            $this->json(['success' => false, 'error' => 'IDs riparazioni non specificati']);
        }
        
        // Valida che tutti gli ID siano numerici
        foreach ($ids as $id) {
            if (!is_numeric($id)) {
                $this->json(['success' => false, 'error' => 'ID riparazione non valido']);
            }
        }
        
        try {
            $deletedCount = 0;
            
            foreach ($ids as $id) {
                // Verifica che la riparazione esista e non sia completa
                $riparazione = Repair::find($id);

                if (!$riparazione) {
                    continue; // Salta se la riparazione non esiste
                }

                if ($riparazione->COMPLETA == 1) {
                    $this->json(['success' => false, 'error' => "La riparazione {$id} è completa e non può essere eliminata"]);
                }

                // Elimina la riparazione
                if ($riparazione->delete()) {
                    $deletedCount++;

                    // Log attività
                    ActivityLog::create([
                        'user_id' => $_SESSION['user_id'] ?? null,
                        'action' => 'DELETE',
                        'module' => 'REPAIRS',
                        'description' => "Eliminata riparazione {$id}",
                        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
                    ]);
                }
            }
            
            if ($deletedCount > 0) {
                $this->json([
                    'success' => true,
                    'deleted' => $deletedCount,
                    'message' => $deletedCount === 1 ? 'Riparazione eliminata con successo' : "{$deletedCount} riparazioni eliminate con successo"
                ]);
            } else {
                $this->json(['success' => false, 'error' => 'Nessuna riparazione eliminata']);
            }
            
        } catch (Exception $e) {
            error_log("Errore eliminazione riparazioni: " . $e->getMessage());
            $this->json(['success' => false, 'error' => 'Errore durante l\'eliminazione delle riparazioni']);
        }
    }
    
    /**
     * Stampa PDF della riparazione
     */
    public function printPdf($id)
    {
        $this->requireAuth();
        $this->requirePermission('riparazioni');
        
        try {
            // Trova la riparazione con Eloquent
            $riparazione = Repair::find($id);

            if (!$riparazione) {
                throw new Exception("Riparazione non trovata");
            }

            // Carica dati numerata se presente (mantenendo l'oggetto Eloquent)
            if ($riparazione->NU) {
                $numerata = IdSize::where('ID', $riparazione->NU)->first();
                if ($numerata) {
                    // Aggiungi proprietà numerata all'oggetto esistente
                    foreach ($numerata->toArray() as $key => $value) {
                        $riparazione->$key = $value;
                    }
                }
            }
            
            // Includi TCPDF
            
            
            // Configura TCPDF
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetMargins(7, 7, 7);
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            // Aggiungi una nuova pagina
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 30);
            
            // Header principale
            $pdf->SetLineWidth(0.7);
            $pdf->SetFillColor(214, 220, 229);
            $pdf->Rect(7, 7, 196, 25, 'DF');
            $pdf->Rect(7, 7, 46, 25, 'D');
            $pdf->MultiCell(180, 10, "CEDOLA DI RIPARAZIONE", 0, 'L', false, 1, 60, 12, true, 0, false, true, 0, 'T', false);
            $pdf->SetY(12);
            
            // Barcode
            $pdf->SetFont('helvetica', '', 10);
            $style = array(
                'position' => '',
                'align' => 'C',
                'stretch' => false,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'hpadding' => 'auto',
                'vpadding' => 'auto',
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false,
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 10,
                'stretchtext' => 3,
            );
            $pdf->write1DBarcode($id, 'C39', '', '', '', 18, 0.4, $style, 'N');
            $pdf->SetFont('helvetica', 'B', 30);
            
            $pdf->Ln(4);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 3, 'CALZATURIFICIO EMMEGIEMME SHOES S.R.L', 1, 1, 'C', true);
            $pdf->SetMargins(9, 7, 7);
            $pdf->SetFont('helvetica', 'N', 12);
            $pdf->Ln(1);
            
            // Contenuto principale
            $pdf->Rect(7, 41, 196, 60, 'D');
            $pdf->Cell(155, 10, 'LABORATORIO:', 0, 0);
            $pdf->Cell(50, 10, 'REPARTO:', 0, 1);
            $pdf->SetFont('helvetica', 'B', 25);
            $pdf->Cell(155, 10, $riparazione['LABORATORIO'] ?? '', 0, 0);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(50, 10, $riparazione['REPARTO'] ?? '', 0, 1);
            $pdf->SetFont('helvetica', 'N', 12);
            $pdf->Cell(50, 10, 'CARTELLINO:', 0, 0);
            $pdf->Cell(70, 10, 'COMMESSA:', 0, 0);
            $pdf->Cell(35, 10, 'QTA:', 0, 0);
            $pdf->Cell(50, 10, 'LINEA:', 0, 1);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(50, 10, $riparazione['CARTELLINO'] ?? '', 0, 0);
            $pdf->Cell(70, 10, $riparazione['COMMESSA'] ?? '', 0, 0);
            $pdf->Cell(35, 10, $riparazione['QTA'] ?? '', 0, 0);
            $pdf->Cell(50, 10, $riparazione['LINEA'] ?? '', 0, 1);
            $pdf->Ln(1);
            $pdf->SetFont('helvetica', 'N', 12);
            $pdf->SetMargins(7, 7, 7);
            $pdf->Cell(120, 10, 'ARTICOLO:', 0, 0);
            $pdf->Cell(35, 10, 'URGENZA:', 0, 0);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(70, 10, $riparazione['URGENZA'] ?? '', 0, 1);
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(196, 10, $riparazione['ARTICOLO'] ?? '', 0, 1, 'C', true, '', 1);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            
            // Sezione numerata
            $pdf->Ln(3);
            $pdf->Rect(7, 104, 196, 35, 'D');
            $pdf->SetMargins(13, 5, 5);
            $pdf->SetFont('helvetica', 'B', 15);
            $pdf->Ln(3);
            $pdf->Cell(10, 5, 'NUMERATA DA RIPARARE:', 0, 1);
            $pdf->SetFont('helvetica', '', 13);
            $pdf->SetFillColor(240, 240, 240);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', 'B', 10);
            
            // Genera tabella numerata (20 colonne)
            $html = '<table style="border-collapse: collapse;"><tr style="background-color: #f2f2f2; text-align: center; font-weight: bold;">';
            for ($i = 1; $i <= 20; $i++) {
                $n_value = $riparazione['N' . str_pad($i, 2, '0', STR_PAD_LEFT)] ?? '';
                $html .= '<td style="border: 1px solid black; padding: 0px; text-align: center; vertical-align: middle;" width="26" height="20">' . $n_value . '</td>';
            }
            $html .= '</tr><tr>';
            for ($i = 1; $i <= 20; $i++) {
                $p_value = $riparazione['P' . str_pad($i, 2, '0', STR_PAD_LEFT)] ?? 0;
                $html .= '<td style="border: 1px solid black; padding: 0px; text-align: center; vertical-align: middle;" width="26" height="20">';
                $html .= ($p_value == 0) ? '&nbsp;' : $p_value;
                $html .= '</td>';
            }
            $html .= '</tr></table>';
            $pdf->Ln(5);
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Ln(5);
            
            // Motivo riparazione
            $pdf->SetFont('helvetica', 'B', 15);
            $pdf->Rect(7, 142, 196, 77, 'D');
            $pdf->Cell(20, 10, 'MOTIVO RIPARAZIONE', 0, 1);
            $pdf->SetFont('helvetica', '', 15);
            $pdf->SetCellPaddings(0, 0, 0, 0);
            if (($riparazione['URGENZA'] ?? '') === 'ALTA') {
                $pdf->SetTextColor(180, 180, 180);
                $pdf->SetFont('helvetica', 'B', 30);
                $pdf->Cell(10, 120, 'URGENTE', 0, 0);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('helvetica', '', 13);
            }
            $pdf->MultiCell(155, 20, $riparazione['CAUSALE'] ?? '', 0, 'L', false, 0, '', '', true, 0, false, true, 0, 'T', false);
            $pdf->SetMargins(7, 7, 7);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->Ln(70);
            
            $pdf->SetMargins(13, 5, 5);
            $pdf->Cell(196, 10, $riparazione['CODICE'] ?? '', 0, 1, 'C', true, '', 1);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->Ln(5);
            
            // Footer
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->SetTextColor(115, 115, 115);
            $pdf->Cell(60, 10, 'RIPARAZIONE N°:', 0, 0);
            $pdf->SetFont('helvetica', '', 25);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(128, 128, 128);
            $pdf->Cell(30, 10, $riparazione['IDRIP'], 1, 0, 'C', true);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->Cell(30, 10, '', 0, 0);
            $pdf->SetFillColor(222, 222, 222);
            $pdf->Cell(60, 10, $riparazione['REPARTO'] ?? '', 1, 1, 'C', true);
            $pdf->SetMargins(7, 7, 7);
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Ln(5);
            
            $pdf->Cell(50, 10, 'CEDOLA CREATA IL:', 0, 0, 'R');
            $pdf->Cell(60, 10, $riparazione['DATA'] ?? '', 0, 0, 'R');
            $pdf->Line(10, 263, 200, 263);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(80, 10, $riparazione['UTENTE'] ?? $_SESSION['username'], 0, 1, 'R');
            $pdf->Ln(3);
            
            // QR Code (optional - richiede URL mobile)
            /*
            $pdf->SetFont('helvetica', '', 12);
            $url = BASE_URL . '/riparazioni/' . $id;
            $qrCodeSize = 20;
            $qrCodeX = 95;
            $qrCodeY = 265;
            
            $style = array(
                'border' => 0,
                'vpadding' => 'auto',
                'hpadding' => 'auto',
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false,
                'module_width' => 0.7,
                'module_height' => 0.7
            );
            $pdf->write2DBarcode($url, 'QRCODE,L', $qrCodeX, $qrCodeY, $qrCodeSize, $qrCodeSize, $style, 'N');
            */
            
            // Output del PDF
            $filename = 'CEDOLA_' . $id . '_' . date('Ymd_His') . '.pdf';
            $pdf->Output($filename, 'D');
            
            // Log attività
            $this->logActivity('REPAIRS', 'PRINT', "Stampata cedola riparazione {$id}");
            
        } catch (Exception $e) {
            error_log("Errore stampa PDF riparazione {$id}: " . $e->getMessage());
            
            if ($this->isAjax()) {
                $this->json(['error' => 'Errore durante la generazione del PDF'], 500);
            } else {
                $_SESSION['alert_error'] = 'Errore durante la generazione del PDF.';
                $this->redirect($this->url('/riparazioni/' . $id));
            }
        }
    }
}