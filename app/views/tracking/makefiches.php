<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-file-alt mr-3 text-purple-500"></i>
                Fiches Cartellini
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Genera fiches PDF dettagliate per cartellini con lotti raggruppati per tipo
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
                    <a href="<?= $this->url('/tracking') ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        Tracking
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Fiches Cartellini</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Main Content -->
<div class="grid grid-cols-12 gap-8">
    <!-- Options Panel -->
    <div class="col-span-12 lg:col-span-5 xl:col-span-4">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-cog mr-3 text-purple-500"></i>
                    Opzioni di Generazione
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <!-- Input Textarea -->
                <div>
                    <label for="cartellini-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Cartellini da Processare
                    </label>
                    <textarea 
                        id="cartellini-input" 
                        class="w-full h-48 rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 resize-none"
                        placeholder="Inserisci cartellini, uno per riga&#10;Esempio:&#10;12345&#10;67890&#10;11111"
                        rows="10"
                    ></textarea>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        Un cartellino per riga. Righe vuote saranno ignorate.
                    </p>
                </div>

                <!-- Generate Button -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        id="generateFichesBtn"
                        type="button" 
                        class="w-full inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4 text-sm font-semibold text-white shadow-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 transform hover:scale-105"
                    >
                        <i class="fas fa-file-pdf mr-3"></i>
                        Genera Fiches PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Display Panel -->
    <div class="col-span-12 lg:col-span-7 xl:col-span-8">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-file-pdf mr-3 text-purple-500"></i>
                    Anteprima Fiches
                </h3>
            </div>
            <div class="p-6">
                <div id="fiches-container" class="min-h-96">
                    <!-- Initial State -->
                    <div class="h-96 flex items-center justify-center bg-gray-50 dark:bg-gray-700/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                        <div class="text-center">
                            <i class="fas fa-file-pdf text-6xl text-gray-400 mb-4"></i>
                            <h4 class="text-xl font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nessuna Fiche Generata
                            </h4>
                            <p class="text-gray-500 dark:text-gray-400 max-w-sm">
                                Inserisci i cartellini e clicca "Genera Fiches PDF" per visualizzare l'anteprima qui
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    let eventListeners = [];
    
    function initMakeFiches() {
        // Cleanup precedenti listeners
        cleanupEventListeners();
        
        const textarea = document.getElementById("cartellini-input");
        const generateBtn = document.getElementById("generateFichesBtn");
        
        if (!textarea || !generateBtn) {
            console.log("Elementi non trovati, riprovando...");
            return;
        }
        
        // Generate fiches functionality
        async function generateFichesHandler() {
            console.log("Generate fiches button clicked"); // Debug
            
            const button = generateBtn;
            const originalText = button.innerHTML;
        
        const inputText = textarea.value.trim();
        console.log("Input text:", inputText); // Debug
        
        if (!inputText) {
            alert("Inserisci almeno un cartellino");
            return;
        }
        
        const lines = inputText.split("\n").filter(Boolean);
        console.log("Lines:", lines); // Debug
        
        // Disable button and show loading
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generando...';
        
        try {
            const dataToSend = {
                cartellini: lines
            };
            
            console.log("Data to send:", dataToSend); // Debug
            
            const endpoint = "<?= $this->url('/tracking/report-fiches-pdf') ?>";
            console.log("Endpoint:", endpoint); // Debug
            
            const response = await fetch(endpoint, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(dataToSend)
            });
            
            console.log("Response status:", response.status); // Debug
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error("Response error:", errorText);
                throw new Error(`Errore nella generazione delle fiches: ${response.status}`);
            }
            
            const blob = await response.blob();
            console.log("PDF blob size:", blob.size); // Debug
            
            const url = URL.createObjectURL(blob);
            const fichesContainer = document.getElementById("fiches-container");
            fichesContainer.innerHTML = `
                <div class="text-center mb-4">
                    <h5 class="text-gray-900 dark:text-white">Fiches generate con successo</h5>
                    <p class="text-gray-600 dark:text-gray-400">Visualizza le fiches PDF qui sotto o scaricale</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
                    <iframe src="${url}" width="100%" height="600px" class="rounded border"></iframe>
                </div>
            `;
            
        } catch (error) {
            console.error("Errore nella generazione delle fiches:", error);
            alert("Errore durante la generazione delle fiches: " + error.message);
        } finally {
            // Re-enable button
            button.disabled = false;
            button.innerHTML = originalText;
        }
        }
        
        generateBtn.addEventListener("click", generateFichesHandler);
        eventListeners.push({ element: generateBtn, event: "click", handler: generateFichesHandler });
    }
    
    // Funzione di cleanup degli event listeners
    function cleanupEventListeners() {
        eventListeners.forEach(({ element, event, handler }) => {
            element.removeEventListener(event, handler);
        });
        eventListeners = [];
    }
    
    // Registrazione PJAX
    if (window.WEBGRE && window.WEBGRE.onPageLoad) {
        window.WEBGRE.onPageLoad(initMakeFiches);
    }
    
    // Fallback primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMakeFiches);
    } else {
        initMakeFiches();
    }
})();
</script>