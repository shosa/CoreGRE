

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                Gestione Permessi
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Configura i permessi per <?= htmlspecialchars($user->nome) ?> (@<?= htmlspecialchars($user->user_name) ?>)
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="<?= $this->url('/users/' . $user->id . '/edit') ?>"
                class="inline-flex items-center rounded-lg border border-blue-300 bg-white px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-50 dark:border-blue-600 dark:bg-gray-800 dark:text-blue-300 dark:hover:bg-blue-900/20 transition-colors">
                <i class="fas fa-user-edit mr-2"></i>
                Modifica Utente
            </a>
            <a href="<?= $this->url('/users') ?>"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alla Lista
            </a>
        </div>
    </div>
</div>
<!-- Breadcrumb -->
<nav class="flex mb-8" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/') ?>"
                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/users') ?>"
                    class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    Utenti
                </a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                    Permessi - <?= htmlspecialchars($user->nome) ?>
                </span>
            </div>
        </li>
    </ol>
</nav>

<!-- User Info Card -->
<div class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
    <div class="flex items-center">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
            <i class="fas fa-user text-white text-xl"></i>
        </div>
        <div class="ml-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($user->nome) ?></h3>
            <p class="text-gray-600 dark:text-gray-400">@<?= htmlspecialchars($user->user_name) ?></p>
            <div class="flex items-center mt-2">
                <?php 
                $badgeColors = [
                    'admin' => 'bg-red-100 text-red-800 dark:bg-red-800/20 dark:text-red-300',
                    'manager' => 'bg-purple-100 text-purple-800 dark:bg-purple-800/20 dark:text-purple-300',
                    'user' => 'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300',
                    'viewer' => 'bg-gray-100 text-gray-800 dark:bg-gray-800/20 dark:text-gray-300'
                ];
                $badgeClass = $badgeColors[$user->admin_type] ?? $badgeColors['user'];
                ?>
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full <?= $badgeClass ?>">
                    <?= ucfirst($user->admin_type) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Permissions Form -->
<form method="POST" action="<?= $this->url('/users/update-permissions') ?>" id="permissionsForm">
    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
    <input type="hidden" name="user_id" value="<?= $user->id ?>">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Riparazioni -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-hammer mr-3 text-blue-500"></i>
                    Riparazioni
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Gestione delle riparazioni e ordini di lavoro
                </p>
            </div>
            <div class="p-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="riparazioni" 
                           value="1"
                           <?= ($permissions->riparazioni ?? 0) ? 'checked' : '' ?>
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Accesso completo al modulo riparazioni
                    </span>
                </label>
            </div>
        </div>

        <!-- Produzione -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-industry mr-3 text-yellow-500"></i>
                    Produzione
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Gestione della produzione
                </p>
            </div>
            <div class="p-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="produzione" 
                           value="1"
                           <?= ($permissions->produzione ?? 0) ? 'checked' : '' ?>
                           class="h-4 w-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Accesso completo al modulo produzione
                    </span>
                </label>
            </div>
        </div>

        <!-- Controllo Qualità -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    Controllo Qualità
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Sistema di controllo e verifica qualità
                </p>
            </div>
            <div class="p-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="quality" 
                           value="1"
                           <?= ($permissions->quality ?? 0) ? 'checked' : '' ?>
                           class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Accesso completo al controllo qualità
                    </span>
                </label>
            </div>
        </div>

        <!-- Export -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-file-export mr-3 text-purple-500"></i>
                    Export
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Gestione esportazioni e documenti
                </p>
            </div>
            <div class="p-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="export" 
                           value="1"
                           <?= ($permissions->export ?? 0) ? 'checked' : '' ?>
                           class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Accesso completo al modulo export
                    </span>
                </label>
            </div>
        </div>

        <!-- SCM -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-shipping-fast mr-3 text-indigo-500"></i>
                    SCM
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Supply Chain Management
                </p>
            </div>
            <div class="p-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="scm" 
                           value="1"
                           <?= ($permissions->scm ?? 0) ? 'checked' : '' ?>
                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Accesso completo al modulo SCM
                    </span>
                </label>
            </div>
        </div>

        <!-- Tracking -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-map-marker-alt mr-3 text-red-500"></i>
                    Tracking
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Tracciabilità materiali
                </p>
            </div>
            <div class="p-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="tracking" 
                           value="1"
                           <?= ($permissions->tracking ?? 0) ? 'checked' : '' ?>
                           class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Accesso completo al tracking
                    </span>
                </label>
            </div>
        </div>
          <!-- MRP -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-box mr-3 text-red-500"></i>
                    MRP
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                   Gestione Ordini e Fabbisogni
                </p>
            </div>
            <div class="p-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="mrp" 
                           value="1"
                           <?= ($permissions->mrp ?? 0) ? 'checked' : '' ?>
                           class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Accesso completo al modulo MRP
                    </span>
                </label>
            </div>
        </div>

        <!-- Utenti -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-users mr-3 text-teal-500"></i>
                    Gestione Utenti
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Gestione Utenti COREGRE
                </p>
            </div>
            <div class="p-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="utenti" 
                           value="1"
                           <?= ($permissions->utenti ?? 0) ? 'checked' : '' ?>
                           class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Accesso completo alla gestione.
                    </span>
                </label>
            </div>
        </div>
         <!-- Log -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-chart-line mr-3 text-dark-500"></i>
                    Log Attività
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Gestione del registro attività.
                </p>
            </div>
            <div class="p-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="log" 
                           value="1"
                           <?= ($permissions->log ?? 0) ? 'checked' : '' ?>
                           class="h-4 w-4 text-dark-600 border-gray-300 rounded focus:ring-dark-500">
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Consulta e modifica il registro attività.
                    </span>
                </label>
            </div>
        </div>

          <!-- Etichette -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-barcode mr-3 text-blue-500"></i>
                   Etichette DYMO
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Stampa e Crea liste
                </p>
            </div>
            <div class="p-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="etichette" 
                           value="1"
                           <?= ($permissions->etichette ?? 0) ? 'checked' : '' ?>
                           class="h-4 w-4 text-blue-600 border-blue-300 rounded focus:ring-blue-500">
                     <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                       Sezione per la stampa di etichette su framework DYMO
                    </span>
                </label>
            </div>
        </div>

          <!-- Database -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-database mr-3 text-indigo-500"></i>
                    Database e Migrazioni
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Modifiche Database, SQL e sistema migrazioni
                </p>
            </div>
            <div class="p-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="dbsql" 
                           value="1"
                           <?= ($permissions->dbsql ?? 0) ? 'checked' : '' ?>
                           class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Accesso completo alla sezione Database
                    </span>
                </label>
            </div>
        </div>
     <!-- Settings -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-cog mr-3 text-orange-500"></i>
                    Impostazioni
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Accesso alle impostazioni e import Dati
                </p>
            </div>
            <div class="p-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="settings" 
                           value="1"
                           <?= ($permissions->settings ?? 0) ? 'checked' : '' ?>
                           class="h-4 w-4 text-orange-600 border-gray-300 rounded focus:ring-teal-500">
                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Accesso alla sezione Impostazioni
                    </span>
                </label>
            </div>
        </div>

        <!-- Admin -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-red-200 dark:border-red-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-user-shield mr-3 text-red-500"></i>
                    Amministrazione
                </h3>
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <strong>Attenzione:</strong> Fornisce accesso specifico a gestioni tecniche.
                </p>
            </div>
            <div class="p-6 bg-red-50 dark:bg-red-900/10">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="admin" 
                           value="1"
                           <?= ($permissions->admin ?? 0) ? 'checked' : '' ?>
                           class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <span class="ml-3 text-sm font-medium text-red-700 dark:text-red-300">
                        Permessi di amministratore
                    </span>
                </label>
             
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-8 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <button type="button" onclick="selectAllPermissions()" 
                    class="inline-flex items-center px-4 py-2 border border-green-300 rounded-lg text-sm font-medium text-green-700 bg-white hover:bg-green-50 dark:bg-gray-700 dark:text-green-300 dark:border-green-600 dark:hover:bg-green-900/20">
                <i class="fas fa-check-double mr-2"></i>
                Seleziona Tutto
            </button>
            <button type="button" onclick="clearAllPermissions()" 
                    class="inline-flex items-center px-4 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 dark:bg-gray-700 dark:text-red-300 dark:border-red-600 dark:hover:bg-red-900/20">
                <i class="fas fa-times mr-2"></i>
                Deseleziona Tutto
            </button>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="<?= $this->url('/users') ?>" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                <i class="fas fa-times mr-2"></i>
                Annulla
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-save mr-2"></i>
                Salva Permessi
            </button>
        </div>
    </div>
