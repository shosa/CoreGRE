<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-boxes mr-3 text-purple-500"></i>
                Packing List
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Genera report PDF ed Excel per cartellini e lotti di produzione
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
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Packing List</span>
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
                        Dati da Processare
                    </label>
                    <textarea 
                        id="cartellini-input" 
                        class="w-full h-48 rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 resize-none"
                        placeholder="Inserisci cartellini, uno per riga"
                        rows="10"
                    ></textarea>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        Un elemento per riga. Righe vuote saranno ignorate.
                    </p>
                </div>

                <!-- Report Type -->
                <div>
                    <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-search mr-2 text-purple-500"></i>
                        Tipo di Ricerca
                    </legend>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center gap-2 p-3 border border-gray-300 rounded-lg cursor-pointer hover:border-purple-500 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700 transition-all radio-option">
                            <input type="radio" name="reportType" value="perCartellino" checked class="text-purple-600 focus:ring-purple-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Per Cartellino</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 border border-gray-300 rounded-lg cursor-pointer hover:border-purple-500 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700 transition-all radio-option">
                            <input type="radio" name="reportType" value="perLotto" class="text-purple-600 focus:ring-purple-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Per Lotto</span>
                        </label>
                    </div>
                </div>

                <!-- Format Type -->
                <div>
                    <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-file mr-2 text-purple-500"></i>
                        Formato di Output
                    </legend>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center gap-3 p-3 border border-gray-300 rounded-lg cursor-pointer hover:border-purple-500 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700 transition-all radio-option">
                            <input type="radio" name="formatType" value="pdf" checked class="text-purple-600 focus:ring-purple-500">
                            <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">PDF</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-gray-300 rounded-lg cursor-pointer hover:border-purple-500 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700 transition-all radio-option">
                            <input type="radio" name="formatType" value="xlsx" class="text-purple-600 focus:ring-purple-500">
                            <i class="fas fa-file-excel text-green-500 text-xl"></i>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Excel</span>
                        </label>
                    </div>
                </div>

                <!-- Generate Button -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        id="generateReportBtn"
                        type="button" 
                        class="w-full inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4 text-sm font-semibold text-white shadow-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 transform hover:scale-105"
                    >
                        <i class="fas fa-chart-line mr-3"></i>
                        Genera Report
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
                    <i class="fas fa-file-alt mr-3 text-purple-500"></i>
                    Anteprima Report
                </h3>
            </div>
            <div class="p-6">
                <div id="report-container" class="min-h-96">
                    <!-- Initial State -->
                    <div class="h-96 flex items-center justify-center bg-gray-50 dark:bg-gray-700/50 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                        <div class="text-center">
                            <i class="fas fa-file-pdf text-6xl text-gray-400 mb-4"></i>
                            <h4 class="text-xl font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nessun Report Generato
                            </h4>
                            <p class="text-gray-500 dark:text-gray-400 max-w-sm">
                                Inserisci i dati e clicca "Genera Report" per visualizzare l'anteprima qui
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .radio-option {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .radio-option:hover {
        border-color: #9333ea;
    }
    
    .radio-option.active {
        border-color: #9333ea;
        background-color: #f3e8ff;
    }
</style>

<script>
(function() {
    let eventListeners = [];
    let isGenerating = false; // Flag per prevenire doppi click
    let isInitialized = false; // Flag per prevenire doppia inizializzazione

    function initPackingList() {
        // Previeni doppia inizializzazione
        if (isInitialized) {
            console.log("PackingList già inizializzato, evito duplicazione");
            return;
        }

        // Cleanup precedenti listeners
        cleanupEventListeners();

        // Handle radio button changes with visual feedback
        const radioButtons = document.querySelectorAll("input[name='reportType']");
        const textarea = document.getElementById("cartellini-input");

        if (!radioButtons.length || !textarea) {
            console.log("Elementi non trovati, riprovando...");
            return;
        }

        // Marca come inizializzato
        isInitialized = true;

        // Initialize placeholder
        updatePlaceholder();

        radioButtons.forEach(radio => {
            function changeHandler() {
                updatePlaceholder();
                updateRadioStyles();
            }
            radio.addEventListener("change", changeHandler);
            eventListeners.push({ element: radio, event: "change", handler: changeHandler });
        });

    // Update placeholder text
    function updatePlaceholder() {
        const selectedType = document.querySelector("input[name='reportType']:checked").value;
        if (selectedType === "perCartellino") {
            textarea.placeholder = "Inserisci cartellini, uno per riga\nEsempio:\n12345\n67890\n11111";
        } else if (selectedType === "perLotto") {
            textarea.placeholder = "Inserisci lotti, uno per riga\nEsempio:\nLOT001\nLOT002\nLOT003";
        }
    }

    // Update radio button visual styles
    function updateRadioStyles() {
        document.querySelectorAll(".radio-option").forEach(option => {
            const radio = option.querySelector("input[type='radio']");
            if (radio.checked) {
                option.classList.add("active");
            } else {
                option.classList.remove("active");
            }
        });
    }

    // Initialize radio styles
    updateRadioStyles();

        // Handle format type changes
        document.querySelectorAll("input[name='formatType']").forEach(radio => {
            function formatChangeHandler() {
                updateRadioStyles();
            }
            radio.addEventListener("change", formatChangeHandler);
            eventListeners.push({ element: radio, event: "change", handler: formatChangeHandler });
        });

        // Generate report functionality
        const generateBtn = document.getElementById("generateReportBtn");
        if (generateBtn) {
            async function generateReportHandler(event) {
                // Previeni comportamento di default e propagazione
                event.preventDefault();
                event.stopPropagation();

                // Previeni doppi click
                if (isGenerating) {
                    console.log("Generazione già in corso, ignoro il click");
                    return;
                }

                console.log("Generate report button clicked"); // Debug
                isGenerating = true;

                const button = generateBtn;
                const originalText = button.innerHTML;

        const inputText = textarea.value.trim();
        console.log("Input text:", inputText); // Debug

        if (!inputText) {
            alert("Inserisci almeno un cartellino o lotto");
            isGenerating = false; // Reset flag
            return;
        }

        const lines = inputText.split("\n").filter(Boolean);
        console.log("Lines:", lines); // Debug

        const reportType = document.querySelector("input[name='reportType']:checked")?.value;
        const formatType = document.querySelector("input[name='formatType']:checked")?.value;

        console.log("Report type:", reportType, "Format type:", formatType); // Debug

        if (!reportType || !formatType) {
            alert("Seleziona il tipo di report e il formato");
            isGenerating = false; // Reset flag
            return;
        }

        // Disable button and show loading
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generando...';

        try {
            const dataToSend = {};
            if (reportType === "perCartellino") {
                dataToSend.cartellini = lines;
            } else {
                dataToSend.lotti = lines;
            }

            console.log("Data to send:", dataToSend); // Debug

            let endpoint = "";
            if (formatType === "pdf") {
                endpoint = reportType === "perCartellino" ? "<?= $this->url('/tracking/report-cartel-pdf') ?>" : "<?= $this->url('/tracking/report-lot-pdf') ?>";
            } else {
                endpoint = reportType === "perCartellino" ? "<?= $this->url('/tracking/report-cartel-excel') ?>" : "<?= $this->url('/tracking/report-lot-excel') ?>";
            }

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
                throw new Error(`Errore nella generazione del report: ${response.status}`);
            }

            if (formatType === "pdf") {
                const blob = await response.blob();
                console.log("PDF blob size:", blob.size); // Debug

                const url = URL.createObjectURL(blob);
                const reportContainer = document.getElementById("report-container");
                reportContainer.innerHTML = `
                    <div class="text-center mb-4">
                        <h5 class="text-gray-900 dark:text-white">Report generato con successo</h5>
                        <p class="text-gray-600 dark:text-gray-400">Visualizza il PDF qui sotto o scaricalo</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
                        <iframe src="${url}" width="100%" height="600px" class="rounded border"></iframe>
                    </div>
                `;

                // Mostra notifica di successo
                addAlert("Report PDF generato con successo!", "success");
            } else {
                const blob = await response.blob();
                console.log("Excel blob size:", blob.size); // Debug

                const url = URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.style.display = "none";
                a.href = url;
                a.download = `report_${reportType}_${new Date().toISOString().split("T")[0]}.xlsx`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                // Aggiorna il container per mostrare il successo del download
                const reportContainer = document.getElementById("report-container");
                reportContainer.innerHTML = `
                    <div class="text-center">
                        <div class="text-6xl text-green-500 mb-4">
                            <i class="fas fa-download"></i>
                        </div>
                        <h5 class="text-xl font-medium text-gray-900 dark:text-white mb-2">Download Completato</h5>
                        <p class="text-gray-600 dark:text-gray-400">Il file Excel è stato scaricato con successo</p>
                    </div>
                `;

                // Mostra notifica di successo
                addAlert("Report Excel scaricato con successo!", "success");
            }

        } catch (error) {
            console.error("Errore nella generazione del report:", error);
            addAlert("Errore durante la generazione del report: " + error.message, "error");
        } finally {
                // Re-enable button e reset flag
                button.disabled = false;
                button.innerHTML = originalText;
                isGenerating = false;
                console.log("Generazione completata, pulsante ripristinato");
            }
            }

            // Aggiungi event listener con opzioni per prevenire duplicati
            generateBtn.addEventListener("click", generateReportHandler, { once: false });
            eventListeners.push({ element: generateBtn, event: "click", handler: generateReportHandler });
        }

    }

    // Funzione di cleanup degli event listeners
    function cleanupEventListeners() {
        eventListeners.forEach(({ element, event, handler }) => {
            if (element && element.removeEventListener) {
                element.removeEventListener(event, handler);
            }
        });
        eventListeners = [];
        isInitialized = false; // Reset flag di inizializzazione
    }

    // Utility function for alerts
    function addAlert(message, type = 'info') {
        if (window.showAlert) {
            window.showAlert(message, type);
        } else if (window.CoregreNotifications) {
            if (type === 'success') {
                window.CoregreNotifications.success(message, 3000);
            } else if (type === 'error') {
                window.CoregreNotifications.error(message, 5000);
            } else {
                window.CoregreNotifications.info(message, 3000);
            }
        } else {
            // Fallback to alert
            alert(message);
        }
    }

    // Registrazione PJAX
    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initPackingList);
    } else {
        // Fallback primo caricamento se PJAX non disponibile
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPackingList);
        } else {
            initPackingList();
        }
    }
})();
</script>