<?php
/**
 * Export - Gestione Terzisti
 * Anagrafica destinatari DDT
 */
?>


<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-building mr-3 text-orange-500"></i>
                Gestione Terzisti
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Anagrafica destinatari per i documenti di trasporto
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?= $this->url('/export/terzisti/create') ?>" 
               class="inline-flex items-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-3 text-sm font-medium text-white hover:from-orange-600 hover:to-orange-700 shadow-md hover:shadow-lg transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                Nuovo Terzista
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
                <a href="<?= $this->url('/export/dashboard') ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    Export
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Gestione Terzisti</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Filtri -->
<form method="GET" action="<?= $this->url('/export/terzisti') ?>" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-6">
    <div class="flex items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-filter mr-3 text-orange-500"></i>
            Filtri
        </h3>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-building mr-1"></i>
                Ragione Sociale
            </label>
            <input type="text" 
                   name="ragione_sociale"
                   value="<?= htmlspecialchars($_GET['ragione_sociale'] ?? '') ?>"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                   placeholder="Nome azienda">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-globe mr-1"></i>
                Nazione
            </label>
            <input type="text" 
                   name="nazione"
                   value="<?= htmlspecialchars($_GET['nazione'] ?? '') ?>"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                   placeholder="Nazione">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-truck mr-1"></i>
                Consegna
            </label>
            <input type="text" 
                   name="consegna"
                   value="<?= htmlspecialchars($_GET['consegna'] ?? '') ?>"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                   placeholder="Modalità consegna">
        </div>
    </div>
    
    <div class="mt-4 flex justify-end space-x-3">
        <button type="button" 
                onclick="window.location.href='<?= $this->url('/export/terzisti') ?>'"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
            <i class="fas fa-times mr-2"></i>
            Reset
        </button>
        <button type="submit" 
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700">
            <i class="fas fa-search mr-2"></i>
            Filtra
        </button>
    </div>
</form>

<!-- Tabella terzisti o stato vuoto -->
<?php if (empty($terzisti)): ?>
    <!-- Stato vuoto -->
    <div class="text-center py-12 bg-white dark:bg-gray-800/40 rounded-2xl border border-gray-200 dark:border-gray-800">
        <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
            <i class="fas fa-building text-6xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
            Nessun terzista trovato
        </h3>
        <p class="text-gray-500 dark:text-gray-400 mb-6">
            Crea un nuovo terzista facendo clic sul pulsante "Nuovo Terzista".
        </p>
        <a href="<?= $this->url('/export/terzisti/create') ?>" 
           class="inline-flex items-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-3 text-sm font-medium text-white hover:from-orange-600 hover:to-orange-700 shadow-md hover:shadow-lg transition-all duration-200">
            <i class="fas fa-plus mr-2"></i>
            Nuovo Terzista
        </a>
    </div>
<?php else: ?>
    <!-- Card con tabella terzisti -->
    <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-building mr-3 text-orange-500"></i>
                Lista Terzisti
            </h3>
            <div class="flex items-center space-x-3">
                <button class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        onclick="location.reload()">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Aggiorna
                </button>
            </div>
        </div>
        
        <!-- Tabella responsive -->
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Ragione Sociale
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Indirizzo
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Nazione
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Consegna
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Azioni
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800/40 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($terzisti as $terzista): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($terzista->id) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($terzista->ragione_sociale) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($terzista->indirizzo_1 ?? '') ?>
                                    <?php if ($terzista->indirizzo_2): ?>
                                        <br><span class="text-gray-600 dark:text-gray-400"><?= htmlspecialchars($terzista->indirizzo_2) ?></span>
                                    <?php endif; ?>
                                    <?php if ($terzista->indirizzo_3): ?>
                                        <br><span class="text-gray-600 dark:text-gray-400"><?= htmlspecialchars($terzista->indirizzo_3) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($terzista->nazione ?? 'N/A') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($terzista->consegna ?? 'N/A') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="<?= $this->url('/export/terzisti/' . $terzista->id . '/edit') ?>"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/40 transition-colors"
                                       title="Modifica terzista">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>

                                    <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-700 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-800/40 transition-colors"
                                            onclick="confirmDeleteTerzista(<?= $terzista->id ?>)"
                                            title="Elimina terzista">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginazione -->
        <?php 
        // Helper per costruire URL con filtri
        function buildTerzistiFilterUrl($baseUrl, $page = null) {
            $params = [];
            if ($page) $params['page'] = $page;
            if (!empty($_GET['ragione_sociale'])) $params['ragione_sociale'] = $_GET['ragione_sociale'];
            if (!empty($_GET['nazione'])) $params['nazione'] = $_GET['nazione'];
            if (!empty($_GET['consegna'])) $params['consegna'] = $_GET['consegna'];
            
            return $baseUrl . (empty($params) ? '' : '?' . http_build_query($params));
        }

        if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Mostrando <?= count($terzisti) ?> di <?= $totalRecords ?> terzisti totali
                    </div>
                    <nav class="flex items-center space-x-2">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= buildTerzistiFilterUrl($this->url('/export/terzisti'), $currentPage - 1) ?>" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-500 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="<?= buildTerzistiFilterUrl($this->url('/export/terzisti'), $i) ?>" 
                               class="inline-flex items-center px-3 py-2 border rounded-lg text-sm font-medium <?= $i == $currentPage ? 'border-orange-500 bg-orange-50 text-orange-600 dark:bg-orange-800/20 dark:text-orange-400' : 'border-gray-300 text-gray-500 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= buildTerzistiFilterUrl($this->url('/export/terzisti'), $currentPage + 1) ?>" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-500 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
(function() {
    'use strict';
    
    let eventListeners = [];

    function initTerzistiIndex() {
        // Cleanup existing listeners
        cleanupEventListeners();
    }
    
    window.confirmDeleteTerzista = function(id) {
        if (window.CoregreModals && window.CoregreModals.confirmDelete) {
            window.CoregreModals.confirmDelete(
                'Sei sicuro di voler eliminare questo terzista? Questa operazione non può essere annullata!',
                () => deleteTerzista(id),
                1
            );
        } else {
            // Fallback semplice se CoregreModals non disponibile
            if (confirm('Sei sicuro di voler eliminare questo terzista? Questa operazione non può essere annullata!')) {
                deleteTerzista(id);
            }
        }
    };
    
    function deleteTerzista(id) {
        fetch(window.COREGRE.baseUrl + '/export/terzisti/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + encodeURIComponent(id)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.showAlert) {
                    window.showAlert(data.message || 'Terzista eliminato con successo', 'success');
                } else if (window.CoregreNotifications) {
                    window.CoregreNotifications.success(data.message || 'Terzista eliminato con successo');
                }
                // Ricarica la pagina dopo un breve delay
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                if (window.showAlert) {
                    window.showAlert(data.message || 'Errore durante l\'eliminazione', 'error');
                } else if (window.CoregreNotifications) {
                    window.CoregreNotifications.error(data.message || 'Errore durante l\'eliminazione');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (window.showAlert) {
                window.showAlert('Errore durante l\'eliminazione del terzista', 'error');
            } else if (window.CoregreNotifications) {
                window.CoregreNotifications.error('Errore durante l\'eliminazione del terzista');
            }
        });
    }
    
    function cleanupEventListeners() {
        eventListeners.forEach(({ element, event, handler }) => {
            element.removeEventListener(event, handler);
        });
        eventListeners = [];
    }
    
    // Registrazione PJAX
    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initTerzistiIndex);
    }
    
    // Fallback primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTerzistiIndex);
    } else {
        initTerzistiIndex();
    }
})();
</script>