<!-- Header con titolo -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <span class="bg-gradient-to-r from-blue-500 to-blue-600 bg-clip-text text-transparent mr-3">STEP 3</span>
                <i class="fas fa-arrow-right text-gray-400 mx-2"></i>
                Elenco Articoli DDT n° <?= $progressivo ?>
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Anteprima delle schede caricate e generazione finale del documento
            </p>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="flex mt-4" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="<?= $this->url('/') ?>" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/export') ?>" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Registro DDT
                </a>
            </li>
            <li class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/export/upload/' . $progressivo) ?>" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Step 2
                </a>
            </li>
            <li class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-900 font-medium dark:text-white">Step 3</span>
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
        
        <!-- Step 2 - Completato -->
        <div class="flex items-center text-green-600">
            <div class="flex-shrink-0 w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-white text-sm"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-600">Caricamento</p>
                <p class="text-xs text-green-500">Completato</p>
            </div>
        </div>
        
        <div class="flex-1 h-0.5 bg-green-300 mx-4"></div>
        
        <!-- Step 3 - Attivo -->
        <div class="flex items-center text-blue-600">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-bold">3</span>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-blue-600">Generazione</p>
                <p class="text-xs text-blue-500">Anteprima e DDT</p>
            </div>
        </div>
    </div>
</div>

<?php if (empty($tempFiles)): ?>
    <!-- Nessun file caricato -->
    <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="p-6">
            <div class="rounded-lg bg-blue-50 p-4 mb-6 border border-blue-200 dark:bg-blue-800/20 dark:border-blue-700">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            Nessun file Excel caricato
                        </h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <p>Nessun file Excel caricato. Torna allo <a href="<?= $this->url('/export/upload/' . $progressivo) ?>" class="underline hover:no-underline">step precedente</a> per caricare dei file.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                    <i class="fas fa-file-excel text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    Nessuna scheda tecnica
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    Puoi comunque generare un DDT vuoto o tornare indietro per caricare file
                </p>
                
                <div class="flex justify-center space-x-3">
                    <a href="<?= $this->url('/export/upload/' . $progressivo) ?>" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Torna Indietro
                    </a>
                    <button onclick="generaDDT()" 
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700">
                        <i class="fas fa-file-import mr-2"></i>
                        Genera DDT vuoto
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- File caricati presenti -->
    <div class="rounded-lg bg-blue-50 p-4 mb-6 border border-blue-200 dark:bg-blue-800/20 dark:border-blue-700">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                    Schede pronte per l'elaborazione
                </h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <p>Le schede caricate sono pronte per essere elaborate. Clicca su "Visualizza" per vedere i dettagli di ciascun file.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista schede tecniche -->
    <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-file-excel mr-3 text-green-500"></i>
                Schede Tecniche Caricate
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300">
                    <?= count($tempFiles) ?> file
                </span>
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <?php foreach ($tempFiles as $file): ?>
                <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-excel text-green-500 text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                <?= htmlspecialchars($file['name']) ?>
                            </h4>
                            <div class="flex items-center space-x-4 mt-1">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/20 dark:text-blue-300">
                                    <i class="fas fa-tag mr-1"></i>
                                    Lancio: <?= htmlspecialchars($file['lancio'] ?? 'N/A') ?>
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-800/20 dark:text-orange-300">
                                    <i class="fas fa-cubes mr-1"></i>
                                    Paia: <?= htmlspecialchars($file['paia'] ?? 'N/A') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button onclick="showExcelContent('<?= htmlspecialchars($file['name']) ?>')" 
                                class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 dark:border-blue-600 dark:bg-blue-800/20 dark:text-blue-400">
                            <i class="fas fa-eye mr-1"></i>
                            Visualizza
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Pulsante genera DDT -->
    <div class="text-center">
        <button onclick="generaDDT()" 
                class="inline-flex items-center px-8 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 shadow-lg hover:shadow-xl transition-all duration-200">
            <i class="fas fa-file-import mr-3"></i>
            Genera DDT
        </button>
    </div>
<?php endif; ?>

