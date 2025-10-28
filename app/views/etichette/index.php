<!-- Etichette DYMO Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-orange-500 to-amber-600 shadow-lg mr-4">
                    <i class="fas fa-tags text-xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Etichette DYMO
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Sistema di stampa etichette con stampanti DYMO
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="<?= $this->url('/etichette/decode') ?>"
                class="inline-flex items-center rounded-lg border border-green-300 bg-gradient-to-r from-green-500 to-green-600 px-4 py-2 text-sm font-medium text-white hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-list-ul mr-2"></i>
                Crea Lista
            </a>
            <button onclick="refreshPrinters()"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-sync mr-2"></i>
                Aggiorna Stampanti
            </button>
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
                <span class="text-gray-700 dark:text-gray-300">Etichette</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Status Alert -->
<div id="printer-status-alert" class="mb-6 rounded-lg border border-orange-200 bg-orange-50 dark:border-orange-800 dark:bg-orange-900/20 p-4 shadow hidden">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-400"></i>
        </div>
        <div class="ml-3 flex-1">
            <h3 class="font-semibold text-orange-900 dark:text-orange-200">
                Controllo Stampanti DYMO
            </h3>
            <p class="text-sm text-orange-800 dark:text-orange-300" id="printer-status-message">
                Verifica della connessione alle stampanti in corso...
            </p>
        </div>
    </div>
</div>

<!-- Main Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Pannello Stampa -->
    <div class="lg:col-span-1">
        <div class="card">
            <div class="card-header">
                <h3 class="font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-print mr-3 text-orange-500"></i>
                    Configurazione Stampa
                </h3>
            </div>
            <div class="card-body space-y-4">
                <!-- Selezione Stampante -->
                <div id="printers-section">
                    <label for="printersSelect" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Stampante DYMO:
                    </label>
                    <select id="printersSelect" class="form-select w-full">
                        <option value="">Caricamento stampanti...</option>
                    </select>
                </div>

                <!-- Ricerca Articolo -->
                <div class="relative">
                    <label for="codice_articolo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Codice Articolo:
                    </label>
                    <input type="text" 
                           id="codice_articolo" 
                           name="codice_articolo" 
                           autocomplete="off"
                           placeholder="Inserisci codice o barcode..." 
                           class="form-input w-full">
                    
                    <!-- Container suggerimenti -->
                    <div id="suggestions-container" 
                         class="absolute z-10 w-full max-h-60 overflow-y-auto mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg hidden">
                    </div>
                </div>

                <!-- Pulsanti Azione -->
                <div class="space-y-3">
                    <button id="printButton" 
                            class="btn btn-primary w-full disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-print mr-2"></i>
                        STAMPA ETICHETTA
                    </button>
                    <a href="<?= $this->url('/etichette/decode') ?>" 
                       class="btn btn-success w-full inline-flex items-center justify-center">
                        <i class="fas fa-list-ul mr-2"></i>
                        CREA LISTA
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Anteprima Etichetta -->
    <div class="lg:col-span-1">
        <div class="card">
            <div class="card-header">
                <h3 class="font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-eye mr-3 text-green-500"></i>
                    Anteprima Etichetta
                </h3>
            </div>
            <div class="card-body text-center">
                <div id="label-preview-container" class="min-h-48 flex items-center justify-center">
                    <div class="text-gray-400 dark:text-gray-600">
                        <i class="fas fa-tags text-4xl mb-4"></i>
                        <p class="text-sm">Inserisci un codice articolo per vedere l'anteprima</p>
                    </div>
                </div>
                <img id="labelPreview" 
                     alt="Anteprima etichetta" 
                     class="max-w-full h-auto rounded-lg shadow-md hidden"
                     onerror="this.style.display='none';">
            </div>
        </div>
    </div>

    <!-- Dettagli Stampante -->
    <div class="lg:col-span-1">
        <div class="card">
            <div class="card-header">
                <h3 class="font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-cogs mr-3 text-purple-500"></i>
                    Dettagli Stampante
                </h3>
            </div>
            <div class="card-body">
                <div id="printer-details-container" class="min-h-32">
                    <div class="text-center text-gray-400 dark:text-gray-600">
                        <i class="fas fa-printer text-3xl mb-3"></i>
                        <p class="text-sm">Seleziona una stampante per vedere i dettagli</p>
                    </div>
                </div>
                <div id="printer-details" class="hidden">
                    <!-- Dettagli stampante verranno popolati dinamicamente -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-99999">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8 max-w-sm w-full mx-4">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-900 dark:text-white font-medium">Operazione in corso...</p>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">Attendere prego</p>
        </div>
    </div>
