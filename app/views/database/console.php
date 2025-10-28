<!-- SQL Console Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-gray-500 to-gray-600 shadow-lg mr-4">
                    <i class="fas fa-terminal text-xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Console SQL
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Esecuzione sicura di query SQL personalizzate
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <button onclick="showSavedQueries()"
                class="inline-flex items-center rounded-lg border border-blue-300 bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-history mr-2"></i>
                Query Salvate
            </button>
            <button onclick="showQueryHistory()"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-clock mr-2"></i>
                Cronologia
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
                <a href="<?= $this->url('/database') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">Database Manager</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Console SQL</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Warning Banner -->
<div class="mb-6 rounded-2xl border border-orange-200 bg-orange-50 dark:border-orange-800 dark:bg-orange-900/20 p-6 shadow-lg">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-400 text-xl"></i>
        </div>
        <div class="ml-4 flex-1">
            <h3 class="text-lg font-semibold text-orange-900 dark:text-orange-200">
                Attenzione: Console Amministratore
            </h3>
            <p class="mt-1 text-sm text-orange-800 dark:text-orange-300">
                Questa console permette l'esecuzione diretta di query SQL. Usa con estrema cautela. 
                Le operazioni di modifica (INSERT, UPDATE, DELETE, DROP) sono irreversibili.
            </p>
            <div class="mt-4 flex items-center space-x-4 text-xs text-orange-700 dark:text-orange-300">
                <span class="flex items-center">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Solo Super Admin
                </span>
                <span class="flex items-center">
                    <i class="fas fa-history mr-1"></i>
                    Tutte le query sono registrate
                </span>
                <span class="flex items-center">
                    <i class="fas fa-database mr-1"></i>
                    Database: <?= DB_NAME ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Main Console -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Query Editor -->
    <div class="lg:col-span-2">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-code mr-3 text-gray-500"></i>
                            Editor SQL
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Scrivi e esegui le tue query SQL personalizzate
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="formatQuery()" title="Formatta query"
                            class="p-2 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-align-left"></i>
                        </button>
                        <button onclick="clearQuery()" title="Pulisci editor"
                            class="p-2 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fas fa-eraser"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="relative">
                    <textarea id="sql-query" 
                              placeholder="-- Inserisci la tua query SQL qui
SELECT * FROM users WHERE active = 1 LIMIT 10;

-- Esempi di query comuni:
-- SHOW TABLES;
-- DESCRIBE table_name;
-- SELECT COUNT(*) FROM table_name;"
                              class="w-full h-64 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg font-mono text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:bg-gray-700 dark:text-white resize-none"
                              spellcheck="false"></textarea>
                    
                    <!-- Query Stats -->
                    <div class="absolute bottom-2 right-2 text-xs text-gray-400 bg-white dark:bg-gray-700 px-2 py-1 rounded border">
                        <span id="query-length">0</span> caratteri
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" id="save-query" class="form-checkbox h-4 w-4 text-blue-600 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Salva query</span>
                        </label>
                        
                        <input type="text" id="query-name" placeholder="Nome query..." 
                               class="hidden w-48 px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:border-primary focus:outline-none dark:bg-gray-700 dark:text-white">
                    </div>

                    <div class="flex items-center space-x-3">
                        <button onclick="validateQuery()" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-check-circle mr-2"></i>
                            Valida
                        </button>
                        
                        <button onclick="executeQuery()" id="execute-btn"
                                class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg text-sm font-medium hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                            <i class="fas fa-play mr-2"></i>
                            Esegui Query
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="lg:col-span-1 space-y-6">
        
        <!-- Common Queries -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-title-sm font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-bolt mr-3 text-yellow-500"></i>
                    Query Comuni
                </h3>
            </div>
            <div class="p-4 space-y-2">
                <button onclick="insertCommonQuery('SHOW TABLES')" 
                        class="w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-list text-blue-500 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white text-sm">Mostra Tabelle</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SHOW TABLES</p>
                        </div>
                    </div>
                </button>

                <button onclick="insertCommonQuery('SHOW PROCESSLIST')" 
                        class="w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-tasks text-green-500 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white text-sm">Processi Attivi</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SHOW PROCESSLIST</p>
                        </div>
                    </div>
                </button>

                <button onclick="insertCommonQuery('SELECT VERSION()')" 
                        class="w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-purple-500 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white text-sm">Versione MySQL</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SELECT VERSION()</p>
                        </div>
                    </div>
                </button>

                <button onclick="showTableSelector()" 
                        class="w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-database text-orange-500 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white text-sm">Struttura Tabella</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">DESCRIBE table</p>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Execution Status -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-title-sm font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-tachometer-alt mr-3 text-green-500"></i>
                    Stato Esecuzione
                </h3>
            </div>
            <div class="p-4" id="execution-status">
                <div class="text-center py-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 mx-auto mb-4">
                        <i class="fas fa-clock text-gray-400 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">In attesa di esecuzione</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Results Section -->
