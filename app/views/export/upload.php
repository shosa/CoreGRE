<!-- Header con titolo -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <span class="bg-gradient-to-r from-blue-500 to-blue-600 bg-clip-text text-transparent mr-3">STEP 2</span>
                <i class="fas fa-arrow-right text-gray-400 mx-2"></i>
                Caricamento Schede DDT n° <?= $progressivo ?>
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Carica le schede tecniche in formato Excel per il documento di trasporto
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
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Caricamento Schede</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Progress Steps -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <!-- Step 1 - Completato -->
        <div class="flex items-center text-green-600">
            <div class="flex-shrink-0 w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-white text-sm"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-600">Dettagli</p>
                <p class="text-xs text-green-500">Completato</p>
            </div>
        </div>
        
        <div class="flex-1 h-0.5 bg-green-300 mx-4"></div>
        
        <!-- Step 2 - Attivo -->
        <div class="flex items-center text-blue-600">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-bold">2</span>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-blue-600">Caricamento</p>
                <p class="text-xs text-blue-500">Schede tecniche Excel</p>
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

<!-- Alert informativo -->
<div class="rounded-lg bg-blue-50 p-4 mb-6 border border-blue-200 dark:bg-blue-800/20 dark:border-blue-700">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-400 text-xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                Istruzioni per il caricamento
            </h3>
            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                <p>Carica le schede tecniche trascinandole nell'area sottostante o cliccando su di essa per selezionarle. Solo file Excel (.xlsx) sono supportati.</p>
            </div>
        </div>
    </div>
</div>

<!-- Drag and Drop Area -->
<div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-6">
    <div class="p-6">
        <div id="drop-zone" class="relative border-2 border-dashed border-blue-300 dark:border-blue-600 rounded-xl p-12 text-center cursor-pointer transition-colors hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-800/10">
            <div class="mx-auto h-16 w-16 text-blue-400 mb-4">
                <i class="fas fa-file-excel text-6xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                Trascina qui le schede tecniche Excel
            </h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
                o clicca per selezionarle dal computer
            </p>
            <div class="flex items-center justify-center space-x-2 text-sm text-gray-400">
                <i class="fas fa-file text-blue-500"></i>
                <span>Solo file .xlsx supportati</span>
            </div>
            
            <!-- Input file nascosto -->
            <input type="file" id="fileInput" multiple accept=".xlsx" class="hidden">
        </div>
    </div>
</div>

<!-- Lista file caricati -->
<div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-list mr-3 text-blue-500"></i>
            File caricati
            <span id="fileCount" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/20 dark:text-blue-300">
                0 file
            </span>
        </h3>
    </div>
    <div class="p-6">
        <div id="file-list" class="space-y-3">
            <!-- I file verranno aggiunti qui dinamicamente -->
            <div id="empty-state" class="text-center py-8">
                <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                    <i class="fas fa-folder-open text-4xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400">
                    Nessun file caricato ancora
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Pulsanti navigazione -->
<div class="flex justify-between">
    <a href="<?= $this->url('/export/create') ?>" 
       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>
        Indietro
    </a>
    
    <button onclick="navigateToStep3()" 
            class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200">
        Avanti
        <i class="fas fa-arrow-right ml-2"></i>
    </button>
</div>

