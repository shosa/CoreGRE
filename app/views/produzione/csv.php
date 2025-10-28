<?php
/**
 * Report Produzione da CSV
 * Vista moderna per l'upload e analisi di file CSV produzione
 */
?>

<div class="container-xl px-4">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center space-x-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 shadow-lg">
                <i class="fas fa-file-csv text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Report Produzione da CSV
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Genera report di produzione a partire da file CSV
                </p>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="<?= $this->url('/') ?>" class="text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500">
                    <i class="fas fa-home w-4 h-4"></i>
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="<?= $this->url('/produzione') ?>" class="text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500">Produzione</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500 dark:text-gray-400">Report CSV</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Upload Section -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600">
                            <i class="fas fa-upload text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Carica File CSV
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">
                                Seleziona il file CSV da elaborare
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <form id="csvUploadForm" enctype="multipart/form-data" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Seleziona File CSV
                            </label>
                            
                            <!-- Hidden file input -->
                            <input type="file" id="csvFile" name="csvFile" accept=".csv" class="hidden" required>
                            
                            <!-- Elegant Drop Zone -->
                            <div id="drop-zone-csv" class="relative border-2 border-dashed border-blue-300 dark:border-blue-600 rounded-2xl p-8 text-center bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 hover:from-blue-100 hover:to-indigo-100 dark:hover:from-blue-900/30 dark:hover:to-indigo-900/30 transition-all duration-300 cursor-pointer group hover:border-blue-400 dark:hover:border-blue-500">
                                
                                <!-- Upload State -->
                                <div id="upload-state-csv" class="space-y-4">
                                    <div class="flex justify-center">
                                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-300">
                                            <i class="fas fa-file-csv text-white text-2xl"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                            Carica file CSV
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                            <span class="font-medium text-blue-600 dark:text-blue-400">Clicca per selezionare</span> 
                                            o trascina qui il file
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500">
                                            Formato: .csv • Max 10MB
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- File Selected State -->
                                <div id="file-selected-state-csv" class="hidden space-y-4">
                                    <div class="flex justify-center">
                                        <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                                            <i class="fas fa-file-csv text-white text-2xl"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 id="selected-filename-csv" class="text-lg font-semibold text-green-700 dark:text-green-400 mb-1">
                                            File selezionato
                                        </h4>
                                        <p id="selected-filesize-csv" class="text-sm text-green-600 dark:text-green-500 mb-3">
                                            Dimensione file
                                        </p>
                                        <button type="button" onclick="clearSelectedFileCsv()" 
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 border border-red-300 rounded-lg hover:bg-red-200 hover:border-red-400 transition-colors dark:text-red-400 dark:bg-red-900/30 dark:border-red-800 dark:hover:bg-red-900/40">
                                            <i class="fas fa-times mr-1.5"></i>
                                            Rimuovi file
                                        </button>
                                    </div>
                                </div>

                                <!-- Drag Over State -->
                                <div id="drag-over-state-csv" class="hidden absolute inset-0 bg-blue-500/10 backdrop-blur-sm rounded-2xl border-2 border-blue-500 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-blue-500 rounded-2xl flex items-center justify-center shadow-xl mx-auto mb-4">
                                            <i class="fas fa-download text-white text-2xl"></i>
                                        </div>
                                        <p class="text-lg font-semibold text-blue-700 dark:text-blue-300">
                                            Rilascia il file qui
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-info-circle mr-2"></i>
                                Formato richiesto: Commessa;Fase;Data;Articolo;Qta
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button id="upload-btn-csv" type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                                <i id="upload-icon-csv" class="fas fa-upload mr-2"></i>
                                <span id="upload-text-csv">Carica e Analizza</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview Card -->
            <div id="previewCard" class="hidden mt-8 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-green-600">
                                <i class="fas fa-eye text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Preview Dati
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">
                                    Anteprima dell'elaborazione CSV
                                </p>
                            </div>
                        </div>
                        <button id="generateReportBtn" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium rounded-lg transition-all shadow-lg hover:shadow-xl">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Genera Report PDF
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div id="previewContent" class="overflow-x-auto"></div>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-600">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Informazioni
                        </h3>
                    </div>
                </div>
                
                <div class="p-6 space-y-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">
                            Formato CSV Richiesto:
                        </h4>
                        <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-xl font-mono text-sm text-gray-700 dark:text-gray-300">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Header:</div>
                            Commessa;Fase;Data;Articolo;Qta<br>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-3 mb-2">Esempio:</div>
                            2025 - 40094695 - S;04 - ORLATURA;05/09/2025 07:44;HE222297Z005--ME;20
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">
                            Come funziona:
                        </h4>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    Estrae il numero commessa dal formato
                                </span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    Incrocia con la tabella <code class="bg-gray-200 dark:bg-gray-600 px-1 py-0.5 rounded text-xs">dati</code>
                                </span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    Recupera la commessa cliente
                                </span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-3 mt-0.5 flex-shrink-0"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    Genera report PDF raggruppato
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400 mr-3 mt-0.5 flex-shrink-0"></i>
                            <div>
                                <h5 class="font-medium text-amber-800 dark:text-amber-200">Nota:</h5>
                                <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                                    Il sistema estrae automaticamente le ultime 5 cifre dal numero centrale della commessa.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setupCsvUpload();
});

