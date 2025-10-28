<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                Nuovo Utente
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Aggiungi un nuovo utente al sistema
            </p>

            <!-- Breadcrumb -->
            <nav class="flex mt-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500 dark:text-gray-400">
                    <li class="inline-flex items-center">
                        <a href="<?= $this->url('/') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                            <i class="fas fa-home mr-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="<?= $this->url('/users') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                                Utenti
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-700 dark:text-gray-300">
                                Nuovo Utente
                            </span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="mt-4 sm:mt-0">
            <a href="<?= $this->url('/users') ?>"
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alla Lista
            </a>
        </div>
    </div>
</div>

<!-- Form -->
<div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-user-plus mr-3 text-green-500"></i>
            Informazioni Utente
        </h3>
    </div>

    <form method="POST" action="<?= $this->url('/users/store') ?>" id="createUserForm" class="p-6">
        <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Username -->
            <div>
                <label for="user_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Username <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="user_name" 
                       name="user_name" 
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="inserisci username">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Username univoco per l'accesso al sistema</p>
            </div>

            <!-- Nome Completo -->
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nome Completo <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="nome" 
                       name="nome" 
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Nome e Cognome">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="email@example.com">
            </div>

            <!-- Ruolo -->
            <div>
                <label for="admin_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Ruolo <span class="text-red-500">*</span>
                </label>
                <select id="admin_type" 
                        name="admin_type" 
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Seleziona ruolo</option>
                    <?php foreach ($adminTypes as $type => $label): ?>
                        <option value="<?= $type ?>"><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    <strong>Admin:</strong> Accesso completo • 
                    <strong>Manager:</strong> Gestione operativa • 
                    <strong>User:</strong> Accesso standard • 
                    <strong>Viewer:</strong> Solo lettura
                </p>
            </div>
        </div>

        <!-- Password Section -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-key mr-2 text-blue-500"></i>
                Credenziali di Accesso
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required
                               minlength="6"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                               placeholder="Inserisci password">
                        <button type="button" onclick="togglePassword('password')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" id="password-icon"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimo 6 caratteri</p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Conferma Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               required
                               minlength="6"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                               placeholder="Ripeti password">
                        <button type="button" onclick="togglePassword('confirm_password')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" id="confirm_password-icon"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end space-x-3">
            <a href="<?= $this->url('/users') ?>" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                <i class="fas fa-times mr-2"></i>
                Annulla
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-save mr-2"></i>
                Crea Utente
            </button>
        </div>
    </form>
</div>

<script>
// Users Create - JavaScript compatibile con PJAX
(function() {
    // Variabili globali per cleanup
    let eventListeners = [];
    
    // Funzione di inizializzazione
    function initUsersCreate() {
        // Cleanup precedenti event listeners
        cleanupEventListeners();
        
        const form = document.getElementById('createUserForm');
        
        if (form) {
            function formSubmitHandler(e) {
                // Validazione password
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    if (window.showAlert) {
                        window.showAlert('Le password non coincidono', 'error');
                    } else {
                        alert('Le password non coincidono');
                    }
                    return false;
                }
                
                // Validazione campi richiesti
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('border-red-500', 'focus:ring-red-500');
                    } else {
                        field.classList.remove('border-red-500', 'focus:ring-red-500');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    if (window.showAlert) {
                        window.showAlert('Per favore, compila tutti i campi obbligatori.', 'error');
                    } else {
                        alert('Per favore, compila tutti i campi obbligatori.');
                    }
                    return false;
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

    // Funzione globale per mostrare/nascondere password
    window.togglePassword = function(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    };

    // Registra l'inizializzatore per PJAX
    if (window.WEBGRE && window.WEBGRE.onPageLoad) {
        window.WEBGRE.onPageLoad(initUsersCreate);
    }

    // Inizializza anche al primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUsersCreate);
    } else {
        initUsersCreate();
    }
})();
</script>