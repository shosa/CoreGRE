<?php
/**
 * Users Controller
 * Gestisce il sistema di gestione utenti e permessi
 */

use App\Models\User;
use App\Models\Permission;

class UsersController extends BaseController
{
    /**
     * Index - Lista utenti
     */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('utenti');

        // Carica tutti gli utenti con Eloquent
        $users = User::orderBy('nome', 'ASC')->get();

        $data = [
            'pageTitle' => 'Gestione Utenti - ' . APP_NAME,
            'users' => $users,
            'totalUsers' => $users->count()
        ];

        $this->render('users.index', $data);
    }

    /**
     * Create - Form creazione utente
     */
    public function create()
    {
        $this->requireAuth();
        $this->requireAdmin(); // Solo admin può gestire utenti

        $data = [
            'pageTitle' => 'Nuovo Utente - ' . APP_NAME,
            'adminTypes' => [
                'admin' => 'Administrator',
                'manager' => 'Manager',
                'user' => 'Utente Standard',
                'viewer' => 'Solo Visualizzazione'
            ]
        ];

        $this->render('users.create', $data);
    }

    /**
     * Store - Salva nuovo utente
     */
    public function store()
    {
        $this->requireAuth();
        $this->requireAdmin(); // Solo admin può gestire utenti

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/users'));
        }

        // Validazione dati
        $userName = trim($_POST['user_name'] ?? '');
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $adminType = $_POST['admin_type'] ?? '';

        $errors = [];

        if (empty($userName)) {
            $errors[] = 'Username è obbligatorio';
        }

        if (empty($nome)) {
            $errors[] = 'Nome è obbligatorio';
        }

        if (empty($email)) {
            $errors[] = 'Email è obbligatoria';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email non valida';
        }

        if (empty($password)) {
            $errors[] = 'Password è obbligatoria';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password deve essere di almeno 6 caratteri';
        }

        if (empty($adminType)) {
            $errors[] = 'Ruolo è obbligatorio';
        }

        // Verifica username univoco
        if (!empty($userName)) {
            $existingUser = User::where('user_name', $userName)->first();
            if ($existingUser) {
                $errors[] = 'Username già esistente';
            }
        }

        // Verifica email univoca
        if (!empty($email)) {
            $existingEmail = User::where('mail', $email)->first();
            if ($existingEmail) {
                $errors[] = 'Email già esistente';
            }
        }

        if (!empty($errors)) {
            $this->setFlash(implode(', ', $errors), 'error');
            $this->redirect($this->url('/users/create'));
        }

        try {
            // Crea utente con Eloquent
            $user = User::create([
                'user_name' => $userName,
                'nome' => $nome,
                'mail' => $email,
                'password' => $password,
                'admin_type' => $adminType
            ]);

            // Crea record permessi con valori di default
            Permission::create(['id_utente' => $user->id]);

            $this->setFlash('Utente creato con successo', 'success');
            $this->redirect($this->url('/users'));

        } catch (Exception $e) {
            error_log('Error creating user: ' . $e->getMessage());
            $this->setFlash('Errore durante la creazione dell\'utente', 'error');
            $this->redirect($this->url('/users/create'));
        }
    }

    /**
     * Show - Dettagli utente
     */
    public function show($userId = null)
    {
        $this->requireAuth();
        $this->requireAdmin(); // Solo admin può gestire utenti

        if (!$userId) {
            $this->redirect($this->url('/users'));
        }

        $user = User::with('permissions')->find($userId);

        if (!$user) {
            $this->setFlash('Utente non trovato', 'error');
            $this->redirect($this->url('/users'));
        }

        $data = [
            'pageTitle' => 'Dettagli Utente - ' . APP_NAME,
            'user' => $user
        ];

        $this->render('users.show', $data);
    }

    /**
     * Edit - Form modifica utente
     */
    public function edit($userId = null)
    {
        $this->requireAuth();
        $this->requireAdmin(); // Solo admin può gestire utenti

        if (!$userId) {
            $this->redirect($this->url('/users'));
        }

        $user = User::find($userId);

        if (!$user) {
            $this->setFlash('Utente non trovato', 'error');
            $this->redirect($this->url('/users'));
        }

        $data = [
            'pageTitle' => 'Modifica Utente - ' . APP_NAME,
            'user' => $user,
            'adminTypes' => [
                'admin' => 'Administrator',
                'manager' => 'Manager',
                'user' => 'Utente Standard',
                'viewer' => 'Solo Visualizzazione'
            ]
        ];

        $this->render('users.edit', $data);
    }

    /**
     * Update - Aggiorna utente
     */
    public function update()
    {
        $this->requireAuth();
        $this->requireAdmin(); // Solo admin può gestire utenti

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/users'));
        }

        $userId = $_POST['id'] ?? null;
        if (!$userId) {
            $this->redirect($this->url('/users'));
        }

        // Validazione dati
        $userName = trim($_POST['user_name'] ?? '');
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $adminType = $_POST['admin_type'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';

        $errors = [];

        if (empty($userName)) {
            $errors[] = 'Username è obbligatorio';
        }

        if (empty($nome)) {
            $errors[] = 'Nome è obbligatorio';
        }

        if (empty($email)) {
            $errors[] = 'Email è obbligatoria';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email non valida';
        }

        if (empty($adminType)) {
            $errors[] = 'Ruolo è obbligatorio';
        }

        if (!empty($newPassword) && strlen($newPassword) < 6) {
            $errors[] = 'Nuova password deve essere di almeno 6 caratteri';
        }

        // Verifica username univoco (escludendo l'utente corrente)
        if (!empty($userName)) {
            $existingUser = User::where('user_name', $userName)->where('id', '!=', $userId)->first();
            if ($existingUser) {
                $errors[] = 'Username già esistente';
            }
        }

        // Verifica email univoca (escludendo l'utente corrente)
        if (!empty($email)) {
            $existingEmail = User::where('mail', $email)->where('id', '!=', $userId)->first();
            if ($existingEmail) {
                $errors[] = 'Email già esistente';
            }
        }

        if (!empty($errors)) {
            $this->setFlash(implode(', ', $errors), 'error');
            $this->redirect($this->url('/users/' . $userId . '/edit'));
        }

        try {
            $user = User::find($userId);
            if (!$user) {
                $this->setFlash('Utente non trovato', 'error');
                $this->redirect($this->url('/users'));
            }

            // Aggiorna i dati con Eloquent
            $user->user_name = $userName;
            $user->nome = $nome;
            $user->mail = $email;
            $user->admin_type = $adminType;

            if (!empty($newPassword)) {
                $user->password = $newPassword;
            }

            $user->save();

            $this->setFlash('Utente aggiornato con successo', 'success');
            $this->redirect($this->url('/users'));

        } catch (Exception $e) {
            error_log('Error updating user: ' . $e->getMessage());
            $this->setFlash('Errore durante l\'aggiornamento dell\'utente', 'error');
            $this->redirect($this->url('/users/' . $userId . '/edit'));
        }
    }

    /**
     * Delete - Elimina utente/i
     */
    public function delete()
    {
        $this->requireAuth();
        $this->requireAdmin(); // Solo admin può gestire utenti

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Metodo non valido']);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Supporto sia singolo che multiplo
        $userIds = [];
        if (isset($input['id'])) {
            $userIds = [$input['id']];
        } elseif (isset($input['ids']) && is_array($input['ids'])) {
            $userIds = $input['ids'];
        } else {
            $this->json(['success' => false, 'error' => 'ID utente/i mancanti']);
        }

        if (empty($userIds)) {
            $this->json(['success' => false, 'error' => 'Nessun utente selezionato']);
        }

        // Verifica che l'utente non stia eliminando se stesso
        if (in_array($_SESSION['user_id'], $userIds)) {
            $this->json(['success' => false, 'error' => 'Non puoi eliminare il tuo account']);
        }

        try {
            $deletedCount = 0;
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    // Elimina automaticamente tutti i record collegati tramite foreign key
                    $this->deleteUserRelatedRecords($userId);

                    // Elimina l'utente con Eloquent
                    $user->delete();
                    $deletedCount++;
                }
            }

            if ($deletedCount > 0) {
                $message = $deletedCount === 1
                    ? 'Utente eliminato con successo'
                    : "$deletedCount utenti eliminati con successo";
                $this->json(['success' => true, 'message' => $message, 'count' => $deletedCount]);
            } else {
                $this->json(['success' => false, 'error' => 'Nessun utente trovato']);
            }

        } catch (Exception $e) {
            error_log('Error deleting users: ' . $e->getMessage());

            // In production, usa messaggio generico; in debug mostra dettagli
            $errorMessage = 'Errore durante l\'eliminazione dell\'utente';

            $this->json(['success' => false, 'error' => $errorMessage]);
        }
    }

    /**
     * Gestisce i permessi utente
     */
    public function permissions($userId = null)
    {
        $this->requireAuth();
        $this->requireAdmin(); // Solo admin può gestire utenti

        if (!$userId) {
            $this->redirect($this->url('/users'));
        }

        $user = User::with('permissions')->find($userId);
        if (!$user) {
            $this->setFlash('Utente non trovato', 'error');
            $this->redirect($this->url('/users'));
        }

        $permissions = $user->permissions;

        $data = [
            'pageTitle' => 'Permessi Utente - ' . APP_NAME,
            'user' => $user,
            'permissions' => $permissions ?: []
        ];

        $this->render('users.permissions', $data);
    }

    /**
     * Aggiorna i permessi utente
     */
    public function updatePermissions()
    {
        $this->requireAuth();
        $this->requireAdmin(); // Solo admin può gestire utenti

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/users'));
        }

        $userId = $_POST['user_id'] ?? null;
        if (!$userId) {
            $this->redirect($this->url('/users'));
        }

        // Lista dei permessi disponibili
        $availablePermissions = [
            'riparazioni',
            'produzione',
            'log',
            'etichette',
            'dbsql',
            'utenti',
            'tracking',
            'settings',
            'scm',
            'export',
            'admin',
            'quality',
            'mrp'
        ];

        try {
            // Debug completo
            error_log('=== PERMISSIONS DEBUG ===');
            error_log('REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD']);
            error_log('User ID: ' . $userId);
            error_log('POST data: ' . print_r($_POST, true));
            error_log('Available permissions: ' . implode(', ', $availablePermissions));

            // Prepara i dati dei permessi (1 se selezionato, 0 altrimenti)
            $permissionData = ['id_utente' => $userId];
            foreach ($availablePermissions as $permission) {
                $isChecked = isset($_POST[$permission]);
                $permissionData[$permission] = $isChecked ? 1 : 0;
                error_log("Permission '$permission': " . ($isChecked ? 'CHECKED' : 'NOT CHECKED') . " -> " . $permissionData[$permission]);
            }

            error_log('Final permission data: ' . print_r($permissionData, true));

            // Trova o crea record permessi per questo utente
            $permission = Permission::firstOrCreate(['id_utente' => $userId]);

            // Aggiorna i permessi con Eloquent
            unset($permissionData['id_utente']); // Rimuove id_utente dai dati da aggiornare
            $permission->setPermissions($permissionData);
            $permission->save();

            error_log('SAVED PERMISSIONS: ' . print_r($permission->toArray(), true));

            $_SESSION['alert_success'] = 'Permessi aggiornati con successo!';
            $this->redirect($this->url('/users/' . $userId . '/permissions'));

        } catch (Exception $e) {
            error_log('Error updating permissions: ' . $e->getMessage());

            // Per debug, mostra errore specifico
            $errorMessage = 'Errore: ' . $e->getMessage();
            $_SESSION['alert_error'] = 'Errore!';
            $this->redirect($this->url('/users/' . $userId . '/permissions'));
        }
    }

    /**
     * Elimina automaticamente tutti i record collegati a un utente tramite foreign key
     */
    private function deleteUserRelatedRecords($userId)
    {
        // Query per trovare tutte le foreign key che puntano alla tabella utenti
        $foreignKeys = $this->db->fetchAll("
            SELECT 
                TABLE_NAME,
                COLUMN_NAME
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE 
                REFERENCED_TABLE_SCHEMA = DATABASE()
                AND REFERENCED_TABLE_NAME = 'utenti' 
                AND REFERENCED_COLUMN_NAME = 'id'
        ");

        // Elimina i record da ogni tabella che ha foreign key verso utenti
        foreach ($foreignKeys as $fk) {
            $table = $fk['TABLE_NAME'];
            $column = $fk['COLUMN_NAME'];

            // Skip della tabella utenti stessa per evitare loop
            if ($table === 'utenti')
                continue;

            $sql = "DELETE FROM `{$table}` WHERE `{$column}` = ?";
            $this->db->execute($sql, [$userId]);
        }
    }
}