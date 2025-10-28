<?php
/**
 * Riparazioni - Step 2: Form precompilato con dati dal cartellino
 */
?>


<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <!-- Icon Box -->
                <div class="mr-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3 rounded-2xl shadow-lg">
                    <i class="fas fa-tools fa-2x"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Nuova Riparazione #<?= $nextId ?> - Step 2
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Conferma i dati inseriti e completa la creazione della riparazione
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <span
                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                <i class="fas fa-check-circle mr-1"></i>
                Dati Caricati
            </span>
            <a href="<?= $this->url('/riparazioni/create') ?>"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Indietro
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
                <a href="<?= $this->url('/riparazioni') ?>"
                    class="hover:text-gray-700 dark:hover:text-gray-300">
                    Riparazioni
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Nuova Riparazione - Step 2</span>
            </div>
        </li>
    </ol>
</nav>


<form action="<?= $this->url('/riparazioni/store') ?>" method="POST" id="riparazioneForm">
    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contenuto Principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Dati Articolo (Read-only) -->
            <div class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mr-3">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                Dati Articolo
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                Informazioni precompilate automaticamente dal cartellino <span class="font-semibold text-blue-600 dark:text-blue-400"><?= htmlspecialchars($datiCartellino->Cartel) ?></span>
            </p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Grid cards per i dati -->
                <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    
                    <!-- Codice Articolo Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800 shadow-lg">
                        <div class="flex items-center mb-2">
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-500 mr-2">
                                <i class="fas fa-barcode text-white text-xs"></i>
                            </div>
                            <label class="text-xs font-medium text-blue-800 dark:text-blue-200">Codice Articolo</label>
                        </div>
                        <input
                            type="text"
                            name="CODICE"
                            value="<?= htmlspecialchars($datiCartellino->Articolo ?? '') ?>"
                            readonly
                            class="w-full px-3 py-2 text-sm font-semibold bg-white/80 dark:bg-gray-800/80 border border-blue-200 dark:border-blue-600 rounded-lg text-gray-900 dark:text-white"
                        >
                    </div>

                    <!-- Cliente Card -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-4 border border-green-200 dark:border-green-800 shadow-lg">
                        <div class="flex items-center mb-2">
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-green-500 mr-2">
                                <i class="fas fa-user-tie text-white text-xs"></i>
                            </div>
                            <label class="text-xs font-medium text-green-800 dark:text-green-200">Cliente</label>
                        </div>
                        <input
                            type="text"
                            name="cliente"
                            value="<?= htmlspecialchars($datiCartellino->{'Ragione Sociale'} ?? '') ?>"
                            readonly
                            class="w-full px-3 py-2 text-sm font-semibold bg-white/80 dark:bg-gray-800/80 border border-green-200 dark:border-green-600 rounded-lg text-gray-900 dark:text-white"
                        >
                    </div>

                    <!-- Linea Card -->
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-xl p-4 border border-yellow-200 dark:border-yellow-800 shadow-lg">
                        <div class="flex items-center mb-2">
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-yellow-500 mr-2">
                                <i class="fas fa-stream text-white text-xs"></i>
                            </div>
                            <label class="text-xs font-medium text-yellow-800 dark:text-yellow-200">Linea</label>
                        </div>
                        <input
                            type="text"
                            name="linea"
                            value="<?= htmlspecialchars($datiCartellino->Ln ?? '') ?>"
                            readonly
                            class="w-full px-3 py-2 text-sm font-semibold bg-white/80 dark:bg-gray-800/80 border border-yellow-200 dark:border-yellow-600 rounded-lg text-gray-900 dark:text-white"
                        >
                    </div>
                </div>

                <!-- Articolo (Description) - Full width -->
                <div class="lg:col-span-3">
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl p-4 border border-purple-200 dark:border-purple-800 shadow-lg">
                        <div class="flex items-center mb-2">
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-purple-500 mr-2">
                                <i class="fas fa-tag text-white text-xs"></i>
                            </div>
                            <label class="text-xs font-medium text-purple-800 dark:text-purple-200">Descrizione Articolo</label>
                        </div>
                        <input
                            type="text"
                            name="ARTICOLO"
                            value="<?= htmlspecialchars($datiCartellino->{'Descrizione Articolo'} ?? '') ?>"
                            readonly
                            class="w-full px-3 py-2 text-sm font-semibold bg-white/80 dark:bg-gray-800/80 border border-purple-200 dark:border-purple-600 rounded-lg text-gray-900 dark:text-white"
                        >
                    </div>
                </div>

                <!-- Bottom row -->
                <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Cartellino Card -->
                    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-xl p-4 border border-indigo-200 dark:border-indigo-800 shadow-lg">
                        <div class="flex items-center mb-2">
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-500 mr-2">
                                <i class="fas fa-id-card text-white text-xs"></i>
                            </div>
                            <label class="text-xs font-medium text-indigo-800 dark:text-indigo-200">Cartellino</label>
                        </div>
                        <input
                            type="text"
                            name="cartellino"
                            value="<?= htmlspecialchars($datiCartellino->Cartel ?? '') ?>"
                            readonly
                            class="w-full px-3 py-2 text-sm font-semibold bg-white/80 dark:bg-gray-800/80 border border-indigo-200 dark:border-indigo-600 rounded-lg text-gray-900 dark:text-white"
                        >
                    </div>

                    <!-- Commessa Card -->
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-xl p-4 border border-orange-200 dark:border-orange-800 shadow-lg">
                        <div class="flex items-center mb-2">
                            <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-orange-500 mr-2">
                                <i class="fas fa-briefcase text-white text-xs"></i>
                            </div>
                            <label class="text-xs font-medium text-orange-800 dark:text-orange-200">Commessa</label>
                        </div>
                        <input
                            type="text"
                            name="commessa"
                            value="<?= htmlspecialchars($datiCartellino->{'Commessa Cli'} ?? '') ?>"
                            readonly
                            class="w-full px-3 py-2 text-sm font-semibold bg-white/80 dark:bg-gray-800/80 border border-orange-200 dark:border-orange-600 rounded-lg text-gray-900 dark:text-white"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Numerata Section -->
    <?php if ($numerata): ?>
    <div class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 mb-8 backdrop-blur-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg mr-3">
                            <i class="fas fa-list-ol text-white"></i>
                        </div>
                        Numerata - Quantità da Riparare
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        Inserisci le quantità da riparare per ogni taglia. Le quantità attuali sono mostrate per riferimento.
                    </p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Header con conteggio taglie attive -->
            <?php 
            $activeSizes = 0;
            for ($i = 1; $i <= 20; $i++) {
                $nField = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                if (!empty($numerata->$nField)) $activeSizes++;
            }
            ?>
            <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 shadow-md">
                            <i class="fas fa-ruler text-white text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                <?= $activeSizes ?> Taglie Disponibili
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Articolo: <?= htmlspecialchars($datiCartellino->{'Descrizione Articolo'} ?? '') ?>
                            </p>
                        </div>
                    </div>
                    <button type="button" onclick="fillAllSizes()" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-sm rounded-lg hover:from-purple-600 hover:to-purple-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                        <i class="fas fa-magic mr-2"></i>
                        Compila Tutto
                    </button>
                </div>
            </div>

            <!-- Tabella creativa per le taglie -->
            <div class="overflow-x-auto bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-inner">
                <table class="w-full">
                    <!-- Header con le taglie -->
                    <thead>
                        <tr class="border-b-2 border-gray-200 dark:border-gray-600">
                            <?php
                            // Header con le etichette della numerata - tutte blu
                            for ($i = 1; $i <= 20; $i++) {
                                $nField = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                if (!empty($numerata->$nField)) {
                                    $taglia = htmlspecialchars($numerata->$nField);
                                    
                                    echo '<th class="p-6 text-center">';
                                    echo '<div class="inline-flex items-center justify-center w-16 h-12 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-lg font-bold text-lg transform hover:scale-105 transition-all duration-200">';
                                    echo $taglia;
                                    echo '</div>';
                                    echo '</th>';
                                }
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Quantità attuali dal cartellino -->
                        <tr class="bg-blue-50/30 dark:bg-blue-900/20 border-b border-gray-200 dark:border-gray-600">
                            <?php
                            for ($i = 1; $i <= 20; $i++) {
                                $nField = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                $pField = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                if (!empty($numerata->$nField)) {
                                    $currentValue = $datiCartellino->$pField ?? 0;
                                    echo '<td class="p-6 text-center">';
                                    echo '<div class="inline-flex items-center justify-center w-12 h-8 bg-blue-100 dark:bg-blue-800/50 border border-blue-200 dark:border-blue-600 rounded-lg text-blue-800 dark:text-blue-200 font-bold text-sm">';
                                    echo $currentValue;
                                    echo '</div>';
                                    echo '</td>';
                                }
                            }
                            ?>
                        </tr>
                        <!-- Input per quantità da riparare -->
                        <tr class="bg-white dark:bg-gray-800">
                            <?php
                            for ($i = 1; $i <= 20; $i++) {
                                $nField = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                $pField = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                if (!empty($numerata->$nField)) {
                                    $currentValue = $datiCartellino->$pField ?? 0;

                                    echo '<td class="p-6 text-center">';
                                    // Solo input senza validazione max e senza etichette ridondanti
                                    echo '<input type="number" name="' . $pField . '" value="" min="0" ';
                                    echo 'class="w-20 h-14 px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-center text-xl font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 hover:border-blue-400 dark:hover:border-blue-500 shadow-sm hover:shadow-md" ';
                                    echo 'placeholder="0" oninput="updateTableProgress(this)">';
                                    echo '</td>';
                                }
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Riepilogo totale -->
            <div class="mt-8 p-6 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800 dark:to-blue-900/20 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                            <i class="fas fa-calculator text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">Totale da Riparare</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Quantità complessiva</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div id="totalQuantity" class="text-3xl font-bold text-green-600 dark:text-green-400">0</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">paia</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Dettagli Riparazione -->
            <div class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 shadow-lg mr-3">
                    <i class="fas fa-clipboard-list text-white"></i>
                </div>
                Dettagli Riparazione
            </h2>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Urgenza -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>
                        Urgenza *
                    </label>
                    <select name="urgenza" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="BASSA">BASSA</option>
                        <option value="MEDIA">MEDIA</option>
                        <option value="ALTA">ALTA</option>
                    </select>
                </div>

                <!-- Reparto -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-building text-blue-500 mr-1"></i>
                        Reparto *
                    </label>
                    <select name="reparto" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Seleziona un reparto</option>
                        <?php foreach ($reparti as $reparto): ?>
                            <option value="<?= htmlspecialchars($reparto->Nome) ?>">
                                <?= htmlspecialchars($reparto->Nome) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Laboratorio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-flask text-green-500 mr-1"></i>
                        Laboratorio *
                    </label>
                    <select name="laboratorio" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Seleziona un laboratorio</option>
                        <?php foreach ($laboratori as $lab): ?>
                            <option value="<?= htmlspecialchars($lab->Nome) ?>">
                                <?= htmlspecialchars($lab->Nome) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Causale -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-comment-alt text-purple-500 mr-1"></i>
                        Causale *
                    </label>
                    <textarea
                        name="causale"
                        required
                        rows="4"
                        placeholder="Descrivi il problema e le note della riparazione..."
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                    ></textarea>
                </div>
            </div>
            
            <!-- Azioni -->
            <div class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
                <div class="p-6">
                    <div class="space-y-3">
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg">
                            <i class="fas fa-save mr-2"></i>
                            Crea Riparazione
                        </button>
                        
                        <a href="<?= $this->url('/riparazioni/create') ?>"
                           class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 font-medium rounded-lg transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Torna al Step 1
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Fields -->
    <input type="hidden" name="Idrip" value="<?= $nextId ?>">
    <input type="hidden" name="nu" value="<?= htmlspecialchars($datiCartellino->Nu ?? '') ?>">
    <input type="hidden" name="utente" value="<?= htmlspecialchars($currentUser) ?>">
    <input type="hidden" name="data" value="<?= date('Y-m-d') ?>">
</form>

<script>
// Riparazioni Create Step 2 - JavaScript compatibile con PJAX
(function() {
    // Variabili globali per cleanup
    let eventListeners = [];
    
    // Funzione di inizializzazione
    function initRiparazioniCreateStep2() {
        // Cleanup precedenti event listeners
        cleanupEventListeners();
        
        const form = document.getElementById('riparazioneForm');
        
        if (form) {
            function formSubmitHandler(e) {
                // Validate that at least one quantity is specified
                const quantityInputs = form.querySelectorAll('input[type="number"][name^="P"]');
                let hasQuantity = false;
                
                quantityInputs.forEach(input => {
                    if (parseInt(input.value) > 0) {
                        hasQuantity = true;
                    }
                });
                
                if (!hasQuantity) {
                    e.preventDefault();
                    WebgreNotifications.warning('Deve essere specificata almeno una quantità da riparare.');
                    return;
                }
                
                // Show loading
                if (window.showLoading) {
                    showLoading();
                }
            }
            
            form.addEventListener('submit', formSubmitHandler);
            eventListeners.push({ element: form, event: 'submit', handler: formSubmitHandler });
        }
        
        // Initialize progress tracking
        updateTotalQuantity();
    }
    
    function cleanupEventListeners() {
        eventListeners.forEach(({ element, event, handler }) => {
            element.removeEventListener(event, handler);
        });
        eventListeners = [];
    }

    // Funzioni globali (usate da onclick inline)
    window.updateTableProgress = function(input) {
        const value = parseInt(input.value) || 0;
        
        // Semplice feedback visivo
        if (value > 0) {
            input.classList.remove('border-gray-300', 'dark:border-gray-600');
            input.classList.add('border-blue-400', 'dark:border-blue-500');
        } else {
            input.classList.remove('border-blue-400', 'dark:border-blue-500');
            input.classList.add('border-gray-300', 'dark:border-gray-600');
        }
        
        // Add scaling effect
        input.classList.add('scale-105');
        setTimeout(() => {
            input.classList.remove('scale-105');
        }, 150);
        
        // Update total
        updateTotalQuantity();
    };

    // Calculate and update total quantity
    window.updateTotalQuantity = function() {
        const quantityInputs = document.querySelectorAll('input[type="number"][name^="P"]');
        let total = 0;
        
        quantityInputs.forEach(input => {
            const value = parseInt(input.value) || 0;
            total += value;
        });
        
        const totalElement = document.getElementById('totalQuantity');
        if (totalElement) {
            totalElement.textContent = total;
            
            // Animate the number change
            totalElement.classList.add('animate-pulse');
            setTimeout(() => {
                totalElement.classList.remove('animate-pulse');
            }, 300);
        }
    };

    // Fill all sizes with 1 (helper function)
    window.fillAllSizes = function() {
        const quantityInputs = document.querySelectorAll('input[type="number"][name^="P"]');
        
        quantityInputs.forEach(input => {
            input.value = 1;
            updateTableProgress(input);
        });
        
        WebgreNotifications.success('Tutte le quantità sono state impostate a 1.');
    };

    // Keyboard shortcuts handler
    let keyboardListener = null;
    
    function initKeyboardShortcuts() {
        // Rimuovi listener esistente per evitare duplicati
        if (keyboardListener) {
            document.removeEventListener('keydown', keyboardListener);
        }
        
        keyboardListener = function(e) {
            // Ctrl+Shift+F = Fill all sizes
            if (e.ctrlKey && e.shiftKey && e.key === 'F') {
                e.preventDefault();
                fillAllSizes();
            }
        };
        
        document.addEventListener('keydown', keyboardListener);
    }

    // Registra l'inizializzatore per PJAX
    if (window.WEBGRE && window.WEBGRE.onPageLoad) {
        window.WEBGRE.onPageLoad(() => {
            initRiparazioniCreateStep2();
            initKeyboardShortcuts();
        });
    }

    // Inizializza anche al primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initRiparazioniCreateStep2();
            initKeyboardShortcuts();
        });
    } else {
        initRiparazioniCreateStep2();
        initKeyboardShortcuts();
    }
})();
</script>