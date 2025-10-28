<?php
/**
 * Mobile API Controller - Unificato
 * Controller centralizzato per tutte le API mobile (Quality + Riparazioni Interne)
 */

use App\Models\QualityRecord;
use App\Models\QualityException;
use App\Models\QualityOperator;
use App\Models\InternalRepair;
use App\Models\Reparti;
use App\Models\Laboratory;
use App\Models\Line;
use App\Models\CoreData;
use App\Models\IdSize;
use App\Models\QualityDepartment;
use App\Models\QualityDefectType;

class MobileApiController extends BaseController
{
    /**
     * Constructor - CORS gestiti globalmente in index.php
     */
    public function __construct()
    {
        parent::__construct();

        // Solo Content-Type - CORS gestiti in index.php per evitare duplicati
        header("Content-Type: application/json; charset=UTF-8");
    }

    /**
     * API Login Unificato - per tutte le app mobile
     * POST /api/mobile/login
     *
     * Supporta parametro 'app_type' per distinguere:
     * - 'quality' = App controllo qualità
     * - 'repairs' = App riparazioni interne
     */
    public function login()
    {
        if (!$this->isPost()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $data = $this->getJsonInput();
            $action = $data['action'] ?? '';
            $appType = $data['app_type'] ?? $this->getHeader('X-App-Type') ?? 'quality'; // default quality per compatibilità

            if ($action === 'get_users') {
                // Lista tutti gli operatori - ELOQUENT
                $users = QualityOperator::select('id', 'user', 'full_name', 'reparto')
                    ->orderBy('user', 'ASC')
                    ->get()
                    ->toArray();

                $this->logActivity('MOBILE_API', 'GET_USERS', "Richiesta lista operatori app: $appType");

                $this->json([
                    'status' => 'success',
                    'message' => 'Utenti recuperati con successo',
                    'data' => $users,
                    'app_type' => $appType
                ]);

            } elseif ($action === 'login' && !empty($data['username']) && !empty($data['password'])) {
                // Verifica credenziali login - ELOQUENT
                $user = QualityOperator::select('id', 'user', 'full_name', 'reparto')
                    ->where('user', $data['username'])
                    ->where('pin', $data['password'])
                    ->first();

                if ($user) {
                    $userData = $user->toArray();

                    // Aggiungi informazioni specifiche per app
                    $userData['app_type'] = $appType;
                    $userData['permissions'] = $this->getUserPermissions($userData['id'], $appType);
                    $userData['features'] = $this->getAppFeatures($appType);

                    $this->logActivity('MOBILE_API', 'LOGIN_SUCCESS', "Login operatore: {$userData['user']} app: $appType");

                    $this->json([
                        'status' => 'success',
                        'message' => 'Login effettuato con successo',
                        'data' => $userData
                    ]);
                } else {
                    $this->logActivity('MOBILE_API', 'LOGIN_FAILED', "Tentativo login fallito: {$data['username']} app: $appType");

                    $this->json([
                        'status' => 'error',
                        'message' => 'Credenziali non valide'
                    ]);
                }
            } else {
                $this->json([
                    'status' => 'error',
                    'message' => 'Parametri mancanti o non validi'
                ]);
            }

        } catch (Exception $e) {
            error_log("Mobile API Login error: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per ottenere profilo operatore unificato
     * GET /api/mobile/profile?id=1
     */
    public function getProfile()
    {
        if (!$this->isGet()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $id = $this->input('id');
            $appType = $this->getHeader('X-App-Type') ?? 'quality';

            if (empty($id)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'ID operatore mancante'
                ]);
                return;
            }

            // ELOQUENT
            $operator = QualityOperator::select('id', 'user', 'full_name', 'reparto')
                ->where('id', $id)
                ->first();

            if (!$operator) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Operatore non trovato'
                ]);
                return;
            }

            // Converti a array
            $operatorData = $operator->toArray();

            // Statistiche specifiche per app
            $stats = $this->getOperatorStats($operatorData['user'], $appType);
            $operatorData['statistiche'] = $stats;
            $operatorData['app_type'] = $appType;