let droppedFileCsv = null;

function clearSelectedFileCsv() {
    const fileInput = document.getElementById('csvFile');
    const uploadState = document.getElementById('upload-state-csv');
    const selectedState = document.getElementById('file-selected-state-csv');
    
    if (fileInput) fileInput.value = '';
    if (uploadState) uploadState.classList.remove('hidden');
    if (selectedState) selectedState.classList.add('hidden');
    
    droppedFileCsv = null;
    addAlert('File deselezionato', 'info');
}

function setupCsvUpload() {
    const fileInput = document.getElementById('csvFile');
    const form = document.getElementById('csvUploadForm');
    const dropZone = document.getElementById('drop-zone-csv');
    const dragOverState = document.getElementById('drag-over-state-csv');
    const uploadState = document.getElementById('upload-state-csv');
    
    if (!fileInput || !form || !dropZone) return;

    // Click handler for drop zone
    dropZone.addEventListener('click', () => {
        fileInput.click();
    });

    // Drag & Drop handlers
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // Drag enter/over
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            if (dragOverState) dragOverState.classList.remove('hidden');
            if (uploadState) uploadState.classList.add('hidden');
        });
    });

    // Drag leave
    dropZone.addEventListener('dragleave', (e) => {
        if (!dropZone.contains(e.relatedTarget)) {
            if (dragOverState) dragOverState.classList.add('hidden');
            if (uploadState) uploadState.classList.remove('hidden');
        }
    });

    // Drop
    dropZone.addEventListener('drop', (e) => {
        if (dragOverState) dragOverState.classList.add('hidden');
        if (uploadState) uploadState.classList.remove('hidden');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            showFileSelectedCsv(file);
            droppedFileCsv = file;
        }
    });

    // File input change handler
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            showFileSelectedCsv(file);
            droppedFileCsv = null;
        }
    });

    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const file = droppedFileCsv || fileInput.files[0];
        if (!file) {
            addAlert('Seleziona un file prima di procedere', 'error');
            return;
        }
        
        handleUploadCsv(file);
    });

    // Generate report handler
    const generateBtn = document.getElementById('generateReportBtn');
    if (generateBtn) {
        generateBtn.addEventListener('click', function() {
            window.open('<?= $this->url('/produzione/generate-csv-report') ?>', '_blank');
        });
    }
}

