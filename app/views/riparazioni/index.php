<!-- Dashboard Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <!-- Icon Box -->
                
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Riparazioni
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Gestisci tutte le riparazioni in lavorazione
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="<?= $this->url('/riparazioni/create') ?>" 
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <i class="fas fa-plus mr-2"></i>
                Nuova Riparazione
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
                <span class="text-gray-700 dark:text-gray-300">Riparazioni</span>
            </div>
        </li>
    </ol>
</nav>


<!-- Filtri -->
<div class="mb-8">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-800/40 md:p-6 shadow-lg backdrop-blur-sm">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Ricerca
                </label>
                <input type="text" name="search" value="<?= htmlspecialchars($currentSearch ?? '') ?>"
                       placeholder="ID, Codice, Articolo, Cartellino..."
                       class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Stato
                </label>
                <select name="status" 
                        class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
                    <option value="">Tutti gli stati</option>
                    <option value="open" <?= ($currentStatus ?? '') === 'open' ? 'selected' : '' ?>>Aperte</option>
                    <option value="complete" <?= ($currentStatus ?? '') === 'complete' ? 'selected' : '' ?>>Complete</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Urgenza
                </label>
                <select name="urgency" 
                        class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
                    <option value="">Tutte le urgenze</option>
                    <option value="BASSA" <?= ($currentUrgency ?? '') === 'BASSA' ? 'selected' : '' ?>>Bassa</option>
                    <option value="MEDIA" <?= ($currentUrgency ?? '') === 'MEDIA' ? 'selected' : '' ?>>Media</option>
                    <option value="ALTA" <?= ($currentUrgency ?? '') === 'ALTA' ? 'selected' : '' ?>>Alta</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" 
                        class="w-full rounded-lg border border-gray-300 bg-gradient-to-r from-gray-500 to-gray-600 px-4 py-2.5 text-sm font-medium text-white hover:from-gray-600 hover:to-gray-700 shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-search mr-2"></i>
                    Filtra
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabella Riparazioni -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-title-md font-bold text-gray-900 dark:text-white">
                Elenco Riparazioni
            </h3>
            <div class="flex items-center space-x-3">
                <button id="select-all" type="button" 
                        class="rounded-lg px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-check-square mr-1"></i>
                    Seleziona tutto
                </button>
                <button type="button" id="delete-selected" disabled 
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-trash-alt mr-1"></i>
                Elimina Selezionati
            </button>
            </div>
        </div>
    </div>
    
    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="w-8 px-6 py-4 text-left">
                            <input type="checkbox" id="master-checkbox" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Codice</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Articolo</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Qtà</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Cartellino</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Data</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Reparto</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Stato</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($riparazioni)): ?>
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-search text-4xl mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium">Nessuna riparazione trovata</p>
                            <p class="text-sm">Prova a modificare i filtri o crea una nuova riparazione</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($riparazioni as $rip): ?>
                        <?php
                        // Determina colore urgenza
                        $urgencyColor = 'gray';
                        $urgencyBg = 'bg-gray-100 text-gray-800';
                        switch ($rip->URGENZA) {
                            case 'ALTA':
                                $urgencyColor = 'red';
                                $urgencyBg = 'bg-red-100 text-red-800';
                                break;
                            case 'MEDIA':
                                $urgencyColor = 'yellow';
                                $urgencyBg = 'bg-yellow-100 text-yellow-800';
                                break;
                            case 'BASSA':
                                $urgencyColor = 'green';
                                $urgencyBg = 'bg-green-100 text-green-800';
                                break;
                        }
                        
                        $isComplete = $rip->COMPLETA == 1;
                        $statusColor = $isComplete ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
                        $statusText = $isComplete ? 'Completa' : 'Aperta';
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="row-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                                       value="<?= htmlspecialchars($rip->IDRIP) ?>">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $urgencyBg ?>">
                                        <?= htmlspecialchars($rip->IDRIP) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                <?= htmlspecialchars($rip->CODICE ?? '') ?>
                            </td>
                            <td class="px-4 py-3 max-w-xs truncate dark:text-white">
                                <span title="<?= htmlspecialchars($rip->ARTICOLO ?? '') ?>">
                                    <?= htmlspecialchars($rip->ARTICOLO ?? '') ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center dark:text-white">
                                <span class="font-semibold"><?= htmlspecialchars($rip->QTA ?? '0') ?></span>
                            </td>
                            <td class="px-4 py-3 dark:text-white">
                                <?= htmlspecialchars($rip->CARTELLINO ?? '') ?>
                            </td>
                            <td class="px-4 py-3 dark:text-white">
                                <?= htmlspecialchars($rip->DATA ?? '') ?>
                            </td>
                            <td class="px-4 py-3 dark:text-white">
                                <?= htmlspecialchars($rip->REPARTO ?? '') ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColor ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <a href="<?= $this->url('/riparazioni/' . $rip->IDRIP) ?>" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/40 transition-colors"
                                       title="Visualizza dettagli">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <?php if (!$isComplete): ?>
                                        <a href="<?= $this->url('/riparazioni/' . $rip->IDRIP . '/edit') ?>"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 hover:text-green-700 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-800/40 transition-colors"
                                           title="Modifica riparazione">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <button onclick="deleteRiparazione(<?= $rip->IDRIP ?>)"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-700 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-800/40 transition-colors"
                                                title="Elimina riparazione">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Riparazioni Index - JavaScript compatibile con PJAX
