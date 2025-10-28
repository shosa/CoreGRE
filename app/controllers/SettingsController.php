<?php

use App\Models\Setting;
use App\Models\Department;
use App\Models\Laboratory;
use App\Models\Line;
use App\Models\CoreData;
use App\Models\Repair;
use App\Models\QualityRecord;
use Illuminate\Database\Capsule\Manager as Capsule;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Settings Controller - Sistema Impostazioni Coregre 
 * Gestisce tutte le impostazioni di sistema: Database, Strumenti, Email, Tabelle, Generale
 */
class SettingsController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->requirePermission('settings');
    }

    /**
     * Dashboard principale impostazioni
     */
    public function index()
    {
        $settings = Setting::getAllGrouped();

        $data = [
            'pageTitle' => 'Impostazioni Sistema',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/'],
                ['title' => 'Impostazioni', 'url' => '/settings']
            ],
            'settings' => $settings,
            'stats' => $this->getSettingsStats(),
            'datiInfo' => $this->getDatiInfo()
        ];

        $this->render('settings/index', $data);
    }

    /**
     * API per caricare sezioni dinamicamente
     */
    public function loadSection()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $section = $_POST['section'] ?? '';
        
        try {
            switch ($section) {
                case 'database':
                    echo $this->getDatabaseSection();
                    break;
                case 'tools':
                    echo $this->getToolsSection();
                    break;
                case 'email':
                    echo $this->getEmailSection();
                    break;
                case 'tables':
                    echo $this->getTablesSection();
                    break;
                case 'general':
                    echo $this->getGeneralSection();
                    break;
                case 'logs':
                    echo $this->getLogsSection();
                    break;
                default:
                    echo '<div class="text-center p-8"><i class="fas fa-cog text-4xl text-gray-400 mb-4"></i><p class="text-gray-500">Seleziona una sezione dal menu</p></div>';
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-error"><i class="fas fa-exclamation-circle mr-2"></i>Errore nel caricamento: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    /**
     * Salva impostazioni
     */
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $section = $_POST['section'] ?? '';
        
        try {
            switch ($section) {
                case 'email':
                    $this->saveEmailSettings();
                    break;
                case 'pagination':
                case 'system':
                case 'notifications':
                    $this->saveGeneralSettings($section);
                    break;
                case 'tables_departments':
                    $this->saveDepartmentSettings();
                    break;
                case 'tables_laboratories':
                    $this->saveLaboratorySettings();
                    break;
                case 'tables_lines':
                    $this->saveLineSettings();
                    break;
                default:
                    throw new Exception('Sezione non valida');
            }
            
            $_SESSION['alert_success'] = 'Impostazioni salvate correttamente';
            echo json_encode(['success' => true]);
            
        } catch (Exception $e) {
            $_SESSION['alert_error'] = 'Errore nel salvataggio: ' . $e->getMessage();
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Gestione upload XLSX
     */
    public function uploadXlsx()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        try {
            if (!isset($_FILES['xlsx_file'])) {
                throw new Exception('Nessun file selezionato');
            }

            $file = $_FILES['xlsx_file'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Errore nel caricamento del file');
            }

            // Processa il file XLSX per aggiornamento cartellini
            $result = $this->processXlsxUpdate($file);
            
            $_SESSION['alert_success'] = "File elaborato correttamente. {$result['updated']} record aggiornati";
            echo json_encode(['success' => true, 'data' => $result]);
            
        } catch (Exception $e) {
            // Log a more detailed error to the PHP error log
            error_log("Gemini Debug - Exception in uploadXlsx: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile());
            error_log("Gemini Debug - Trace: " . $e->getTraceAsString());

            $_SESSION['alert_error'] = 'Errore nell\'elaborazione: ' . $e->getMessage();
            echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }


    /**
     * Sezione Database
     */
    private function getDatabaseSection()
    {
        return '
        <div class="space-y-6">
            <div class="border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    <i class="fas fa-database mr-2 text-blue-600"></i>
                    Aggiornamento Cartellini
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Carica file XLSX per aggiornare i dati dei cartellini</p>
            </div>
            
            <form id="xlsx-upload-form" class="space-y-4" enctype="multipart/form-data">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        File XLSX
                    </label>
                    
                    <div class="relative">
                        <input type="file" id="xlsx-file-input" name="xlsx_file" accept=".xlsx,.xls" required
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                        
                        <!-- File Selected Indicator -->
                        <div id="file-selected-info" class="hidden mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-file-excel text-green-600 mr-2"></i>
                                <div>
                                    <p id="selected-filename" class="text-sm font-medium text-green-700 dark:text-green-400"></p>
                                    <p id="selected-filesize" class="text-xs text-green-600 dark:text-green-500"></p>
                                </div>
                                <button type="button" onclick="clearSelectedFile()" class="ml-auto text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <p class="mt-1 text-sm text-gray-500">Solo file Excel (.xlsx, .xls)</p>
                </div>
                
                <!-- Progress Indicator -->
                <div id="upload-progress" class="hidden">
                    <div class="bg-blue-100 dark:bg-blue-900/30 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Elaborazione in corso...</span>
                            <span id="progress-percentage" class="text-sm text-blue-600 dark:text-blue-400">0%</span>
                        </div>
                        <div class="w-full bg-blue-200 dark:bg-blue-800 rounded-full h-2">
                            <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="progress-text" class="text-xs text-blue-600 dark:text-blue-400 mt-2">Preparazione file...</p>
                    </div>
                </div>
                
                <button id="upload-btn" type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-2 px-4 rounded-lg transition-all">
                    <i id="upload-icon" class="fas fa-upload mr-2"></i>
                    <span id="upload-text">Carica e Processa</span>
                </button>
            </form>
        </div>';
    }

    /**
     * Sezione Strumenti
     */
    private function getToolsSection()
    {
        return '
        <div class="space-y-6">
            <!-- Log Sistema -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    <i class="fas fa-code mr-2 text-green-600"></i>
                    Log Sistema
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Visualizza i log di sistema e attività utenti</p>
                
                <button onclick="loadSystemLogs()" class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium py-2 px-4 rounded-lg transition-all">
                    <i class="fas fa-eye mr-2"></i>
                    Visualizza Log
                </button>
            </div>
        </div>';
    }

    /**
     * Sezione Email
     */
    private function getEmailSection()
    {
        $settings = $this->getEmailSettings();
        
        return '
        <div class="space-y-6">
            <div class="border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    <i class="fas fa-envelope mr-2 text-red-600"></i>
                    SMTP E-mail Produzione
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Configurazione server SMTP per invio email automatiche</p>
            </div>
            
            <form id="email-settings-form" class="space-y-4">
                <input type="hidden" name="section" value="email">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Indirizzo E-Mail del Mittente</label>
                    <input type="email" name="production_senderEmail" value="' . htmlspecialchars($settings['production_senderEmail'] ?? '') . '" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                    <input type="password" name="production_senderPassword" value="' . htmlspecialchars($settings['production_senderPassword'] ?? '') . '" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Server SMTP</label>
                        <input type="text" name="production_senderSMTP" value="' . htmlspecialchars($settings['production_senderSMTP'] ?? '') . '" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Porta</label>
                        <input type="number" name="production_senderPORT" value="' . htmlspecialchars($settings['production_senderPORT'] ?? '587') . '" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Destinatari (separare con ";")</label>
                    <textarea name="production_recipients" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">' . htmlspecialchars($settings['production_recipients'] ?? '') . '</textarea>
                </div>
                
                <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium py-2 px-4 rounded-lg transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Salva Configurazione
                </button>
            </form>
        </div>';
    }

    /**
     * Sezione Tabelle
     */
    private function getTablesSection()
    {
        $departments = $this->getDepartments();
        $laboratories = $this->getLaboratories();
        $lines = $this->getLines();
        
        return '
        <div class="space-y-8">
            <!-- Linee -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-tasks mr-2 text-blue-600"></i>
                    Gestione Linee
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sigla</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Descrizione</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="lines-tbody" class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            ' . $this->renderLinesTable($lines) . '
                        </tbody>
                    </table>
                </div>
                
                <button onclick="addNewLine()" class="mt-4 w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-2 px-4 rounded-lg transition-all">
                    <i class="fas fa-plus mr-2"></i>
                    Aggiungi Linea
                </button>
            </div>
            
            <!-- Laboratori -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-microscope mr-2 text-purple-600"></i>
                    Gestione Laboratori
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nome</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="laboratories-tbody" class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            ' . $this->renderLaboratoriesTable($laboratories) . '
                        </tbody>
                    </table>
                </div>
                
                <button onclick="addNewLaboratory()" class="mt-4 w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-2 px-4 rounded-lg transition-all">
                    <i class="fas fa-plus mr-2"></i>
                    Aggiungi Laboratorio
                </button>
            </div>
            
            <!-- Reparti -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-building mr-2 text-green-600"></i>
                    Gestione Reparti
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nome</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="departments-tbody" class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            ' . $this->renderDepartmentsTable($departments) . '
                        </tbody>
                    </table>
                </div>
                
                <button onclick="addNewDepartment()" class="mt-4 w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium py-2 px-4 rounded-lg transition-all">
                    <i class="fas fa-plus mr-2"></i>
                    Aggiungi Reparto
                </button>
            </div>
        </div>';
    }

    /**
     * Sezione Generale
     */
    private function getGeneralSection()
    {
        return '
        <div class="space-y-6">
            <div class="border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    <i class="fas fa-sync-alt mr-2 text-indigo-600"></i>
                    Aggiornamento Applicazione
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Controlla e installa aggiornamenti del sistema</p>
            </div>
            
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Versione Corrente</h4>
                        <p class="text-sm text-blue-600 dark:text-blue-300">COREGRE v2.1.0 (Build 2024.12)</p>
                    </div>
                </div>
            </div>
            
            <div class="space-y-4">
                <button onclick="checkUpdates()" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-medium py-2 px-4 rounded-lg transition-all">
                    <i class="fas fa-search mr-2"></i>
                    Controlla Aggiornamenti
                </button>
                
                <div class="grid grid-cols-2 gap-4">
                    <button onclick="clearCache()" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-all">
                        <i class="fas fa-trash mr-2"></i>
                        Pulisci Cache
                    </button>
                    
                    <button onclick="optimizeDb()" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-all">
                        <i class="fas fa-database mr-2"></i>
                        Ottimizza DB
                    </button>
                </div>
            </div>
        </div>';
    }

    // Helper methods per database
    private function getSettingsStats()
    {
        $stats = [];

        try {
            // Conta impostazioni salvate usando Eloquent
            $stats['total_settings'] = Setting::count();

            // Conta reparti usando Eloquent
            $stats['departments_count'] = Department::count();

            // Conta laboratori usando Eloquent
            $stats['laboratories_count'] = Laboratory::count();

            // Conta linee usando Eloquent
            $stats['lines_count'] = Line::count();

        } catch (Exception $e) {
            // Log error but continue
            $stats = [
                'total_settings' => 0,
                'departments_count' => 0,
                'laboratories_count' => 0,
                'lines_count' => 0
            ];
        }

        return $stats;
    }

    private function getDatiInfo()
    {
        $info = [
            'totalRows' => 0,
            'minCartel' => 0,
            'maxCartel' => 0
        ];
        
        try {
            $info['totalRows'] = CoreData::count();
            $info['minCartel'] = CoreData::min('Cartel');
            $info['maxCartel'] = CoreData::max('Cartel');
        } catch (Exception $e) {
            // Log error but continue with defaults
        }
        
        return $info;
    }

    private function getEmailSettings()
    {
        $settings = [];
        $items = ['production_senderEmail', 'production_senderPassword', 'production_senderSMTP', 'production_senderPORT', 'production_recipients'];

        foreach ($items as $item) {
            $settings[$item] = Setting::getValue($item, '');
        }

        return $settings;
    }

    private function getDepartments()
    {
        return Department::orderBy('Nome')->get();
    }

    private function getLaboratories()
    {
        return Laboratory::orderBy('Nome')->get();
    }

    private function getLines()
    {
        return Line::orderBy('descrizione')->get();
    }

    private function saveEmailSettings()
    {
        $settings = [
            'production_senderEmail' => $_POST['production_senderEmail'] ?? '',
            'production_senderPassword' => $_POST['production_senderPassword'] ?? '',
            'production_senderSMTP' => $_POST['production_senderSMTP'] ?? '',
            'production_senderPORT' => $_POST['production_senderPORT'] ?? '587',
            'production_recipients' => $_POST['production_recipients'] ?? ''
        ];

        foreach ($settings as $item => $value) {
            Setting::setValue($item, $value);
        }

        $this->logActivity('settings', 'email_save', 'Impostazioni email salvate');
    }

    private function saveGeneralSettings($section)
    {
        // Map sections to their settings keys
        $settingsMap = [
            'sistema' => [
                'pagination_default', 'pagination_logs', 'pagination_database',
                'pagination_export', 'pagination_treeview', 'pagination_max_limit',
                'cache_ttl', 'recent_items_limit', 'max_upload_size_mb',
                'session_timeout_warning', 'php_cli_path'
            ],
            'pagination' => [
                'pagination_default', 'pagination_logs', 'pagination_database',
                'pagination_export', 'pagination_treeview', 'pagination_max_limit'
            ],
            'system' => [
                'cache_ttl', 'recent_items_limit', 'max_upload_size_mb',
                'session_timeout_warning', 'php_cli_path'
            ],
            'notifiche' => [
                'alert_timeout', 'alert_position', 'enable_browser_notifications', 'enable_sound_notifications'
            ],
            'notifications' => [
                'alert_timeout', 'alert_position', 'enable_browser_notifications', 'enable_sound_notifications'
            ]
        ];

        $settingsToSave = $settingsMap[$section] ?? [];

        foreach ($settingsToSave as $key) {
            if (isset($_POST[$key])) {
                Setting::setValue($key, $_POST[$key]);
            }
        }

        $this->logActivity('settings', "{$section}_save", "Impostazioni {$section} salvate");
    }

    private function renderLinesTable($lines)
    {
        $html = '';
        foreach ($lines as $line) {
            $html .= '<tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">' . htmlspecialchars($line->ID ?? '') . '</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="text" value="' . htmlspecialchars($line->sigla ?? '') . '" 
                           onchange="updateLine(' . ($line->ID ?? 0) . ', this.value, \'' . htmlspecialchars($line->descrizione ?? '') . '\')"
                           class="w-full px-2 py-1 border rounded text-sm" placeholder="Sigla (2 caratteri)">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="text" value="' . htmlspecialchars($line->descrizione ?? '') . '" 
                           onchange="updateLineDescription(' . ($line->ID ?? 0) . ', \'' . htmlspecialchars($line->sigla ?? '') . '\', this.value)"
                           class="w-full px-2 py-1 border rounded text-sm" placeholder="Descrizione">
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <button onclick="deleteLine(' . ($line->ID ?? 0) . ')" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>';
        }
        return $html;
    }

    private function renderLaboratoriesTable($laboratories)
    {
        $html = '';
        foreach ($laboratories as $lab) {
            $html .= '<tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">' . htmlspecialchars($lab->ID ?? '') . '</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="text" value="' . htmlspecialchars($lab->Nome ?? '') . '" 
                           onchange="updateLaboratory(' . ($lab->ID ?? 0) . ', this.value)"
                           class="w-full px-2 py-1 border rounded text-sm">
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <button onclick="deleteLaboratory(' . ($lab->ID ?? 0) . ')" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>';
        }
        return $html;
    }

    private function renderDepartmentsTable($departments)
    {
        $html = '';
        foreach ($departments as $dept) {
            $html .= '<tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">' . htmlspecialchars($dept->ID ?? '') . '</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="text" value="' . htmlspecialchars($dept->Nome ?? '') . '" 
                           onchange="updateDepartment(' . ($dept->ID ?? 0) . ', this.value)"
                           class="w-full px-2 py-1 border rounded text-sm">
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <button onclick="deleteDepartment(' . ($dept->ID ?? 0) . ')" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>';
        }
        return $html;
    }

    /**
     * API per gestione reparti
     */
    public function manageDepartment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        try {
            $action = $_POST['action'] ?? '';
            $id = $_POST['id'] ?? 0;
            
            switch ($action) {
                case 'create':
                    $nome = $_POST['nome'] ?? '';
                    if (empty($nome)) {
                        throw new Exception('Nome reparto obbligatorio');
                    }

                    $department = Department::create(['Nome' => $nome]);

                    $_SESSION['alert_success'] = 'Reparto aggiunto correttamente';
                    echo json_encode(['success' => true, 'id' => $department->ID]);
                    break;
                    
                case 'update':
                    $nome = $_POST['nome'] ?? '';
                    if (empty($nome)) {
                        throw new Exception('Nome reparto obbligatorio');
                    }
                    
                    $department = Department::findOrFail($id);
                    $department->Nome = $nome;
                    $department->save();
                    
                    $_SESSION['alert_success'] = 'Reparto modificato correttamente';
                    echo json_encode(['success' => true]);
                    break;
                    
                case 'delete':
                    $department = Department::findOrFail($id);

                    // Verifica se il reparto è utilizzato in riparazioni
                    $isUsed = Repair::where('REPARTO', $department->Nome)->exists();
                    if ($isUsed) {
                        throw new Exception('Impossibile eliminare: reparto utilizzato in riparazioni');
                    }
                    
                    $department->delete();
                    
                    $_SESSION['alert_success'] = 'Reparto eliminato correttamente';
                    echo json_encode(['success' => true]);
                    break;
                    
                default:
                    throw new Exception('Azione non valida');
            }
            
            $this->logActivity('settings', 'department_' . $action, "Reparto {$action}: " . ($_POST['nome'] ?? "ID {$id}"));
            
        } catch (Exception $e) {
            $_SESSION['alert_error'] = 'Errore: ' . $e->getMessage();
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * API per gestione laboratori
     */
    public function manageLaboratory()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        try {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'create':
                    $nome = $_POST['nome'] ?? '';
                    if (empty($nome)) {
                        throw new Exception('Nome laboratorio obbligatorio');
                    }
                    
                    $lab = Laboratory::create(['Nome' => $nome]);
                    
                    $_SESSION['alert_success'] = 'Laboratorio aggiunto correttamente';
                    echo json_encode(['success' => true, 'id' => $lab->ID]);
                    break;
                    
                case 'update':
                    $id = $_POST['id'] ?? 0;
                    $nome = $_POST['nome'] ?? '';
                    
                    if (empty($nome)) {
                        throw new Exception('Nome laboratorio obbligatorio');
                    }
                    
                    $lab = Laboratory::findOrFail($id);
                    $lab->Nome = $nome;
                    $lab->save();
                    
                    $_SESSION['alert_success'] = 'Laboratorio modificato correttamente';
                    echo json_encode(['success' => true]);
                    break;
                    
                case 'delete':
                    $id = $_POST['id'] ?? 0;
                    
                    $lab = Laboratory::findOrFail($id);
                    $lab->delete();
                    
                    $_SESSION['alert_success'] = 'Laboratorio eliminato correttamente';
                    echo json_encode(['success' => true]);
                    break;
                    
                default:
                    throw new Exception('Azione non valida');
            }
            
            $this->logActivity('settings', 'laboratory_' . $action, "Laboratorio {$action}: " . ($_POST['nome'] ?? $id));
            
        } catch (Exception $e) {
            $_SESSION['alert_error'] = 'Errore: ' . $e->getMessage();
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * API per gestione linee
     */
    public function manageLine()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        try {
            $action = $_POST['action'] ?? '';
            $id = $_POST['id'] ?? 0;
            
            switch ($action) {
                case 'create':
                    $sigla = $_POST['sigla'] ?? '';
                    $descrizione = $_POST['descrizione'] ?? '';
                    
                    if (empty($sigla) || empty($descrizione)) {
                        throw new Exception('Sigla e descrizione sono obbligatorie');
                    }
                    
                    $line = Line::create(['sigla' => $sigla, 'descrizione' => $descrizione]);
                    
                    $_SESSION['alert_success'] = 'Linea aggiunta correttamente';
                    echo json_encode(['success' => true, 'id' => $line->ID]);
                    break;
                    
                case 'update':
                    $sigla = $_POST['sigla'] ?? '';
                    $descrizione = $_POST['descrizione'] ?? '';
                    
                    if (empty($sigla) || empty($descrizione)) {
                        throw new Exception('Sigla e descrizione sono obbligatorie');
                    }
                    
                    $line = Line::findOrFail($id);
                    $line->sigla = $sigla;
                    $line->descrizione = $descrizione;
                    $line->save();
                    
                    $_SESSION['alert_success'] = 'Linea modificata correttamente';
                    echo json_encode(['success' => true]);
                    break;
                    
                case 'delete':
                    $line = Line::findOrFail($id);
                    $line->delete();
                    
                    $_SESSION['alert_success'] = 'Linea eliminata correttamente';
                    echo json_encode(['success' => true]);
                    break;
                    
                default:
                    throw new Exception('Azione non valida');
            }
            
            $logDetails = $action === 'create' ? ($line->sigla ?? '') : "ID {$id}";
            $this->logActivity('settings', 'line_' . $action, "Linea {$action}: {$logDetails}");
            
        } catch (Exception $e) {
            $_SESSION['alert_error'] = 'Errore: ' . $e->getMessage();
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Metodi avanzati per funzionalità complete
    public function getImportProgress()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $progress = $_SESSION['import_progress'] ?? ['processed' => 0, 'total' => 0, 'text' => 'In attesa...'];
        header('Content-Type: application/json');
        echo json_encode($progress);
        exit();
    }

    private function processXlsxUpdate($file)
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            ini_set('max_execution_time', 300); // 5 minuti

            // STEP 1: Get records to preserve from the current database
            $preservedRecords = CoreData::where(function ($query) {
                $query->orWhereExists(function ($subQuery) {
                    $subQuery->select(Capsule::raw(1))->from('track_links')->whereColumn('track_links.cartel', 'core_dati.Cartel');
                })->orWhereExists(function ($subQuery) {
                    $subQuery->select(Capsule::raw(1))->from('rip_riparazioni')->whereColumn('rip_riparazioni.CARTELLINO', 'core_dati.Cartel');
                })->orWhereExists(function ($subQuery) {
                    $subQuery->select(Capsule::raw(1))->from('cq_records')->whereColumn('cq_records.numero_cartellino', 'core_dati.Cartel');
                });
            })->get()->toArray();

            // STEP 2: Load and process the new data from the XLSX file
            $spreadsheet = IOFactory::load($file['tmp_name']);
            $sheet = $spreadsheet->getActiveSheet();
            $excelData = $sheet->toArray();
            $header = array_shift($excelData);
            $dbColumns = ['St', 'Ordine', 'Rg', 'CCli', 'Ragione Sociale', 'Cartel', 'Commessa Cli', 'PO', 'Articolo', 'Descrizione Articolo', 'Nu', 'Marca Etich', 'Ln', 'P01', 'P02', 'P03', 'P04', 'P05', 'P06', 'P07', 'P08', 'P09', 'P10', 'P11', 'P12', 'P13', 'P14', 'P15', 'P16', 'P17', 'P18', 'P19', 'P20', 'Tot'];

            $newDataToInsert = [];
            foreach ($excelData as $row) {
                $rowData = array_slice($row, 0, count($dbColumns));
                while (count($rowData) < count($dbColumns)) {
                    $rowData[] = null;
                }
                $newDataToInsert[] = array_combine($dbColumns, $rowData);
            }

            // STEP 3: Filter new data to exclude any records that must be preserved.
            $preservedCartelsLookup = array_flip(array_column($preservedRecords, 'Cartel'));
            $filteredNewData = array_filter($newDataToInsert, function ($newRecord) use ($preservedCartelsLookup) {
                return !isset($preservedCartelsLookup[$newRecord['Cartel']]);
            });

            $insertedCount = count($filteredNewData);
            $preservedCount = count($preservedRecords);
            $errors = [];

            // Progress reporting setup
            $chunkSize = 500; // Smaller chunk size for more frequent updates
            $totalChunks = 0;
            if (!empty($filteredNewData)) {
                $totalChunks += count(array_chunk($filteredNewData, $chunkSize));
            }
            if (!empty($preservedRecords)) {
                $totalChunks += count(array_chunk($preservedRecords, $chunkSize));
            }
            $processedChunks = 0;
            $_SESSION['import_progress'] = ['processed' => 0, 'total' => $totalChunks, 'text' => 'Avvio...'];
            session_write_close();


            // STEP 4: Execute database operations within a transaction
            Capsule::transaction(function () use ($filteredNewData, $preservedRecords, $chunkSize, &$processedChunks, $totalChunks) {
                CoreData::query()->delete();
                
                session_start();
                $_SESSION['import_progress'] = ['processed' => $processedChunks, 'total' => $totalChunks, 'text' => 'Dati precedenti eliminati...'];
                session_write_close();

                // Insert the filtered new data in chunks
                if (!empty($filteredNewData)) {
                    foreach (array_chunk($filteredNewData, $chunkSize) as $chunk) {
                        CoreData::insert($chunk);
                        $processedChunks++;
                        session_start();
                        $_SESSION['import_progress'] = ['processed' => $processedChunks, 'total' => $totalChunks, 'text' => "Inserimento nuovi dati..."];
                        session_write_close();
                    }
                }

                // Re-insert the original preserved records in chunks
                if (!empty($preservedRecords)) {
                    $cleanPreservedRecords = array_map(function ($record) {
                        unset($record['ID']);
                        return $record;
                    }, $preservedRecords);

                    foreach (array_chunk($cleanPreservedRecords, $chunkSize) as $chunk) {
                        CoreData::insert($chunk);
                        $processedChunks++;
                        session_start();
                        $_SESSION['import_progress'] = ['processed' => $processedChunks, 'total' => $totalChunks, 'text' => "Inserimento dati preservati..."];
                        session_write_close();
                    }
                }
            });

            session_start();
            unset($_SESSION['import_progress']);
            session_write_close();

            $result = [
                'updated' => $insertedCount,
                'preserved' => $preservedCount,
                'errors' => $errors,
                'message' => "Importazione completata: {$insertedCount} nuovi record, {$preservedCount} cartellini preservati"
            ];

            if (!empty($errors)) {
                $result['warnings'] = count($errors) . ' errori durante l\'importazione';
            }

            $this->logActivity('settings', 'DATI_UPDATE', "Importati {$insertedCount} record, preservati {$preservedCount} cartellini");

            return $result;

        } catch (Exception $e) {
            session_start();
            unset($_SESSION['import_progress']);
            session_write_close();
            throw new Exception('Errore durante l\'importazione: ' . $e->getMessage());
        }
    }
    
    
    private function saveDepartmentSettings() 
    {
        // Già implementato in manageDepartment
    }
    
    private function saveLaboratorySettings() 
    {
        // Già implementato in manageLaboratory
    }
    
    private function saveLineSettings() 
    {
        // Già implementato in manageLine
    }

    /**
     * Sezione Log PHP Sistema
     */
    private function getLogsSection()
    {
        $logs = $this->getPhpErrorLogs();
        
        return '
        <div class="space-y-6">
            <div class="border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    <i class="fas fa-terminal mr-2 text-green-600"></i>
                    Log PHP Sistema
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Log degli errori PHP e attività del sistema</p>
            </div>
            
            <div class="bg-black rounded-lg p-4 font-mono text-sm overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-gray-400 ml-2">system.log</span>
                    </div>
                    <button onclick="refreshLogs()" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-sync-alt text-xs"></i>
                    </button>
                </div>
                
                <div id="logs-content" class="h-96 overflow-y-auto text-green-400 space-y-1 text-xs leading-relaxed">
                    ' . $this->renderLogEntries($logs) . '
                </div>
            </div>
            
            <div class="flex gap-4">
                <button onclick="clearPhpLogs()" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-all">
                    <i class="fas fa-trash mr-2"></i>
                    Pulisci Log
                </button>
                <button onclick="downloadLogs()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-all">
                    <i class="fas fa-download mr-2"></i>
                    Scarica Log
                </button>
            </div>
        </div>';
    }

    /**
     * Ottiene i log degli errori PHP
     */
    private function getPhpErrorLogs()
    {
        $logs = [];
        
        // Array di possibili percorsi per il log degli errori PHP
        $possibleLogPaths = [
            ini_get('error_log'),
            '/xampp/apache/logs/error.log',
            'C:\\xampp\\apache\\logs\\error.log',
            '/var/log/apache2/error.log',
            '/var/log/httpd/error_log',
            '/usr/local/var/log/apache2/error.log',
            APP_ROOT . '/logs/error.log',
            APP_ROOT . '/storage/logs/laravel.log'
        ];
        
        // Trova il primo file di log esistente
        $logFile = null;
        foreach ($possibleLogPaths as $path) {
            if ($path && file_exists($path) && is_readable($path)) {
                $logFile = $path;
                break;
            }
        }
        
        if (!$logFile) {
            return [
                ['timestamp' => date('Y-m-d H:i:s'), 'level' => 'INFO', 'message' => 'Nessun file di log PHP trovato sui percorsi standard'],
                ['timestamp' => date('Y-m-d H:i:s'), 'level' => 'INFO', 'message' => 'Percorsi controllati: ' . implode(', ', array_filter($possibleLogPaths))]
            ];
        }
        
        try {
            $content = file_get_contents($logFile);
            $lines = array_filter(explode("\n", $content));
            
            // Prende le ultime 100 righe
            $lines = array_slice($lines, -100);
            
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                
                // Parsing basic per log PHP
                $timestamp = '';
                $level = 'ERROR';
                $message = $line;
                
                // Cerca pattern comuni nei log PHP/Apache
                if (preg_match('/^\[([^\]]+)\]/', $line, $matches)) {
                    $timestamp = $matches[1];
                    $message = trim(substr($line, strlen($matches[0])));
                }
                
                // Determina il livello di log
                if (stripos($message, 'fatal') !== false) {
                    $level = 'FATAL';
                } elseif (stripos($message, 'error') !== false) {
                    $level = 'ERROR';
                } elseif (stripos($message, 'warning') !== false) {
                    $level = 'WARN';
                } elseif (stripos($message, 'notice') !== false) {
                    $level = 'NOTICE';
                } elseif (stripos($message, 'info') !== false) {
                    $level = 'INFO';
                }
                
                $logs[] = [
                    'timestamp' => $timestamp ?: date('Y-m-d H:i:s'),
                    'level' => $level,
                    'message' => $message
                ];
            }
            
        } catch (Exception $e) {
            $logs[] = [
                'timestamp' => date('Y-m-d H:i:s'),
                'level' => 'ERROR',
                'message' => 'Errore nella lettura del file di log: ' . $e->getMessage()
            ];
        }
        
        // Se non ci sono log, aggiungi un messaggio informativo
        if (empty($logs)) {
            $logs[] = [
                'timestamp' => date('Y-m-d H:i:s'),
                'level' => 'INFO',
                'message' => 'Nessun errore PHP registrato. Sistema funziona correttamente.'
            ];
        }
        
        return array_reverse($logs); // Mostra i più recenti per primi
    }

    /**
     * Renderizza le voci del log in formato terminale
     */
    private function renderLogEntries($logs)
    {
        $html = '';
        foreach ($logs as $log) {
            $levelColor = $this->getLogLevelColor($log['level']);
            $html .= '<div class="flex">
                <span class="text-gray-500 mr-3 w-20 flex-shrink-0">' . htmlspecialchars($log['timestamp']) . '</span>
                <span class="' . $levelColor . ' mr-2 w-12 flex-shrink-0 font-bold">[' . htmlspecialchars($log['level']) . ']</span>
                <span class="text-gray-300 break-all">' . htmlspecialchars($log['message']) . '</span>
            </div>';
        }
        return $html;
    }

    /**
     * Ottiene il colore per il livello di log
     */
    private function getLogLevelColor($level)
    {
        switch (strtoupper($level)) {
            case 'FATAL':
                return 'text-red-600';
            case 'ERROR':
                return 'text-red-400';
            case 'WARN':
                return 'text-yellow-400';
            case 'NOTICE':
                return 'text-blue-400';
            case 'INFO':
                return 'text-green-400';
            default:
                return 'text-gray-400';
        }
    }

    /**
     * API per aggiornare i log in tempo reale
     */
    public function refreshLogs()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        try {
            $logs = $this->getPhpErrorLogs();
            echo json_encode([
                'success' => true,
                'logs' => $logs,
                'html' => $this->renderLogEntries($logs)
            ]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}