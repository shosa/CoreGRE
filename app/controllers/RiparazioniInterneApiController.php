<?php
/**
 * Riparazioni Interne API Controller
 * API per mobile app riparazioni interne - Segue pattern QualityApiController
 */

use App\Models\InternalRepair;
use App\Models\QualityOperator;
use App\Models\Reparti;
use App\Models\Laboratory;
use App\Models\Line;
use App\Models\QualityDepartment;
use App\Models\CoreData;
use App\Models\IdSize;
use App\Models\Tabid;

class RiparazioniInterneApiController extends BaseController
{
    /**
     * Constructor - CORS gestiti globalmente in index.php
     */
    public function __construct()
    {
        parent::__construct();

        if (!(isset($_GET['action']) && $_GET['action'] === 'pdf')) {
            header("Content-Type: application/json; charset=UTF-8");
        }
    }

    /**
     * API per ottenere lista riparazioni interne
     * GET /api/riparazioni-interne
     */
    public function index()
    {
        if (!$this->isGet()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            // Parametri di paginazione e filtri
            $page = intval($this->input('page', 1));
            $limit = intval($this->input('limit', 20));
            $search = trim($this->input('search', ''));
            $operatore = trim($this->input('operatore', ''));
            $completa = $this->input('completa', ''); // '' = tutte, '0' = incomplete, '1' = complete

            $offset = ($page - 1) * $limit;

            // Conta totale - ELOQUENT
            $countQuery = InternalRepair::query();
            if (!empty($search)) {
                $countQuery->where(function($q) use ($search) {
                    $q->where('ID', 'LIKE', "%$search%")
                      ->orWhere('ARTICOLO', 'LIKE', "%$search%")
                      ->orWhere('CODICE', 'LIKE', "%$search%")
                      ->orWhere('CARTELLINO', 'LIKE', "%$search%");
                });
            }
            if (!empty($operatore)) {
                $countQuery->where('OPERATORE', $operatore);
            }
            if ($completa !== '') {
                $countQuery->where('COMPLETA', intval($completa));
            }
            $total = $countQuery->count();

            // Query principale con JOIN - ELOQUENT
            $query = InternalRepair::select('rip_interne.*',
                       'o.full_name as operatore_nome',
                       'rep.nome_reparto as reparto_nome',
                       'lin.descrizione as linea_descrizione')
                ->leftJoin('cq_operators as o', 'rip_interne.OPERATORE', '=', 'o.user')
                ->leftJoin('cq_departments as rep', 'rip_interne.REPARTO', '=', 'rep.id')
                ->leftJoin('rip_linee as lin', 'rip_interne.LINEA', '=', 'lin.sigla');

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('rip_interne.ID', 'LIKE', "%$search%")
                      ->orWhere('rip_interne.ARTICOLO', 'LIKE', "%$search%")
                      ->orWhere('rip_interne.CODICE', 'LIKE', "%$search%")
                      ->orWhere('rip_interne.CARTELLINO', 'LIKE', "%$search%");
                });
            }
            if (!empty($operatore)) {
                $query->where('rip_interne.OPERATORE', $operatore);
            }
            if ($completa !== '') {
                $query->where('rip_interne.COMPLETA', intval($completa));
            }

            $riparazioni = $query
                ->orderBy('rip_interne.DATA', 'DESC')
                ->orderBy('rip_interne.ID', 'DESC')
                ->limit($limit)
                ->offset($offset)
                ->get()
                ->toArray();

            // Calcola QTA per ogni riparazione (somma P01-P20)
            foreach ($riparazioni as &$riparazione) {
                $qta = 0;
                for ($i = 1; $i <= 20; $i++) {
                    $field = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    if ($i == 3)
                        $field = 'P04'; // Gestisce ordine particolare P03/P04
                    if ($i == 4)
                        $field = 'P03';
                    $qta += intval($riparazione[$field] ?? 0);
                }
                $riparazione['QTA_CALCOLATA'] = $qta;
            }

            $this->json([
                'status' => 'success',
                'message' => 'Riparazioni recuperate con successo',
                'data' => [
                    'riparazioni' => $riparazioni,
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => $total,
                        'pages' => ceil($total / $limit)
                    ]
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore API riparazioni-interne index: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore nel recupero dei dati'
            ]);
        }
    }

    /**
     * API per ottenere dettagli singola riparazione
     * GET /api/riparazioni-interne/show?id=000001
     */
    public function show()
    {
        if (!$this->isGet()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $id = $this->input('id');

            if (empty($id)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'ID riparazione mancante'
                ]);
                return;
            }

            // ELOQUENT
            $riparazione = InternalRepair::select('rip_interne.*',
                       'o.full_name as operatore_nome',
                       'rep.nome_reparto as reparto_nome',
                       'lin.descrizione as linea_descrizione',
                       'num.N01', 'num.N02', 'num.N03', 'num.N04', 'num.N05', 'num.N06', 'num.N07', 'num.N08', 'num.N09', 'num.N10',
                       'num.N11', 'num.N12', 'num.N13', 'num.N14', 'num.N15', 'num.N16', 'num.N17', 'num.N18', 'num.N19', 'num.N20')
                ->leftJoin('cq_operators as o', 'rip_interne.OPERATORE', '=', 'o.user')
                ->leftJoin('cq_departments as rep', 'rip_interne.REPARTO', '=', 'rep.id')
                ->leftJoin('rip_linee as lin', 'rip_interne.LINEA', '=', 'lin.sigla')
                ->leftJoin('rip_idnumerate as num', 'rip_interne.NU', '=', 'num.id')
                ->where('rip_interne.ID', $id)
                ->first();

            if (!$riparazione) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Riparazione non trovata'
                ]);
                return;
            }

            $riparazione = $riparazione->toArray();

            // Calcola QTA
            $qta = 0;
            for ($i = 1; $i <= 20; $i++) {
                $field = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                if ($i == 3)
                    $field = 'P04';
                if ($i == 4)
                    $field = 'P03';
                $qta += intval($riparazione[$field] ?? 0);
            }
            $riparazione['QTA_CALCOLATA'] = $qta;

            // Prepara array delle taglie con nomi
            $taglie = [];
            for ($i = 1; $i <= 20; $i++) {
                $field = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                if ($i == 3)
                    $field = 'P04';
                if ($i == 4)
                    $field = 'P03';

                $nField = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                if ($i == 3)
                    $nField = 'N04';
                if ($i == 4)
                    $nField = 'N03';

                $taglie[] = [
                    'numero' => $i,
                    'nome' => $riparazione[$nField] ?? '',
                    'quantita' => intval($riparazione[$field] ?? 0)
                ];
            }
            $riparazione['TAGLIE'] = $taglie;

            $this->json([
                'status' => 'success',
                'message' => 'Dettagli riparazione recuperati',
                'data' => $riparazione
            ]);

        } catch (Exception $e) {
            error_log("Errore API riparazioni-interne show: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per creare nuova riparazione interna
     * POST /api/riparazioni-interne
     */
    public function store()
    {
        if (!$this->isPost()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $data = $this->getJsonInput();

            // Validazione campi obbligatori
            $required = ['ARTICOLO', 'CODICE', 'CARTELLINO', 'CAUSALE', 'OPERATORE'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    $this->json([
                        'status' => 'error',
                        'message' => "Campo obbligatorio mancante: $field"
                    ]);
                    return;
                }
            }

            // Genera nuovo ID dalla tabella tabid - ELOQUENT
            $maxId = Tabid::max('ID') ?? 0;
            $newId = $maxId + 1;

            // Inserisci nuovo ID in tabid - ELOQUENT
            Tabid::create(['ID' => $newId]);

            // Calcola QTA totale dalle taglie
            $qta = 0;
            for ($i = 1; $i <= 20; $i++) {
                $field = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                if ($i == 3)
                    $field = 'P04';
                if ($i == 4)
                    $field = 'P03';
                $qta += intval($data[$field] ?? 0);
            }

            // Prepara dati per inserimento
            $insertData = [
                'ID' => $newId,
                'ARTICOLO' => $data['ARTICOLO'],
                'CODICE' => $data['CODICE'],
                'CARTELLINO' => $data['CARTELLINO'],
                'REPARTO' => $data['REPARTO'] ?? null,
                'CAUSALE' => $data['CAUSALE'],
                'DATA' => date('d/m/Y'),
                'NU' => $data['NU'] ?? null,
                'OPERATORE' => $data['OPERATORE'],
                'CLIENTE' => $data['CLIENTE'] ?? null,
                'COMMESSA' => $data['COMMESSA'] ?? null,
                'LINEA' => $data['LINEA'] ?? null,
                'QTA' => strval($qta),
                'COMPLETA' => 0
            ];

            // Aggiungi quantità per taglie
            for ($i = 1; $i <= 20; $i++) {
                $field = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                if ($i == 3)
                    $field = 'P04';
                if ($i == 4)
                    $field = 'P03';
                $insertData[$field] = intval($data[$field] ?? 0);
            }

            // Inserisci riparazione - ELOQUENT
            InternalRepair::create($insertData);

            $this->logActivity('RIPARAZIONI_INTERNE_API', 'CREATE', "Creata riparazione interna: {$insertData['ID']}");

            $this->json([
                'status' => 'success',
                'message' => 'Riparazione creata con successo',
                'data' => [
                    'id' => $insertData['ID'],
                    'qta_totale' => $qta
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore API riparazioni-interne store: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore durante la creazione'
            ]);
        }
    }

    /**
     * API per aggiornare riparazione esistente
     * POST /api/riparazioni-interne/update
     */
    public function update()
    {
        if (!$this->isPost()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $data = $this->getJsonInput();
            $id = $data['id'] ?? null;

            if (empty($id)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'ID riparazione mancante'
                ]);
                return;
            }

            // Verifica esistenza e stato - ELOQUENT
            $existing = InternalRepair::find($id);
            if (!$existing) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Riparazione non trovata'
                ]);
                return;
            }

            if ($existing->COMPLETA == 1) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Non è possibile modificare una riparazione completata'
                ]);
                return;
            }

            // Calcola QTA totale dalle taglie
            $qta = 0;
            for ($i = 1; $i <= 20; $i++) {
                $field = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                if ($i == 3)
                    $field = 'P04';
                if ($i == 4)
                    $field = 'P03';
                $qta += intval($data[$field] ?? $existing->{$field} ?? 0);
            }

            // Prepara campi aggiornabili
            $allowedFields = [
                'ARTICOLO',
                'CODICE',
                'CARTELLINO',
                'REPARTO',
                'CAUSALE',
                'NU',
                'CLIENTE',
                'COMMESSA',
                'LINEA'
            ];

            // Aggiorna riparazione - ELOQUENT
            $updateData = [];
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }

            for ($i = 1; $i <= 20; $i++) {
                $field = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                if ($i == 3) $field = 'P04';
                if ($i == 4) $field = 'P03';

                if (isset($data[$field])) {
                    $updateData[$field] = intval($data[$field]);
                }
            }

            $updateData['QTA'] = strval($qta);

            $existing->update($updateData);

            $this->logActivity('RIPARAZIONI_INTERNE_API', 'UPDATE', "Aggiornata riparazione interna: $id");

            $this->json([
                'status' => 'success',
                'message' => 'Riparazione aggiornata con successo',
                'data' => [
                    'id' => $id,
                    'qta_totale' => $qta
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore API riparazioni-interne update: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore durante l\'aggiornamento'
            ]);
        }
    }

    /**
     * API per completare riparazione
     * POST /api/riparazioni-interne/complete
     */
    public function complete()
    {
        if (!$this->isPost()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $data = $this->getJsonInput();
            $id = $data['id'] ?? null;

            if (empty($id)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'ID riparazione mancante'
                ]);
                return;
            }

            // Verifica esistenza - ELOQUENT
            $existing = InternalRepair::find($id);
            if (!$existing) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Riparazione non trovata'
                ]);
                return;
            }

            if ($existing->COMPLETA == 1) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Riparazione già completata'
                ]);
                return;
            }

            // Completa riparazione - ELOQUENT
            $existing->update(['COMPLETA' => 1]);

            $this->logActivity('RIPARAZIONI_INTERNE_API', 'COMPLETE', "Completata riparazione interna: $id");

            $this->json([
                'status' => 'success',
                'message' => 'Riparazione completata con successo'
            ]);

        } catch (Exception $e) {
            error_log("Errore API riparazioni-interne complete: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per eliminare riparazione
     * POST /api/riparazioni-interne/delete
     */
    public function delete()
    {
        if (!$this->isPost()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $data = $this->getJsonInput();
            $id = $data['id'] ?? null;

            if (empty($id)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'ID riparazione mancante'
                ]);
                return;
            }

            // Verifica esistenza e stato - ELOQUENT
            $existing = InternalRepair::find($id);
            if (!$existing) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Riparazione non trovata'
                ]);
                return;
            }

            if ($existing->COMPLETA == 1) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Non è possibile eliminare una riparazione completata'
                ]);
                return;
            }

            // Elimina riparazione - ELOQUENT
            $existing->delete();

            $this->logActivity('RIPARAZIONI_INTERNE_API', 'DELETE', "Eliminata riparazione interna: $id");

            $this->json([
                'status' => 'success',
                'message' => 'Riparazione eliminata con successo'
            ]);

        } catch (Exception $e) {
            error_log("Errore API riparazioni-interne delete: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per verificare esistenza cartellino - STESSA LOGICA DI QualityApiController
     * POST /api/riparazioni-interne/check-cartellino
     */
    public function checkCartellino()
    {
        if (!$this->isPost()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $data = $this->getJsonInput();
            $cartellino = trim($data['cartellino'] ?? '');

            if (empty($cartellino)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Parametro cartellino mancante'
                ]);
                return;
            }

            // Verifica esistenza cartellino - ELOQUENT
            $exists = CoreData::where('Cartel', $cartellino)->exists();

            if ($exists) {
                // Recupera dati completi del cartellino - ELOQUENT
                $cartellino_record = CoreData::select('Cartel', 'Commessa Cli', 'Articolo', 'Descrizione Articolo',
                           'Ragione Sociale', 'Tot', 'Ln', 'Nu')
                    ->where('Cartel', $cartellino)
                    ->first();

                $cartellino_data = $cartellino_record->toArray();

                $this->json([
                    'status' => 'success',
                    'exists' => true,
                    'message' => 'Cartellino trovato',
                    'data' => [
                        'cartellino' => $cartellino_data['Cartel'],
                        'commessa' => $cartellino_data['Commessa Cli'],
                        'codice_articolo' => $cartellino_data['Articolo'],
                        'descrizione_articolo' => $cartellino_data['Descrizione Articolo'],
                        'cliente' => $cartellino_data['Ragione Sociale'],
                        'paia' => $cartellino_data['Tot'],
                        'linea' => $cartellino_data['Ln'],
                        'nu' => $cartellino_data['Nu']
                    ]
                ]);
            } else {
                $this->json([
                    'status' => 'success',
                    'exists' => false,
                    'message' => 'Cartellino non trovato'
                ]);
            }

        } catch (Exception $e) {
            error_log("Errore API riparazioni-interne check-cartellino: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per verificare esistenza commessa - STESSA LOGICA DI QualityApiController
     * POST /api/riparazioni-interne/check-commessa
     */
    public function checkCommessa()
    {
        if (!$this->isPost()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $data = $this->getJsonInput();
            $commessa = trim($data['commessa'] ?? '');

            if (empty($commessa)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Parametro commessa mancante'
                ]);
                return;
            }

            // Query ELOQUENT
            $commessa_record = CoreData::select('Cartel', 'Articolo', 'Descrizione Articolo', 'Ragione Sociale', 'Tot', 'Ln', 'Nu')
                ->where('Commessa Cli', $commessa)
                ->first();

            if ($commessa_record) {
                $result = $commessa_record->toArray();
                $this->json([
                    'status' => 'success',
                    'exists' => true,
                    'message' => 'Commessa trovata',
                    'data' => [
                        'cartellino' => $result['cartel'],
                        'codice_articolo' => $result['Articolo'],
                        'descrizione_articolo' => $result['Descrizione Articolo'],
                        'cliente' => $result['Ragione Sociale'],
                        'paia' => $result['Tot'],
                        'linea' => $result['Ln'],
                        'nu' => $result['Nu']
                    ]
                ]);
            } else {
                $this->json([
                    'status' => 'success',
                    'exists' => false,
                    'message' => 'Commessa non trovata'
                ]);
            }

        } catch (Exception $e) {
            error_log("Errore API riparazioni-interne check-commessa: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per ottenere opzioni - Simile a quality/options
     * POST /api/riparazioni-interne/options
     */
    public function getOptions()
    {
        if (!$this->isPost()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $data = $this->getJsonInput();
            $cartellino = trim($data['cartellino'] ?? '');

            $calzateOptions = [];

            // Se è stato fornito un cartellino, recupera le calzate specifiche - ELOQUENT
            if (!empty($cartellino)) {
                $datiResult = CoreData::select('Nu')->where('Cartel', $cartellino)->first();

                if ($datiResult && !empty($datiResult->Nu)) {
                    $idNumerate = IdSize::find($datiResult->Nu);

                    if ($idNumerate) {
                        $idNumerateArray = $idNumerate->toArray();
                        for ($j = 1; $j <= 20; $j++) {
                            $field = 'N' . str_pad($j, 2, '0', STR_PAD_LEFT);
                            if (!empty($idNumerateArray[$field])) {
                                $calzateOptions[] = $idNumerateArray[$field];
                            }
                        }
                    }
                }
            }



            // Recupera i reparti - ELOQUENT
            $repartiOptions = QualityDepartment::select('id', 'nome_reparto')
                ->orderBy('nome_reparto', 'ASC')
                ->get()
                ->map(function($r) {
                    return ['id' => $r->id, 'nome' => $r->nome_reparto];
                })
                ->toArray();

            // Recupera le rip_linee - ELOQUENT
            $rip_lineeOptions = Line::select('sigla', 'descrizione')
                ->orderBy('sigla', 'ASC')
                ->get()
                ->map(function($l) {
                    return ['sigla' => $l->sigla, 'descrizione' => $l->descrizione];
                })
                ->toArray();

            // Recupera i laboratori - ELOQUENT
            $laboratoriOptions = Laboratory::select('id', 'nome')
                ->orderBy('nome', 'ASC')
                ->get()
                ->map(function($lab) {
                    return ['id' => $lab->id, 'nome' => $lab->nome];
                })
                ->toArray();

            // Causali più frequenti - ELOQUENT
            $causaliOptions = InternalRepair::select('CAUSALE')
                ->selectRaw('COUNT(*) as freq')
                ->whereNotNull('CAUSALE')
                ->where('CAUSALE', '!=', '')
                ->groupBy('CAUSALE')
                ->orderBy('freq', 'DESC')
                ->limit(20)
                ->get()
                ->pluck('CAUSALE')
                ->toArray();

            $this->json([
                'status' => 'success',
                'message' => 'Opzioni recuperate con successo',
                'data' => [
                    'calzate' => $calzateOptions,
                    'reparti' => $repartiOptions,
                    'rip_linee' => $rip_lineeOptions,
                    'laboratori' => $laboratoriOptions,
                    'causali_frequenti' => $causaliOptions
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore API riparazioni-interne options: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per ottenere dettagli completi cartellino per riparazioni
     * POST /api/riparazioni-interne/cartellino-details
     */
    public function getCartellinoDetails()
    {
        if (!$this->isPost()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $data = $this->getJsonInput();
            $cartellino = trim($data['cartellino'] ?? '');

            if (empty($cartellino)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Parametro cartellino mancante'
                ]);
                return;
            }

            // Ottieni informazioni del cartellino - ELOQUENT
            $informazione = CoreData::where('Cartel', $cartellino)->first();

            if (!$informazione) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Informazioni cartellino non trovate'
                ]);
                return;
            }

            // Ottieni informazioni sulla linea - ELOQUENT
            $descrizioneLinea = Line::where('sigla', $informazione->Ln)->first();

            // Ottieni informazioni sulla numerata - ELOQUENT
            $numerataInfo = null;
            if (!empty($informazione->Nu)) {
                $numerataInfo = IdSize::find($informazione->Nu);
            }

            // Prepara taglie disponibili
            $taglie = [];
            if ($numerataInfo) {
                $numerataArray = $numerataInfo->toArray();
                for ($i = 1; $i <= 20; $i++) {
                    $field = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    if (!empty($numerataArray[$field])) {
                        $taglie[] = [
                            'numero' => $i,
                            'nome' => $numerataArray[$field],
                            'field' => 'P' . str_pad($i, 2, '0', STR_PAD_LEFT)
                        ];
                    }
                }
            }

            // Genera nuovo ID per riparazione - ELOQUENT
            $maxId = Tabid::max('ID') ?? 0;
            $newId = $maxId + 1;
            $newIdFormatted = $newId;

            $this->json([
                'status' => 'success',
                'message' => 'Dettagli cartellino trovati',
                'data' => [
                    'cartellino_info' => [
                        'cartellino' => $informazione->Cartel,
                        'commessa' => $informazione->{'Commessa Cli'},
                        'codice_articolo' => $informazione->Articolo,
                        'descrizione_articolo' => $informazione->{'Descrizione Articolo'},
                        'cliente' => $informazione->{'Ragione Sociale'},
                        'paia' => $informazione->Tot,
                        'nu' => $informazione->Nu
                    ],
                    'linea_info' => [
                        'sigla' => $informazione->Ln,
                        'descrizione' => $descrizioneLinea->descrizione ?? ''
                    ],
                    'riparazione_info' => [
                        'nuovo_id' => $newIdFormatted,
                        'data' => date('d/m/Y'),
                        'taglie_disponibili' => $taglie
                    ]
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore API riparazioni-interne cartellino-details: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per ottenere statistiche riparazioni
     * GET /api/riparazioni-interne/stats
     */
    public function getStats()
    {
        if (!$this->isGet()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $operatore = trim($this->input('operatore', ''));
            $date = $this->input('data', date('Y-m-d'));

            // Statistiche generali
            $stats = [];

            // Totale riparazioni - ELOQUENT
            $totalQuery = InternalRepair::query();
            if (!empty($operatore)) {
                $totalQuery->where('OPERATORE', $operatore);
            }
            $stats['totale'] = $totalQuery->count();

            // Riparazioni incomplete - ELOQUENT
            $incompleteQuery = InternalRepair::where('COMPLETA', 0);
            if (!empty($operatore)) {
                $incompleteQuery->where('OPERATORE', $operatore);
            }
            $stats['incomplete'] = $incompleteQuery->count();

            // Riparazioni del giorno - ELOQUENT
            $todayQuery = InternalRepair::where('DATA', date('d/m/Y', strtotime($date)));
            if (!empty($operatore)) {
                $todayQuery->where('OPERATORE', $operatore);
            }
            $stats['oggi'] = $todayQuery->count();

            // Top 5 reparti - REFACTORED TO ELOQUENT
            $repartiQuery = InternalRepair::select('rip_interne.REPARTO')
                ->selectRaw('COUNT(*) as count')
                ->leftJoin('cq_departments as rep', 'rip_interne.REPARTO', '=', 'rep.id')
                ->addSelect('rep.nome_reparto')
                ->whereNotNull('rip_interne.REPARTO')
                ->where('rip_interne.REPARTO', '!=', '');

            if (!empty($operatore)) {
                $repartiQuery->where('rip_interne.OPERATORE', $operatore);
            }

            $stats['reparti'] = $repartiQuery
                ->groupBy('rip_interne.REPARTO', 'rep.nome_reparto')
                ->orderBy('count', 'DESC')
                ->limit(5)
                ->get()
                ->toArray();

            // Causali più frequenti - REFACTORED TO ELOQUENT
            $causaliQuery = InternalRepair::select('CAUSALE')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('CAUSALE')
                ->where('CAUSALE', '!=', '');

            if (!empty($operatore)) {
                $causaliQuery->where('OPERATORE', $operatore);
            }

            $stats['causali'] = $causaliQuery
                ->groupBy('CAUSALE')
                ->orderBy('count', 'DESC')
                ->limit(5)
                ->get()
                ->toArray();

            $this->json([
                'status' => 'success',
                'message' => 'Statistiche recuperate con successo',
                'data' => $stats
            ]);

        } catch (Exception $e) {
            error_log("Errore API riparazioni-interne stats: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * Utility per ottenere input JSON - ESATTO COME QualityApiController
     */
    private function getJsonInput()
    {
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Fallback a POST normale se JSON non valido
            return $_POST;
        }

        return $data ?: [];
    }

    /**
     * Check if request method is PUT
     */
    private function isPut()
    {
        return $_SERVER['REQUEST_METHOD'] === 'PUT';
    }

    /**
     * Check if request method is DELETE
     */
    private function isDelete()
    {
        return $_SERVER['REQUEST_METHOD'] === 'DELETE';
    }

    /**
     * API per generare PDF cedola riparazione interna
     * GET /api/riparazioni-interne/pdf?id=000001
     */
    public function generatePdf()
    {
        try {
            $id = $_GET['id'] ?? null;

            if (empty($id)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'ID riparazione mancante'
                ]);
                return;
            }

            // Recupera i dati della riparazione interna - ELOQUENT
            $riparazione = InternalRepair::select('rip_interne.*',
                           'r.nome_reparto as NOMEREPARTO', 'd.Nu as NU_NUMERATA',
                           'd.Commessa Cli as COMMESSA', 'd.Ragione Sociale', 'd.Tot', 'd.Ln')
                    ->leftJoin('core_dati as d', 'rip_interne.CARTELLINO', '=', 'd.Cartel')
                    ->leftJoin('cq_departments as r', 'rip_interne.REPARTO', '=', 'r.id')
                    ->where('rip_interne.ID', $id)
                    ->first();

            if (!$riparazione) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Riparazione non trovata'
                ]);
                return;
            }

            $riparazione = $riparazione->toArray();

            // Recupera dati numerata se disponibile - ELOQUENT
            $numerata_data = [];
            if (!empty($riparazione['NU_NUMERATA'])) {
                $numerata = IdSize::find($riparazione['NU_NUMERATA']);
                if ($numerata) {
                    $numerata_data = $numerata->toArray();
                }
            }

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
            $pdf->MultiCell(180, 10, "RIPARAZIONE INTERNA", 0, 'L', false, 1, 60, 12, true, 0, false, true, 0, 'T', false);
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
            $pdf->Cell(155, 10, 'REPARTO:', 0, 0);
            $pdf->Cell(50, 10, '', 0, 1);
            $pdf->SetFont('helvetica', 'B', 25);
            $pdf->Cell(155, 10, $riparazione['NOMEREPARTO'] ?? '', 0, 0);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(50, 10, '', 0, 1);
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
            $pdf->Cell(35, 10, '', 0, 0);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(70, 10, '', 0, 1);
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(196, 10, $riparazione['ARTICOLO'] ?? '', 0, 1, 'C', true, '', 1);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            // Sezione numerata - usa dati da rip_interne (P01-P20)
            $pdf->Ln(3);
            $pdf->Rect(7, 104, 196, 35, 'D');
            $pdf->SetMargins(13, 5, 5);
            $pdf->SetFont('helvetica', 'B', 15);
            $pdf->Ln(3);
            $pdf->Cell(10, 5, 'TAGLIE DA RIPARARE:', 0, 1);
            $pdf->SetFont('helvetica', '', 13);
            $pdf->SetFillColor(240, 240, 240);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', 'B', 10);

            // Genera tabella taglie (20 colonne) - prima riga numerata, seconda riga quantità
            $html = '<table style="border-collapse: collapse;"><tr style="background-color: #f2f2f2; text-align: center; font-weight: bold;">';

            // Prima riga: numeri taglia dalla numerata se disponibile
            for ($i = 1; $i <= 20; $i++) {
                $n_field = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $n_value = $numerata_data[$n_field] ?? ($i + 34); // Default progression 35,36,37...
                $html .= '<td style="border: 1px solid black; padding: 0px; text-align: center; vertical-align: middle;" width="26" height="20">' . $n_value . '</td>';
            }
            $html .= '</tr><tr>';

            // Seconda riga: quantità da rip_interne (P01-P20)
            for ($i = 1; $i <= 20; $i++) {
                $p_field = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $p_value = $riparazione[$p_field] ?? 0;
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
            $pdf->SetTextColor(180, 180, 180);
            $pdf->SetFont('helvetica', 'B', 30);
            $pdf->Cell(10, 120, 'INTERNA', 0, 0);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 13);

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
            $pdf->Cell(60, 10, 'INTERNA N°:', 0, 0);
            $pdf->SetFont('helvetica', '', 25);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(128, 128, 128);
            $pdf->Cell(30, 10, $riparazione['ID'], 1, 0, 'C', true);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->Cell(30, 10, '', 0, 0);
            $pdf->SetFillColor(222, 222, 222);
            $pdf->Cell(60, 10, $riparazione['OPERATORE'] ?? '', 1, 1, 'C', true);
            $pdf->SetMargins(7, 7, 7);
            $pdf->SetFont('helvetica', '', 12);
            $pdf->Ln(5);

            $pdf->Cell(50, 10, 'CEDOLA CREATA IL:', 0, 0, 'R');
            $pdf->Cell(60, 10, $riparazione['DATA'] ?? '', 0, 0, 'R');
            $pdf->Line(10, 263, 200, 263);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(80, 10, $riparazione['OPERATORE'] ?? 'Sistema', 0, 1, 'R');
            $pdf->Ln(3);

            // Output del PDF
            $filename = 'CEDOLA_INTERNA_' . $id . '_' . date('Ymd_His') . '.pdf';

            // Controlla se è una richiesta per base64 (da mobile app)
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $isCapacitorApp = strpos($userAgent, 'Capacitor') !== false;
            $hasAppTypeHeader = isset($_SERVER['HTTP_X_APP_TYPE']) && $_SERVER['HTTP_X_APP_TYPE'] === 'repairs';

            if ($isCapacitorApp || $hasAppTypeHeader) {
                // Per app mobile: restituisci base64 puro per il plugin Printer
                $pdfBinary = $pdf->Output($filename, 'S'); // 'S' per string binaria
                $base64String = base64_encode($pdfBinary);

                header('Content-Type: application/json');
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');

                echo json_encode([
                    'success' => true,
                    'data' => $base64String,
                    'filename' => $filename,
                    'size' => strlen($pdfBinary)
                ]);
            } else {
                // Per browser web: output normale
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . $filename . '"');
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');

                // Header CORS per iframe embedding
                header('X-Frame-Options: SAMEORIGIN');
                header('Content-Security-Policy: frame-ancestors \'self\' https://localhost https://www.mgmshoes.it');

                $pdf->Output($filename, 'E'); // 'I' per inline nel browser
            }

            // Log attività
            $this->logActivity('RIPARAZIONI_INTERNE_API', 'PRINT', "Generato PDF riparazione interna: {$id}");

        } catch (Exception $e) {
            error_log("Errore API riparazioni-interne PDF: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Errore durante la generazione del PDF'
            ]);
        }
    }
}