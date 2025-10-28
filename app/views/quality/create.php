<!-- Quality Create Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <!-- Icon Box -->
                <div class="mr-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 shadow-lg">
                    <i class="fas fa-plus-circle text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Nuovo Test CQ
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Avvia un nuovo controllo qualità
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="<?= $this->url('/quality') ?>" 
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alla Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
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
                <a href="<?= $this->url('/quality') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                    Sistema CQ
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Nuovo Test</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Main Form Card -->
<div class="max-w-2xl mx-auto">
    <div class="rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-8 text-center">
            <div class="mx-auto h-16 w-16 rounded-full bg-white/20 flex items-center justify-center mb-4">
                <i class="fas fa-clipboard-check text-2xl text-white"></i>
            </div>
            <h2 class="text-xl font-bold text-white">Nuova Registrazione CQ</h2>
            <p class="text-emerald-100 text-sm mt-2">Inserisci il cartellino o la commessa per iniziare</p>
        </div>

        <!-- Form Body -->
        <div class="p-8">
            <form id="registrationForm" class="space-y-6">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                
                <!-- Info Alert -->
                <div class="rounded-lg bg-blue-50 border border-blue-200 p-4 dark:bg-blue-900/20 dark:border-blue-800">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                Informazioni
                            </h3>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                                <p>Inserisci un <strong>cartellino</strong> o una <strong>commessa</strong> per procedere con la registrazione del test di controllo qualità.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Campo Cartellino -->
                    <div class="space-y-2">
                        <label for="cartellino" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cartellino
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="cartellino" 
                                   name="cartellino" 
                                   placeholder="Inserisci il cartellino"
                                   autocomplete="off"
                                   class="w-full rounded-lg border-gray-300 px-4 py-3 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-emerald-400 dark:placeholder-gray-400">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class="fas fa-id-card text-gray-400"></i>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Numero identificativo del cartellino produzione
                        </div>
                    </div>

                    <!-- Campo Commessa -->
                    <div class="space-y-2">
                        <label for="commessa" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Commessa
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="commessa" 
                                   name="commessa" 
                                   placeholder="Inserisci la commessa"
                                   autocomplete="off"
                                   class="w-full rounded-lg border-gray-300 px-4 py-3 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-emerald-400 dark:placeholder-gray-400">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class="fas fa-file-contract text-gray-400"></i>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Numero commessa cliente
                        </div>
                    </div>
                </div>

                <!-- OR Separator -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="bg-white dark:bg-gray-800 px-2 text-gray-500 dark:text-gray-400 font-medium">OPPURE</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" 
                            id="submitBtn"
                            class="w-full inline-flex items-center justify-center rounded-lg border border-transparent bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-3 text-base font-medium text-white hover:from-emerald-600 hover:to-emerald-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-arrow-right mr-2"></i>
                        <span id="btn-text">PROCEDI</span>
                        <div id="btn-spinner" class="ml-2 hidden">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
                        </div>
                    </button>
                </div>

                <!-- Help Text -->
                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-lightbulb mr-1"></i>
                        Il sistema verificherà automaticamente l'esistenza del cartellino o della commessa
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// JavaScript compatibile con PJAX per Quality Create Form
(function() {
    let eventListeners = [];

    function initQualityCreate() {
        cleanupEventListeners();
        setupEventListeners();
        setupFormValidation();
    }

    function cleanupEventListeners() {
        eventListeners.forEach(({ element, event, handler }) => {
            if (element) {
                element.removeEventListener(event, handler);
            }
        });
        eventListeners = [];
    }

    function setupEventListeners() {
        // Form submission
        const form = document.getElementById('registrationForm');
        if (form) {
            function submitHandler(e) {
                e.preventDefault();
                handleFormSubmit();
            }
            form.addEventListener('submit', submitHandler);
            eventListeners.push({ element: form, event: 'submit', handler: submitHandler });
        }

        // Input field interactions - clear other field when typing
        const cartellinoInput = document.getElementById('cartellino');
        const commessaInput = document.getElementById('commessa');

        if (cartellinoInput && commessaInput) {
            function cartellinoHandler() {
                if (this.value.trim()) {
                    commessaInput.value = '';
                    commessaInput.classList.remove('border-emerald-500');
                    this.classList.add('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/20');
                } else {
                    this.classList.remove('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/20');
                }
            }

            function commessaHandler() {
                if (this.value.trim()) {
                    cartellinoInput.value = '';
                    cartellinoInput.classList.remove('border-emerald-500');
                    this.classList.add('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/20');
                } else {
                    this.classList.remove('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/20');
                }
            }

            cartellinoInput.addEventListener('input', cartellinoHandler);
            commessaInput.addEventListener('input', commessaHandler);
            
            eventListeners.push({ element: cartellinoInput, event: 'input', handler: cartellinoHandler });
            eventListeners.push({ element: commessaInput, event: 'input', handler: commessaHandler });
        }
    }

    function setupFormValidation() {
        // Real-time validation feedback
        const inputs = document.querySelectorAll('input[type="text"]');
        inputs.forEach(input => {
            function focusHandler() {
                this.classList.add('ring-2', 'ring-emerald-200', 'dark:ring-emerald-800');
            }
            function blurHandler() {
                this.classList.remove('ring-2', 'ring-emerald-200', 'dark:ring-emerald-800');
            }

            input.addEventListener('focus', focusHandler);
            input.addEventListener('blur', blurHandler);
            
            eventListeners.push({ element: input, event: 'focus', handler: focusHandler });
            eventListeners.push({ element: input, event: 'blur', handler: blurHandler });
        });
    }

    function handleFormSubmit() {
        const cartellinoValue = document.getElementById('cartellino').value.trim();
        const commessaValue = document.getElementById('commessa').value.trim();
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');

        // Validation
        if (!cartellinoValue && !commessaValue) {
            WebgreNotifications.warning('Inserisci un cartellino o una commessa');
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        btnText.textContent = 'Verifica in corso...';
        btnSpinner.classList.remove('hidden');

        // Determine which field to check
        if (cartellinoValue) {
            checkCartellino(cartellinoValue);
        } else {
            checkCommessa(commessaValue);
        }
    }

    function checkCartellino(cartellino) {
        fetch(`<?= $this->url('/quality/check-cartellino') ?>?cartellino=${encodeURIComponent(cartellino)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            resetSubmitButton();
            if (data.exists) {
                // Redirect to add page
                if (window.pjax) {
                    window.pjax.navigateTo(`<?= $this->url('/quality/add') ?>?cartellino=${encodeURIComponent(cartellino)}`);
                } else {
                    window.location.href = `<?= $this->url('/quality/add') ?>?cartellino=${encodeURIComponent(cartellino)}`;
                }
            } else {
                WebgreNotifications.error('Il cartellino non esiste. Verificare o contattare l\'amministratore.');
            }
        })
        .catch(error => {
            console.error('Errore verifica cartellino:', error);
            resetSubmitButton();
            if (window.showAlert) {
                window.showAlert('Errore durante la verifica del cartellino', 'error');
            }
        });
    }

    function checkCommessa(commessa) {
        fetch(`<?= $this->url('/quality/check-commessa') ?>?commessa=${encodeURIComponent(commessa)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            resetSubmitButton();
            if (data.exists && data.cartellino) {
                // Redirect to add page with found cartellino
                if (window.pjax) {
                    window.pjax.navigateTo(`<?= $this->url('/quality/add') ?>?cartellino=${encodeURIComponent(data.cartellino)}`);
                } else {
                    window.location.href = `<?= $this->url('/quality/add') ?>?cartellino=${encodeURIComponent(data.cartellino)}`;
                }
            } else {
                WebgreNotifications.error('La commessa non esiste. Verificare o contattare l\'amministratore.');
                }
            }
        })
        .catch(error => {
            console.error('Errore verifica commessa:', error);
            resetSubmitButton();
            if (window.showAlert) {
                window.showAlert('Errore durante la verifica della commessa', 'error');
            }
        });
    }

    function resetSubmitButton() {
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');

        if (submitBtn) submitBtn.disabled = false;
        if (btnText) btnText.textContent = 'PROCEDI';
        if (btnSpinner) btnSpinner.classList.add('hidden');
    }

    // Registra l'inizializzatore per PJAX
    if (window.WEBGRE && window.WEBGRE.onPageLoad) {
        window.WEBGRE.onPageLoad(initQualityCreate);
    }

    // Inizializza anche al primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQualityCreate);
    } else {
        initQualityCreate();
    }
})();
</script>