<?php
/**
 * Export - Griglia Materiali
 * Pagina per selezionare i materiali e generare PDF con griglia per incollare campioni
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-th mr-3 text-purple-500"></i>
                Griglia Materiali - DDT <?= htmlspecialchars($progressivo) ?>
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Seleziona i materiali da includere nella griglia per la stampa
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?= $this->url('/export') ?>"
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alla lista
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
                <a href="<?= $this->url('/export/dashboard') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                    Export
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/export') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                    Lista DDT
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-500 dark:text-gray-400">Griglia Materiali</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Info documento -->
<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
        Informazioni Documento
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Numero DDT</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($progressivo) ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Destinatario</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($documento->terzista->ragione_sociale ?? 'N/A') ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Data</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white"><?= date('d/m/Y', strtotime($documento->data)) ?></p>
        </div>
    </div>
</div>

<!-- Form selezione materiali -->
<form id="griglia-form" method="POST" action="<?= $this->url('/export/griglia-materiali/genera-pdf') ?>">
    <input type="hidden" name="progressivo" value="<?= htmlspecialchars($progressivo) ?>">

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-boxes mr-2 text-purple-500"></i>
                Selezione Materiali
            </h3>
            <div class="flex items-center space-x-3">
                <button type="button"
                        onclick="selezionaTutti()"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    <i class="fas fa-check-double mr-2"></i>
                    Seleziona Tutti
                </button>
                <button type="button"
                        onclick="deselezionaTutti()"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>
                    Deseleziona Tutti
                </button>
            </div>
        </div>

        <?php if (empty($materiali) || count($materiali) == 0): ?>
            <!-- Nessun materiale -->
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                    <i class="fas fa-inbox text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    Nessun materiale disponibile
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    Questo documento non contiene materiali da selezionare.
                </p>
            </div>
        <?php else: ?>
            <!-- Lista materiali con checkbox -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($materiali as $materiale): ?>
                    <label class="relative flex items-start p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer transition-colors">
                        <div class="flex items-center h-5">
                            <input type="checkbox"
                                   name="materiali[]"
                                   value="<?= htmlspecialchars($materiale->codice_articolo) ?>"
                                   class="material-checkbox w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                   checked>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">
                                <?= htmlspecialchars($materiale->codice_articolo) ?>
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <?= htmlspecialchars($materiale->descrizione ?? 'N/A') ?>
                            </p>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>

            <!-- Info e azioni -->
            <div class="mt-6 p-4 bg-purple-50 dark:bg-purple-800/20 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-purple-400 mr-2 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-sm text-purple-700 dark:text-purple-300 mb-2">
                            La griglia verrà generata con <strong id="selected-count"><?= count($materiali) ?></strong> materiali selezionati.
                            Ogni riga conterrà 2 materiali con spazi quadrati per incollare i campioni fisici.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pulsante genera PDF -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="<?= $this->url('/export') ?>"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    Annulla
                </a>
                <button type="submit"
                        id="genera-pdf-btn"
                        class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Genera PDF Griglia
                </button>
            </div>
        <?php endif; ?>
    </div>
</form>

<script>
(function() {
    function initGrigliaMateriali() {
        const form = document.getElementById('griglia-form');
        const checkboxes = document.querySelectorAll('.material-checkbox');
        const selectedCount = document.getElementById('selected-count');
        const generaPdfBtn = document.getElementById('genera-pdf-btn');

        // Funzioni globali per i pulsanti
        window.selezionaTutti = function() {
            checkboxes.forEach(cb => cb.checked = true);
            updateSelectedCount();
        };

        window.deselezionaTutti = function() {
            checkboxes.forEach(cb => cb.checked = false);
            updateSelectedCount();
        };

        // Aggiorna conteggio selezionati
        function updateSelectedCount() {
            const count = Array.from(checkboxes).filter(cb => cb.checked).length;
            if (selectedCount) {
                selectedCount.textContent = count;
            }

            // Disabilita pulsante se nessun materiale selezionato
            if (generaPdfBtn) {
                if (count === 0) {
                    generaPdfBtn.disabled = true;
                    generaPdfBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    generaPdfBtn.disabled = false;
                    generaPdfBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        // Event listener per checkbox
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });

        // Inizializza conteggio
        updateSelectedCount();

        // Submit form
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const materialiSelezionati = formData.getAll('materiali[]');

                if (materialiSelezionati.length === 0) {
                    if (window.showAlert) {
                        window.showAlert('Seleziona almeno un materiale per generare la griglia', 'warning');
                    } else {
                        alert('Seleziona almeno un materiale per generare la griglia');
                    }
                    return;
                }

                // Apri PDF in nuova finestra
                form.target = '_blank';
                form.submit();
            });
        }
    }

    // Registrazione PJAX
    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initGrigliaMateriali);
    }

    // Fallback primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGrigliaMateriali);
    } else {
        initGrigliaMateriali();
    }
})();
</script>