<!-- Modal per visualizzare il contenuto Excel -->
<div id="excel-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-99999">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-2xl bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-file-excel mr-2 text-green-500"></i>
                Contenuto Excel
            </h3>
            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" onclick="closeModal('excel-modal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="mt-6">
            <!-- Informazioni modello -->
            <div id="excel-info" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-tag mr-1 text-blue-500"></i>
                        Modello
                    </label>
                    <div id="modello-info" class="text-gray-900 dark:text-white font-medium"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-rocket mr-1 text-green-500"></i>
                        Lancio
                    </label>
                    <input type="text" id="lancio-input" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Inserisci lancio">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-cubes mr-1 text-orange-500"></i>
                        Quantità
                    </label>
                    <input type="number" id="qty-input" value="1" min="1" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           oninput="updateTotals()">
                </div>
            </div>
            
            <!-- Tabelle contenuto -->
            <div class="space-y-6">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-cut mr-2 text-red-500"></i>
                        TAGLIO
                    </h4>
                    <div class="overflow-x-auto">
                        <div id="taglio-table" class="min-w-full"></div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-ruler mr-2 text-blue-500"></i>
                        ORLATURA
                    </h4>
                    <div class="overflow-x-auto">
                        <div id="orlatura-table" class="min-w-full"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700 mt-6">
            <button onclick="closeModal('excel-modal')" 
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                Chiudi
            </button>
            <button onclick="saveExcelData()" 
                    class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700">
                <i class="fas fa-save mr-2"></i>
                Salva
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    let eventListeners = [];
    let processedFiles = new Set();
    
    function initExportUpload() {
        cleanupEventListeners();
        
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('file-list');
        const emptyState = document.getElementById('empty-state');
        const fileCount = document.getElementById('fileCount');
        
        // Verifica che gli elementi esistano (evita errori se script eseguito su pagine diverse)
        if (!dropZone || !fileInput || !fileList || !emptyState || !fileCount) {
            return; // Non è la pagina di upload, esci
        }
        
        // Aggiorna conteggio file
        function updateFileCount() {
            const count = document.querySelectorAll('#file-list .file-item').length;
            fileCount.textContent = count + ' file' + (count !== 1 ? '' : '');
            
            if (count === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }
        
        // Event listeners per drag & drop
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        function highlight() {
            dropZone.classList.add('border-blue-400', 'bg-blue-50');
            dropZone.classList.remove('border-blue-300');
        }
        
        function unhighlight() {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
            dropZone.classList.add('border-blue-300');
        }
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }
        
        function handleFileSelect(e) {
            const files = e.target.files;
            handleFiles(files);
            // Reset input per permettere riselezionare stesso file
            e.target.value = '';
        }
        
        function handleFiles(files) {
            [...files].forEach(file => {
                if (file.type !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                    if (window.showAlert) {
                        window.showAlert('Formato non valido: ' + file.name + '. Carica solo file Excel (.xlsx)', 'error');
                    } else if (window.WebgreNotifications) {
                        window.WebgreNotifications.error('Formato non valido: ' + file.name);
                    }
                    return;
                }
                
                uploadFile(file);
            });
        }
        
        function uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            
            // Aggiungi alla lista con stato loading
            const fileId = 'file-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
            addFileToList(file.name, fileId, 'loading');
            
            fetch(window.WEBGRE.baseUrl + '/export/api/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.WEBGRE.csrfToken
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateFileStatus(fileId, 'success', file.name, data.fileName);
                } else {
                    updateFileStatus(fileId, 'error', file.name);
                    if (window.showAlert) {
                        window.showAlert(data.error || 'Errore nel caricamento del file', 'error');
                    } else if (window.WebgreNotifications) {
                        window.WebgreNotifications.error(data.error || 'Errore nel caricamento del file');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                updateFileStatus(fileId, 'error', file.name);
                if (window.showAlert) {
                    window.showAlert('Errore di rete nel caricamento del file', 'error');
                } else if (window.WebgreNotifications) {
                    window.WebgreNotifications.error('Errore di rete nel caricamento del file');
                }
            });
        }
        
        function addFileToList(fileName, fileId, status, generatedFileName) {
            const fileItem = document.createElement('div');
            fileItem.id = fileId;
            fileItem.className = 'file-item flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg';
            
            let statusIcon, statusClass, actionButton;
            
            switch (status) {
                case 'loading':
                    statusIcon = '<i class="fas fa-spinner fa-spin text-blue-500"></i>';
                    statusClass = 'text-blue-600';
                    actionButton = '';
                    break;
                case 'success':
                    statusIcon = '<i class="fas fa-file-excel text-green-500"></i>';
                    statusClass = 'text-green-600';
                    const fileNameToUse = generatedFileName || fileName;
                    actionButton = `<button onclick="showExcelContent('${fileNameToUse}', '${fileId}')" 
                                             class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 dark:border-blue-600 dark:bg-blue-800/20 dark:text-blue-400">
                                        <i class="fas fa-eye mr-1"></i>
                                        Visualizza
                                    </button>`;
                    break;
                case 'error':
                    statusIcon = '<i class="fas fa-exclamation-triangle text-red-500"></i>';
                    statusClass = 'text-red-600';
                    actionButton = `<button onclick="removeFile('${fileId}')" 
                                             class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100">
                                        <i class="fas fa-trash mr-1"></i>
                                        Rimuovi
                                    </button>`;
                    break;
            }
            
            fileItem.innerHTML = `
                <div class="flex items-center space-x-3">
                    ${statusIcon}
                    <div>
                        <div class="text-sm font-medium ${statusClass}">${fileName}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400" id="${fileId}-status">
                            ${status === 'loading' ? 'Caricamento...' : status === 'success' ? 'Caricato con successo' : 'Errore nel caricamento'}
                        </div>
                    </div>
                </div>
                <div class="flex space-x-2">
                    ${actionButton}
                </div>
            `;
            
            fileList.appendChild(fileItem);
            updateFileCount();
        }
        
        function updateFileStatus(fileId, status, fileName, generatedFileName) {
            const fileItem = document.getElementById(fileId);
            if (!fileItem) return;
            
            // Aggiorna completamente il file item
            fileItem.remove();
            addFileToList(fileName, fileId, status, generatedFileName);
        }
        
        // Funzioni globali per onclick inline
        window.showExcelContent = function(fileName, fileId) {
            // Memorizza l'ID del file corrente per il processing
            currentProcessedFileId = fileId;
            // Memorizza anche il nome del file per il salvataggio
            window.currentFileName = fileName;
            
            // Mostra modal con stato di loading
            WebgreModals.openModal('excel-modal');
            document.getElementById('modello-info').textContent = 'Caricamento...';
            document.getElementById('taglio-table').innerHTML = '<div class="text-center py-4">Caricamento...</div>';
            document.getElementById('orlatura-table').innerHTML = '<div class="text-center py-4">Caricamento...</div>';
            
            // Chiama API per processare il file Excel
            fetch(window.WEBGRE.baseUrl + '/export/api/processExcel?fileName=' + encodeURIComponent(fileName), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aggiorna le informazioni del modello
                    document.getElementById('modello-info').textContent = data.modello || fileName;
                    document.getElementById('lancio-input').value = '';
                    document.getElementById('qty-input').value = 1;
                    
                    // Memorizza gli headers globalmente
                    window.excelHeaders = data.headers || ['Colonna 1', 'Colonna 2', 'Colonna 3', 'Colonna 4', 'Colonna 5'];
                    
                    // Crea tabelle per TAGLIO e ORLATURA
                    createExcelTable('taglio-table', window.excelHeaders, data.rows.taglio || []);
                    createExcelTable('orlatura-table', window.excelHeaders, data.rows.orlatura || []);
                } else {
                    if (window.showAlert) {
                        window.showAlert(data.error || 'Errore nel caricamento del contenuto Excel', 'error');
                    }
                    // Chiudi modal in caso di errore
                    WebgreModals.closeModal('excel-modal');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (window.showAlert) {
                    window.showAlert('Errore di rete nel caricamento del contenuto', 'error');
                }
                WebgreModals.closeModal('excel-modal');
            });
        };
        
        window.removeFile = function(fileId) {
            const fileItem = document.getElementById(fileId);
            if (fileItem) {
                fileItem.remove();
                updateFileCount();
            }
        };
        
        window.closeModal = function(modalId) {
            WebgreModals.closeModal(modalId);
        };
        
        window.saveExcelData = function() {
            const lancio = document.getElementById('lancio-input').value;
            const modello = document.getElementById('modello-info').textContent;
            const qty = parseFloat(document.getElementById('qty-input').value) || 1;
            
            if (!lancio.trim()) {
                if (window.showAlert) {
                    window.showAlert('Inserisci un valore per il campo Lancio', 'warning');
                } else if (window.WebgreNotifications) {
                    window.WebgreNotifications.warning('Inserisci un valore per il campo Lancio');
                }
                return;
            }
            
            // Raccogli i dati dalle tabelle
            const tableTaglio = collectTableData('taglio-table');
            const tableOrlatura = collectTableData('orlatura-table');
            
            const progressivo = <?= json_encode($progressivo) ?>;
            
            const data = {
                id_documento: progressivo,
                modello: modello,
                lancio: lancio,
                qty: qty,
                tableTaglio: tableTaglio,
                tableOrlatura: tableOrlatura,
                originalFileName: window.currentFileName // Aggiungi nome file originale
            };
            
            // Invia dati al server
            fetch(window.WEBGRE.baseUrl + '/export/api/saveExcel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (window.showAlert) {
                        window.showAlert('Scheda salvata nella cartella temporanea! Carica la scheda successiva o vai al prossimo Step.', 'success');
                    } else if (window.WebgreNotifications) {
                        window.WebgreNotifications.success('Scheda salvata! Carica la scheda successiva o vai al prossimo Step.');
                    }
                    
                    // Segna il file come processato nella lista
                    markFileAsProcessed();
                    
                    window.closeModal('excel-modal');
                } else {
                    if (window.showAlert) {
                        window.showAlert(data.error || 'Errore nel salvataggio del file Excel', 'error');
                    } else if (window.WebgreNotifications) {
                        window.WebgreNotifications.error(data.error || 'Errore nel salvataggio del file Excel');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (window.showAlert) {
                    window.showAlert('Errore di rete nel salvataggio', 'error');
                } else if (window.WebgreNotifications) {
                    window.WebgreNotifications.error('Errore di rete nel salvataggio');
                }
            });
        };
        
        window.navigateToStep3 = function() {
            const progressivo = <?= json_encode($progressivo) ?>;
            const url = window.WEBGRE.baseUrl + '/export/preview/' + progressivo;
            
            if (window.pjax) {
                window.pjax.navigateTo(url);
            } else {
                window.location.href = url;
            }
        };
        
        window.updateTotals = function() {
            // Aggiorna i totali di tutte le tabelle
            const qty = parseFloat(document.getElementById('qty-input').value) || 1;
            
            // Aggiorna tabella TAGLIO
            updateTableTotals('taglio-table', qty);
            
            // Aggiorna tabella ORLATURA  
            updateTableTotals('orlatura-table', qty);
        };
        
        // Funzione per aggiornare i totali di una specifica tabella
        function updateTableTotals(tableId, qty) {
            const table = document.querySelector(`[data-table-id="${tableId}"] tbody`);
            if (!table) return;
            
            const rows = table.querySelectorAll('tr');
            rows.forEach((row, index) => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 7) { // Azioni + 5 dati + Totale
                    // La quinta colonna dati (index 5 perché iniziamo da 0 e la prima è azioni)
                    const unitValueCell = cells[5];
                    const unitValue = parseFloat(unitValueCell.textContent) || 0;
                    const total = (qty * unitValue).toFixed(2);
                    
                    // Aggiorna la colonna totale (ultima colonna)
                    const totalCell = cells[cells.length - 1];
                    totalCell.textContent = total;
                }
            });
        }
        
        // Funzione per eliminare una riga
        window.deleteExcelRow = function(containerId, rowIndex) {
            // Rimuovi la riga dai dati memorizzati
            if (window.excelData && window.excelData[containerId]) {
                window.excelData[containerId].splice(rowIndex, 1);
                
                // Ricostruisci la tabella con i dati aggiornati
                const headers = window.excelHeaders || ['Colonna 1', 'Colonna 2', 'Colonna 3', 'Colonna 4', 'Colonna 5'];
                createExcelTable(containerId, headers, window.excelData[containerId]);
            }
        };
        
        // Funzione per raccogliere dati dalle tabelle (come nel sistema legacy)
        function collectTableData(tableId) {
            const table = document.querySelector(`[data-table-id="${tableId}"] tbody`);
            if (!table) return [];
            
            const rows = table.querySelectorAll('tr');
            const data = [];
            const qty = parseFloat(document.getElementById('qty-input').value) || 1;
            
            rows.forEach((row) => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 6) { // Azioni + 5 colonne dati + totale
                    const rowData = [];
                    let totale = 0;
                    
                    // Salta la prima colonna (azioni) e raccogli le prime 5 colonne dati
                    for (let i = 1; i <= 5; i++) {
                        const cellValue = cells[i].textContent.trim();
                        rowData.push(cellValue);
                        
                        // La colonna 5 (indice 5) è la colonna "Cons/Pa" per il calcolo totale
                        if (i === 5) {
                            totale = parseFloat(cellValue) * qty;
                        }
                    }
                    
                    // Aggiungi totale alla fine della riga (come nel sistema legacy)
                    rowData.push(totale.toFixed(2));
                    data.push(rowData);
                }
            });
            
            return data;
        }
        
        // Funzione per segnare file come processato
        let currentProcessedFileId = null;
        
        function markFileAsProcessed() {
            if (currentProcessedFileId) {
                const fileItem = document.getElementById(currentProcessedFileId);
                if (fileItem) {
                    // Trova il div dei pulsanti e aggiungi il badge elaborato
                    const actionDiv = fileItem.querySelector('div:last-child');
                    if (actionDiv) {
                        // Mantieni il pulsante "Visualizza" esistente e aggiungi badge "Elaborato"
                        const visualizzaButton = actionDiv.querySelector('button');
                        if (visualizzaButton) {
                            const elaboratoBadge = `
                                <span class="inline-flex items-center px-3 py-1.5 border border-green-300 rounded-lg text-xs font-medium text-green-700 bg-green-50 ml-2">
                                    <i class="fas fa-check mr-1"></i>
                                    Elaborato
                                </span>
                            `;
                            // Aggiungi il badge dopo il pulsante esistente
                            actionDiv.insertAdjacentHTML('beforeend', elaboratoBadge);
                        }
                    }
                }
                currentProcessedFileId = null;
            }
        }
        
        // Funzione per creare tabelle Excel
        function createExcelTable(containerId, headers, rows) {
            const container = document.getElementById(containerId);
            
            if (!rows || rows.length === 0) {
                container.innerHTML = '<div class="text-center py-4 text-gray-500">Nessun dato disponibile</div>';
                return;
            }
            
            let tableHTML = `
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" data-table-id="${containerId}">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400" style="width: 50px;">
                                Azioni
                            </th>
            `;
            
            // Aggiungi header (solo i primi 5)
            headers.slice(0, 5).forEach((header, index) => {
                tableHTML += `
                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        ${header || 'Colonna ' + (index + 1)}
                    </th>
                `;
            });
            
            // Aggiungi colonna Totale
            tableHTML += `
                            <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Totale
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
            `;
            
            // Aggiungi righe
            rows.forEach((row, rowIndex) => {
                const qty = parseFloat(document.getElementById('qty-input').value) || 1;
                const unitValue = parseFloat(row[4]) || 0;
                const total = (qty * unitValue).toFixed(2);
                
                tableHTML += `<tr class="${rowIndex % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700'}" data-row-index="${rowIndex}">`;
                
                // Colonna azioni con pulsante elimina
                tableHTML += `
                    <td class="px-2 py-2 text-center">
                        <button onclick="deleteExcelRow('${containerId}', ${rowIndex})" 
                                class="inline-flex items-center px-2 py-1 border border-red-300 rounded text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 dark:border-red-600 dark:bg-red-800/20 dark:text-red-400 dark:hover:bg-red-700/20">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </td>
                `;
                
                // Celle dati (solo le prime 5)
                row.slice(0, 5).forEach((cell, cellIndex) => {
                    tableHTML += `
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            ${cell || '-'}
                        </td>
                    `;
                });
                
                // Colonna totale
                tableHTML += `
                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-right">
                        ${total}
                    </td>
                `;
                
                tableHTML += '</tr>';
            });
            
            tableHTML += `
                    </tbody>
                </table>
            `;
            
            container.innerHTML = tableHTML;
            
            // Memorizza i dati delle righe per la gestione dell'eliminazione
            window.excelData = window.excelData || {};
            window.excelData[containerId] = rows;
        }
        
        // Registra event listeners
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            function handler(e) { preventDefaults(e); }
            dropZone.addEventListener(eventName, handler);
            eventListeners.push({ element: dropZone, event: eventName, handler });
        });
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight);
            eventListeners.push({ element: dropZone, event: eventName, handler: highlight });
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight);
            eventListeners.push({ element: dropZone, event: eventName, handler: unhighlight });
        });
        
        dropZone.addEventListener('drop', handleDrop);
        eventListeners.push({ element: dropZone, event: 'drop', handler: handleDrop });
        
        function clickHandler() {
            fileInput.click();
        }
        dropZone.addEventListener('click', clickHandler);
        eventListeners.push({ element: dropZone, event: 'click', handler: clickHandler });
        
        fileInput.addEventListener('change', handleFileSelect);
        eventListeners.push({ element: fileInput, event: 'change', handler: handleFileSelect });
        
        // Click fuori dal modal per chiudere
        const modals = document.querySelectorAll('[id$="-modal"]');
        modals.forEach(modal => {
            function clickHandler(e) {
                if (e.target === modal) {
                    WebgreModals.closeModal(modal.id);
                }
            }
            modal.addEventListener('click', clickHandler);
            eventListeners.push({ element: modal, event: 'click', handler: clickHandler });
        });
        
        // Inizializza conteggio
        updateFileCount();
    }
    
    function cleanupEventListeners() {
        eventListeners.forEach(({ element, event, handler }) => {
            element.removeEventListener(event, handler);
        });
        eventListeners = [];
    }
    
    // Registrazione PJAX
    if (window.WEBGRE && window.WEBGRE.onPageLoad) {
        window.WEBGRE.onPageLoad(initExportUpload);
    }
    
    // Fallback primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initExportUpload);
    } else {
        initExportUpload();
    }
})();
</script>