<?php
/**
 * InWorkAdmin Controller
 * Gestisce gli operatori mobile e i permessi moduli per CoreInWork
 */

use App\Models\InWorkOperator;
use App\Models\InWorkModulePermission;

class InWorkAdminController extends BaseController
{
    /**
     * Index - Dashboard e lista operatori
     */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('admin'); // Solo admin possono gestire operatori

        // Carica tutti gli operatori con Eloquent + permessi
        $operators = InWorkOperator::with('modulePermissions')
            ->orderBy('full_name', 'ASC')
            ->get();

        // Statistiche base
        $totalOperators = $operators->count();
        $activeOperators = $operators->where('active', 1)->count();
        $inactiveOperators = $totalOperators - $activeOperators;

        // Distribuzione per reparto
        $operatorsByDepartment = [];
        foreach ($operators as $operator) {
            $dept = $operator->reparto ?: 'Non specificato';
            if (!isset($operatorsByDepartment[$dept])) {
                $operatorsByDepartment[$dept] = 0;
            }
            $operatorsByDepartment[$dept]++;
        }
        arsort($operatorsByDepartment);

        // Statistiche permessi moduli
        $qualityCount = 0;
        $repairsCount = 0;
        $bothModules = 0;
        $noModules = 0;

        foreach ($operators as $operator) {
            $permissions = $operator->modulePermissions;
            $hasQuality = false;
            $hasRepairs = false;

            foreach ($permissions as $perm) {
                if ($perm->module === 'quality' && $perm->enabled) {
                    $hasQuality = true;
                    $qualityCount++;
                }
                if ($perm->module === 'repairs' && $perm->enabled) {
                    $hasRepairs = true;
                    $repairsCount++;
                }
            }

            if ($hasQuality && $hasRepairs) {
                $bothModules++;
            } elseif (!$hasQuality && !$hasRepairs) {
                $noModules++;
            }
        }

        // Operatori senza contatti
        $operatorsWithoutContacts = $operators->filter(function($op) {
            return empty($op->email) && empty($op->phone);
        })->count();

        // Ultimi 5 operatori creati
        $recentOperators = InWorkOperator::with('modulePermissions')
            ->whereNotNull('created_at')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get();

        // Ultimi 5 operatori modificati
        $recentlyUpdated = InWorkOperator::with('modulePermissions')
            ->whereNotNull('updated_at')
            ->orderBy('updated_at', 'DESC')
            ->limit(5)
            ->get();

        $data = [
            'pageTitle' => 'Dashboard InWork - ' . APP_NAME,
            'operators' => $operators,

            // Statistiche generali
            'totalOperators' => $totalOperators,
            'activeOperators' => $activeOperators,
            'inactiveOperators' => $inactiveOperators,

            // Distribuzione
            'operatorsByDepartment' => $operatorsByDepartment,

            // Permessi moduli
            'qualityCount' => $qualityCount,
            'repairsCount' => $repairsCount,
            'bothModules' => $bothModules,
            'noModules' => $noModules,

            // Alert
            'operatorsWithoutContacts' => $operatorsWithoutContacts,

            // Liste recenti
            'recentOperators' => $recentOperators,
            'recentlyUpdated' => $recentlyUpdated
        ];

