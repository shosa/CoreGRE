<?php
/**
 * Auth Controller
 * Gestisce l'autenticazione degli utenti con Eloquent ORM
 */

use App\Models\User;
use App\Models\Permission;
use App\Models\ActivityLog;

class AuthController extends BaseController
{
    /**
     * Mostra la pagina di login
     */
    public function showLogin()
    {
        // Se l'utente è già loggato, reindirizza alla dashboard
        if ($this->isAuthenticated()) {
            $this->redirect($this->url('/'));
        }

        // Controlla cookie "ricordami"
        $this->checkRememberMeCookie();

        $data = [
            'pageTitle' => 'Accesso - ' . APP_NAME,
            'csrfToken' => $this->generateCsrfToken()
        ];

        $this->render('auth.login', $data, 'auth');
    }

    /**
     * Processa il login
     */
    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/login'));
        }

        $username = $this->input('username');
        $password = $this->input('passwd');
        $remember = $this->input('remember');
        $csrfToken = $this->input('csrf_token');

        // Valida CSRF token
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['alert_error'] = 'Token di sicurezza non valido.';
            $this->redirect($this->url('/login'));
        }

        // Valida input
        $errors = $this->validate([
            'username' => $username,
            'passwd' => $password
        ], [
            'username' => 'required',
            'passwd' => 'required'
        ]);

        if (!empty($errors)) {
            $_SESSION['alert_error'] = 'Username e password sono obbligatori.';
            $this->redirect($this->url('/login'));
        }

        try {
            // Cerca l'utente con Eloquent
            $user = User::where('user_name', $username)->first();

            if (!$user || !$user->checkPassword($password)) {
                $_SESSION['alert_error'] = 'Username o password non validi.';
                $this->redirect($this->url('/login'));
            }

            // Login riuscito - imposta sessione
            $this->setUserSession($user);

            // Carica permessi
            $this->loadUserPermissions($user->id);

            // Gestisce "ricordami"
            if ($remember) {
                $this->setRememberMeCookie($user->id);
            }

            // Log attività
            $this->logLoginActivity($user->id);

            $_SESSION['alert_success'] = 'Accesso eseguito con successo!';
            $this->redirect($this->url('/'));

        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante l\'accesso. Riprova più tardi.';
            $this->redirect($this->url('/login'));
        }
    }

    /**
     * Effettua il logout
     */
    public function logout()
    {
        $userId = $_SESSION['user_id'] ?? null;

        // Log attività prima di pulire la sessione
        if ($userId) {
            $this->logActivity('LOGIN', 'LOGOUT', 'Logout eseguito');
        }

        // Pulisci cookie "ricordami"
        $this->clearRememberMeCookie();

        // Distruggi sessione
        session_destroy();
        session_start();

        $_SESSION['alert_success'] = 'Logout eseguito con successo.';
        $this->redirect($this->url('/login'));
    }

    /**
     * Imposta la sessione utente
     */
    private function setUserSession($user)
    {
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->user_name;
        $_SESSION['nome'] = $user->nome;
        $_SESSION['mail'] = $user->mail;
        $_SESSION['admin_type'] = $user->admin_type;
        $_SESSION['tema'] = $user->theme_color ?? 'primary';

        // Aggiorna last_login con Eloquent
        try {
            $user->updateLastLogin();
        } catch (Exception $e) {
            error_log('Error updating last_login: ' . $e->getMessage());
        }
    }

    /**
     * Carica i permessi dell'utente
     */
    private function loadUserPermissions($userId)
    {
        $permission = Permission::where('id_utente', $userId)->first();

        if ($permission) {
            $modules = $permission->getPermissionModules();
            foreach (array_keys($modules) as $module) {
                $_SESSION["permessi_{$module}"] = $permission->hasPermission($module);
            }
        }
    }

    /**
     * Imposta il cookie "ricordami"
     */
    private function setRememberMeCookie($userId)
    {
        $user = User::find($userId);
        if (!$user) return;

        $seriesId = bin2hex(random_bytes(16));
        $rememberToken = bin2hex(random_bytes(20));
        $hashedToken = password_hash($rememberToken, PASSWORD_DEFAULT);
        $expires = time() + REMEMBER_TOKEN_LIFETIME;

        // Salva nel database con Eloquent
        $user->series_id = $seriesId;
        $user->remember_token = $hashedToken;
        $user->expires = date('Y-m-d H:i:s', $expires);
        $user->save();

        // Imposta i cookie
        setcookie('series_id', $seriesId, $expires, '/');
        setcookie('remember_token', $rememberToken, $expires, '/');
    }

    /**
     * Controlla il cookie "ricordami"
     */
    private function checkRememberMeCookie()
    {
        $seriesId = $_COOKIE['series_id'] ?? null;
        $rememberToken = $_COOKIE['remember_token'] ?? null;

        if (!$seriesId || !$rememberToken) {
            return;
        }

        try {
            $user = User::where('series_id', $seriesId)->first();

            if (!$user || !password_verify($rememberToken, $user->remember_token)) {
                $this->clearRememberMeCookie();
                return;
            }

            // Verifica scadenza
            $expires = strtotime($user->expires);
            if (time() > $expires) {
                $this->clearRememberMeCookie();
                return;
            }

            // Login automatico
            $this->setUserSession($user);
            $this->loadUserPermissions($user->id);

            $this->redirect($this->url('/'));

        } catch (Exception $e) {
            error_log("Remember me error: " . $e->getMessage());
            $this->clearRememberMeCookie();
        }
    }

    /**
     * Pulisce i cookie "ricordami"
     */
    private function clearRememberMeCookie()
    {
        setcookie('series_id', '', time() - 3600, '/');
        setcookie('remember_token', '', time() - 3600, '/');
        unset($_COOKIE['series_id']);
        unset($_COOKIE['remember_token']);
    }

    /**
     * Log dell'attività di login
     */
    private function logLoginActivity($userId)
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Determina browser e dispositivo
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera'];
        $browser = 'Sconosciuto';

        foreach ($browsers as $b) {
            if (strpos($userAgent, $b) !== false) {
                $browser = $b;
                break;
            }
        }

        $device = (strpos($userAgent, 'Mobile') !== false) ? 'Mobile' : 'Desktop';

        ActivityLog::logActivity(
            $userId,
            'auth',
            'login',
            'Accesso eseguito',
            "{$device} / {$browser}"
        );
    }
}