function showFileSelectedCsv(file) {
    const uploadState = document.getElementById('upload-state-csv');
    const selectedState = document.getElementById('file-selected-state-csv');
    const filename = document.getElementById('selected-filename-csv');
    const filesize = document.getElementById('selected-filesize-csv');

    // Validate file
    if (!file.name.toLowerCase().endsWith('.csv')) {
        addAlert('Formato file non supportato. Utilizzare solo file CSV (.csv)', 'error');
        clearSelectedFileCsv();
        return;
    }

    const maxSize = 10 * 1024 * 1024; // 10MB
    if (file.size > maxSize) {
        addAlert('File troppo grande. Dimensione massima: 10MB', 'error');
        clearSelectedFileCsv();
        return;
    }

    // Show file info
    if (filename) filename.textContent = file.name;
    if (filesize) filesize.textContent = formatFileSize(file.size);
    
    // Switch states
    if (uploadState) uploadState.classList.add('hidden');
    if (selectedState) selectedState.classList.remove('hidden');

    addAlert('File CSV selezionato: ' + file.name, 'success');
}

function handleUploadCsv(file) {
    const uploadBtn = document.getElementById('upload-btn-csv');
    const uploadIcon = document.getElementById('upload-icon-csv');
    const uploadText = document.getElementById('upload-text-csv');
    
    // Update button state
    if (uploadBtn) uploadBtn.disabled = true;
    if (uploadIcon) uploadIcon.className = 'fas fa-spinner fa-spin mr-2';
    if (uploadText) uploadText.textContent = 'Elaborando...';

    const formData = new FormData();
    formData.append('csvFile', file);

    fetch('<?= $this->url('/produzione/process-csv') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayPreview(data.data);
            document.getElementById('previewCard').classList.remove('hidden');
            addAlert('File elaborato con successo! ' + data.message, 'success');
            
            // Update button to success state
            if (uploadIcon) uploadIcon.className = 'fas fa-check mr-2';
            if (uploadText) uploadText.textContent = 'Elaborazione Completata';
        } else {
            addAlert('Errore: ' + data.message, 'error');
            
            // Reset button
            if (uploadIcon) uploadIcon.className = 'fas fa-upload mr-2';
            if (uploadText) uploadText.textContent = 'Carica e Analizza';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        addAlert('Errore durante l\'elaborazione: ' + error.message, 'error');
        
        // Reset button
        if (uploadIcon) uploadIcon.className = 'fas fa-upload mr-2';
        if (uploadText) uploadText.textContent = 'Carica e Analizza';
    })
    .finally(() => {
        if (uploadBtn) uploadBtn.disabled = false;
    });
}

function displayPreview(data) {
    let html = '<div class="overflow-x-auto">';
    html += '<table class="w-full text-sm">';
    html += '<thead class="bg-gray-50 dark:bg-gray-700">';
    html += '<tr>';
    html += '<th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Commessa CSV</th>';
    html += '<th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Commessa Estratta</th>';
    html += '<th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Fase</th>';
    html += '<th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Data</th>';
    html += '<th class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-white">Qta</th>';
    html += '<th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-white">Cliente Trovato</th>';
    html += '</tr>';
    html += '</thead><tbody class="divide-y divide-gray-200 dark:divide-gray-600">';
    
    data.forEach(function(row, index) {
        const isEven = index % 2 === 0;
        html += '<tr class="' + (isEven ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-700') + '">';
        html += '<td class="px-4 py-3 text-gray-900 dark:text-white">' + escapeHtml(row.commessa_csv) + '</td>';
        html += '<td class="px-4 py-3 text-gray-900 dark:text-white">' + escapeHtml(row.commessa_estratta) + '</td>';
        html += '<td class="px-4 py-3 text-gray-900 dark:text-white">' + escapeHtml(row.fase) + '</td>';
        html += '<td class="px-4 py-3 text-gray-900 dark:text-white">' + escapeHtml(row.data) + '</td>';
        html += '<td class="px-4 py-3 text-center font-medium text-gray-900 dark:text-white">' + row.qta + '</td>';
        
        if (row.cliente) {
            html += '<td class="px-4 py-3"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">' + escapeHtml(row.cliente) + '</span></td>';
        } else {
            html += '<td class="px-4 py-3"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Non trovato</span></td>';
        }
        
        html += '</tr>';
    });
    
    html += '</tbody></table></div>';
    document.getElementById('previewContent').innerHTML = html;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>