<div id="results-section" class="mt-8 hidden">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-chart-line mr-3 text-blue-500"></i>
                        <span id="results-title">Risultati Query</span>
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" id="results-subtitle">
                        Risultati dell'ultima query eseguita
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="exportResults('csv')" id="export-csv-btn" class="hidden inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        <i class="fas fa-file-csv mr-1"></i>
                        CSV
                    </button>
                    <button onclick="copyResults()" id="copy-results-btn" class="hidden inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        <i class="fas fa-copy mr-1"></i>
                        Copia
                    </button>
                    <button onclick="clearResults()" class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/30">
                        <i class="fas fa-times mr-1"></i>
                        Pulisci
                    </button>
                </div>
            </div>
        </div>

        <div id="results-content" class="p-6">
            <!-- I risultati verranno inseriti qui via JavaScript -->
        </div>
    </div>
</div>

<!-- Modal Cronologia Query -->
<div id="query-history-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-99999 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-history text-blue-500 mr-3"></i>
                        Cronologia Query
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Le ultime query eseguite nella console
                    </p>
                </div>
                <button onclick="hideQueryHistoryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[70vh]" id="query-history-content">
            <!-- Contenuto popolato dinamicamente -->
        </div>
        
        <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
            <button onclick="clearQueryHistory()" 
                    class="px-4 py-2 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                <i class="fas fa-trash mr-2"></i>
                Pulisci Cronologia
            </button>
            <button onclick="hideQueryHistoryModal()" 
                    class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                Chiudi
            </button>
        </div>
    </div>
</div>

<!-- Modal Query Salvate -->
<div id="saved-queries-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-99999 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-bookmark text-green-500 mr-3"></i>
                        Query Salvate
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Le tue query salvate per riutilizzo rapido
                    </p>
                </div>
                <button onclick="hideSavedQueriesModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[70vh]" id="saved-queries-content">
            <!-- Contenuto popolato dinamicamente -->
        </div>
        
        <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
            <button onclick="hideSavedQueriesModal()" 
                    class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                Chiudi
            </button>
        </div>
    </div>
</div>