<!-- Modal per visualizzare il contenuto delle schede -->
<div id="excel-content-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-99999">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-2xl bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-file-excel mr-2 text-green-500"></i>
                Dettagli Scheda Tecnica
            </h3>
            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" onclick="closeModal('excel-content-modal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="mt-6">
            <!-- Informazioni file -->
            <div id="excel-file-info" class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-600 dark:text-gray-400">Modello:</span>
                        <div id="file-modello" class="text-gray-900 dark:text-white font-medium mt-1">N/A</div>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600 dark:text-gray-400">Lancio:</span>
                        <div id="file-lancio" class="text-gray-900 dark:text-white font-medium mt-1">N/A</div>
                    </div>
                    <div>
                        <span class="font-medium text-gray-600 dark:text-gray-400">Paia:</span>
                        <div id="file-paia" class="text-gray-900 dark:text-white font-medium mt-1">N/A</div>
                    </div>
                </div>
            </div>
            
            <!-- Contenuto tabellare -->
            <div id="excel-content-tables" class="space-y-6">
                <!-- Le tabelle verranno inserite qui dinamicamente -->
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">Caricamento contenuto...</p>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700 mt-6">
            <button onclick="closeModal('excel-content-modal')" 
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                Chiudi
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    let eventListeners = [];
    
    function initExportPreview() {
        cleanupEventListeners();
        
        // Funzioni globali per onclick inline
        window.showExcelContent = function(fileName) {
            const modal = document.getElementById('excel-content-modal');
            const content = document.getElementById('excel-content-tables');
            const infoModello = document.getElementById('file-modello');
            const infoLancio = document.getElementById('file-lancio');
            const infoPaia = document.getElementById('file-paia');
            
            // Mostra modal con loading
            CoregreModals.openModal('file-modal');
            content.innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">Caricamento contenuto...</p>
                </div>
            `;
            
            // Chiamata API reale per processare il file Excel dalla directory temp
            const progressivo = <?= json_encode($progressivo) ?>;
            fetch(window.COREGRE.baseUrl + '/export/api/processExcel?fileName=' + encodeURIComponent(fileName) + '&progressivo=' + progressivo, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aggiorna le informazioni del file
                    infoModello.textContent = data.modello || fileName;
                    infoLancio.textContent = data.lancio || 'N/A';
                    infoPaia.textContent = data.qty || 'N/A';
                    
                    // Costruisci HTML per le tabelle
                    let tablesHTML = '';
                    
                    // Tabella TAGLIO
                    if (data.rows.taglio && data.rows.taglio.length > 0) {
                        tablesHTML += `
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <i class="fas fa-cut mr-2 text-red-500"></i>
                                    TAGLIO
                                </h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                                            <tr>
                                                ${(data.headers || ['Codice', 'Descrizione', 'UM', 'Cons/Pa', 'Totale']).map(header => 
                                                    `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">${header}</th>`
                                                ).join('')}
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800/40 divide-y divide-gray-200 dark:divide-gray-700">
                                            ${data.rows.taglio.map(row => `
                                                <tr>
                                                    ${row.slice(0, 5).map(cell => 
                                                        `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${cell || '-'}</td>`
                                                    ).join('')}
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Tabella ORLATURA
                    if (data.rows.orlatura && data.rows.orlatura.length > 0) {
                        tablesHTML += `
                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                    <i class="fas fa-ruler mr-2 text-blue-500"></i>
                                    ORLATURA
                                </h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                                            <tr>
                                                ${(data.headers || ['Codice', 'Descrizione', 'UM', 'Cons/Pa', 'Totale']).map(header => 
                                                    `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">${header}</th>`
                                                ).join('')}
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800/40 divide-y divide-gray-200 dark:divide-gray-700">
                                            ${data.rows.orlatura.map(row => `
                                                <tr>
                                                    ${row.slice(0, 5).map(cell => 
                                                        `<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${cell || '-'}</td>`
                                                    ).join('')}
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    }
                    
                    if (!tablesHTML) {
                        tablesHTML = '<div class="text-center py-8 text-gray-500 dark:text-gray-400">Nessun dato disponibile</div>';
                    }
                    
                    content.innerHTML = tablesHTML;
                } else {
                    content.innerHTML = `
                        <div class="text-center py-8">
                            <div class="text-red-500 text-4xl mb-4">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400">Errore nel caricamento: ${data.error || 'Errore sconosciuto'}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                content.innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-red-500 text-4xl mb-4">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">Errore di rete nel caricamento del contenuto</p>
                    </div>
                `;
            });
        };
        
        window.closeModal = function(modalId) {
            CoregreModals.closeModal(modalId);
        };
        
        window.generaDDT = function() {
            const progressivo = <?= json_encode($progressivo) ?>;
            
            if (window.showAlert) {
                window.showAlert('Generazione DDT in corso...', 'info');
            } else if (window.CoregreNotifications) {
                window.CoregreNotifications.info('Generazione DDT in corso...');
            }
            
            fetch(window.COREGRE.baseUrl + '/export/api/generaDdt', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': window.COREGRE.csrfToken
                },
                body: 'progressivo=' + encodeURIComponent(progressivo)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (window.showAlert) {
                        window.showAlert(data.message || 'DDT generato con successo!', 'success');
                    } else if (window.CoregreNotifications) {
                        window.CoregreNotifications.success(data.message || 'DDT generato con successo!');
                    }
                    
                    // Reindirizza alla gestione del documento
                    setTimeout(() => {
                        const continueUrl = window.COREGRE.baseUrl + '/export/continue/' + progressivo;
                        if (window.pjax) {
                            window.pjax.navigateTo(continueUrl);
                        } else {
                            window.location.href = continueUrl;
                        }
                    }, 1500);
                } else {
                    if (window.showAlert) {
                        window.showAlert(data.message || 'Errore nella generazione del DDT', 'error');
                    } else if (window.CoregreNotifications) {
                        window.CoregreNotifications.error(data.message || 'Errore nella generazione del DDT');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (window.showAlert) {
                    window.showAlert('Errore nella generazione del DDT', 'error');
                } else if (window.CoregreNotifications) {
                    window.CoregreNotifications.error('Errore nella generazione del DDT');
                }
            });
        };
        
        // Click fuori dal modal per chiudere
        const modals = document.querySelectorAll('[id$="-modal"]');
        modals.forEach(modal => {
            function clickHandler(e) {
                if (e.target === modal) {
                    CoregreModals.closeModal(modal.id);
                }
            }
            modal.addEventListener('click', clickHandler);
            eventListeners.push({ element: modal, event: 'click', handler: clickHandler });
        });
    }
    
    function cleanupEventListeners() {
        eventListeners.forEach(({ element, event, handler }) => {
            element.removeEventListener(event, handler);
        });
        eventListeners = [];
    }
    
    // Registrazione PJAX
    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initExportPreview);
    }
    
    // Fallback primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initExportPreview);
    } else {
        initExportPreview();
    }
})();
</script>