(function() {
    // Variabili globali per cleanup
    let eventListeners = [];
    
    // Funzione di inizializzazione
    function initRiparazioniIndex() {
        // Cleanup precedenti event listeners
        cleanupEventListeners();
        
        const masterCheckbox = document.getElementById('master-checkbox');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const selectAllBtn = document.getElementById('select-all');
        const deleteBtn = document.getElementById('delete-selected');
        
        if (!masterCheckbox || !deleteBtn) return; // Exit se elementi non trovati
        
        // Seleziona/deseleziona tutto
        function masterCheckboxHandler() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = masterCheckbox.checked;
            });
            updateDeleteButton();
            updateRowHighlights();
        }
        masterCheckbox.addEventListener('change', masterCheckboxHandler);
        eventListeners.push({ element: masterCheckbox, event: 'change', handler: masterCheckboxHandler });
        
        // Seleziona tutto button
        if (selectAllBtn) {
            function selectAllHandler() {
                masterCheckbox.checked = !masterCheckbox.checked;
                masterCheckbox.dispatchEvent(new Event('change'));
            }
            selectAllBtn.addEventListener('click', selectAllHandler);
            eventListeners.push({ element: selectAllBtn, event: 'click', handler: selectAllHandler });
        }
        
        // Gestione selezione singola riga
        rowCheckboxes.forEach(checkbox => {
            function checkboxHandler() {
                updateMasterCheckbox();
                updateDeleteButton();
                updateRowHighlight(this);
            }
            checkbox.addEventListener('change', checkboxHandler);
            eventListeners.push({ element: checkbox, event: 'change', handler: checkboxHandler });
        });
        
        function updateMasterCheckbox() {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            masterCheckbox.checked = checkedCount === rowCheckboxes.length;
            masterCheckbox.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
        }
        
        function updateDeleteButton() {
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            deleteBtn.disabled = checkedCount === 0;
        }
        
        function updateRowHighlight(checkbox) {
            const row = checkbox.closest('tr');
            if (checkbox.checked) {
                row.classList.add('bg-blue-50', 'dark:bg-blue-900/20');
            } else {
                row.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
            }
        }
        
        function updateRowHighlights() {
            rowCheckboxes.forEach(updateRowHighlight);
        }
        
        // Gestione eliminazione multipla
        function deleteSelectedHandler() {
            const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                .map(cb => cb.value);
            
            if (selectedIds.length === 0) return;
            
            const count = selectedIds.length;
            if (window.CoregreModals && window.CoregreModals.confirmDelete) {
                window.CoregreModals.confirmDelete(
                    count === 1 
                        ? 'Sei sicuro di voler eliminare questa riparazione?' 
                        : `Sei sicuro di voler eliminare ${count} riparazioni?`,
                    () => confirmDelete(selectedIds),
                    count
                );
            } else {
                // Fallback semplice se CoregreModals non disponibile
                if (confirm(count === 1 
                    ? 'Sei sicuro di voler eliminare questa riparazione?' 
                    : `Sei sicuro di voler eliminare ${count} riparazioni?`)) {
                    confirmDelete(selectedIds);
                }
            }
        }
        deleteBtn.addEventListener('click', deleteSelectedHandler);
        eventListeners.push({ element: deleteBtn, event: 'click', handler: deleteSelectedHandler });
    }
    
    function cleanupEventListeners() {
        eventListeners.forEach(({ element, event, handler }) => {
            element.removeEventListener(event, handler);
        });
        eventListeners = [];
    }

    // Gestione eliminazione singola - funzione globale
    window.deleteRiparazione = function(id) {
        if (window.CoregreModals && window.CoregreModals.confirmDelete) {
            window.CoregreModals.confirmDelete(
                'Sei sicuro di voler eliminare questa riparazione?',
                () => confirmDelete([id]),
                1
            );
        } else {
            // Fallback semplice se CoregreModals non disponibile
            if (confirm('Sei sicuro di voler eliminare questa riparazione?')) {
                confirmDelete([id]);
            }
        }
    };

    // Funzione di eliminazione
    async function confirmDelete(ids) {
        try {
            // Mostra notifica di caricamento
            let loadingId = null;
            if (window.CoregreNotifications && window.CoregreNotifications.loading) {
                loadingId = window.CoregreNotifications.loading('Eliminazione in corso...');
            }
            
            const response = await fetch(`<?= $this->url('/api/riparazioni/delete') ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                },
                body: JSON.stringify({ ids: ids })
            });
            
            // Rimuovi notifica di caricamento
            if (loadingId && window.CoregreNotifications && window.CoregreNotifications.remove) {
                window.CoregreNotifications.remove(loadingId);
            }
            
            if (!response.ok) {
                throw new Error(`Errore server: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            if (data.success) {
                const count = ids.length;
                const message = count === 1 
                    ? 'Riparazione eliminata con successo'
                    : `${count} riparazioni eliminate con successo`;
                    
                // Usa sistema di notifiche globale se disponibile
                if (window.showAlert) {
                    window.showAlert(message, 'success');
                } else if (window.CoregreNotifications && window.CoregreNotifications.success) {
                    window.CoregreNotifications.success(message, 3000);
                }
                
                // Ricarica la pagina con PJAX se possibile, altrimenti normale reload
                setTimeout(() => {
                    if (window.pjax) {
                        window.pjax.navigateTo(window.location.href);
                    } else {
                        window.location.reload();
                    }
                }, 1500);
            } else {
                throw new Error('Errore durante l\'eliminazione');
            }
            
        } catch (error) {
            console.error('Error deleting riparazioni:', error);
            
            // Rimuovi eventuali notifiche di caricamento
            if (window.CoregreNotifications && window.CoregreNotifications.removeByText) {
                window.CoregreNotifications.removeByText('Eliminazione in corso');
            }
            
            const errorMsg = `Errore durante l'eliminazione: ${error.message}`;
            if (window.showAlert) {
                window.showAlert(errorMsg, 'error');
            } else if (window.CoregreNotifications && window.CoregreNotifications.error) {
                window.CoregreNotifications.error(errorMsg);
            }
        }
    }

    // Registra l'inizializzatore per PJAX
    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initRiparazioniIndex);
    }

    // Inizializza anche al primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRiparazioniIndex);
    } else {
        initRiparazioniIndex();
    }
})();
</script>