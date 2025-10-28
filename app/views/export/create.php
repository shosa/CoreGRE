<!-- Header con titolo -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <span class="bg-gradient-to-r from-blue-500 to-blue-600 bg-clip-text text-transparent mr-3">STEP 1</span>
                <i class="fas fa-arrow-right text-gray-400 mx-2"></i>
                Inserimento Dettagli
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Seleziona il terzista e crea un nuovo documento di trasporto
            </p>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="flex mt-2" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="<?= $this->url('/') ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <i class="fas fa-home mr-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="<?= $this->url('/export/dashboard') ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        Export
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nuovo DDT</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Alert informativo -->
<div class="rounded-lg bg-blue-50 p-4 mb-6 border border-blue-200 dark:bg-blue-800/20 dark:border-blue-700">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-400 text-xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                Creazione nuovo documento
            </h3>
            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                <p>Stai per creare un nuovo documento di trasporto. Seleziona il terzista dal menu a tendina e procedi allo step successivo.</p>
            </div>
        </div>
    </div>
</div>

<!-- Progress Steps -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <!-- Step 1 - Attivo -->
        <div class="flex items-center text-blue-600">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-bold">1</span>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-blue-600">Dettagli</p>
                <p class="text-xs text-blue-500">Terzista e informazioni base</p>
            </div>
        </div>
        
        <div class="flex-1 h-0.5 bg-gray-200 mx-4 dark:bg-gray-600"></div>
        
        <!-- Step 2 - Inattivo -->
        <div class="flex items-center text-gray-400">
            <div class="flex-shrink-0 w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center dark:bg-gray-600">
                <span class="text-gray-500 text-sm font-bold dark:text-gray-400">2</span>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-400">Caricamento</p>
                <p class="text-xs text-gray-400">Schede tecniche Excel</p>
            </div>
        </div>
        
        <div class="flex-1 h-0.5 bg-gray-200 mx-4 dark:bg-gray-600"></div>
        
        <!-- Step 3 - Inattivo -->
        <div class="flex items-center text-gray-400">
            <div class="flex-shrink-0 w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center dark:bg-gray-600">
                <span class="text-gray-500 text-sm font-bold dark:text-gray-400">3</span>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-400">Generazione</p>
                <p class="text-xs text-gray-400">Anteprima e DDT</p>
            </div>
        </div>
    </div>
</div>