            $this->json([
                'status' => 'success',
                'message' => 'Profilo operatore recuperato',
                'data' => $operatorData
            ]);

        } catch (Exception $e) {
            error_log("Errore API mobile profile: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per ottenere riepilogo giornaliero unificato
     * GET /api/mobile/daily-summary?id=1&data=2025-09-19
     */
    public function getDailySummary()
    {
        if (!$this->isGet()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $id = $this->input('id');
            $date = $this->input('data', date('Y-m-d'));
            $appType = $this->getHeader('X-App-Type') ?? 'quality';

            if (empty($id)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'ID operatore mancante'
                ]);
                return;
            }

            // Recupera dati operatore - ELOQUENT
            $operator = QualityOperator::select('user', 'full_name')
                ->where('id', $id)
                ->first();

            if (!$operator) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Operatore non trovato'
                ]);
                return;
            }

            $operatorData = $operator->toArray();

            $dateFormatted = date('d/m/Y', strtotime($date));
            $summary = $this->getDailySummaryByApp($operatorData['user'], $dateFormatted, $appType);

            $this->json([
                'status' => 'success',
                'message' => 'Riepilogo giornaliero recuperato',
                'data' => [
                    'operatore' => $operatorData,
                    'data' => $dateFormatted,
                    'app_type' => $appType,
                    'summary' => $summary
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore API mobile daily-summary: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per ottenere dati di sistema comuni (reparti, linee, etc.)
     * GET /api/mobile/system-data
     */
    public function getSystemData()
    {
        if (!$this->isGet()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $type = $this->input('type', 'all'); // all, reparti, linee, taglie
            $nu = $this->input('nu'); // per taglie specifiche

            $data = [];

            if ($type === 'all' || $type === 'reparti') {
                // ELOQUENT
                $data['reparti'] = Reparti::select('sigla', 'nome_reparto as nome')
                    ->orderBy('nome_reparto', 'ASC')
                    ->get()
                    ->toArray();
            }

            if ($type === 'all' || $type === 'linee') {
                // ELOQUENT
                $data['linee'] = Line::select('sigla', 'descrizione')
                    ->orderBy('sigla', 'ASC')
                    ->get()
                    ->toArray();
            }

            if ($type === 'taglie' && !empty($nu)) {
                // ELOQUENT
                $numerata = IdSize::where('id', $nu)->first();

                if ($numerata) {
                    $numerataArray = $numerata->toArray();
                    $taglie = [];
                    for ($i = 1; $i <= 20; $i++) {
                        $field = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                        if ($i == 3) $field = 'N04';
                        if ($i == 4) $field = 'N03';

                        $taglia = $numerataArray[$field] ?? null;
                        if (!empty($taglia)) {
                            $taglie[] = [
                                'numero' => $i,
                                'nome' => $taglia,
                                'field' => $field
                            ];
                        }
                    }
                    $data['taglie'] = [
                        'numerata' => $nu,
                        'taglie' => $taglie
                    ];
                }
            }

            // Dati specifici per app Quality
            if ($type === 'all' || $type === 'quality') {
                // Reparti Hermes per CQ - ELOQUENT
                $data['reparti_hermes'] = QualityDepartment::select('id', 'nome_reparto')
                    ->where('attivo', 1)
                    ->orderBy('ordine', 'ASC')
                    ->orderBy('nome_reparto', 'ASC')
                    ->get()
                    ->toArray();

                // Tipi difetti per CQ - ELOQUENT
                $data['difetti'] = QualityDefectType::select('id', 'descrizione', 'categoria')
                    ->where('attivo', 1)
                    ->orderBy('ordine', 'ASC')
                    ->orderBy('descrizione', 'ASC')
                    ->get()
                    ->toArray();
            }

            // Dati specifici per app Repairs
            if ($type === 'all' || $type === 'repairs') {
                // Laboratori per riparazioni - ELOQUENT
                $data['laboratori'] = Laboratory::select('id', 'nome')
                    ->orderBy('nome', 'ASC')
                    ->get()
                    ->toArray();

                // Causali più frequenti - ELOQUENT
                $causaliQuery = InternalRepair::select('CAUSALE')
                    ->selectRaw('COUNT(*) as freq')
                    ->whereNotNull('CAUSALE')
                    ->where('CAUSALE', '!=', '')
                    ->groupBy('CAUSALE')
                    ->orderBy('freq', 'DESC')
                    ->limit(20)
                    ->get();

                $data['causali_frequenti'] = $causaliQuery->pluck('CAUSALE')->toArray();
            }

            $this->json([
                'status' => 'success',
                'message' => 'Dati di sistema recuperati',
                'data' => $data
            ]);

        } catch (Exception $e) {
            error_log("Errore API mobile system-data: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per verificare cartellino/commessa - UNIFICATA
     * POST /api/mobile/check-data
     */
    public function checkData()
    {
        if (!$this->isPost()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $data = $this->getJsonInput();
            $type = $data['type'] ?? ''; // 'cartellino' o 'commessa'
            $value = trim($data['value'] ?? '');

            if (empty($type) || empty($value)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Parametri type e value sono obbligatori'
                ]);
                return;
            }

            if ($type === 'cartellino') {
                // Verifica cartellino - ELOQUENT
                $exists = CoreData::where('Cartel', $value)->exists();

                if ($exists) {
                    $cartellino = CoreData::select('Cartel', 'Commessa Cli', 'Articolo', 'Descrizione Articolo',
                               'Ragione Sociale', 'Tot', 'Ln', 'Nu')
                        ->where('Cartel', $value)
                        ->first();

                    $cartellino_data = $cartellino->toArray();

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

            } elseif ($type === 'commessa') {
                // Verifica commessa - ELOQUENT
                $commessa = CoreData::select('Cartel', 'Articolo', 'Descrizione Articolo', 'Ragione Sociale', 'Tot', 'Ln', 'Nu')
                    ->where('Commessa Cli', $value)
                    ->first();

                if ($commessa) {
                    $result = $commessa->toArray();

                    $this->json([
                        'status' => 'success',
                        'exists' => true,
                        'message' => 'Commessa trovata',
                        'data' => [
                            'cartellino' => $result['Cartel'],
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
            } else {
                $this->json([
                    'status' => 'error',
                    'message' => 'Tipo non valido. Usa "cartellino" o "commessa"'
                ]);
            }

        } catch (Exception $e) {
            error_log("Errore API mobile check-data: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * Ottiene permessi utente per app specifica
     */
    private function getUserPermissions($operatorId, $appType)
    {
        $permissions = [];

        switch ($appType) {
            case 'quality':
                $permissions = ['cq_view', 'cq_create', 'cq_edit'];
                break;
            case 'repairs':
                $permissions = ['repairs_view', 'repairs_create', 'repairs_edit', 'repairs_complete'];
                break;
        }

        return $permissions;
    }

    /**
     * Ottiene features disponibili per app
     */
    private function getAppFeatures($appType)
    {
        $features = [];

        switch ($appType) {
            case 'quality':
                $features = [
                    'hermes_cq',
                    'photo_upload',
                    'barcode_scan',
                    'reports'
                ];
                break;
            case 'repairs':
                $features = [
                    'repair_crud',
                    'size_management',
                    'statistics',
                    'daily_summary'
                ];
                break;
        }

        return $features;
    }

    /**
     * Ottiene statistiche operatore per app specifica
     */
    private function getOperatorStats($username, $appType)
    {
        $stats = [];

        if ($appType === 'quality') {
            // Statistiche CQ - ELOQUENT
            $totalCq = QualityRecord::where('operatore', $username)->count();

            $todayCq = QualityRecord::where('operatore', $username)
                ->whereDate('data_controllo', date('Y-m-d'))
                ->count();

            $stats = [
                'totale_controlli' => $totalCq,
                'controlli_oggi' => $todayCq
            ];

        } elseif ($appType === 'repairs') {
            // Statistiche riparazioni - ELOQUENT
            $totalRepairs = InternalRepair::where('OPERATORE', $username)->count();

            $incompleteRepairs = InternalRepair::where('OPERATORE', $username)
                ->where('COMPLETA', 0)
                ->count();

            $todayRepairs = InternalRepair::where('OPERATORE', $username)
                ->where('DATA', date('d/m/Y'))
                ->count();

            $stats = [
                'totale_riparazioni' => $totalRepairs,
                'riparazioni_incomplete' => $incompleteRepairs,
                'riparazioni_oggi' => $todayRepairs
            ];
        }

        return $stats;
    }

    /**
     * Ottiene riepilogo giornaliero per app specifica
     */
    private function getDailySummaryByApp($username, $date, $appType)
    {
        if ($appType === 'quality') {
            // Riepilogo CQ - REFACTORED TO ELOQUENT
            $dateFormatted = date('Y-m-d', strtotime(str_replace('/', '-', $date)));

            $controls = QualityRecord::with('qualityExceptions')
                ->where('operatore', $username)
                ->whereDate('data_controllo', $dateFormatted)
                ->orderBy('data_controllo', 'DESC')
                ->get()
                ->map(function($record) {
                    return [
                        'id' => $record->id,
                        'numero_cartellino' => $record->numero_cartellino,
                        'articolo' => $record->articolo,
                        'reparto' => $record->reparto,
                        'ora_controllo' => $record->data_controllo->format('H:i:s'),
                        'tipo_cq' => $record->tipo_cq,
                        'numero_eccezioni' => $record->qualityExceptions->count()
                    ];
                })
                ->toArray();

            return [
                'controlli' => $controls,
                'totale' => count($controls)
            ];

        } elseif ($appType === 'repairs') {
            // Riepilogo riparazioni - ELOQUENT
            $repairs = InternalRepair::select('riparazioni_interne.*', 'rep.nome_reparto')
                ->leftJoin('reparti as rep', 'riparazioni_interne.REPARTO', '=', 'rep.sigla')
                ->where('riparazioni_interne.OPERATORE', $username)
                ->where('riparazioni_interne.DATA', $date)
                ->orderBy('riparazioni_interne.ID', 'DESC')
                ->get()
                ->toArray();

            return [
                'riparazioni' => $repairs,
                'totale' => count($repairs)
            ];
        }

        return [];
    }

    /**
     * Utility per ottenere header HTTP
     */
    private function getHeader($name)
    {
        return $_SERVER['HTTP_' . str_replace('-', '_', strtoupper($name))] ?? null;
    }

    /**
     * Utility per ottenere input JSON - ESATTO COME altri controller
     */
    private function getJsonInput()
    {
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $_POST;
        }

        return $data ?: [];
    }
}