</div>

<script>
    // Carica DYMO Framework solo se non è già presente
    if (typeof dymo === 'undefined') {
        const script = document.createElement('script');
        script.src = '<?= $this->url('/public/js/dymo.js') ?>';
        script.onload = function() {
            console.log('DYMO Framework caricato');
        };
        document.head.appendChild(script);
    }
</script>

<script>
    // Sistema Etichette DYMO - JavaScript compatibile con PJAX
    (function() {
        let eventListeners = [];
        let label = null;
        let _printers = [];
        let currentArticleData = null;

        function initEtichetteDYMO() {
            cleanupEventListeners();
            initializeDYMOFramework();
            setupEventListeners();
        }

        function cleanupEventListeners() {
            eventListeners.forEach(({ element, event, handler }) => {
                element.removeEventListener(event, handler);
            });
            eventListeners = [];
        }

        function initializeDYMOFramework() {
            showStatusAlert('Inizializzazione framework DYMO...', 'info');

            // Aspetta che DYMO si carichi se non è ancora disponibile
            function waitForDYMO(attempts = 0) {
                if (typeof dymo !== 'undefined' && dymo.label && dymo.label.framework) {
                    if (dymo.label.framework.init) {
                        dymo.label.framework.init(loadPrintersAsync);
                    } else {
                        loadPrintersAsync();
                    }
                } else if (attempts < 10) {
                    // Riprova dopo 500ms, max 10 volte (5 secondi)
                    setTimeout(() => waitForDYMO(attempts + 1), 500);
                } else {
                    showStatusAlert('Framework DYMO non disponibile. Assicurati che il plugin DYMO sia installato.', 'error');
                    console.error('DYMO framework not available after timeout');
                }
            }

            waitForDYMO();
        }

        function setupEventListeners() {
            // Ricerca articoli con debounce
            const codiceInput = document.getElementById('codice_articolo');
            if (codiceInput) {
                let debounceTimer;
                function searchHandler() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        handleArticleSearch(this.value.trim());
                    }, 300);
                }
                codiceInput.addEventListener('input', searchHandler);
                eventListeners.push({ element: codiceInput, event: 'input', handler: searchHandler });

                // Gestione focus/blur per suggerimenti
                function focusHandler() {
                    if (this.value.trim()) {
                        handleArticleSearch(this.value.trim());
                    }
                }
                function blurHandler() {
                    setTimeout(() => hideSuggestions(), 200);
                }
                codiceInput.addEventListener('focus', focusHandler);
                codiceInput.addEventListener('blur', blurHandler);
                eventListeners.push({ element: codiceInput, event: 'focus', handler: focusHandler });
                eventListeners.push({ element: codiceInput, event: 'blur', handler: blurHandler });
            }

            // Stampa etichetta
            const printButton = document.getElementById('printButton');
            if (printButton) {
                function printHandler() {
                    printLabel();
                }
                printButton.addEventListener('click', printHandler);
                eventListeners.push({ element: printButton, event: 'click', handler: printHandler });
            }

            // Cambio stampante
            const printersSelect = document.getElementById('printersSelect');
            if (printersSelect) {
                function changeHandler() {
                    populatePrinterDetails();
                }
                printersSelect.addEventListener('change', changeHandler);
                eventListeners.push({ element: printersSelect, event: 'change', handler: changeHandler });
            }
        }

        function loadPrintersAsync() {
            _printers = [];
            const printersSelect = document.getElementById('printersSelect');
            
            if (!printersSelect) {
                showStatusAlert('Elemento select stampanti non trovato', 'error');
                return;
            }
            
            if (typeof dymo === 'undefined' || !dymo.label || !dymo.label.framework) {
                showStatusAlert('Framework DYMO non disponibile', 'error');
                return;
            }

            try {
                var printersPromise = dymo.label.framework.getPrintersAsync();
                
                printersPromise.then(function (printers) {
                    printersSelect.innerHTML = '';
                    
                    if (printers.length === 0) {
                        showStatusAlert('Nessuna stampante DYMO rilevata. Verifica che sia collegata e accesa.', 'warning');
                        printersSelect.innerHTML = '<option value="">Nessuna stampante disponibile</option>';
                        return;
                    }

                    _printers = printers;
                    showStatusAlert('Trovate ' + printers.length + ' stampanti DYMO', 'success');
                    
                    printers.forEach(function (printer, index) {
                        let option = document.createElement("option");
                        option.value = index;
                        option.text = printer.name;
                        printersSelect.appendChild(option);
                    });

                    populatePrinterDetails();
                    loadLabelTemplate();
                });
                
                if (printersPromise.thenCatch) {
                    printersPromise.thenCatch(function (error) {
                        console.error('Load printers failed:', error);
                        showStatusAlert('Errore nel caricamento stampanti: ' + error, 'error');
                        printersSelect.innerHTML = '<option value="">Errore caricamento stampanti</option>';
                    });
                }
            } catch (error) {
                console.error('Error loading printers:', error);
                showStatusAlert('Errore inizializzazione stampanti', 'error');
            }
        }

        function loadLabelTemplate() {
            // Carica template XML etichetta DYMO da file esterno
            fetch('<?= $this->url('/public/templates/dymo-label-template.xml') ?>')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Errore nel caricamento del template XML');
                    }
                    return response.text();
                })
                .then(labelXml => {
                    try {
                        if (typeof dymo !== 'undefined' && dymo.label && dymo.label.framework) {
                            label = dymo.label.framework.openLabelXml(labelXml);
                            console.log('Label template loaded successfully from external file');
                        }
                    } catch (error) {
                        console.error('Error parsing label template:', error);
                        showStatusAlert('Errore nel parsing del template etichetta', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading label template file:', error);
                    showStatusAlert('Errore nel caricamento del file template etichetta', 'error');
                });
        }

        function handleArticleSearch(query) {
            if (!query || query.length < 2) {
                hideSuggestions();
                return;
            }

            fetch(`<?= $this->url('/etichette/suggestions') ?>?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    showSuggestions(data);
                    
                    // Se query esatta, carica dettagli
                    const exactMatch = data.find(item => 
                        item.art.toLowerCase() === query.toLowerCase() || 
                        item.barcode === query
                    );
                    if (exactMatch) {
                        loadArticleDetails(query);
                    }
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    hideSuggestions();
                });
        }

        function showSuggestions(suggestions) {
            const container = document.getElementById('suggestions-container');
            if (!container) return;

            container.innerHTML = '';

            if (suggestions.length === 0) {
                container.classList.add('hidden');
                return;
            }

            suggestions.forEach(suggestion => {
                const div = document.createElement('div');
                div.className = 'px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-b-0';
                div.innerHTML = `
                    <div class="text-sm font-medium text-gray-900 dark:text-white">${suggestion.art}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">${suggestion.des}</div>
                `;
                
                div.addEventListener('click', function() {
                    const input = document.getElementById('codice_articolo');
                    if (input) {
                        input.value = suggestion.art;
                    }
                    hideSuggestions();
                    loadArticleDetails(suggestion.art);
                });

                container.appendChild(div);
            });

            container.classList.remove('hidden');
        }

        function hideSuggestions() {
            const container = document.getElementById('suggestions-container');
            if (container) {
                container.classList.add('hidden');
            }
        }

        function loadArticleDetails(artCode) {
            fetch(`<?= $this->url('/etichette/article-details') ?>?art=${encodeURIComponent(artCode)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        if (window.showAlert) {
                            window.showAlert(data.error, 'error');
                        }
                        return;
                    }
                    
                    currentArticleData = data;
                    updateLabelContent(data);
                })
                .catch(error => {
                    console.error('Error fetching article details:', error);
                    if (window.showAlert) {
                        window.showAlert('Errore nel caricamento dettagli articolo', 'error');
                    }
                });
        }

        function updateLabelContent(data) {
            if (!label) {
                console.warn('Label template not loaded');
                return;
            }

            try {
                // Debug: log dei dati ricevuti
                console.log('Updating label with data:', data);
                
                // Formatta descrizione con a capo ogni 30 caratteri
                const formattedDescription = formatTextWithLineBreaks(data.des || '', 30);
                
                // Debug: log dei valori che stiamo impostando
                console.log('Setting label values:', {
                    categoria: data.cm || 'CM',
                    codice: data.barcode || '',
                    articolo: data.art || '',
                    descrizione: formattedDescription
                });
                
                // Valida e pulisci il barcode per Code128
                let cleanBarcode = data.barcode || '';
                if (cleanBarcode) {
                    // Rimuovi caratteri non supportati da Code128
                    cleanBarcode = cleanBarcode.replace(/[^0-9A-Za-z\-\.\ ]/g, '');
                    
                    // Assicura che il barcode abbia una lunghezza minima
                    if (cleanBarcode.length < 3) {
                        cleanBarcode = cleanBarcode.padStart(10, '0');
                    }
                }
                
                console.log('Clean barcode:', cleanBarcode);
                
                // Aggiorna contenuti etichetta
                label.setObjectText("CATEGORIA", data.cm || 'CM');
                label.setObjectText("CODICE", cleanBarcode);
                label.setObjectText("CODICE_ARTICOLO", data.art || '');
                label.setObjectText("BARCODE", cleanBarcode);
                label.setObjectText("DESCRIZIONE", formattedDescription);

                // Genera anteprima
                const preview = document.getElementById('labelPreview');
                const container = document.getElementById('label-preview-container');
                
                if (preview && container) {
                    const renderData = label.render();
                    if (renderData) {
                        preview.src = "data:image/png;base64," + renderData;
                        preview.classList.remove('hidden');
                        
                        // Nascondi il placeholder
                        const placeholder = container.querySelector('.text-gray-400');
                        if (placeholder) {
                            placeholder.style.display = 'none';
                        }
                        
                        // Sposta l'immagine dentro il container se non è già lì
                        if (preview.parentElement !== container) {
                            container.appendChild(preview);
                        }
                        
                        // Assicura che il container mantenga la centratura
                        container.style.display = 'flex';
                        container.style.alignItems = 'center';
                        container.style.justifyContent = 'center';
                    }
                }

                // Abilita stampa
                const printButton = document.getElementById('printButton');
                if (printButton) {
                    printButton.disabled = false;
                }

            } catch (error) {
                console.error('Error updating label content:', error);
                if (window.showAlert) {
                    window.showAlert('Errore nell\'aggiornamento etichetta', 'error');
                }
            }
        }

        function formatTextWithLineBreaks(text, maxLength) {
            if (!text) return '';
            
            let formattedText = '';
            while (text.length > maxLength) {
                formattedText += text.substring(0, maxLength) + '\n';
                text = text.substring(maxLength);
            }
            formattedText += text;
            return formattedText;
        }

        function populatePrinterDetails() {
            const container = document.getElementById('printer-details');
            const emptyContainer = document.getElementById('printer-details-container');
            const select = document.getElementById('printersSelect');
            
            if (!select || !container) return;

            const selectedIndex = select.value;
            if (selectedIndex === '' || !_printers[selectedIndex]) {
                container.classList.add('hidden');
                if (emptyContainer) {
                    emptyContainer.classList.remove('hidden');
                }
                return;
            }

            const printer = _printers[selectedIndex];
            if (emptyContainer) {
                emptyContainer.classList.add('hidden');
            }
            container.classList.remove('hidden');

            let html = `
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Modello:</span>
                        <span class="text-sm text-gray-900 dark:text-white">${printer.modelName || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Locale:</span>
                        <span class="text-sm text-gray-900 dark:text-white">${printer.isLocal ? 'Sì' : 'No'}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Connessa:</span>
                        <span class="text-sm ${printer.isConnected ? 'text-green-600' : 'text-red-600'}">${printer.isConnected ? 'Sì' : 'No'}</span>
                    </div>
                    <div id="consumable-info" class="space-y-2">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Caricamento info consumabili...</div>
                    </div>
                </div>
            `;

            container.innerHTML = html;

            // Carica info consumabili se supportato
            if (typeof dymo !== 'undefined' && dymo.label && dymo.label.framework) {
                try {
                    var is550Promise = dymo.label.framework.is550PrinterAsync(printer.name);
                    
                    is550Promise.then(function (isSupported) {
                        const consumableDiv = document.getElementById('consumable-info');
                        if (!consumableDiv) return;

                        if (isSupported) {
                            var consumablePromise = dymo.label.framework.getConsumableInfoIn550PrinterAsync(printer.name);
                            
                            consumablePromise.then(function (info) {
                                consumableDiv.innerHTML = `
                                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">SKU Etichette:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">${info.sku || 'N/A'}</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Nome Etichette:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">${info.name || 'N/A'}</span>
                                    </div>
                                    <div class="flex justify-between py-2">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Rimanenti:</span>
                                        <span class="text-sm text-gray-900 dark:text-white">${info.labelsRemaining || 'N/A'}</span>
                                    </div>
                                `;
                            });
                            
                            if (consumablePromise.thenCatch) {
                                consumablePromise.thenCatch(function () {
                                    consumableDiv.innerHTML = '<div class="text-sm text-gray-500 dark:text-gray-400">Info consumabili non disponibili</div>';
                                });
                            }
                        } else {
                            consumableDiv.innerHTML = '<div class="text-sm text-gray-500 dark:text-gray-400">Stampante non supporta info consumabili</div>';
                        }
                    });
                    
                    if (is550Promise.thenCatch) {
                        is550Promise.thenCatch(function () {
                            const consumableDiv = document.getElementById('consumable-info');
                            if (consumableDiv) {
                                consumableDiv.innerHTML = '<div class="text-sm text-gray-500 dark:text-gray-400">Errore nel caricamento info consumabili</div>';
                            }
                        });
                    }
                } catch (error) {
                    console.error('Error loading consumable info:', error);
                }
            }
        }

        function printLabel() {
            const select = document.getElementById('printersSelect');
            
            if (!select || select.value === '') {
                if (window.showAlert) {
                    window.showAlert('Seleziona una stampante', 'error');
                }
                return;
            }

            if (!label) {
                if (window.showAlert) {
                    window.showAlert('Template etichetta non caricato', 'error');
                }
                return;
            }

            if (!currentArticleData) {
                if (window.showAlert) {
                    window.showAlert('Seleziona un articolo prima di stampare', 'error');
                }
                return;
            }

            const printer = _printers[select.value];
            if (!printer) {
                if (window.showAlert) {
                    window.showAlert('Stampante non disponibile', 'error');
                }
                return;
            }

            try {
                showLoading(true);
                
                // Validazione finale prima della stampa
                const barcode = currentArticleData.barcode || '';
                if (!barcode || barcode.length < 3) {
                    showLoading(false);
                    if (window.showAlert) {
                        window.showAlert('Barcode non valido per la stampa', 'error');
                    }
                    return;
                }
                
                console.log('Printing label with printer:', printer.name);
                console.log('Current article data:', currentArticleData);
                
                // Stampa l'etichetta
                label.print(printer.name);
                
                setTimeout(() => {
                    showLoading(false);
                    if (window.showAlert) {
                        window.showAlert('Etichetta inviata alla stampante', 'success');
                    }
                }, 2000);

            } catch (error) {
                showLoading(false);
                console.error('Print error:', error);
                if (window.showAlert) {
                    window.showAlert('Errore di stampa: ' + error.message, 'error');
                }
            }
        }

        function showStatusAlert(message, type = 'info') {
            const alert = document.getElementById('printer-status-alert');
            const messageEl = document.getElementById('printer-status-message');
            
            if (!alert || !messageEl) return;

            messageEl.textContent = message;
            alert.classList.remove('hidden');

            // Auto-hide success messages
            if (type === 'success') {
                setTimeout(() => {
                    alert.classList.add('hidden');
                }, 5000);
            }
        }

        function showLoading(show) {
            const overlay = document.getElementById('loading-overlay');
            if (overlay) {
                if (show) {
                    overlay.classList.remove('hidden');
                } else {
                    overlay.classList.add('hidden');
                }
            }
        }

        // Funzioni globali
        window.refreshPrinters = function() {
            loadPrintersAsync();
        };

        // Registra inizializzatore PJAX
        if (window.COREGRE && window.COREGRE.onPageLoad) {
            window.COREGRE.onPageLoad(initEtichetteDYMO);
        }

        // Inizializzazione
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initEtichetteDYMO);
        } else {
            initEtichetteDYMO();
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', cleanupEventListeners);

    })();
</script>