<?php
/**
 * Quality API Controller
 * Gestisce le API per il controllo qualità - RICOSTRUITO ESATTAMENTE DAI FILE LEGACY
 */

use App\Models\QualityRecord;
use App\Models\QualityException;
use App\Models\QualityOperator;
use App\Models\QualityDefectType;
use App\Models\CoreData;
use App\Models\IdSize;
use App\Models\Line;
use App\Models\QualityDepartment;

class QualityApiController extends BaseController
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
     * API Login - ESATTO COME LEGACY login.php
     * POST /api/quality/login
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

                $this->logActivity('QUALITY_API', 'GET_USERS', 'Richiesta lista operatori CQ');

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

                    $this->logActivity('QUALITY_API', 'LOGIN_SUCCESS', "Login operatore: {$userData['user']}");

                    $this->json([
                        'status' => 'success',
                        'message' => 'Login effettuato con successo',
                        'data' => $userData
                    ]);
                } else {
                    $this->logActivity('QUALITY_API', 'LOGIN_FAILED', "Tentativo login fallito: {$data['username']}");

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
            error_log("Quality API Login error: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per verificare esistenza cartellino - ESATTO COME LEGACY check_cartellino.php
     * POST /api/quality/check-cartellino
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
                // Recupera dati di base del cartellino - ELOQUENT
                $cartellino_record = CoreData::select('Cartel')
                    ->where('Cartel', $cartellino)
                    ->first();

                $cartellino_data = $cartellino_record->toArray();

                $this->json([
                    'status' => 'success',
                    'exists' => true,
                    'message' => 'Cartellino trovato',
                    'data' => $cartellino_data
                ]);
            } else {
                $this->json([
                    'status' => 'success',
                    'exists' => false,
                    'message' => 'Cartellino non trovato'
                ]);
            }

        } catch (Exception $e) {
            error_log("Errore API check_cartellino: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per ottenere dettagli completi cartellino - COME get_cartellino_details.php
     * POST /api/quality/cartellino-details
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

            // Calcolo del nuovo valore per testid - ELOQUENT
            $max_testid = QualityRecord::max('ID') ?? 0;
            $new_testid = $max_testid + 1;

            // Prepara data e ora attuali - ESATTO COME LEGACY
            $data = date('d/m/Y');
            $orario = date('H:i');

            // Risposta
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
                        'paia' => $informazione->Tot
                    ],
                    'linea_info' => [
                        'sigla' => $informazione->Ln,
                        'descrizione' => $descrizioneLinea->descrizione ?? ''
                    ],
                    'test_info' => [
                        'testid' => $new_testid,
                        'data' => $data,
                        'orario' => $orario
                    ]
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore API get_cartellino_details: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per verificare esistenza commessa - ESATTO COME LEGACY check_commessa.php
     * POST /api/quality/check-commessa
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
            $result = CoreData::select('Cartel')
                ->where('Commessa Cli', $commessa)
                ->first();

            if ($result) {
                $this->json([
                    'status' => 'success',
                    'exists' => true,
                    'message' => 'Commessa trovata',
                    'data' => [
                        'cartellino' => $result->Cartel
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
            error_log("Errore API check_commessa: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per ottenere opzioni - ESATTO COME LEGACY get_options.php
     * POST /api/quality/options
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
            $repartiOptions = QualityDepartment::select('nome_reparto as Nome')
                ->where('attivo', 1)
                ->orderBy('ordine', 'ASC')
                ->orderBy('nome_reparto', 'ASC')
                ->get()
                ->pluck('Nome')
                ->toArray();

            // Recupera i reparti HERMES - ELOQUENT
            $repartiHermesOptions = QualityDepartment::select('id', 'nome_reparto')
                ->where('attivo', 1)
                ->orderBy('ordine', 'ASC')
                ->orderBy('nome_reparto', 'ASC')
                ->get()
                ->map(function($r) {
                    return ['id' => $r->id, 'nome' => $r->nome_reparto];
                })
                ->toArray();

            // Recupera i tipi di difetti HERMES - ELOQUENT
            $difettiOptions = QualityDefectType::select('id', 'descrizione', 'categoria')
                ->where('attivo', 1)
                ->orderBy('ordine', 'ASC')
                ->orderBy('descrizione', 'ASC')
                ->get()
                ->map(function($d) {
                    return ['id' => $d->id, 'descrizione' => $d->descrizione, 'categoria' => $d->categoria];
                })
                ->toArray();

            // Risposta ESATTA COME LEGACY
            $this->json([
                'status' => 'success',
                'message' => 'Opzioni recuperate con successo',
                'data' => [
                    'calzate' => $calzateOptions,
                    'reparti' => $repartiOptions,
                    'reparti_hermes' => $repartiHermesOptions,
                    'difetti' => $difettiOptions
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore API get_options: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per salvare controlli HERMES CQ - ESATTO COME LEGACY save_hermes_cq.php
     * POST /api/quality/save-hermes-cq
     */
    public function saveHermesCq()
    {
        if (!$this->isPost()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $data = $this->getJsonInput();

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Errore nel formato JSON della richiesta');
            }

            // Campi obbligatori ESATTI COME LEGACY
            $required_fields = [
                'numero_cartellino',
                'reparto',
                'operatore',
                'tipo_cq',
                'paia_totali',
                'cod_articolo',
                'articolo',
                'linea',
                'note',
                'user'
            ];

            foreach ($required_fields as $field) {
                if (!isset($data[$field])) {
                    throw new Exception("Campo obbligatorio mancante: $field");
                }
            }

            $has_exceptions = isset($data['eccezioni']) && is_array($data['eccezioni']) && !empty($data['eccezioni']);

            // Avvia transazione
            $this->db->beginTransaction();

            // Inserisci record principale - ELOQUENT
            $record = QualityRecord::create([
                'numero_cartellino' => $data['numero_cartellino'],
                'reparto' => $data['reparto'],
                'data_controllo' => date('Y-m-d H:i:s'),
                'operatore' => $data['operatore'],
                'tipo_cq' => $data['tipo_cq'],
                'paia_totali' => $data['paia_totali'],
                'cod_articolo' => $data['cod_articolo'],
                'articolo' => $data['articolo'],
                'linea' => $data['linea'],
                'note' => $data['note'],
                'ha_eccezioni' => $has_exceptions ? 1 : 0
            ]);

            $record_id = $record->id;

            // Inserisci eccezioni se presenti - ELOQUENT
            if ($has_exceptions) {
                foreach ($data['eccezioni'] as $eccezione) {
                    // Verifica campi obbligatori
                    if (!isset($eccezione['taglia']) || !isset($eccezione['tipo_difetto'])) {
                        continue; // Salta questa eccezione se mancano campi obbligatori
                    }

                    QualityException::create([
                        'cartellino_id' => $record_id,
                        'taglia' => $eccezione['taglia'],
                        'tipo_difetto' => $eccezione['tipo_difetto'],
                        'note_operatore' => $eccezione['note_operatore'] ?? null,
                        'fotoPath' => $eccezione['fotoPath'] ?? null
                    ]);
                }
            }

            $this->db->commit();

            $this->json([
                'status' => 'success',
                'message' => 'Record salvato con successo',
                'data' => ['record_id' => $record_id]
            ]);

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Errore API save_hermes_cq: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    /**
     * API per riepilogo giornaliero operatore - ESATTO COME LEGACY get_operator_daily_summary.php
     * GET /api/quality/operator-daily-summary
     */
    public function getOperatorDailySummary()
    {
        if (!$this->isGet()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            $user = $this->input('operatore');
            $date = $this->input('data') ?: date('Y-m-d');

            if (empty($user)) {
                throw new Exception('Parametro user mancante o non valido');
            }

            // 1. Conta totale controlli - ELOQUENT
            $operatorRecord = QualityOperator::find($user);
            if (!$operatorRecord) {
                $this->json(['status' => 'error', 'message' => 'Operatore non trovato'], 404);
                return;
            }

            $total_controls = QualityRecord::where('operatore', $operatorRecord->user)
                ->whereDate('data_controllo', $date)
                ->count();

            // 2. Lista controlli con eccezioni - ELOQUENT
            $controls_list = QualityRecord::with('qualityExceptions')
                ->where('operatore', $operatorRecord->user)
                ->whereDate('data_controllo', $date)
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

            // 3. Conta totale eccezioni - ELOQUENT
            $total_exceptions = QualityException::whereHas('qualityRecord', function($q) use ($operatorRecord, $date) {
                $q->where('operatore', $operatorRecord->user)
                  ->whereDate('data_controllo', $date);
            })->count();

            // 4. Ottieni i tipi di difetti più frequenti - ELOQUENT
            $top_defects = QualityException::select('tipo_difetto')
                ->selectRaw('COUNT(*) AS count')
                ->whereHas('qualityRecord', function($q) use ($operatorRecord, $date) {
                    $q->where('operatore', $operatorRecord->user)
                      ->whereDate('data_controllo', $date);
                })
                ->groupBy('tipo_difetto')
                ->orderBy('count', 'DESC')
                ->limit(5)
                ->get()
                ->toArray();

            // 5. Ottieni i reparti con più controlli - ELOQUENT
            $top_departments = QualityRecord::select('reparto')
                ->selectRaw('COUNT(*) AS count')
                ->where('operatore', $operatorRecord->user)
                ->whereDate('data_controllo', $date)
                ->groupBy('reparto')
                ->orderBy('count', 'DESC')
                ->get()
                ->toArray();

            // Formatta data ESATTO COME LEGACY
            $formatted_date = date('d/m/Y', strtotime($date));

            $this->json([
                'status' => 'success',
                'message' => 'Riepilogo giornaliero recuperato con successo',
                'data' => [
                    'data' => $formatted_date,
                    'total_controls' => $total_controls,
                    'total_exceptions' => $total_exceptions,
                    'controls_list' => $controls_list,
                    'top_defects' => $top_defects,
                    'top_departments' => $top_departments
                ]
            ]);

        } catch (PDOException $e) {
            error_log("Errore API get_operator_daily_summary (PDO): " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Errore durante il recupero dei dati dal database'
            ]);
        } catch (Exception $e) {
            error_log("Errore API get_operator_daily_summary: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * API per dettagli record - ESATTO COME LEGACY get_record_details.php
     * GET /api/quality/record-details
     */
    public function getRecordDetails()
    {


        try {
            $record_id = $this->input('record_id');

            if (empty($record_id)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Parametro record_id mancante'
                ]);
                return;
            }

            // Ottieni dettagli record - ELOQUENT
            $record = QualityRecord::find($record_id);

            if (!$record) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Record non trovato'
                ]);
                return;
            }

            // Ottieni eccezioni associate - ELOQUENT
            $exceptions = QualityException::select('cq_hermes_eccezioni.*', 't.descrizione as tipo_difetto_desc')
                ->leftJoin('cq_hermes_tipi_difetti as t', 'cq_hermes_eccezioni.tipo_difetto', '=', 't.id')
                ->where('cq_hermes_eccezioni.cartellino_id', $record_id)
                ->orderBy('cq_hermes_eccezioni.id')
                ->get()
                ->toArray();

            $record = $record->toArray();

            $this->json([
                'status' => 'success',
                'message' => 'Dettagli record recuperati',
                'data' => [
                    'record' => $record,
                    'exceptions' => $exceptions
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore API get_record_details: " . $e->getMessage());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore'
            ]);
        }
    }

    /**
     * API per upload foto eccezioni - ESATTO COME LEGACY upload_eccezione_foto.php
     * POST /api/quality/upload-photo
     */
    public function uploadPhoto()
    {
        if (!$this->isPost()) {
            $this->json(['status' => 'error', 'message' => 'Metodo non consentito'], 405);
            return;
        }

        try {
            // Verifica upload file con dettagli errore
            if (!isset($_FILES['photo'])) {
                error_log("Upload photo: Nessun file ricevuto in \$_FILES['photo']");
                $this->json([
                    'status' => 'error',
                    'message' => 'Nessun file ricevuto'
                ]);
                return;
            }

            $file = $_FILES['photo'];

            // Log dettagliato dell'errore
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error_messages = [
                    UPLOAD_ERR_INI_SIZE => 'Il file supera upload_max_filesize in php.ini',
                    UPLOAD_ERR_FORM_SIZE => 'Il file supera MAX_FILE_SIZE',
                    UPLOAD_ERR_PARTIAL => 'Upload parziale del file',
                    UPLOAD_ERR_NO_FILE => 'Nessun file caricato',
                    UPLOAD_ERR_NO_TMP_DIR => 'Directory temporanea mancante',
                    UPLOAD_ERR_CANT_WRITE => 'Impossibile scrivere su disco',
                    UPLOAD_ERR_EXTENSION => 'Upload bloccato da estensione PHP'
                ];

                $error_msg = $error_messages[$file['error']] ?? 'Errore sconosciuto';
                error_log("Upload photo error code {$file['error']}: {$error_msg}");

                $this->json([
                    'status' => 'error',
                    'message' => "Errore upload: {$error_msg}"
                ]);
                return;
            }

            // Parametri obbligatori
            $cartellino_id = $_POST['cartellino_id'] ?? '';
            $tipo_difetto = $_POST['tipo_difetto'] ?? '';
            $calzata = $_POST['calzata'] ?? '';
            $note = $_POST['note'] ?? '';

            if (empty($cartellino_id) || empty($tipo_difetto)) {
                error_log("Upload photo: Parametri mancanti - cartellino_id: '{$cartellino_id}', tipo_difetto: '{$tipo_difetto}'");
                $this->json([
                    'status' => 'error',
                    'message' => 'Parametri cartellino_id e tipo_difetto sono obbligatori'
                ]);
                return;
            }

            // RIMOSSA LIMITAZIONE FORMATO - Accetta tutti i formati immagine da mobile
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            // Se l'estensione è vuota o sconosciuta, usa 'jpg' come default
            if (empty($file_extension)) {
                $file_extension = 'jpg';
            }

            // Directory upload
            $upload_dir = APP_ROOT . '/storage/quality/cq_uploads/';

            // Verifica e crea directory con gestione errori
            if (!is_dir($upload_dir)) {
                error_log("Upload photo: Directory non esistente, creazione: {$upload_dir}");
                if (!mkdir($upload_dir, 0755, true)) {
                    error_log("Upload photo: ERRORE creazione directory {$upload_dir}");
                    $this->json([
                        'status' => 'error',
                        'message' => 'Impossibile creare la directory di upload'
                    ]);
                    return;
                }
                error_log("Upload photo: Directory creata con successo");
            }

            // Verifica permessi scrittura
            if (!is_writable($upload_dir)) {
                error_log("Upload photo: Directory non scrivibile: {$upload_dir}");
                $this->json([
                    'status' => 'error',
                    'message' => 'Directory di upload non scrivibile. Controllare i permessi.'
                ]);
                return;
            }

            // Nome file
            $filename = 'eccezione_' . $cartellino_id . '_' . time() . '.' . $file_extension;
            $file_path = $upload_dir . $filename;

            error_log("Upload photo: Tentativo salvataggio file da '{$file['tmp_name']}' a '{$file_path}'");

            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                error_log("Upload photo: File salvato con successo: {$file_path}");

                // Ritorna solo il filename - l'inserimento nel DB avverrà con saveHermesCq
                $this->json([
                    'status' => 'success',
                    'message' => 'Foto caricata con successo',
                    'data' => [
                        'filename' => $filename,
                        'url' => BASE_URL . '/storage/quality/cq_uploads/' . $filename
                    ]
                ]);
            } else {
                $last_error = error_get_last();
                error_log("Upload photo: FALLITO move_uploaded_file - " . json_encode($last_error));
                error_log("Upload photo: Source exists: " . (file_exists($file['tmp_name']) ? 'YES' : 'NO'));
                error_log("Upload photo: Dest dir writable: " . (is_writable($upload_dir) ? 'YES' : 'NO'));

                $this->json([
                    'status' => 'error',
                    'message' => 'Errore durante il salvataggio del file. Verificare i permessi della directory.'
                ]);
            }

        } catch (Exception $e) {
            error_log("Errore API upload_photo: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->json([
                'status' => 'error',
                'message' => 'Si è verificato un errore durante l\'upload: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Utility per ottenere input JSON - ESATTO COME LEGACY
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