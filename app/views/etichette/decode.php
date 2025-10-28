<!-- Lista Prelievo/Versamento Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg mr-4">
                    <i class="fas fa-list-ul text-xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Crea Lista Prelievo/Versamento
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Genera PDF da scansione barcode per movimentazione magazzino
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?= $this->url('/etichette') ?>"
                class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alle Etichette
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
                <a href="<?= $this->url('/etichette') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">Etichette</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Crea Lista</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Info Cards -->
<div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <!-- Info Prelievo -->
    <div class="card bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800">
        <div class="card-body">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500 shadow-lg">
                    <i class="fas fa-arrow-down text-white"></i>
                </div>
                <div class="ml-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Prelievo</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Materiali in uscita dal magazzino</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Versamento -->
    <div class="card bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800">
        <div class="card-body">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-500 shadow-lg">
                    <i class="fas fa-arrow-up text-white"></i>
                </div>
                <div class="ml-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Versamento</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Materiali in entrata al magazzino</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Suggerimenti -->
    <div class="card bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800">
        <div class="card-body">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-500 shadow-lg">
                    <i class="fas fa-lightbulb text-white"></i>
                </div>
                <div class="ml-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Tip</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Usa uno scanner per velocità</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Principale -->
<div class="card">
    <div class="card-header">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-barcode mr-3 text-green-500"></i>
                    Generazione Lista
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Inserisci i barcode degli articoli per generare la lista PDF
                </p>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form action="<?= $this->url('/etichette/generate-list') ?>" method="POST" id="barcode-form">
            <div class="space-y-6">
                <!-- Tipo Operazione -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Tipo di Operazione *
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="radio-card cursor-pointer">
                            <input type="radio" name="azione" value="PRELIEVO" class="sr-only" checked>
                            <div class="flex flex-col items-center p-4 border-2 border-gray-300 rounded-lg transition-all">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/20 mb-3">
                                    <i class="fas fa-arrow-down text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">PRELIEVO</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">Materiali in uscita</span>
                            </div>
                        </label>
                        <label class="radio-card cursor-pointer">
                            <input type="radio" name="azione" value="VERSAMENTO" class="sr-only">
                            <div class="flex flex-col items-center p-4 border-2 border-gray-300 rounded-lg transition-all">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/20 mb-3">
                                    <i class="fas fa-arrow-up text-green-600 dark:text-green-400 text-xl"></i>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">VERSAMENTO</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">Materiali in entrata</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Area Barcode -->
                <div>
                    <label for="barcodes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Barcode degli Articoli *
                    </label>
                    <div class="relative">
                        <textarea 
                            id="barcodes" 
                            name="barcodes" 
                            rows="12" 
                            required
                            placeholder="Inserisci o scansiona i barcode qui, uno per riga..."
                            class="form-input w-full font-mono resize-none"></textarea>
                        <div class="absolute top-2 right-2">
                            <button type="button" onclick="clearBarcodes()" 
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                    title="Pulisci area">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            I prefissi "MGM" verranno rimossi automaticamente
                        </p>
                        <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                            <span>Righe: <span id="line-count">0</span></span>
                            <span>Unique: <span id="unique-count">0</span></span>
                        </div>
                    </div>
                </div>

                <!-- Strumenti Rapidi -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-tools mr-2 text-gray-500"></i>
                        Strumenti Rapidi
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button type="button" onclick="focusTextarea()" class="btn btn-secondary text-xs">
                            <i class="fas fa-crosshairs mr-1"></i>
                            Focus
                        </button>
                        <button type="button" onclick="addTestBarcode()" class="btn btn-secondary text-xs">
                            <i class="fas fa-vial mr-1"></i>
                            Test
                        </button>
                        <button type="button" onclick="removeDuplicates()" class="btn btn-secondary text-xs">
                            <i class="fas fa-compress mr-1"></i>
                            Dedup
                        </button>
                        <button type="button" onclick="sortBarcodes()" class="btn btn-secondary text-xs">
                            <i class="fas fa-sort mr-1"></i>
                            Ordina
                        </button>
                    </div>
                </div>

                <!-- Pulsanti Azione -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="<?= $this->url('/etichette') ?>" class="btn btn-secondary">
                        <i class="fas fa-times mr-2"></i>
                        Annulla
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Genera PDF
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Sistema Lista Barcode - JavaScript compatibile con PJAX
    (function() {
        let eventListeners = [];

        function initBarcodeList() {
            cleanupEventListeners();
            setupEventListeners();
            updateCounters();
        }

        function cleanupEventListeners() {
            eventListeners.forEach(({ element, event, handler }) => {
                element.removeEventListener(event, handler);
            });
            eventListeners = [];
        }

        function setupEventListeners() {
            // Gestione radio buttons
            const radioButtons = document.querySelectorAll('input[name="azione"]');
            radioButtons.forEach(radio => {
                function changeHandler() {
                    radioButtons.forEach(r => {
                        const card = r.closest('.radio-card');
                        const cardDiv = card.querySelector('div');
                        if (r.checked) {
                            if (r.value === 'PRELIEVO') {
                                cardDiv.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10');
                                cardDiv.classList.remove('border-gray-300');
                            } else {
                                cardDiv.classList.add('border-green-500', 'bg-green-50', 'dark:bg-green-900/10');
                                cardDiv.classList.remove('border-gray-300');
                            }
                        } else {
                            cardDiv.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10', 'border-green-500', 'bg-green-50', 'dark:bg-green-900/10');
                            cardDiv.classList.add('border-gray-300');
                        }
                    });
                }
                radio.addEventListener('change', changeHandler);
                eventListeners.push({ element: radio, event: 'change', handler: changeHandler });
                
                // Inizializza stato
                changeHandler.call(radio);
            });

            // Contatori barcode
            const textarea = document.getElementById('barcodes');
            if (textarea) {
                function inputHandler() {
                    updateCounters();
                }
                textarea.addEventListener('input', inputHandler);
                eventListeners.push({ element: textarea, event: 'input', handler: inputHandler });
            }

            // Submit form validation
            const form = document.getElementById('barcode-form');
            if (form) {
                function submitHandler(e) {
                    const textarea = document.getElementById('barcodes');
                    if (!textarea || !textarea.value.trim()) {
                        e.preventDefault();
                        if (window.showAlert) {
                            window.showAlert('Inserire almeno un barcode', 'error');
                        }
                        return false;
                    }
                }
                form.addEventListener('submit', submitHandler);
                eventListeners.push({ element: form, event: 'submit', handler: submitHandler });
            }
        }

        function updateCounters() {
            const textarea = document.getElementById('barcodes');
            const lineCountEl = document.getElementById('line-count');
            const uniqueCountEl = document.getElementById('unique-count');

            if (!textarea || !lineCountEl || !uniqueCountEl) return;

            const text = textarea.value.trim();
            if (!text) {
                lineCountEl.textContent = '0';
                uniqueCountEl.textContent = '0';
                return;
            }

            const lines = text.split('\n').filter(line => line.trim().length > 0);
            const uniqueLines = [...new Set(lines.map(line => line.trim()))];

            lineCountEl.textContent = lines.length;
            uniqueCountEl.textContent = uniqueLines.length;
        }

        // Funzioni globali
        window.clearBarcodes = function() {
            const textarea = document.getElementById('barcodes');
            if (textarea) {
                textarea.value = '';
                textarea.focus();
                updateCounters();
            }
        };

        window.focusTextarea = function() {
            const textarea = document.getElementById('barcodes');
            if (textarea) {
                textarea.focus();
                textarea.setSelectionRange(textarea.value.length, textarea.value.length);
            }
        };

        window.addTestBarcode = function() {
            const textarea = document.getElementById('barcodes');
            if (!textarea) return;

            const testBarcodes = [
                '1234567890123',
                'MGM9876543210',
                '5555555555555',
                'mgm1111111111',
                '9999999999999'
            ];

            const currentValue = textarea.value.trim();
            const newValue = currentValue + (currentValue ? '\n' : '') + testBarcodes.join('\n');
            
            textarea.value = newValue;
            updateCounters();
            
            if (window.showAlert) {
                window.showAlert('Barcode di test aggiunti', 'info');
            }
        };

        window.removeDuplicates = function() {
            const textarea = document.getElementById('barcodes');
            if (!textarea) return;

            const text = textarea.value.trim();
            if (!text) return;

            const lines = text.split('\n').map(line => line.trim()).filter(line => line.length > 0);
            const uniqueLines = [...new Set(lines)];

            textarea.value = uniqueLines.join('\n');
            updateCounters();
            
            const removed = lines.length - uniqueLines.length;
            if (removed > 0 && window.showAlert) {
                window.showAlert(`Rimossi ${removed} duplicati`, 'success');
            } else if (removed === 0 && window.showAlert) {
                window.showAlert('Nessun duplicato trovato', 'info');
            }
        };

        window.sortBarcodes = function() {
            const textarea = document.getElementById('barcodes');
            if (!textarea) return;

            const text = textarea.value.trim();
            if (!text) return;

            const lines = text.split('\n').map(line => line.trim()).filter(line => line.length > 0);
            const sortedLines = lines.sort();

            textarea.value = sortedLines.join('\n');
            updateCounters();
            
            if (window.showAlert) {
                window.showAlert('Barcode ordinati alfabeticamente', 'success');
            }
        };

        // Registra inizializzatore PJAX
        if (window.COREGRE && window.COREGRE.onPageLoad) {
            window.COREGRE.onPageLoad(initBarcodeList);
        }

        // Inizializzazione
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initBarcodeList);
        } else {
            initBarcodeList();
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', cleanupEventListeners);

    })();
</script>