</form>

<script>
// Users Permissions - JavaScript compatibile con PJAX
(function() {
    // Variabili globali per cleanup
    let eventListeners = [];
    
    // Funzione di inizializzazione
    function initUsersPermissions() {
        // Cleanup precedenti event listeners
        cleanupEventListeners();
        
        const form = document.getElementById('permissionsForm');
        
        if (form) {
            function formSubmitHandler(e) {
                e.preventDefault();
                
                // Usa modal system invece di confirm
                if (window.CoregreModals) {
                    window.CoregreModals.confirm({
                        title: 'Conferma Aggiornamento Permessi',
                        message: 'Sei sicuro di voler aggiornare i permessi di questo utente?',
                        type: 'info',
                        confirmText: 'Salva Permessi',
                        cancelText: 'Annulla',
                        onConfirm: () => {
                            // Mostra notifica di caricamento
                            if (window.CoregreNotifications) {
                                window.CoregreNotifications.loading('Salvataggio permessi in corso...');
                            }
                            form.submit();
                        }
                    });
                } else {
                    // Fallback
                    CoregreModals.confirm({
                        message: 'Sei sicuro di voler aggiornare i permessi di questo utente?',
                        onConfirm: () => {
                            form.submit();
                        }
                    });
                }
            }
            
            form.addEventListener('submit', formSubmitHandler);
            eventListeners.push({ element: form, event: 'submit', handler: formSubmitHandler });
        }
    }
    
    function cleanupEventListeners() {
        eventListeners.forEach(({ element, event, handler }) => {
            element.removeEventListener(event, handler);
        });
        eventListeners = [];
    }

    // Funzioni globali
    window.selectAllPermissions = function() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        
        if (window.showAlert) {
            window.showAlert('Tutti i permessi selezionati', 'info');
        }
    };

    window.clearAllPermissions = function() {
        CoregreModals.confirm({
            message: 'Sei sicuro di voler deselezionare tutti i permessi?',
            onConfirm: () => {
                const checkboxes = document.querySelectorAll('input[type="checkbox"][name]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });

                CoregreNotifications.success('Tutti i permessi deselezionati');
                window.showAlert('Tutti i permessi rimossi', 'warning');
            }
        }
    };

    // Registra l'inizializzatore per PJAX
    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initUsersPermissions);
    }

    // Inizializza anche al primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUsersPermissions);
    } else {
        initUsersPermissions();
    }
})();
</script>