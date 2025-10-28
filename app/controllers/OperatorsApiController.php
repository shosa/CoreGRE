<?php
/**
 * Operators API Controller
 * API per gestione operatori mobile app - Estende login QualityApiController
 */

use App\Models\QualityOperator;
use App\Models\InternalRepair;
use App\Models\Reparti;
use App\Models\Line;
use App\Models\IdSize;

class OperatorsApiController extends BaseController
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
     * API Login operatori - UTILIZZA STESSA LOGICA DI QualityApiController
     * POST /api/operators/login
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

            if ($action === 'get_users') {
                // Lista tutti gli operatori - ELOQUENT
                $users = QualityOperator::select('id', 'user', 'full_name', 'reparto')
                    ->orderBy('user', 'ASC')
                    ->get()
                    ->toArray();

                $this->logActivity('OPERATORS_API', 'GET_USERS', 'Richiesta lista operatori riparazioni interne');

                $this->json([
                    'status' => 'success',
                    'message' => 'Utenti recuperati con successo',
                    'data' => $users
                ]);

            } elseif ($action === 'login' && !empty($data['username']) && !empty($data['password'])) {
                // Verifica credenziali login - ELOQUENT
                $user = QualityOperator::select('id', 'user', 'full_name', 'reparto')
                    ->where('user', $data['username'])
                    ->where('pin', $data['password'])
                    ->first();

                if ($user) {
                    $userData = $user->toArray();

                    $this->logActivity('OPERATORS_API', 'LOGIN_SUCCESS', "Login operatore riparazioni interne: {$userData['user']}");

                    $this->json([
                        'status' => 'success',
                        'message' => 'Login effettuato con successo',
                        'data' => $userData
                    ]);
                } else {
                    $this->logActivity('OPERATORS_API', 'LOGIN_FAILED', "Tentativo login fallito: {$data['username']}");

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
            error_log("Operators API Login error: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per ottenere profilo operatore
     * GET /api/operators/profile?id=1
     */
    public function getProfile()
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

            $operatorData = $operator->toArray();

            // Statistiche operatore - ELOQUENT
            $stats = [
                'totale_riparazioni' => InternalRepair::where('OPERATORE', $operatorData['user'])->count(),
                'riparazioni_incomplete' => InternalRepair::where('OPERATORE', $operatorData['user'])->where('COMPLETA', 0)->count(),
                'riparazioni_oggi' => InternalRepair::where('OPERATORE', $operatorData['user'])->where('DATA', date('d/m/Y'))->count()
            ];

            $operatorData['statistiche'] = $stats;

            $this->json([
                'status' => 'success',
                'message' => 'Profilo operatore recuperato',
                'data' => $operatorData
            ]);

        } catch (Exception $e) {
            error_log("Errore API operators profile: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per ottenere lista reparti
     * GET /api/operators/reparti
     */
    public function getReparti()
    {
        if (!$this->isGet()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            // ELOQUENT
            $reparti = Reparti::select('sigla', 'nome_reparto as nome')
                ->orderBy('nome_reparto', 'ASC')
                ->get()
                ->toArray();

            $this->json([
                'status' => 'success',
                'message' => 'Reparti recuperati con successo',
                'data' => $reparti
            ]);

        } catch (Exception $e) {
            error_log("Errore API operators reparti: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per ottenere lista linee
     * GET /api/operators/linee
     */
    public function getLinee()
    {
        if (!$this->isGet()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            // ELOQUENT
            $linee = Line::select('sigla', 'descrizione')
                ->orderBy('sigla', 'ASC')
                ->get()
                ->toArray();

            $this->json([
                'status' => 'success',
                'message' => 'Linee recuperate con successo',
                'data' => $linee
            ]);

        } catch (Exception $e) {
            error_log("Errore API operators linee: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per ottenere taglie da numerata
     * GET /api/operators/taglie?nu=5
     */
    public function getTaglie()
    {
        if (!$this->isGet()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $nu = $this->input('nu');

            if (empty($nu)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Parametro nu mancante'
                ]);
                return;
            }

            // ELOQUENT
            $numerata = IdSize::find($nu);

            if (!$numerata) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Numerata non trovata'
                ]);
                return;
            }

            $numerataArray = $numerata->toArray();
            $taglie = [];
            for ($i = 1; $i <= 20; $i++) {
                $field = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                if ($i == 3) $field = 'N04'; // Gestione ordine particolare
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

            $this->json([
                'status' => 'success',
                'message' => 'Taglie recuperate con successo',
                'data' => [
                    'numerata' => $nu,
                    'taglie' => $taglie
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore API operators taglie: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per riepilogo giornaliero operatore
     * GET /api/operators/daily-summary?id=1&data=2025-09-19
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

            // Conta riparazioni totali del giorno - ELOQUENT
            $totalCount = InternalRepair::where('OPERATORE', $operatorData['user'])
                ->where('DATA', $dateFormatted)
                ->count();

            // Lista riparazioni del giorno - ELOQUENT
            $riparazioni = InternalRepair::select('riparazioni_interne.*', 'rep.nome_reparto')
                ->leftJoin('reparti as rep', 'riparazioni_interne.REPARTO', '=', 'rep.sigla')
                ->where('riparazioni_interne.OPERATORE', $operatorData['user'])
                ->where('riparazioni_interne.DATA', $dateFormatted)
                ->orderBy('riparazioni_interne.ID', 'DESC')
                ->get()
                ->toArray();

            // Reparti più utilizzati - REFACTORED TO ELOQUENT
            $repartiStats = InternalRepair::select('riparazioni_interne.REPARTO')
                ->selectRaw('COUNT(*) as count')
                ->leftJoin('rip_reparti as rep', 'riparazioni_interne.REPARTO', '=', 'rep.sigla')
                ->addSelect('rep.nome_reparto')
                ->where('riparazioni_interne.OPERATORE', $operator['user'])
                ->where('riparazioni_interne.DATA', $dateFormatted)
                ->groupBy('riparazioni_interne.REPARTO', 'rep.nome_reparto')
                ->orderBy('count', 'DESC')
                ->limit(5)
                ->get()
                ->toArray();

            $this->json([
                'status' => 'success',
                'message' => 'Riepilogo giornaliero recuperato',
                'data' => [
                    'operatore' => $operatorData,
                    'data' => $dateFormatted,
                    'totale_riparazioni' => $totalCount,
                    'riparazioni' => $riparazioni,
                    'reparti_stats' => $repartiStats
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore API operators daily-summary: " . $e->getMessage());
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
}