<!-- Form principale -->
<div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-file-invoice mr-3 text-blue-500"></i>
            Nuovo Documento
        </h3>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/20 dark:text-blue-300">
            DDT N° <?= $newId ?>
        </span>
    </div>
    
    <form id="createDdtForm" class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonna principale - Selezione terzista -->
            <div class="lg:col-span-2">
                <div class="mb-6">
                    <label for="terzista" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-user-tie mr-2 text-blue-500"></i>
                        Seleziona Terzista <span class="text-red-500">*</span>
                    </label>
                    <select id="terzista" name="terzista" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleziona un terzista</option>
                        <?php foreach ($terzisti as $terzista): ?>
                            <option value="<?= $terzista->id ?>">
                                <?= htmlspecialchars($terzista->ragione_sociale) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Anteprima terzista selezionato -->
                <div id="terzistaPreview" class="hidden p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    <h4 class="text-sm font-semibold text-blue-600 dark:text-blue-400 mb-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        Dettagli Terzista
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-600 dark:text-gray-400">Ragione Sociale:</span>
                            <div id="terzistaNome" class="text-gray-900 dark:text-white mt-1"></div>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600 dark:text-gray-400">Indirizzo:</span>
                            <div id="terzistaIndirizzo" class="text-gray-900 dark:text-white mt-1"></div>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600 dark:text-gray-400">Città:</span>
                            <div id="terzistaCitta" class="text-gray-900 dark:text-white mt-1"></div>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600 dark:text-gray-400">Nazione:</span>
                            <div id="terzistaNazione" class="text-gray-900 dark:text-white mt-1"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonna laterale - Informazioni documento -->
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <div>
                        <label for="progressivo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-hashtag mr-2 text-blue-500"></i>
                            Progressivo
                        </label>
                        <input type="text" id="progressivo" name="progressivo" readonly
                               value="<?= $newId ?>"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Numero documento generato automaticamente
                        </p>
                    </div>

                    <div>
                        <label for="dataDoc" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            Data
                        </label>
                        <input type="text" id="dataDoc" name="dataDoc" readonly
                               value="<?= date('d/m/Y') ?>"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Data odierna
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pulsanti azione -->
        <div class="flex justify-end mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex space-x-3">
                <a href="<?= $this->url('/export') ?>" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Annulla
                </a>
                <button type="submit" id="submitBtn" disabled
                        class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed shadow-md hover:shadow-lg transition-all duration-200">
                    Avanti
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
(function() {
    let eventListeners = [];
    
    function initExportCreate() {
        cleanupEventListeners();
        
        const terzistaSelect = document.getElementById('terzista');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('createDdtForm');
        const terzistaPreview = document.getElementById('terzistaPreview');
        
        // Verifica che gli elementi esistano (evita errori se script eseguito su pagine diverse)
        if (!terzistaSelect || !submitBtn || !form || !terzistaPreview) {
            return; // Non è la pagina di creazione, esci
        }
        
        // Gestisce il cambio del terzista
        function handleTerzistaChange() {
            const terzistaId = terzistaSelect.value;
            
            if (terzistaId) {
                // Abilita il pulsante Avanti
                submitBtn.disabled = false;
                
                // Carica dettagli terzista via AJAX
                fetch(window.COREGRE.baseUrl + '/export/getTerzistaDetails', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': window.COREGRE.csrfToken
                    },
                    body: 'id=' + encodeURIComponent(terzistaId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Popola l'anteprima del terzista
                        document.getElementById('terzistaNome').textContent = data.data.ragione_sociale || '';
                        document.getElementById('terzistaIndirizzo').textContent = data.data.indirizzo_1 || '';
                        document.getElementById('terzistaCitta').textContent = data.data.indirizzo_2 || '';
                        document.getElementById('terzistaNazione').textContent = data.data.nazione || '';
                        
                        // Mostra la card di anteprima con animazione
                        terzistaPreview.classList.remove('hidden');
                    } else {
                        // Se c'è un errore, mostra comunque il nome selezionato
                        document.getElementById('terzistaNome').textContent = terzistaSelect.options[terzistaSelect.selectedIndex].text;
                        document.getElementById('terzistaIndirizzo').textContent = '';
                        document.getElementById('terzistaCitta').textContent = '';
                        document.getElementById('terzistaNazione').textContent = '';
                        
                        terzistaPreview.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Fallback: mostra solo il nome
                    document.getElementById('terzistaNome').textContent = terzistaSelect.options[terzistaSelect.selectedIndex].text;
                    document.getElementById('terzistaIndirizzo').textContent = '';
                    document.getElementById('terzistaCitta').textContent = '';
                    document.getElementById('terzistaNazione').textContent = '';
                    
                    terzistaPreview.classList.remove('hidden');
                });
            } else {
                // Disabilita il pulsante Avanti
                submitBtn.disabled = true;
                
                // Nascondi la card di anteprima
                terzistaPreview.classList.add('hidden');
            }
        }
        
        // Gestisce il submit del form
        function handleFormSubmit(e) {
            e.preventDefault();
            
            // Verifica se il form è valido
            if (!form.checkValidity()) {
                if (window.showAlert) {
                    window.showAlert('Seleziona un terzista prima di procedere', 'warning');
                } else if (window.CoregreNotifications) {
                    window.CoregreNotifications.warning('Seleziona un terzista prima di procedere');
                }
                return false;
            }
            
            // Mostra loading
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creazione in corso...';
            
            // Prepara i dati del form
            const formData = new FormData(form);
            
            // Invia richiesta
            fetch(window.COREGRE.baseUrl + '/export/create', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.COREGRE.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (window.showAlert) {
                        window.showAlert('Documento creato con successo!', 'success');
                    } else if (window.CoregreNotifications) {
                        window.CoregreNotifications.success('Documento creato con successo!');
                    }
                    
                    // Reindirizza al prossimo step
                    if (data.redirect) {
                        if (window.pjax) {
                            window.pjax.navigateTo(data.redirect);
                        } else {
                            window.location.href = data.redirect;
                        }
                    }
                } else {
                    if (window.showAlert) {
                        window.showAlert(data.message || 'Errore nella creazione del documento', 'error');
                    } else if (window.CoregreNotifications) {
                        window.CoregreNotifications.error(data.message || 'Errore nella creazione del documento');
                    }
                    
                    // Ripristina il pulsante
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (window.showAlert) {
                    window.showAlert('Errore di rete durante la creazione del documento', 'error');
                } else if (window.CoregreNotifications) {
                    window.CoregreNotifications.error('Errore di rete durante la creazione del documento');
                }
                
                // Ripristina il pulsante
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }
        
        // Aggiungi event listeners
        terzistaSelect.addEventListener('change', handleTerzistaChange);
        eventListeners.push({ element: terzistaSelect, event: 'change', handler: handleTerzistaChange });
        
        form.addEventListener('submit', handleFormSubmit);
        eventListeners.push({ element: form, event: 'submit', handler: handleFormSubmit });
    }
    
    function cleanupEventListeners() {
        eventListeners.forEach(({ element, event, handler }) => {
            element.removeEventListener(event, handler);
        });
        eventListeners = [];
    }
    
    // Registrazione PJAX
    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initExportCreate);
    }
    
    // Fallback primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initExportCreate);
    } else {
        initExportCreate();
    }
})();
</script>