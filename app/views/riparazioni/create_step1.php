<?php
/**
 * Riparazioni - Step 1: Inserimento Cartellino o Commessa
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
                        Nuova Riparazione - Step 1
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Inserisci i dati della nuova riparazione per procedere allo step successivo
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?= $this->url('/riparazioni') ?>"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna all'elenco
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
                <span class="text-gray-700 dark:text-gray-300">Nuova Riparazione - Step 1</span>
            </div>
        </li>
    </ol>
</nav>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Principale -->
    <div class="lg:col-span-2">
        <div
            class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mr-3">
                            <i class="fas fa-search text-white"></i>
                        </div>
                        Ricerca Dati Produzione
                    </h2>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                        Step 1 di 2
                    </span>
                </div>
            </div>

            <div class="p-6">
                <form id="searchForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4
                                class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                                Ricerca per Cartellino
                            </h4>
                            <div>
                                <label for="cartellino"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-barcode text-blue-500 mr-2"></i>
                                    Numero Cartellino
                                </label>
                                <input type="text" id="cartellino" name="cartellino"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                    placeholder="es. 12345" autocomplete="off">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Inserisci il numero del cartellino di produzione
                                </p>
                            </div>
                        </div>

                        <div>
                            <h4
                                class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                                Ricerca per Commessa
                            </h4>
                            <div>
                                <label for="commessa"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-briefcase text-green-500 mr-2"></i>
                                    Commessa Cliente
                                </label>
                                <input type="text" id="commessa" name="commessa"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                    placeholder="es. COM2024001" autocomplete="off">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Inserisci il codice della commessa cliente
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- OR Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="px-3 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-medium">
                                SCEGLI UNA OPZIONE
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg space-x-2">
                            <i class="fas fa-search"></i>
                            <span>Verifica e Continua</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Informazioni -->
    <div class="space-y-6">
        <!-- Come funziona -->
        <div
            class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mr-3">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    Come funziona?
                </h3>
                <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-start space-x-3">
                        <div
                            class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400">1</span>
                        </div>
                        <p>Inserisci il <strong>cartellino</strong> o la <strong>commessa cliente</strong></p>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div
                            class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400">2</span>
                        </div>
                        <p>Il sistema recupera automaticamente i dati dalla produzione</p>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div
                            class="flex-shrink-0 w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400">3</span>
                        </div>
                        <p>Compila il form di riparazione con i dati precaricati</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suggerimenti -->
        <div
            class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-yellow-500 to-yellow-600 shadow-lg mr-3">
                        <i class="fas fa-lightbulb text-white"></i>
                    </div>
                    Suggerimenti
                </h3>
                <div class="space-y-3">
                    <div
                        class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            <strong>Non trovi il cartellino?</strong><br>
                            Prova a cercare con la commessa cliente. Il sistema troverà automaticamente il cartellino
                            corrispondente.
                        </p>
                    </div>
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Ricerca veloce:</strong><br>
                            Puoi utilizzare solo una parte del codice per trovare rapidamente quello che stai cercando.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    // AbortController per gestire cleanup event listeners
    let formController = null;
    let isInitialized = false;

    function initSearchForm() {
        const form = document.getElementById('searchForm');
        const cartellinoInput = document.getElementById('cartellino');
        const commessaInput = document.getElementById('commessa');

        if (!form || !cartellinoInput || !commessaInput) return;

        // Previeni inizializzazione multipla
        if (isInitialized) {
            console.log('[Riparazioni Step1] Already initialized, skipping');
            return;
        }

        console.log('[Riparazioni Step1] Initializing');
        isInitialized = true;

        // Abort vecchi listener se esistono
        if (formController) {
            formController.abort();
        }
        formController = new AbortController();
        const signal = formController.signal;

        // Clear the other field when typing in one
        cartellinoInput.addEventListener('input', function () {
            if (this.value.trim()) {
                commessaInput.value = '';
            }
        }, { signal });

        commessaInput.addEventListener('input', function () {
            if (this.value.trim()) {
                cartellinoInput.value = '';
            }
        }, { signal });

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            console.log('Form submitted'); // Debug

            const cartellinoValue = cartellinoInput.value.trim();
            const commessaValue = commessaInput.value.trim();

            console.log('Cartellino:', cartellinoValue, 'Commessa:', commessaValue); // Debug

            // Validate input
            if (!cartellinoValue && !commessaValue) {
                CoregreNotifications.warning('Per favore, inserisci un cartellino o una commessa.');
                return;
            }

            // Determine search type and execute
            if (cartellinoValue) {
                console.log('Checking cartellino:', cartellinoValue); // Debug
                checkCartellino(cartellinoValue);
            } else {
                console.log('Checking commessa:', commessaValue); // Debug
                checkCommessa(commessaValue);
            }
        }, { signal });

        async function checkCartellino(cartellino) {
            // Mostra modale di loading
            const loadingModalId = showLoadingModal('Verifica Cartellino', `Ricerca del cartellino <strong>${cartellino}</strong> in corso...`);

            // Timestamp per garantire tempo minimo di visualizzazione
            const startTime = Date.now();
            const minDisplayTime = 500; // millisecondi

            try {
                console.log('Starting checkCartellino for:', cartellino); // Debug

                const response = await fetch(`<?= $this->url('/api/riparazioni/check-cartellino') ?>?cartellino=${encodeURIComponent(cartellino)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('Response received:', response); // Debug

                if (!response.ok) {
                    throw new Error(`Errore server: ${response.status}`);
                }

                const data = await response.json();
                console.log('Data received:', data); // Debug

                // Calcola tempo rimanente per raggiungere il minimo
                const elapsed = Date.now() - startTime;
                const remainingTime = Math.max(0, minDisplayTime - elapsed);

                // Attendi il tempo rimanente prima di chiudere
                await new Promise(resolve => setTimeout(resolve, remainingTime));

                // Chiudi modale loading
                CoregreModals.close(loadingModalId);

                if (data.error) {
                    throw new Error(data.error);
                }

                if (data.exists) {
                    // Cartellino trovato - mostra successo e naviga
                    showSuccessModal(
                        'Cartellino Trovato!',
                        `Il cartellino <strong>${cartellino}</strong> è stato trovato nel database.<br><br>Reindirizzamento allo step 2...`,
                        () => {
                            const url = `<?= $this->url('/riparazioni/create-step2') ?>?cartellino=${encodeURIComponent(cartellino)}`;
                            if (window.pjax && typeof window.pjax.navigateTo === 'function') {
                                window.pjax.navigateTo(url);
                            } else {
                                window.location.href = url;
                            }
                        }
                    );
                } else {
                    // Cartellino non trovato
                    showErrorModal(
                        'Cartellino Non Trovato',
                        `Il cartellino <strong>${cartellino}</strong> non è stato trovato nel database.<br><br>Verifica il numero inserito e riprova.`,
                        () => {
                            cartellinoInput.value = '';
                            cartellinoInput.focus();
                        }
                    );
                }
            } catch (error) {
                console.error('Error checking cartellino:', error);

                // Calcola tempo rimanente per raggiungere il minimo
                const elapsed = Date.now() - startTime;
                const remainingTime = Math.max(0, minDisplayTime - elapsed);

                // Attendi il tempo rimanente prima di chiudere
                await new Promise(resolve => setTimeout(resolve, remainingTime));

                // Chiudi modale loading
                CoregreModals.close(loadingModalId);

                // Mostra errore
                showErrorModal(
                    'Errore di Verifica',
                    `Si è verificato un errore durante la verifica del cartellino:<br><br><em>${error.message}</em>`,
                    () => {
                        cartellinoInput.value = '';
                        cartellinoInput.focus();
                    }
                );
            }
        }

        async function checkCommessa(commessa) {
            // Mostra modale di loading
            const loadingModalId = showLoadingModal('Verifica Commessa', `Ricerca della commessa <strong>${commessa}</strong> in corso...`);

            // Timestamp per garantire tempo minimo di visualizzazione
            const startTime = Date.now();
            const minDisplayTime = 500; // millisecondi

            try {
                console.log('Starting checkCommessa for:', commessa); // Debug

                const response = await fetch(`<?= $this->url('/api/riparazioni/check-commessa') ?>?commessa=${encodeURIComponent(commessa)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('Response received:', response); // Debug

                if (!response.ok) {
                    throw new Error(`Errore server: ${response.status}`);
                }

                const data = await response.json();
                console.log('Data received:', data); // Debug

                // Calcola tempo rimanente per raggiungere il minimo
                const elapsed = Date.now() - startTime;
                const remainingTime = Math.max(0, minDisplayTime - elapsed);

                // Attendi il tempo rimanente prima di chiudere
                await new Promise(resolve => setTimeout(resolve, remainingTime));

                // Chiudi modale loading
                CoregreModals.close(loadingModalId);

                if (data.error) {
                    throw new Error(data.error);
                }

                if (data.exists && data.cartellino) {
                    // Commessa trovata - mostra successo e naviga
                    showSuccessModal(
                        'Commessa Trovata!',
                        `La commessa <strong>${commessa}</strong> è stata trovata!<br>Cartellino associato: <strong>${data.cartellino}</strong><br><br>Reindirizzamento allo step 2...`,
                        () => {
                            const url = `<?= $this->url('/riparazioni/create-step2') ?>?cartellino=${encodeURIComponent(data.cartellino)}`;
                            if (window.pjax && typeof window.pjax.navigateTo === 'function') {
                                window.pjax.navigateTo(url);
                            } else {
                                window.location.href = url;
                            }
                        }
                    );
                } else {
                    // Commessa non trovata
                    showErrorModal(
                        'Commessa Non Trovata',
                        `La commessa <strong>${commessa}</strong> non è stata trovata nel database.<br><br>Verifica il codice inserito e riprova.`,
                        () => {
                            commessaInput.value = '';
                            commessaInput.focus();
                        }
                    );
                }
            } catch (error) {
                console.error('Error checking commessa:', error);

                // Calcola tempo rimanente per raggiungere il minimo
                const elapsed = Date.now() - startTime;
                const remainingTime = Math.max(0, minDisplayTime - elapsed);

                // Attendi il tempo rimanente prima di chiudere
                await new Promise(resolve => setTimeout(resolve, remainingTime));

                // Chiudi modale loading
                CoregreModals.close(loadingModalId);

                // Mostra errore
                showErrorModal(
                    'Errore di Verifica',
                    `Si è verificato un errore durante la verifica della commessa:<br><br><em>${error.message}</em>`,
                    () => {
                        commessaInput.value = '';
                        commessaInput.focus();
                    }
                );
            }
        }
    }

    // ============================================================================
    // Helper Functions per Modali Custom
    // ============================================================================

    /**
     * Mostra modale di loading con spinner
     */
    function showLoadingModal(title, message) {
        const modalId = 'loading-modal-' + Date.now();

        const modalHtml = `
            <div id="${modalId}" class="fixed inset-0 z-[99999] overflow-y-auto modal-backdrop" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-4 text-center">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

                    <!-- Modal panel -->
                    <div class="modal-content relative inline-block align-middle bg-white dark:bg-gray-800 rounded-2xl px-6 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all max-w-lg w-full mx-4 border border-gray-200 dark:border-gray-700">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-spinner fa-spin text-blue-600 dark:text-blue-400 text-lg"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">
                                    ${title}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        ${message}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        return modalId;
    }

    /**
     * Mostra modale di successo con auto-close
     */
    function showSuccessModal(title, message, onClose) {
        const modalId = 'success-modal-' + Date.now();

        const modalHtml = `
            <div id="${modalId}" class="fixed inset-0 z-[99999] overflow-y-auto modal-backdrop" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-4 text-center">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="CoregreModals.close('${modalId}')"></div>

                    <!-- Modal panel -->
                    <div class="modal-content relative inline-block align-middle bg-white dark:bg-gray-800 rounded-2xl px-6 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all max-w-lg w-full mx-4 border border-gray-200 dark:border-gray-700">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-green-100 dark:bg-green-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-lg"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">
                                    ${title}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        ${message}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Auto-close dopo 1.5 secondi e naviga
        setTimeout(() => {
            CoregreModals.close(modalId);
            if (typeof onClose === 'function') {
                setTimeout(onClose, 200); // Aspetta che l'animazione finisca
            }
        }, 1500);

        return modalId;
    }

    /**
     * Mostra modale di errore con pulsante OK
     */
    function showErrorModal(title, message, onClose) {
        const modalId = 'error-modal-' + Date.now();

        const modalHtml = `
            <div id="${modalId}" class="fixed inset-0 z-[99999] overflow-y-auto modal-backdrop" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-4 text-center">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="CoregreModals.close('${modalId}')"></div>

                    <!-- Modal panel -->
                    <div class="modal-content relative inline-block align-middle bg-white dark:bg-gray-800 rounded-2xl px-6 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all max-w-lg w-full mx-4 border border-gray-200 dark:border-gray-700">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 text-lg"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">
                                    ${title}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        ${message}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button type="button" onclick="CoregreModals.close('${modalId}')"
                                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm transition-all duration-200 hover:shadow-lg">
                                <i class="fas fa-times mr-2"></i>
                                OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Salva callback per quando viene chiuso
        if (typeof onClose === 'function') {
            const modal = document.getElementById(modalId);
            modal.addEventListener('click', (e) => {
                if (e.target.id === modalId || e.target.closest('button')) {
                    setTimeout(onClose, 200);
                }
            }, { once: true });
        }

        return modalId;
    }

    // Cleanup function per PJAX navigation
    function cleanupSearchForm() {
        console.log('[Riparazioni Step1] Cleanup');
        isInitialized = false;

        if (formController) {
            formController.abort();
            formController = null;
        }
    }

    // Registra cleanup per PJAX
    document.addEventListener('pjax:beforeNavigate', cleanupSearchForm);

    // Registra l'inizializzatore per PJAX
    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initSearchForm);
    }

    // Initialize anche al primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSearchForm);
    } else {
        initSearchForm();
    }
})();
</script>