        $this->render('inwork.index', $data);
    }

    /**
     * Create - Form creazione operatore
     */
    public function create()
    {
        $this->requireAuth();
        $this->requireAdmin();

        $data = [
            'pageTitle' => 'Nuovo Operatore Mobile - ' . APP_NAME,
            'modules' => InWorkModulePermission::MODULES
        ];

        $this->render('inwork.create', $data);
    }

    /**
     * Store - Salva nuovo operatore
     */
    public function store()
    {
        $this->requireAuth();
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/inwork-admin'));
        }

        // Validazione dati
        $user = trim($_POST['user'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $pin = trim($_POST['pin'] ?? '');
        $reparto = trim($_POST['reparto'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $enabledModules = $_POST['enabled_modules'] ?? [];

        $errors = [];

        if (empty($user)) {
            $errors[] = 'Username è obbligatorio';
        }

        if (empty($fullName)) {
            $errors[] = 'Nome completo è obbligatorio';
        }

        if (empty($pin)) {
            $errors[] = 'PIN è obbligatorio';
        } elseif (!is_numeric($pin)) {
            $errors[] = 'PIN deve essere numerico';
        } elseif (strlen($pin) < 4 || strlen($pin) > 6) {
            $errors[] = 'PIN deve essere di 4-6 cifre';
        }

        if (empty($reparto)) {
            $errors[] = 'Reparto è obbligatorio';
        }

        // Verifica username univoco
        if (!empty($user)) {
            $existingOperator = InWorkOperator::where('user', $user)->first();
            if ($existingOperator) {
                $errors[] = 'Username già esistente';
            }
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('/inwork-admin/create'));
            return;
        }

        try {
            // Crea operatore
            $operator = InWorkOperator::create([
                'user' => $user,
                'full_name' => $fullName,
                'pin' => (int)$pin,
                'reparto' => $reparto,
                'email' => !empty($email) ? $email : null,
                'phone' => !empty($phone) ? $phone : null,
                'notes' => !empty($notes) ? $notes : null,
                'active' => 1
            ]);

            // Crea permessi moduli
            InWorkModulePermission::syncForOperator($operator->id, $enabledModules);

            // Log activity
            $this->logActivity('INWORK_ADMIN', 'CREATE_OPERATOR', "Operatore creato: {$operator->user}");

            $this->setFlash('success', 'Operatore creato con successo');
            $this->redirect($this->url('/inwork-admin'));
        } catch (Exception $e) {
            error_log("Errore creazione operatore: " . $e->getMessage());
            $this->setFlash('error', 'Errore durante la creazione dell\'operatore');
            $this->redirect($this->url('/inwork-admin/create'));
        }
    }

    /**
     * Edit - Form modifica operatore
     */
    public function edit($id)
    {
        $this->requireAuth();
        $this->requireAdmin();

        $operator = InWorkOperator::with('modulePermissions')->find($id);

        if (!$operator) {
            $this->setFlash('error', 'Operatore non trovato');
            $this->redirect($this->url('/inwork-admin'));
            return;
        }

        $data = [
            'pageTitle' => 'Modifica Operatore - ' . APP_NAME,
            'operator' => $operator,
            'modules' => InWorkModulePermission::MODULES,
            'enabledModules' => $operator->modulePermissions()
                ->where('enabled', 1)
                ->pluck('module')
                ->toArray()
        ];

        $this->render('inwork.edit', $data);
    }

    /**
     * Update - Aggiorna operatore
     */
    public function update($id)
    {
        $this->requireAuth();
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/inwork-admin'));
        }

        $operator = InWorkOperator::find($id);

        if (!$operator) {
            $this->setFlash('error', 'Operatore non trovato');
            $this->redirect($this->url('/inwork-admin'));
            return;
        }

        // Validazione dati
        $user = trim($_POST['user'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $pin = trim($_POST['pin'] ?? '');
        $reparto = trim($_POST['reparto'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $active = isset($_POST['active']) ? 1 : 0;
        $enabledModules = $_POST['enabled_modules'] ?? [];

        $errors = [];

        if (empty($user)) {
            $errors[] = 'Username è obbligatorio';
        }

        if (empty($fullName)) {
            $errors[] = 'Nome completo è obbligatorio';
        }

        if (!empty($pin)) {
            if (!is_numeric($pin)) {
                $errors[] = 'PIN deve essere numerico';
            } elseif (strlen($pin) < 4 || strlen($pin) > 6) {
                $errors[] = 'PIN deve essere di 4-6 cifre';
            }
        }

        // Verifica username univoco (escludendo operatore corrente)
        if (!empty($user) && $user !== $operator->user) {
            $existingOperator = InWorkOperator::where('user', $user)
                ->where('id', '!=', $id)
                ->first();
            if ($existingOperator) {
                $errors[] = 'Username già esistente';
            }
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect($this->url('/inwork-admin/' . $id . '/edit'));
            return;
        }

        try {
            // Aggiorna operatore
            $operator->user = $user;
            $operator->full_name = $fullName;
            if (!empty($pin)) {
                $operator->pin = (int)$pin;
            }
            $operator->reparto = $reparto;
            $operator->email = !empty($email) ? $email : null;
            $operator->phone = !empty($phone) ? $phone : null;
            $operator->notes = !empty($notes) ? $notes : null;
            $operator->active = $active;
            $operator->save();

            // Aggiorna permessi moduli
            InWorkModulePermission::syncForOperator($operator->id, $enabledModules);

            // Log activity
            $this->logActivity('INWORK_ADMIN', 'UPDATE_OPERATOR', "Operatore aggiornato: {$operator->user}");

            $this->setFlash('success', 'Operatore aggiornato con successo');
            $this->redirect($this->url('/inwork-admin'));
        } catch (Exception $e) {
            error_log("Errore aggiornamento operatore: " . $e->getMessage());
            $this->setFlash('error', 'Errore durante l\'aggiornamento dell\'operatore');
            $this->redirect($this->url('/inwork-admin/' . $id . '/edit'));
        }
    }

    /**
     * Delete - Elimina operatore (AJAX)
     */
    public function delete()
    {
        $this->requireAuth();
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID mancante'], 400);
            return;
        }

        $operator = InWorkOperator::find($id);

        if (!$operator) {
            $this->json(['success' => false, 'message' => 'Operatore non trovato'], 404);
            return;
        }

        try {
            $username = $operator->user;
            $operator->delete();

            // Log activity
            $this->logActivity('INWORK_ADMIN', 'DELETE_OPERATOR', "Operatore eliminato: $username");

            $this->json(['success' => true, 'message' => 'Operatore eliminato con successo']);
        } catch (Exception $e) {
            error_log("Errore eliminazione operatore: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore durante l\'eliminazione'], 500);
        }
    }

    /**
     * Toggle - Attiva/Disattiva operatore (AJAX)
     */
    public function toggle()
    {
        $this->requireAuth();
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID mancante'], 400);
            return;
        }

        $operator = InWorkOperator::find($id);

        if (!$operator) {
            $this->json(['success' => false, 'message' => 'Operatore non trovato'], 404);
            return;
        }

        try {
            $operator->active = !$operator->active;
            $operator->save();

            $status = $operator->active ? 'attivato' : 'disattivato';

            // Log activity
            $this->logActivity('INWORK_ADMIN', 'TOGGLE_OPERATOR', "Operatore $status: {$operator->user}");

            $this->json([
                'success' => true,
                'message' => "Operatore $status con successo",
                'active' => $operator->active
            ]);
        } catch (Exception $e) {
            error_log("Errore toggle operatore: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore durante l\'operazione'], 500);
        }
    }
}
