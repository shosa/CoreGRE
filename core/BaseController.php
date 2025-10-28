<?php
/**
 * Base Controller
 * Classe base per tutti i controller dell'applicazione
 */

use App\Models\User;
use App\Models\Permission;
use App\Models\ActivityLog;

abstract class BaseController
{
    protected $db;
    protected $router;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->initializeSession();
    }

    /**
     * Inizializza la sessione se non già attiva
     */
    private function initializeSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Renderizza una view
     */
    protected function view($viewName, $data = [])
    {
        // Estrae le variabili per renderle disponibili nella view
        extract($data);

        // Percorso completo della view
        $viewPath = VIEW_PATH . '/' . str_replace('.', '/', $viewName) . '.php';

        if (!file_exists($viewPath)) {
            throw new Exception("View {$viewName} not found at {$viewPath}");
        }

        // Include la view
        include $viewPath;
    }

    /**
     * Renderizza una view con layout
     */
    protected function render($viewName, $data = [], $layout = 'main')
    {
        // Cattura l'output della view
        ob_start();
        $this->view($viewName, $data);
        $content = ob_get_clean();

        // Se è una richiesta PJAX/AJAX, ritorna solo il contenuto + meta info
        if ($this->isPjax()) {
            $response = [
                'content' => $content,
                'title' => $data['pageTitle'] ?? 'COREGRE',
                'url' => $_SERVER['REQUEST_URI'] ?? '/',
                'scripts' => $data['pageScripts'] ?? null
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }

        // Rendering completo con layout per richieste normali
        $data['content'] = $content;
        $this->view("layouts.{$layout}", $data);
    }

    /**
     * Imposta headers CORS per API mobile (incluso Capacitor)
     */
    protected function setCorsHeaders()
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? null;

        // Permetti specificamente localhost per Capacitor, altrimenti wildcard
        if ($origin && (strpos($origin, 'localhost') !== false || strpos($origin, '127.0.0.1') !== false)) {
            header("Access-Control-Allow-Origin: $origin");
        } else {
            header("Access-Control-Allow-Origin: *");
        }

        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-App-Type, X-Requested-With");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

        // Gestisci richieste OPTIONS (preflight CORS)
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    /**
     * Ritorna JSON response
     */
    protected function json($data, $statusCode = 200)
    {
        // Pulisci qualsiasi output precedente
        if (ob_get_level()) {
            ob_clean();
        }

        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Reindirizza a un URL
     */
    protected function redirect($url, $statusCode = 302)
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    /**
     * Reindirizza alla route precedente o a una route di default
     */
    protected function redirectBack($default = '/')
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? $default;
        $this->redirect($referer);
    }

    /**
     * Verifica se la richiesta è AJAX
     */
    protected function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Verifica se la richiesta è PJAX (navigazione AJAX per SPA)
     */
    protected function isPjax()
    {
        return $this->isAjax() &&
            (isset($_SERVER['HTTP_X_PJAX']) ||
                isset($_GET['_pjax']) ||
                strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);
    }

    /**
     * Verifica se la richiesta è POST
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Verifica se la richiesta è GET
     */
    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Verifica se l'utente è autenticato
     */
    protected function isAuthenticated()
    {
        return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
    }

    /**
     * Richiede autenticazione
     */
    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            if ($this->isAjax()) {
                $this->json(['error' => 'Authentication required'], 401);
            } else {
                $this->redirect($this->url('/login'));
            }
        }
    }

    /**
     * Verifica i permessi dell'utente
     */
    protected function hasPermission($permission)
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        $sessionKey = "permessi_{$permission}";
        return isset($_SESSION[$sessionKey]) && $_SESSION[$sessionKey] == 1;
    }

    /**
     * Richiede un permesso specifico
     */
    protected function requirePermission($permission)
    {
        $this->requireAuth();

        if (!$this->hasPermission($permission)) {
            if ($this->isAjax()) {
                $this->json(['error' => 'Permission denied'], 403);
            } else {
                $this->redirect($this->url('/error/forbidden'));
            }
        }
    }

    /**
     * Verifica se l'utente è  Admin
     */
    protected function isAdmin()
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        return isset($_SESSION['admin_type']) && $_SESSION['admin_type'] === 'admin';
    }

    /**
     * Richiede privilegi  Admin
     */
    protected function requireAdmin()
    {
        $this->requireAuth();

        if (!$this->isAdmin()) {
            if ($this->isAjax()) {
                $this->json(['error' => ' Admin access required'], 403);
            } else {
                $this->redirect($this->url('/error/forbidden'));
            }
        }
    }

    /**
     * Valida token CSRF
     */
    protected function validateCsrfToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Genera token CSRF
     */
    protected function generateCsrfToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Imposta messaggio flash
     */
    protected function setFlash($type, $message)
    {
        $_SESSION['alert_' . $type] = $message;
    }

    /**
     * Ottiene messaggio flash
     */
    protected function getFlash($type)
    {
        $message = $_SESSION['alert_' . $type] ?? null;
        unset($_SESSION['alert_' . $type]);
        return $message;
    }

    /**
     * Sanifica input
     */
    protected function sanitize($input)
    {
        if (is_string($input)) {
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        } elseif (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }
        return $input;
    }

    /**
     * Valida dati con regole
     */
    protected function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;

            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = "Il campo {$field} è obbligatorio";
                continue;
            }

            if (strpos($rule, 'email') !== false && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Il campo {$field} deve essere un'email valida";
            }

            if (preg_match('/min:(\d+)/', $rule, $matches) && strlen($value) < $matches[1]) {
                $errors[$field] = "Il campo {$field} deve essere lungo almeno {$matches[1]} caratteri";
            }

            if (preg_match('/max:(\d+)/', $rule, $matches) && strlen($value) > $matches[1]) {
                $errors[$field] = "Il campo {$field} non può essere più lungo di {$matches[1]} caratteri";
            }
        }

        return $errors;
    }

    /**
     * Ottiene l'input della richiesta
     */
    protected function input($key = null, $default = null)
    {
        $input = array_merge($_GET, $_POST);

        if ($key === null) {
            return $this->sanitize($input);
        }

        return $this->sanitize($input[$key] ?? $default);
    }

    /**
     * Genera URL utilizzando la configurazione dell'applicazione
     */
    protected function url($path = '', $params = [])
    {
        // Usa BASE_URL dalla configurazione
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';

        // Se il path è vuoto, ritorna solo BASE_URL
        if (empty($path)) {
            $url = $baseUrl ?: '/';
        } else {
            $path = '/' . ltrim($path, '/');
            $url = $baseUrl . $path;
        }

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    /**
     * Log attività utente
     */
    protected function logActivity($category, $activityType, $description, $note = '', $textQuery = '')
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        return ActivityLog::logActivity(
            $_SESSION['user_id'],
            $category,
            $activityType,
            $description,
            $note,
            $textQuery
        );
    }

    /**
     * Renderizza un widget specifico
     */
    protected function renderWidget($widget, $data)
    {
        $widgetKey = $widget->widget_key;
        $widgetSize = $widget->widget_size ?? 'medium';
        $widgetColor = $widget->widget_color ?? 'blue';

        // Mappa delle classi CSS per i colori (icone)
        $colorClasses = [
            'blue' => 'from-blue-500 to-blue-600',
            'green' => 'from-green-500 to-emerald-600',
            'yellow' => 'from-yellow-500 to-orange-500',
            'purple' => 'from-purple-500 to-indigo-600',
            'red' => 'from-red-500 to-red-600',
            'gray' => 'from-gray-500 to-gray-600',
        ];

        // Mappa delle classi CSS per gli sfondi sfumati
        $backgroundGradients = [
            'blue' => 'bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/10 dark:to-indigo-800/10',
            'green' => 'bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/10 dark:to-emerald-800/10',
            'yellow' => 'bg-gradient-to-br from-yellow-50 to-orange-100 dark:from-yellow-900/10 dark:to-orange-800/10',
            'purple' => 'bg-gradient-to-br from-purple-50 to-indigo-100 dark:from-purple-900/10 dark:to-indigo-800/10',
            'red' => 'bg-gradient-to-br from-red-50 to-rose-100 dark:from-red-900/10 dark:to-rose-800/10',
            'gray' => 'bg-gradient-to-br from-gray-50 to-slate-100 dark:from-gray-900/10 dark:to-slate-800/10',
        ];

        // Mappa delle classi CSS per le dimensioni
        $sizeClasses = [
            'small' => 'col-span-1',
            'medium' => 'col-span-1 sm:col-span-1 md:col-span-1',
            'large' => 'col-span-1 sm:col-span-2',
            'full' => 'col-span-full'
        ];

        $gradientClass = $colorClasses[$widgetColor] ?? $colorClasses['blue'];
        $backgroundGradient = $backgroundGradients[$widgetColor] ?? $backgroundGradients['blue'];
        $sizeClass = $sizeClasses[$widgetSize] ?? $sizeClasses['medium'];

        // Determina se il widget è di tipo statistiche per applicare il gradiente
        $isStatsWidget = in_array($widgetKey, ['riparazioni', 'my_riparazioni', 'production_week', 'production_month', 'quality_today', 'export_recent']);
        $backgroundClass = $isStatsWidget ? $backgroundGradient : 'bg-white dark:bg-gray-800/40';

        $html = '<div class="' . $sizeClass . ' rounded-2xl border border-gray-200 ' . $backgroundClass . ' p-5 dark:border-gray-800 md:p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 backdrop-blur-sm">';

        // Icona widget
        $html .= '<div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r ' . $gradientClass . ' shadow-lg">';
        $html .= '<i class="' . htmlspecialchars($widget->widget_icon) . ' text-white"></i>';
        $html .= '</div>';

        // Contenuto specifico del widget
        switch ($widgetKey) {
            case 'riparazioni':
                $html .= $this->renderRiparazioniWidget($widget, $data);
                break;
            case 'my_riparazioni':
                $html .= $this->renderMyRiparazioniWidget($widget, $data);
                break;
            case 'production_week':
                $html .= $this->renderProductionWeekWidget($widget, $data);
                break;
            case 'production_month':
                $html .= $this->renderProductionMonthWidget($widget, $data);
                break;
            case 'quality_today':
                $html .= $this->renderQualityTodayWidget($widget, $data);
                break;
            case 'export_recent':
                $html .= $this->renderExportRecentWidget($widget, $data);
                break;
            case 'quick_actions':
                $html .= $this->renderQuickActionsWidget($widget, $data);
                break;
            case 'recent_activities':
                $html .= $this->renderRecentActivitiesWidget($widget, $data);
                break;
            default:
                $html .= $this->renderDefaultWidget($widget, $data);
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Renderizza widget riparazioni
     */
    private function renderRiparazioniWidget($widget, $data)
    {
        $html = '<div class="mt-5 flex items-end justify-between">';
        $html .= '<div>';
        $html .= '<span class="text-sm text-gray-500 dark:text-gray-400">Riparazioni Aperte</span>';
        $html .= '<h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">';
        $html .= '<span class="counter-animate" data-target="' . ($data['totali'] ?? 0) . '">0</span>';
        $html .= '</h4>';
        $html .= '</div>';
        $html .= '<div class="text-right">';
        $html .= '<span class="text-xs text-gray-500 dark:text-gray-400">Oggi</span>';
        $html .= '<div class="text-sm font-medium text-blue-600 dark:text-blue-400">';
        $html .= '<span class="counter-animate" data-target="' . ($data['oggi'] ?? 0) . '">0</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Renderizza widget le mie riparazioni
     */
    private function renderMyRiparazioniWidget($widget, $data)
    {
        $html = '<div class="mt-5 flex items-end justify-between">';
        $html .= '<div>';
        $html .= '<span class="text-sm text-gray-500 dark:text-gray-400">Le Mie Riparazioni</span>';
        $html .= '<h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">';
        $html .= '<span class="counter-animate" data-target="' . ($data['totali'] ?? 0) . '">0</span>';
        $html .= '</h4>';
        $html .= '</div>';
        $html .= '<div class="text-right">';
        $html .= '<span class="text-xs text-gray-500 dark:text-gray-400">Oggi</span>';
        $html .= '<div class="text-sm font-medium text-blue-600 dark:text-blue-400">';
        $html .= '<span class="counter-animate" data-target="' . ($data['oggi'] ?? 0) . '">0</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Renderizza widget produzione settimana
     */
    private function renderProductionWeekWidget($widget, $data)
    {
        $html = '<div class="mt-5 flex items-end justify-between">';
        $html .= '<div>';
        $html .= '<span class="text-sm text-gray-500 dark:text-gray-400">Produzione Settimana</span>';
        $html .= '<h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">';
        $html .= '<span class="counter-animate" data-target="' . ($data['totale'] ?? 0) . '">0</span>';
        $html .= '</h4>';
        $html .= '</div>';
        $html .= '<div class="text-right">';
        $html .= '<span class="text-xs text-gray-500 dark:text-gray-400">Settimana ' . ($data['settimana'] ?? 0) . '</span>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Renderizza widget produzione mese
     */
    private function renderProductionMonthWidget($widget, $data)
    {
        $html = '<div class="mt-5 flex items-end justify-between">';
        $html .= '<div>';
        $html .= '<span class="text-sm text-gray-500 dark:text-gray-400">Produzione Mese</span>';
        $html .= '<h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">';
        $html .= '<span class="counter-animate" data-target="' . ($data['totale'] ?? 0) . '">0</span>';
        $html .= '</h4>';
        $html .= '</div>';
        $html .= '<div class="text-right">';
        $html .= '<span class="text-xs text-gray-500 dark:text-gray-400">' . ucfirst($data['mese'] ?? 'Mese') . '</span>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Renderizza widget controlli qualità
     */
    private function renderQualityTodayWidget($widget, $data)
    {
        $html = '<div class="mt-5 flex items-end justify-between">';
        $html .= '<div>';
        $html .= '<span class="text-sm text-gray-500 dark:text-gray-400">Controlli Oggi</span>';
        $html .= '<h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">';
        $html .= '<span class="counter-animate" data-target="' . ($data['controlli'] ?? 0) . '">0</span>';
        $html .= '</h4>';
        $html .= '</div>';
        $html .= '<div class="text-right">';
        $html .= '<span class="text-xs text-gray-500 dark:text-gray-400">Difetti</span>';
        $html .= '<div class="text-sm font-medium text-red-600 dark:text-red-400">';
        $html .= '<span class="counter-animate" data-target="' . ($data['difetti'] ?? 0) . '">0</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Renderizza widget export recenti
     */
    private function renderExportRecentWidget($widget, $data)
    {
        $html = '<div class="mt-5 flex items-end justify-between">';
        $html .= '<div>';
        $html .= '<span class="text-sm text-gray-500 dark:text-gray-400">Export Oggi</span>';
        $html .= '<h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">';
        $html .= '<span class="counter-animate" data-target="' . ($data['oggi'] ?? 0) . '">0</span>';
        $html .= '</h4>';
        $html .= '</div>';
        $html .= '<div class="text-right">';
        $html .= '<span class="text-xs text-gray-500 dark:text-gray-400">Settimana</span>';
        $html .= '<div class="text-sm font-medium text-purple-600 dark:text-purple-400">';
        $html .= '<span class="counter-animate" data-target="' . ($data['settimana'] ?? 0) . '">0</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Renderizza widget azioni rapide
     */
    private function renderQuickActionsWidget($widget, $data)
    {
        $actions = $data['available_actions'] ?? [];
        $html = '<div class="mt-5">';
        $html .= '<span class="text-sm text-gray-500 dark:text-gray-400">Azioni Rapide</span>';

        if (empty($actions)) {
            $html .= '<div class="mt-3 text-center py-4">';
            $html .= '<i class="fas fa-bolt text-2xl text-gray-400 mb-2"></i>';
            $html .= '<p class="text-xs text-gray-500">Nessuna azione disponibile</p>';
            $html .= '</div>';
        } else {
            $html .= '<div class="mt-3 space-y-2">';

            foreach (array_slice($actions, 0, 4) as $action) {
                // Mappa colori per i gradienti
                $gradientClass = '';
                switch ($action['color']) {
                    case 'blue':
                        $gradientClass = 'from-blue-500 to-blue-600';
                        $hoverClass = 'hover:bg-blue-50 hover:border-blue-300 dark:hover:bg-blue-900/20 dark:hover:border-blue-500';
                        break;
                    case 'green':
                        $gradientClass = 'from-green-500 to-emerald-600';
                        $hoverClass = 'hover:bg-green-50 hover:border-green-300 dark:hover:bg-green-900/20 dark:hover:border-green-500';
                        break;
                    case 'yellow':
                        $gradientClass = 'from-yellow-500 to-orange-500';
                        $hoverClass = 'hover:bg-yellow-50 hover:border-yellow-300 dark:hover:bg-yellow-900/20 dark:hover:border-yellow-500';
                        break;
                    case 'purple':
                        $gradientClass = 'from-purple-500 to-indigo-600';
                        $hoverClass = 'hover:bg-purple-50 hover:border-purple-300 dark:hover:bg-purple-900/20 dark:hover:border-purple-500';
                        break;
                    case 'orange':
                        $gradientClass = 'from-orange-500 to-red-500';
                        $hoverClass = 'hover:bg-orange-50 hover:border-orange-300 dark:hover:bg-orange-900/20 dark:hover:border-orange-500';
                        break;
                    default:
                        $gradientClass = 'from-gray-500 to-gray-600';
                        $hoverClass = 'hover:bg-gray-50 hover:border-gray-300 dark:hover:bg-gray-900/20 dark:hover:border-gray-500';
                        break;
                }

                $html .= '<a href="' . htmlspecialchars($action['url']) . '" ';
                $html .= 'class="flex items-center p-3 border border-gray-200 rounded-xl ' . $hoverClass . ' dark:border-gray-700 transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">';
                $html .= '<div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r ' . $gradientClass . ' shadow-sm">';
                $html .= '<i class="' . htmlspecialchars($action['icon']) . ' text-white text-sm"></i>';
                $html .= '</div>';
                $html .= '<span class="ml-2 text-sm font-medium text-gray-900 dark:text-white">' . htmlspecialchars($action['name']) . '</span>';
                $html .= '</a>';
            }

            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Renderizza widget default per tipi non implementati
     */
    private function renderDefaultWidget($widget, $data)
    {
        $html = '<div class="mt-5">';
        $html .= '<span class="text-sm text-gray-500 dark:text-gray-400">' . htmlspecialchars($widget['widget_name']) . '</span>';
        $html .= '<h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">In sviluppo</h4>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Renderizza widget attività recenti
     */
    private function renderRecentActivitiesWidget($widget, $data)
    {
        $activities = $data['activities'] ?? [];
        $html = '<div class="mt-5">';
        $html .= '<span class="text-sm text-gray-500 dark:text-gray-400">Attività Recenti</span>';

        if (empty($activities)) {
            $html .= '<div class="mt-3 text-center py-4">';
            $html .= '<i class="far fa-calendar-times text-2xl text-gray-400 mb-2"></i>';
            $html .= '<p class="text-xs text-gray-500">Nessuna attività recente</p>';
            $html .= '</div>';
        } else {
            $html .= '<div class="mt-3 space-y-2">';

            foreach ($activities as $activity) {
                $html .= '<div class="flex items-start space-x-2 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">';
                $html .= '<div class="flex-shrink-0">';
                $html .= '<div class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">';
                $html .= '<i class="fas fa-' . htmlspecialchars($activity['icon']) . ' text-xs text-blue-600 dark:text-blue-400"></i>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="flex-1 min-w-0">';
                $html .= '<p class="text-xs font-medium text-gray-900 dark:text-white truncate uppercase">';
                $html .= htmlspecialchars($activity['description']);
                $html .= '</p>';
                $html .= '<p class="text-xs text-gray-500 dark:text-gray-400">';
                $html .= htmlspecialchars($activity['time']);
                $html .= '</p>';
                $html .= '</div>';
                $html .= '</div>';
            }

            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }


    /**
     * Formatta il tempo trascorso in formato "tempo fa"
     */
    protected function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) {
            return $time == 1 ? '1 secondo fa' : $time . ' secondi fa';
        }

        $time = round($time / 60);
        if ($time < 60) {
            return $time == 1 ? '1 minuto fa' : $time . ' minuti fa';
        }

        $time = round($time / 60);
        if ($time < 24) {
            return $time == 1 ? '1 ora fa' : $time . ' ore fa';
        }

        $time = round($time / 24);
        if ($time < 30) {
            return $time == 1 ? '1 giorno fa' : $time . ' giorni fa';
        }

        $time = round($time / 30);
        if ($time < 12) {
            return $time == 1 ? '1 mese fa' : $time . ' mesi fa';
        }

        $time = round($time / 12);
        return $time == 1 ? '1 anno fa' : $time . ' anni fa';
    }
}