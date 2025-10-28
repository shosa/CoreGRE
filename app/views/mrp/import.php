<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Dashboard Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Import Dati ERP
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Carica i file Excel per aggiornare i fabbisogni materiali
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="<?= $this->url('/mrp') ?>"
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna al Dashboard
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
                <a href="<?= $this->url('/mrp') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">MRP</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Import Dati</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Istruzioni -->
<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-400"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                Prima di iniziare
            </h3>
            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                <ul class="list-disc list-inside space-y-1">
                    <li>Assicurati di avere entrambi i file Excel dal sistema ERP</li>
                    <li><strong>Elenco completo materiali:</strong> contiene la lista di tutti i materiali con fornitori e descrizioni</li>
                    <li><strong>Dettaglio quantità a taglie:</strong> contiene i fabbisogni per taglia di ogni materiale</li>
                    <li>I dati esistenti verranno sostituiti con quelli del nuovo import</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Form Upload -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-title-md font-bold text-gray-900 dark:text-white">
            Carica File Excel
        </h3>
    </div>
    <div class="p-6">
        <form id="uploadForm" action="<?= $this->url('/mrp/upload-excel') ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                <!-- File Materiali -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Elenco Completo Materiali
                    </label>
                    <input type="file" id="materials_file" name="materials_file" accept=".xlsx,.xls" class="hidden">

                    <div id="drop-zone-materials"
                        class="relative border-2 border-dashed border-blue-300 dark:border-blue-600 rounded-2xl p-8 text-center bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 hover:from-blue-100 hover:to-indigo-100 dark:hover:from-blue-900/30 dark:hover:to-indigo-900/30 transition-all duration-300 cursor-pointer group hover:border-blue-400 dark:hover:border-blue-500">

                        <div id="upload-state-materials" class="space-y-4">
                            <div class="flex justify-center">
                                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-300">
                                    <i class="fas fa-cloud-upload-alt text-white text-2xl"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                    Carica File Materiali
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Clicca per selezionare o trascina il file qui
                                </p>
                                <div class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                                    Formati supportati: .xlsx, .xls
                                </div>
                            </div>
                        </div>

                        <div id="file-selected-materials" class="hidden space-y-4">
                            <div class="flex justify-center">
                                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-check text-white text-2xl"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-2">
                                    File Selezionato
                                </h4>
                                <p class="text-sm text-green-700 dark:text-green-300 font-medium" id="file-name-materials"></p>
                                <button type="button" onclick="clearFile('materials')" class="mt-2 text-xs text-red-600 hover:text-red-700 font-medium">
                                    Rimuovi file
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Taglie -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Dettaglio Quantità a Taglie
                    </label>
                    <input type="file" id="sizes_file" name="sizes_file" accept=".xlsx,.xls" class="hidden">

                    <div id="drop-zone-sizes"
                        class="relative border-2 border-dashed border-purple-300 dark:border-purple-600 rounded-2xl p-8 text-center bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 hover:from-purple-100 hover:to-pink-100 dark:hover:from-purple-900/30 dark:hover:to-pink-900/30 transition-all duration-300 cursor-pointer group hover:border-purple-400 dark:hover:border-purple-500">

                        <div id="upload-state-sizes" class="space-y-4">
                            <div class="flex justify-center">
                                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-300">
                                    <i class="fas fa-tags text-white text-2xl"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                    Carica File Taglie
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Clicca per selezionare o trascina il file qui
                                </p>
                                <div class="text-xs text-purple-600 dark:text-purple-400 font-medium">
                                    Formati supportati: .xlsx, .xls
                                </div>
                            </div>
                        </div>

                        <div id="file-selected-sizes" class="hidden space-y-4">
                            <div class="flex justify-center">
                                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-check text-white text-2xl"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-2">
                                    File Selezionato
                                </h4>
                                <p class="text-sm text-green-700 dark:text-green-300 font-medium" id="file-name-sizes"></p>
                                <button type="button" onclick="clearFile('sizes')" class="mt-2 text-xs text-red-600 hover:text-red-700 font-medium">
                                    Rimuovi file
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" id="submitBtn"
                        class="w-full rounded-lg border border-gray-300 bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-3 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-upload mr-2"></i>
                    <span id="submitText">Elabora Import</span>
                </button>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                    L'operazione potrebbe richiedere alcuni minuti per file di grandi dimensioni
                </p>
            </div>
        </form>
    </div>
</div>

<script>
// Modern Upload System following Settings pattern
function setupMRPUpload() {
    setupDropZone('materials');
    setupDropZone('sizes');

    // Form submission
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');

        submitBtn.disabled = true;
        submitText.textContent = 'Elaborazione in corso...';
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span>Elaborazione in corso...</span>';
    });
}

function setupDropZone(type) {
    const fileInput = document.getElementById(`${type}_file`);
    const dropZone = document.getElementById(`drop-zone-${type}`);
    const uploadState = document.getElementById(`upload-state-${type}`);
    const selectedState = document.getElementById(`file-selected-${type}`);

    console.log(`Setting up drop zone for ${type}:`, {
        fileInput: !!fileInput,
        dropZone: !!dropZone,
        uploadState: !!uploadState,
        selectedState: !!selectedState
    });

    if (!fileInput || !dropZone) {
        console.error(`Missing elements for ${type} drop zone`);
        return;
    }

    // Click handler
    dropZone.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        fileInput.click();
    });

    // File input change handler
    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            showFileSelected(type, file);
            checkFormReady();
        }
    });

    // Drag and drop handlers
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('ring-2', 'ring-blue-500', 'ring-opacity-50');
    }

    function unhighlight(e) {
        dropZone.classList.remove('ring-2', 'ring-blue-500', 'ring-opacity-50');
    }

    dropZone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            showFileSelected(type, files[0]);
            checkFormReady();
        }
    }, false);
}

function showFileSelected(type, file) {
    const uploadState = document.getElementById(`upload-state-${type}`);
    const selectedState = document.getElementById(`file-selected-${type}`);
    const fileName = document.getElementById(`file-name-${type}`);

    if (uploadState && selectedState && fileName) {
        uploadState.classList.add('hidden');
        selectedState.classList.remove('hidden');
        fileName.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
    }
}

function clearFile(type) {
    const fileInput = document.getElementById(`${type}_file`);
    const uploadState = document.getElementById(`upload-state-${type}`);
    const selectedState = document.getElementById(`file-selected-${type}`);

    if (fileInput) {
        fileInput.value = '';
        uploadState.classList.remove('hidden');
        selectedState.classList.add('hidden');
        checkFormReady();
    }
}

function checkFormReady() {
    const materialsFile = document.getElementById('materials_file').files[0];
    const sizesFile = document.getElementById('sizes_file').files[0];
    const submitBtn = document.getElementById('submitBtn');

    if (materialsFile && sizesFile) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing MRP Import...');

    // Wait a bit for elements to be fully rendered
    setTimeout(() => {
        setupMRPUpload();
        checkFormReady();
        console.log('MRP Import page loaded');
    }, 100);
});

// Cleanup for PJAX
function cleanupEventListeners() {
    // Cleanup if needed
}
</script>