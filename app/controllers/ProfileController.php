<?php
/**
 * Profile Controller
 * Gestisce il profilo utente, aggiornamento dati e cambio password
 */

use App\Models\User;

class ProfileController extends BaseController
{
    /**
     * Mostra la pagina del profilo utente
     */
    public function index()
    {
        $this->requireAuth();

        try {
            // Ottieni i dati dell'utente corrente con Eloquent
            $userId = $_SESSION['user_id'];
            $user = User::find($userId);

            if (!$user) {
                $_SESSION['alert_error'] = 'Utente non trovato.';
                $this->redirect($this->url('/'));
                return;
            }

            $data = [
                'pageTitle' => 'Profilo Utente - ' . APP_NAME,
                'user' => $user
            ];

            $this->render('profile.index', $data);

        } catch (Exception $e) {
            error_log("Errore profilo: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante il caricamento del profilo.';
            $this->redirect($this->url('/'));
        }
    }
    
    /**
     * Aggiorna i dati del profilo utente
     */
    public function update()
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/profile'));
            return;
        }
        
        // Valida CSRF token
        if (!$this->validateCsrfToken($this->input('csrf_token'))) {
            $_SESSION['alert_error'] = 'Token di sicurezza non valido.';
            $this->redirect($this->url('/profile'));
            return;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            
            // Validazione dati
            $name = trim($this->input('name'));
            $email = trim($this->input('email'));
            
            $errors = [];
            
            if (empty($name)) {
                $errors[] = 'Il nome è obbligatorio.';
            }
            
            if (empty($email)) {
                $errors[] = 'L\'email è obbligatoria.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'L\'email non è valida.';
            }
            
            // Verifica che l'email non sia già usata da un altro utente
            $existingUser = User::where('mail', $email)->where('id', '!=', $userId)->first();
            if ($existingUser) {
                $errors[] = 'L\'email è già utilizzata da un altro utente.';
            }

            if (!empty($errors)) {
                $_SESSION['alert_error'] = implode('<br>', $errors);
                $this->redirect($this->url('/profile'));
                return;
            }

            // Aggiorna i dati utente con Eloquent
            $user = User::find($userId);
            $user->nome = $name;
            $user->mail = $email;
            $user->save();
            
            // Aggiorna la sessione
            $_SESSION['nome'] = $name;
            $_SESSION['mail'] = $email;
            
            $_SESSION['alert_success'] = 'Profilo aggiornato con successo.';
            $this->redirect($this->url('/profile'));
            
        } catch (Exception $e) {
            error_log("Errore aggiornamento profilo: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante l\'aggiornamento del profilo.';
            $this->redirect($this->url('/profile'));
        }
    }
    
    /**
     * Cambia la password dell'utente
     */
    public function changePassword()
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/profile'));
            return;
        }
        
        // Valida CSRF token
        if (!$this->validateCsrfToken($this->input('csrf_token_password'))) {
            $_SESSION['alert_error'] = 'Token di sicurezza non valido.';
            $this->redirect($this->url('/profile'));
            return;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            $currentPassword = $this->input('current_password');
            $newPassword = $this->input('new_password');
            $confirmPassword = $this->input('confirm_password');
            
            $errors = [];
            
            if (empty($currentPassword)) {
                $errors[] = 'La password attuale è obbligatoria.';
            }
            
            if (empty($newPassword)) {
                $errors[] = 'La nuova password è obbligatoria.';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'La nuova password deve contenere almeno 6 caratteri.';
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors[] = 'Le password non coincidono.';
            }
            
            if (!empty($errors)) {
                $_SESSION['alert_error'] = implode('<br>', $errors);
                $this->redirect($this->url('/profile'));
                return;
            }
            
            // Verifica la password attuale con Eloquent
            $user = User::find($userId);
            if (!$user || !$user->checkPassword($currentPassword)) {
                $_SESSION['alert_error'] = 'La password attuale non è corretta.';
                $this->redirect($this->url('/profile'));
                return;
            }

            // Aggiorna la password usando il mutator Eloquent
            $user->password = $newPassword; // Il mutator hashera automaticamente
            $user->save();
            
            $_SESSION['alert_success'] = 'Password cambiata con successo.';
            $this->redirect($this->url('/profile'));
            
        } catch (Exception $e) {
            error_log("Errore cambio password: " . $e->getMessage());
            $_SESSION['alert_error'] = 'Errore durante il cambio password.';
            $this->redirect($this->url('/profile'));
        }
    }
    
    /**
     * Aggiorna le preferenze tema
     */
    public function updateTheme()
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/profile'));
            return;
        }
        
        try {
            $userId = $_SESSION['user_id'];
            $theme = $this->input('theme');
            
            // Valida il tema
            $validThemes = ['light', 'dark', 'auto'];
            if (!in_array($theme, $validThemes)) {
                $theme = 'auto';
            }
            
            // Aggiorna le preferenze utente con Eloquent
            $user = User::find($userId);
            $user->theme_color = $theme;
            $user->save();
            
            // Aggiorna la sessione
            $_SESSION['theme_color'] = $theme;
            
            // Risposta JSON per AJAX
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Tema aggiornato con successo.']);
                exit;
            }
            
            $_SESSION['alert_success'] = 'Tema aggiornato con successo.';
            $this->redirect($this->url('/profile'));
            
        } catch (Exception $e) {
            error_log("Errore aggiornamento tema: " . $e->getMessage());
            
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiornamento del tema.']);
                exit;
            }
            
            $_SESSION['alert_error'] = 'Errore durante l\'aggiornamento del tema.';
            $this->redirect($this->url('/profile'));
        }
    }
    
    /**
     * Controlla se la richiesta è AJAX
     */
    private function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}