<script>
    // SQL Console - JavaScript compatibile con PJAX
    (function() {
        let eventListeners = [];
        let queryHistory = JSON.parse(localStorage.getItem('sql_console_history') || '[]');
        let currentResults = null;

        function initSQLConsole() {
            cleanupEventListeners();

            // Gestione checkbox salva query
            const saveQueryCheckbox = document.getElementById('save-query');
            const queryNameInput = document.getElementById('query-name');

            if (saveQueryCheckbox && queryNameInput) {
                function checkboxHandler() {
                    if (this.checked) {
                        queryNameInput.classList.remove('hidden');
                        queryNameInput.focus();
                    } else {
                        queryNameInput.classList.add('hidden');
                        queryNameInput.value = '';
                    }
                }
                saveQueryCheckbox.addEventListener('change', checkboxHandler);
                eventListeners.push({ element: saveQueryCheckbox, event: 'change', handler: checkboxHandler });
            }

            // Aggiorna contatore caratteri
            const sqlQuery = document.getElementById('sql-query');
            const queryLength = document.getElementById('query-length');

            if (sqlQuery && queryLength) {
                function inputHandler() {
                    queryLength.textContent = this.value.length;
                }
                sqlQuery.addEventListener('input', inputHandler);
                eventListeners.push({ element: sqlQuery, event: 'input', handler: inputHandler });
            }

            // Shortcut keyboard
            if (sqlQuery) {
                function keydownHandler(e) {
                    // Ctrl+Enter per eseguire query
                    if (e.ctrlKey && e.key === 'Enter') {
                        e.preventDefault();
                        executeQuery();
                    }
                    // Ctrl+L per pulire
                    if (e.ctrlKey && e.key === 'l') {
                        e.preventDefault();
                        clearQuery();
                    }
                }
                sqlQuery.addEventListener('keydown', keydownHandler);
                eventListeners.push({ element: sqlQuery, event: 'keydown', handler: keydownHandler });
            }
        }

        function cleanupEventListeners() {
            eventListeners.forEach(({ element, event, handler }) => {
                element.removeEventListener(event, handler);
            });
            eventListeners = [];
        }

        // Funzioni globali
        window.executeQuery = async function() {
            const queryTextarea = document.getElementById('sql-query');
            const executeBtn = document.getElementById('execute-btn');
            const statusDiv = document.getElementById('execution-status');
            
            if (!queryTextarea || !queryTextarea.value.trim()) {
                showAlert('Inserisci una query SQL da eseguire', 'warning');
                return;
            }

            const query = queryTextarea.value.trim();
            const saveQuery = document.getElementById('save-query')?.checked;
            const queryName = document.getElementById('query-name')?.value?.trim();

            try {
                // Mostra stato di caricamento
                executeBtn.disabled = true;
                executeBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Eseguendo...';
                
                statusDiv.innerHTML = `
                    <div class="flex items-center py-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/20 mr-3">
                            <i class="fas fa-spinner fa-spin text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-blue-900 dark:text-blue-200 text-sm">Esecuzione in corso...</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400">Query: ${query.length} caratteri</p>
                        </div>
                    </div>
                `;

                const formData = new FormData();
                formData.append('query', query);
                if (saveQuery && queryName) {
                    formData.append('save_query', '1');
                    formData.append('query_name', queryName);
                }

                const response = await fetch('<?= $this->url('/database/execute-query') ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                // Salva in cronologia
                addToHistory(query);

                // Salva query se richiesto
                if (saveQuery && queryName) {
                    const savedQueries = JSON.parse(localStorage.getItem('sql_console_saved_queries') || '[]');
                    savedQueries.push({
                        name: queryName,
                        query: query,
                        saved_at: new Date().toISOString()
                    });
                    localStorage.setItem('sql_console_saved_queries', JSON.stringify(savedQueries));
                    showAlert(`Query "${queryName}" salvata`, 'success');
                    
                    // Reset del form salvataggio
                    document.getElementById('save-query').checked = false;
                    document.getElementById('query-name').classList.add('hidden');
                    document.getElementById('query-name').value = '';
                } else {
                    showAlert('Query eseguita con successo', 'success');
                }

                // Mostra risultati
                displayResults(result, query);

                // Aggiorna stato
                updateExecutionStatus('success', result);

            } catch (error) {
                console.error('Query execution error:', error);
                
                updateExecutionStatus('error', { error: error.message });
                
                if (window.showAlert) {
                    window.showAlert(`Errore SQL: ${error.message}`, 'error');
                }
            } finally {
                executeBtn.disabled = false;
                executeBtn.innerHTML = '<i class="fas fa-play mr-2"></i>Esegui Query';
            }
        };

        function displayResults(result, query) {
            const resultsSection = document.getElementById('results-section');
            const resultsContent = document.getElementById('results-content');
            const resultsTitle = document.getElementById('results-title');
            const resultsSubtitle = document.getElementById('results-subtitle');
            const exportCsvBtn = document.getElementById('export-csv-btn');
            const copyResultsBtn = document.getElementById('copy-results-btn');

            if (!resultsSection || !resultsContent) return;

            currentResults = result;

            if (result.type === 'select' && result.data && result.data.length > 0) {
                // Query SELECT con dati
                resultsTitle.textContent = `Risultati Query (${result.count} record)`;
                resultsSubtitle.textContent = `Query eseguita: ${new Date().toLocaleString()}`;

                const table = createResultsTable(result.data, result.columns);
                resultsContent.innerHTML = `<div class="overflow-x-auto">${table}</div>`;

                exportCsvBtn.classList.remove('hidden');
                copyResultsBtn.classList.remove('hidden');

            } else if (result.type === 'modify') {
                // Query di modifica
                resultsTitle.textContent = 'Query Eseguita';
                resultsSubtitle.textContent = `${result.affected_rows} righe interessate`;

                resultsContent.innerHTML = `
                    <div class="text-center py-8">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20 mx-auto mb-4">
                            <i class="fas fa-check text-2xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Operazione Completata</h3>
                        <p class="text-gray-600 dark:text-gray-400">${result.message}</p>
                    </div>
                `;

                exportCsvBtn.classList.add('hidden');
                copyResultsBtn.classList.add('hidden');

            } else {
                // Nessun risultato
                resultsTitle.textContent = 'Query Eseguita';
                resultsSubtitle.textContent = 'Nessun risultato restituito';

                resultsContent.innerHTML = `
                    <div class="text-center py-8">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 mx-auto mb-4">
                            <i class="fas fa-inbox text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nessun Risultato</h3>
                        <p class="text-gray-600 dark:text-gray-400">La query è stata eseguita ma non ha restituito dati</p>
                    </div>
                `;

                exportCsvBtn.classList.add('hidden');
                copyResultsBtn.classList.add('hidden');
            }

            resultsSection.classList.remove('hidden');
            resultsSection.scrollIntoView({ behavior: 'smooth' });
        }

        function createResultsTable(data, columns) {
            let html = `
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
            `;

            columns.forEach(column => {
                html += `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">${column}</th>`;
            });

            html += `
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800/40 divide-y divide-gray-200 dark:divide-gray-700">
            `;

            data.forEach((row, index) => {
                html += `<tr class="${index % 2 === 0 ? 'bg-white dark:bg-gray-800/40' : 'bg-gray-50 dark:bg-gray-700/50'}">`;
                
                columns.forEach(column => {
                    const value = row[column];
                    let displayValue;
                    
                    if (value === null) {
                        displayValue = '<span class="text-gray-400 italic">NULL</span>';
                    } else if (typeof value === 'string' && value.length > 100) {
                        displayValue = `<span title="${escapeHtml(value)}">${escapeHtml(value.substring(0, 100))}...</span>`;
                    } else {
                        displayValue = escapeHtml(String(value));
                    }
                    
                    html += `<td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">${displayValue}</td>`;
                });
                
                html += '</tr>';
            });

            html += `
                    </tbody>
                </table>
            `;

            return html;
        }

        function updateExecutionStatus(type, result) {
            const statusDiv = document.getElementById('execution-status');
            
            if (type === 'success') {
                statusDiv.innerHTML = `
                    <div class="flex items-center py-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20 mr-3">
                            <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-green-900 dark:text-green-200 text-sm">Eseguita con successo</p>
                            <p class="text-xs text-green-600 dark:text-green-400">
                                ${result.type === 'select' ? `${result.count || 0} record` : `${result.affected_rows || 0} righe modificate`}
                            </p>
                        </div>
                    </div>
                `;
            } else if (type === 'error') {
                statusDiv.innerHTML = `
                    <div class="flex items-center py-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20 mr-3">
                            <i class="fas fa-times text-red-600 dark:text-red-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-red-900 dark:text-red-200 text-sm">Errore nell'esecuzione</p>
                            <p class="text-xs text-red-600 dark:text-red-400">${result.error}</p>
                        </div>
                    </div>
                `;
            }
        }

        function addToHistory(query) {
            const historyItem = {
                query: query,
                timestamp: new Date().toISOString()
            };
            
            queryHistory.unshift(historyItem);
            queryHistory = queryHistory.slice(0, 50); // Mantieni solo le ultime 50
            
            localStorage.setItem('sql_console_history', JSON.stringify(queryHistory));
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Altre funzioni globali
        window.clearQuery = function() {
            const queryTextarea = document.getElementById('sql-query');
            if (queryTextarea) {
                queryTextarea.value = '';
                queryTextarea.focus();
                document.getElementById('query-length').textContent = '0';
            }
        };

        window.clearResults = function() {
            const resultsSection = document.getElementById('results-section');
            if (resultsSection) {
                resultsSection.classList.add('hidden');
                currentResults = null;
            }
        };

        window.insertCommonQuery = function(query) {
            const queryTextarea = document.getElementById('sql-query');
            if (queryTextarea) {
                queryTextarea.value = query + ';';
                queryTextarea.focus();
                document.getElementById('query-length').textContent = queryTextarea.value.length;
            }
        };

        window.formatQuery = function() {
            if (window.showAlert) {
                window.showAlert('Funzionalità di formattazione in fase di sviluppo', 'info');
            }
        };

        window.validateQuery = function() {
            const query = document.getElementById('sql-query')?.value?.trim();
            if (!query) {
                if (window.showAlert) {
                    window.showAlert('Nessuna query da validare', 'warning');
                }
                return;
            }

            // Validazioni di base
            const dangerousPatterns = [
                /DROP\s+DATABASE/i,
                /DROP\s+SCHEMA/i,
                /TRUNCATE\s+\*/i
            ];

            const warnings = [];
            dangerousPatterns.forEach(pattern => {
                if (pattern.test(query)) {
                    warnings.push('Query potenzialmente pericolosa rilevata');
                }
            });

            if (warnings.length > 0) {
                if (window.showAlert) {
                    window.showAlert(warnings.join(', '), 'warning');
                }
            } else {
                if (window.showAlert) {
                    window.showAlert('Query validata - nessun problema rilevato', 'success');
                }
            }
        };

        window.showSavedQueries = function() {
            const savedQueries = JSON.parse(localStorage.getItem('sql_console_saved_queries') || '[]');
            populateSavedQueriesModal(savedQueries);
            showSavedQueriesModal();
        };

        window.showQueryHistory = function() {
            if (queryHistory.length === 0) {
                showAlert('Nessuna query nella cronologia', 'info');
                return;
            }

            populateQueryHistoryModal();
            showQueryHistoryModal();
        };

        function populateQueryHistoryModal() {
            const content = document.getElementById('query-history-content');
            if (!content) return;

            if (queryHistory.length === 0) {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 mx-auto mb-4">
                            <i class="fas fa-history text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Cronologia Vuota</h3>
                        <p class="text-gray-600 dark:text-gray-400">Non hai ancora eseguito query in questa sessione</p>
                    </div>
                `;
                return;
            }

            let html = '<div class="space-y-4">';
            queryHistory.forEach((item, index) => {
                const date = new Date(item.timestamp);
                const formattedDate = date.toLocaleString('it-IT');
                
                html += `
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-clock mr-2"></i>
                                ${formattedDate}
                            </div>
                            <div class="flex items-center space-x-2">
                                <button onclick="useHistoryQuery(${index})" 
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300" title="Usa questa query">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                                <button onclick="copyToClipboard('${escapeJs(item.query)}')" 
                                        class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300" title="Copia query">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-800 rounded p-3 font-mono text-sm">
                            <pre class="whitespace-pre-wrap text-gray-900 dark:text-gray-200">${escapeHtml(item.query)}</pre>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            content.innerHTML = html;
        }

        function populateSavedQueriesModal(savedQueries) {
            const content = document.getElementById('saved-queries-content');
            if (!content) return;

            if (savedQueries.length === 0) {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 mx-auto mb-4">
                            <i class="fas fa-bookmark text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nessuna Query Salvata</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Salva le tue query più utilizzate spuntando "Salva query" durante l'esecuzione
                        </p>
                        <button onclick="hideSavedQueriesModal()" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Torna alla Console
                        </button>
                    </div>
                `;
                return;
            }

            let html = '<div class="space-y-4">';
            savedQueries.forEach((item, index) => {
                const date = new Date(item.saved_at || new Date()).toLocaleString('it-IT');
                
                html += `
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">${escapeHtml(item.name)}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center mt-1">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Salvata il ${date}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button onclick="useSavedQuery(${index})" 
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300" title="Usa questa query">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                                <button onclick="copyToClipboard('${escapeJs(item.query)}')" 
                                        class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300" title="Copia query">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button onclick="deleteSavedQuery(${index})" 
                                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300" title="Elimina query">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-800 rounded p-3 font-mono text-sm">
                            <pre class="whitespace-pre-wrap text-gray-900 dark:text-gray-200">${escapeHtml(item.query)}</pre>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            content.innerHTML = html;
        }

        function showQueryHistoryModal() {
            CoregreModals.openModal('query-history-modal');
        }

        window.hideQueryHistoryModal = function() {
            CoregreModals.closeModal('query-history-modal');
        };

        function showSavedQueriesModal() {
            CoregreModals.openModal('saved-queries-modal');
        }

        window.hideSavedQueriesModal = function() {
            CoregreModals.closeModal('saved-queries-modal');
        };

        window.clearQueryHistory = function() {
            if (confirm('Sei sicuro di voler cancellare tutta la cronologia delle query?')) {
                queryHistory = [];
                localStorage.removeItem('sql_console_history');
                showAlert('Cronologia cancellata', 'success');
                hideQueryHistoryModal();
            }
        };

        window.useHistoryQuery = function(index) {
            const item = queryHistory[index];
            if (item) {
                const queryTextarea = document.getElementById('sql-query');
                if (queryTextarea) {
                    queryTextarea.value = item.query;
                    queryTextarea.focus();
                    document.getElementById('query-length').textContent = item.query.length;
                }
                hideQueryHistoryModal();
            }
        };

        window.useSavedQuery = function(index) {
            const savedQueries = JSON.parse(localStorage.getItem('sql_console_saved_queries') || '[]');
            const item = savedQueries[index];
            if (item) {
                const queryTextarea = document.getElementById('sql-query');
                if (queryTextarea) {
                    queryTextarea.value = item.query;
                    queryTextarea.focus();
                    document.getElementById('query-length').textContent = item.query.length;
                }
                hideSavedQueriesModal();
            }
        };

        window.deleteSavedQuery = function(index) {
            if (confirm('Sei sicuro di voler eliminare questa query salvata?')) {
                let savedQueries = JSON.parse(localStorage.getItem('sql_console_saved_queries') || '[]');
                savedQueries.splice(index, 1);
                localStorage.setItem('sql_console_saved_queries', JSON.stringify(savedQueries));
                populateSavedQueriesModal(savedQueries);
                showAlert('Query eliminata', 'success');
            }
        };

        window.showTableSelector = function() {
            if (window.showAlert) {
                window.showAlert('Selettore tabelle in fase di sviluppo', 'info');
            }
        };

        window.exportResults = function(format) {
            if (!currentResults || !currentResults.data) {
                showAlert('Nessun risultato da esportare', 'warning');
                return;
            }

            if (format === 'csv') {
                exportToCSV(currentResults.data, currentResults.columns);
            } else {
                showAlert(`Export ${format} non ancora implementato`, 'info');
            }
        };

        window.copyResults = function() {
            if (!currentResults || !currentResults.data) {
                showAlert('Nessun risultato da copiare', 'warning');
                return;
            }

            try {
                const csvContent = generateCSVContent(currentResults.data, currentResults.columns);
                copyToClipboard(csvContent);
                showAlert('Risultati copiati negli appunti', 'success');
            } catch (error) {
                console.error('Copy error:', error);
                showAlert('Errore durante la copia', 'error');
            }
        };

        function exportToCSV(data, columns) {
            try {
                const csvContent = generateCSVContent(data, columns);
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                
                if (link.download !== undefined) {
                    const url = URL.createObjectURL(blob);
                    link.setAttribute('href', url);
                    link.setAttribute('download', `query_results_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.csv`);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    showAlert('File CSV scaricato con successo', 'success');
                } else {
                    showAlert('Il tuo browser non supporta il download automatico', 'warning');
                }
            } catch (error) {
                console.error('Export error:', error);
                showAlert('Errore durante l\'export CSV', 'error');
            }
        }

        function generateCSVContent(data, columns) {
            const csvRows = [];
            
            // Header
            csvRows.push(columns.map(col => `"${col.replace(/"/g, '""')}"`).join(','));
            
            // Data rows
            data.forEach(row => {
                const values = columns.map(column => {
                    let value = row[column];
                    if (value === null || value === undefined) {
                        return '';
                    }
                    if (typeof value === 'string') {
                        // Escape quotes and wrap in quotes
                        return `"${value.replace(/"/g, '""')}"`;
                    }
                    return value;
                });
                csvRows.push(values.join(','));
            });
            
            return csvRows.join('\\n');
        }

        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    showAlert('Copiato negli appunti', 'success');
                }).catch(err => {
                    fallbackCopyTextToClipboard(text);
                });
            } else {
                fallbackCopyTextToClipboard(text);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.top = '0';
            textArea.style.left = '0';
            textArea.style.position = 'fixed';
            
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                showAlert('Copiato negli appunti', 'success');
            } catch (err) {
                console.error('Fallback copy failed:', err);
                showAlert('Impossibile copiare negli appunti', 'error');
            }
            
            document.body.removeChild(textArea);
        }

        function escapeJs(str) {
            return str ? str.replace(/'/g, "\\\\'").replace(/"/g, '\\\\"').replace(/\\n/g, '\\\\n').replace(/\\r/g, '\\\\r') : '';
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
        }

        // Registra l'inizializzatore per PJAX
        if (window.COREGRE && window.COREGRE.onPageLoad) {
            window.COREGRE.onPageLoad(initSQLConsole);
        }

        // Inizializza anche al primo caricamento
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSQLConsole);
        } else {
            initSQLConsole();
        }
    })();
</script>