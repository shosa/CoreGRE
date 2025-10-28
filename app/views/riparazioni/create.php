<!-- Breadcrumb -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Nuova Riparazione
        </h1>
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
                        <a href="<?= $this->url('/riparazioni') ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                            Riparazioni
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nuova</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
    
    <a href="<?= $this->url('/riparazioni') ?>" 
       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>
        Torna all'elenco
    </a>
</div>

<form method="POST" action="<?= $this->url('/riparazioni/store') ?>" class="max-w-5xl mx-auto">
    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonna Principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informazioni Articolo -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Informazioni Articolo
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="CODICE" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Codice Articolo *
                            </label>
                            <input type="text" name="CODICE" id="CODICE" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Inserisci codice articolo">
                        </div>
                        
                        <div>
                            <label for="cartellino" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Cartellino *
                            </label>
                            <input type="text" name="cartellino" id="cartellino" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Numero cartellino">
                        </div>
                    </div>
                    
                    <div>
                        <label for="ARTICOLO" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descrizione Articolo *
                        </label>
                        <input type="text" name="ARTICOLO" id="ARTICOLO" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                               placeholder="Descrizione completa dell'articolo">
                    </div>
                </div>
            </div>
            
            <!-- Quantità per Taglia -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Quantità per Taglia
                        </h3>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Numerazione:
                            <select name="numerata" id="numerata" class="ml-2 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white">
                                <option value="">Seleziona numerazione</option>
                                <?php foreach ($numerazioni as $num): ?>
                                    <option value="<?= htmlspecialchars($num->ID) ?>">
                                        <?= htmlspecialchars($num->ID) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div id="size-table" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="text-center text-gray-500 dark:text-gray-400">
                            Seleziona una numerazione per vedere le taglie disponibili
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Totale: <span id="total-quantity" class="text-lg font-bold text-blue-600 dark:text-blue-400">0</span>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Causale -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Causale Riparazione
                    </h3>
                </div>
                <div class="p-6">
                    <textarea name="causale" id="causale" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                              placeholder="Descrivi dettagliatamente la causale della riparazione..."></textarea>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Informazioni Riparazione -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Dettagli Riparazione
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="urgenza" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Urgenza
                        </label>
                        <select name="urgenza" id="urgenza" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="BASSA">Bassa</option>
                            <option value="MEDIA">Media</option>
                            <option value="ALTA">Alta</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="reparto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Reparto
                        </label>
                        <select name="reparto" id="reparto" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Seleziona reparto</option>
                            <?php foreach ($reparti as $reparto): ?>
                                <option value="<?= htmlspecialchars($reparto->Nome) ?>">
                                    <?= htmlspecialchars($reparto->Nome) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="laboratorio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Laboratorio
                        </label>
                        <select name="laboratorio" id="laboratorio" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Seleziona laboratorio</option>
                            <?php foreach ($laboratori as $lab): ?>
                                <option value="<?= htmlspecialchars($lab->Nome) ?>">
                                    <?= htmlspecialchars($lab->Nome) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="data" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Data
                        </label>
                        <input type="date" name="data" id="data" value="<?= date('Y-m-d') ?>"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>
            
            <!-- Informazioni Aggiuntive -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Informazioni Aggiuntive
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label for="utente" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Utente
                        </label>
                        <input type="text" name="utente" id="utente" value="<?= $_SESSION['username'] ?? '' ?>"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="cliente" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Cliente
                        </label>
                        <input type="text" name="cliente" id="cliente"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="commessa" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Commessa
                        </label>
                        <input type="text" name="commessa" id="commessa"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="linea" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Linea
                        </label>
                        <input type="text" name="linea" id="linea"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pulsanti Azione -->
    <div class="mt-6 flex items-center justify-end space-x-3">
        <a href="<?= $this->url('/riparazioni') ?>" 
           class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Annulla
        </a>
        <button type="submit" 
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="fas fa-save mr-2"></i>
            Salva Riparazione
        </button>
    </div>
</form>

<script>
// Riparazioni Create - JavaScript compatibile con PJAX
(function() {
    // Variabili globali per cleanup
    let eventListeners = [];
    
    // Funzione di inizializzazione
    function initRiparazioniCreate() {
        // Cleanup precedenti event listeners
        cleanupEventListeners();
        
        const numerataSelect = document.getElementById('numerata');
        const sizeTable = document.getElementById('size-table');
        const totalQuantitySpan = document.getElementById('total-quantity');
        
        if (!numerataSelect || !sizeTable) return; // Exit se elementi non trovati
        
        // Numerazioni disponibili (dal PHP)
        const numerazioni = <?= json_encode($numerazioni) ?>;
        
        function numerataChangeHandler() {
            const selectedId = this.value;
            if (!selectedId) {
                sizeTable.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400">Seleziona una numerazione per vedere le taglie disponibili</div>';
                return;
            }
            
            const numerazione = numerazioni.find(n => n.ID === selectedId);
            if (!numerazione) return;
            
            // Genera tabella taglie
            let html = '<div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-gray-200 dark:border-gray-600">';
            
            // Header con taglie
            for (let i = 1; i <= 20; i++) {
                const nField = 'N' + String(i).padStart(2, '0');
                if (numerazione[nField] && numerazione[nField].trim() !== '') {
                    html += `<th class="px-2 py-2 text-center font-medium text-gray-700 dark:text-gray-300">${numerazione[nField]}</th>`;
                }
            }
            html += '<th class="px-2 py-2 text-center font-medium text-blue-600 dark:text-blue-400">TOT</th></tr></thead><tbody><tr>';
            
            // Input per quantità
            for (let i = 1; i <= 20; i++) {
                const nField = 'N' + String(i).padStart(2, '0');
                const pField = 'P' + String(i).padStart(2, '0');
                if (numerazione[nField] && numerazione[nField].trim() !== '') {
                    html += `<td class="px-1 py-2 text-center">
                        <input type="number" name="${pField}" min="0" value="0" 
                               class="w-16 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-600 dark:text-white size-input"
                               onchange="updateTotal()">
                    </td>`;
                }
            }
            html += '<td class="px-2 py-2 text-center font-bold text-blue-600 dark:text-blue-400" id="row-total">0</td></tr></tbody></table></div>';
            
            sizeTable.innerHTML = html;
        }
        
        numerataSelect.addEventListener('change', numerataChangeHandler);
        eventListeners.push({ element: numerataSelect, event: 'change', handler: numerataChangeHandler });
    }
    
    function cleanupEventListeners() {
        eventListeners.forEach(({ element, event, handler }) => {
            element.removeEventListener(event, handler);
        });
        eventListeners = [];
    }
    
    // Funzione globale per aggiornare il totale (usata da onchange inline)
    window.updateTotal = function() {
        const sizeInputs = document.querySelectorAll('.size-input');
        const totalQuantitySpan = document.getElementById('total-quantity');
        const rowTotal = document.getElementById('row-total');
        
        let total = 0;
        
        sizeInputs.forEach(input => {
            total += parseInt(input.value) || 0;
        });
        
        if (totalQuantitySpan) {
            totalQuantitySpan.textContent = total;
        }
        if (rowTotal) {
            rowTotal.textContent = total;
        }
    };

    // Registra l'inizializzatore per PJAX
    if (window.WEBGRE && window.WEBGRE.onPageLoad) {
        window.WEBGRE.onPageLoad(initRiparazioniCreate);
    }

    // Inizializza anche al primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRiparazioniCreate);
    } else {
        initRiparazioniCreate();
    }
})();
</script>