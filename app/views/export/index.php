<?php
// Determina le classi per gli stati
function getStatusBadgeClass($stato) {
    switch ($stato) {
        case 'Aperto':
            return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-300';
        case 'Chiuso':
            return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300';
        default:
            return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800/20 dark:text-gray-300';
    }
}

function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('d/m/Y');
}
?>

<!-- Header con titolo e azione principale -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-truck mr-3 text-blue-500"></i>
                Registro DDT
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Gestione documenti di trasporto per terzisti
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?= $this->url('/export/create') ?>" 
               class="inline-flex items-center rounded-lg border border-transparent bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i>
                Nuovo Documento
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
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Registro DDT</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Filtri -->
<form method="GET" action="<?= $this->url('/export') ?>" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-6">
    <div class="flex items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-filter mr-3 text-blue-500"></i>
            Filtri
        </h3>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-hashtag mr-1"></i>
                Numero
            </label>
            <input type="text" 
                   name="numero"
                   value="<?= htmlspecialchars($_GET['numero'] ?? '') ?>"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="Numero documento">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-user-tie mr-1"></i>
                Destinatario
            </label>
            <input type="text" 
                   name="destinatario"
                   value="<?= htmlspecialchars($_GET['destinatario'] ?? '') ?>"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="Ragione sociale">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-calendar mr-1"></i>
                Data
            </label>
            <input type="date" 
                   name="data"
                   value="<?= htmlspecialchars($_GET['data'] ?? '') ?>"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-flag mr-1"></i>
                Stato
            </label>
            <select name="stato" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Tutti</option>
                <option value="Aperto" <?= ($_GET['stato'] ?? '') === 'Aperto' ? 'selected' : '' ?>>Aperto</option>
                <option value="Chiuso" <?= ($_GET['stato'] ?? '') === 'Chiuso' ? 'selected' : '' ?>>Chiuso</option>
            </select>
        </div>
    </div>
    
    <div class="mt-4 flex justify-end space-x-3">
        <button type="button" 
                onclick="window.location.href='<?= $this->url('/export') ?>'"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
            <i class="fas fa-times mr-2"></i>
            Reset
        </button>
        <button type="submit" 
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700">
            <i class="fas fa-search mr-2"></i>
            Filtra
        </button>
    </div>
</form>

<!-- Tabella documenti o stato vuoto -->
<?php if (empty($documents)): ?>
    <!-- Stato vuoto -->
    <div class="text-center py-12 bg-white dark:bg-gray-800/40 rounded-2xl border border-gray-200 dark:border-gray-800">
        <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
            <i class="fas fa-inbox text-6xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
            Nessun documento trovato
        </h3>
        <p class="text-gray-500 dark:text-gray-400 mb-6">
            Crea un nuovo documento facendo clic sul pulsante "Nuovo Documento".
        </p>
        <a href="<?= $this->url('/export/create') ?>" 
           class="inline-flex items-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-3 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200">
            <i class="fas fa-plus mr-2"></i>
            Nuovo Documento
        </a>
    </div>
<?php else: ?>
    <!-- Card con tabella documenti -->
    <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-file-alt mr-3 text-blue-500"></i>
                Lista Documenti
            </h3>
            <div class="flex items-center space-x-3">
                <button class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        onclick="location.reload()">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Aggiorna
                </button>
                <button class="inline-flex items-center px-3 py-2 border border-orange-300 rounded-lg text-sm font-medium text-orange-700 bg-orange-50 hover:bg-orange-100 dark:border-orange-600 dark:bg-orange-800/20 dark:text-orange-400"
                        data-modal-target="segnacolli-modal">
                    <i class="fas fa-tags mr-2"></i>
                    Stampa Segnacolli
                </button>
            </div>
        </div>
        
        <!-- Tabella responsive -->
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Numero
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Destinatario
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Data
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Stato
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Azioni
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800/40 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($documents as $doc): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($doc->id) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($doc->terzista->ragione_sociale ?? 'N/A') ?>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <?= htmlspecialchars($doc->terzista->nazione ?? 'N/A') ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-900 dark:text-white">
                                    <?= formatDate($doc->data) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="<?= getStatusBadgeClass($doc->stato) ?>">
                                    <?= htmlspecialchars($doc->stato) ?>
                                </span>
                                <?php if (!$doc->ha_articoli): ?>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800/20 dark:text-red-300">
                                        Vuoto
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <?php if ($doc->stato == 'Aperto'): ?>
                                        <!-- Documenti aperti -->
                                        <?php if (!$doc->ha_articoli): ?>
                                            <a href="<?= $this->url('/export/upload/' . $doc->id) ?>"
                                               class="inline-flex items-center px-2 py-1.5 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200 hover:text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 dark:hover:bg-yellow-800/40 transition-colors text-xs font-medium">
                                                <i class="fas fa-plus-circle mr-1.5"></i>
                                                Aggiungi
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= $this->url('/export/continue/' . $doc->id) ?>"
                                               class="inline-flex items-center px-2 py-1.5 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/40 transition-colors text-xs font-medium">
                                                <i class="fas fa-edit mr-1.5"></i>
                                                Continua
                                            </a>
                                        <?php endif; ?>

                                        <button class="inline-flex items-center px-2 py-1.5 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 hover:text-green-700 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-800/40 transition-colors text-xs font-medium"
                                                onclick="showDocumentDetails(<?= $doc->id ?>)"
                                                title="Dettagli documento">
                                            <i class="fas fa-info-circle mr-1.5"></i>
                                            Dettagli
                                        </button>

                                        <button class="inline-flex items-center px-2 py-1.5 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-700 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-800/40 transition-colors text-xs font-medium"
                                                onclick="confirmDelete(<?= $doc->id ?>)"
                                                title="Elimina documento">
                                            <i class="fas fa-trash mr-1.5"></i>
                                            Elimina
                                        </button>
                                    <?php else: ?>
                                        <!-- Documenti chiusi -->
                                        <a href="<?= $this->url('/export/view/' . $doc->id) ?>"
                                           target="_blank"
                                           class="inline-flex items-center px-2 py-1.5 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/40 transition-colors text-xs font-medium"
                                           title="Visualizza documento">
                                            <i class="fas fa-eye mr-1.5"></i>
                                            Visualizza
                                        </a>

                                        <button class="inline-flex items-center px-2 py-1.5 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 hover:text-green-700 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-800/40 transition-colors text-xs font-medium"
                                                onclick="showDocumentDetails(<?= $doc->id ?>)"
                                                title="Dettagli documento">
                                            <i class="fas fa-info-circle mr-1.5"></i>
                                            Dettagli
                                        </button>
                                    <?php endif; ?>
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
function buildFilterUrl($baseUrl, $page = null) {
    $params = [];
    if ($page) $params['page'] = $page;
    if (!empty($_GET['numero'])) $params['numero'] = $_GET['numero'];
    if (!empty($_GET['destinatario'])) $params['destinatario'] = $_GET['destinatario'];
    if (!empty($_GET['data'])) $params['data'] = $_GET['data'];
    if (!empty($_GET['stato'])) $params['stato'] = $_GET['stato'];
    
    return $baseUrl . (empty($params) ? '' : '?' . http_build_query($params));
}

if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Mostrando <?= count($documents) ?> di <?= $totalRecords ?> documenti totali
                    </div>
                    <nav class="flex items-center space-x-2">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= buildFilterUrl($this->url('/export'), $currentPage - 1) ?>" 
                               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-500 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="<?= buildFilterUrl($this->url('/export'), $i) ?>" 
                               class="inline-flex items-center px-3 py-2 border rounded-lg text-sm font-medium <?= $i == $currentPage ? 'border-blue-500 bg-blue-50 text-blue-600 dark:bg-blue-800/20 dark:text-blue-400' : 'border-gray-300 text-gray-500 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= buildFilterUrl($this->url('/export'), $currentPage + 1) ?>" 
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

<!-- Modal Dettagli Documento -->
<div id="document-details-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-99999">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-2xl bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                Lanci allegati
            </h3>
            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" onclick="closeModal('document-details-modal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mt-6">
            <div id="document-details-content" class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-4 text-gray-600 dark:text-gray-400">Caricamento dettagli...</p>
            </div>
        </div>
        <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700 mt-6">
            <button onclick="closeModal('document-details-modal')" 
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                Chiudi
            </button>
        </div>
    </div>
</div>

<!-- Modal Stampa Segnacolli -->
<div id="segnacolli-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-99999">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-lg shadow-lg rounded-2xl bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-tags mr-2 text-orange-500"></i>
                Stampa Segnacolli
            </h3>
            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" onclick="closeModal('segnacolli-modal')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="mt-6">
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                Seleziona il documento per cui generare i segnacolli:
            </p>
            <select id="segnacolli-documento" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Seleziona documento...</option>
                <?php foreach ($documents as $doc): ?>
                    <option value="<?= $doc->id ?>"
                            data-colli="<?= htmlspecialchars($doc->piede->n_colli ?? 'N/A') ?>"
                            data-ragione="<?= htmlspecialchars($doc->terzista->ragione_sociale ?? 'N/A') ?>">
                        DDT <?= $doc->id ?> - <?= htmlspecialchars($doc->terzista->ragione_sociale ?? 'N/A') ?>
                        <?= $doc->piede?->n_colli ? '(' . $doc->piede->n_colli . ' colli)' : '(colli non definiti)' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-800/20 rounded-lg">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-400 mr-2 mt-0.5"></i>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        I segnacolli verranno generati in base ai dati del documento selezionato.
                    </p>
                </div>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700 mt-6">
            <button onclick="closeModal('segnacolli-modal')" 
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                Annulla
            </button>
            <button onclick="stampaSegnacolli()" 
                    class="px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700">
                <i class="fas fa-print mr-2"></i>
                Stampa Segnacolli
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    let eventListeners = [];
    
    function initExportIndex() {
        cleanupEventListeners();
        
        // Funzioni globali per onclick inline
        window.showDocumentDetails = function(id) {
            const modal = document.getElementById('document-details-modal');
            const content = document.getElementById('document-details-content');

            // Mostra modal con loading
            CoregreModals.openModal('document-details-modal');
            content.innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">Caricamento dettagli...</p>
                </div>
            `;
            
            // Carica dettagli via AJAX
            fetch(window.COREGRE.baseUrl + '/export/getDdtDetails', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': window.COREGRE.csrfToken
                },
                body: 'id=' + encodeURIComponent(id)
            })
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                content.innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-red-500 mb-4">
                            <i class="fas fa-exclamation-triangle text-4xl"></i>
                        </div>
                        <p class="text-red-600 dark:text-red-400">Errore nel caricamento dei dettagli</p>
                    </div>
                `;
            });
        };
        
        window.confirmDelete = function(id) {
            if (window.CoregreModals && window.CoregreModals.confirmDelete) {
                window.CoregreModals.confirmDelete(
                    'Sei sicuro di voler eliminare questo documento? Questa operazione non può essere annullata!',
                    () => deleteDocument(id),
                    1
                );
            } else {
                // Fallback semplice se CoregreModals non disponibile
                if (confirm('Sei sicuro di voler eliminare questo documento? Questa operazione non può essere annullata!')) {
                    deleteDocument(id);
                }
            }
        };
        
        window.closeModal = function(modalId) {
            CoregreModals.closeModal(modalId);
        };
        
        window.stampaSegnacolli = function() {
            const select = document.getElementById('segnacolli-documento');
            const idDocumento = select.value;
            
            if (!idDocumento) {
                if (window.showAlert) {
                    window.showAlert('Seleziona un documento per procedere', 'warning');
                } else {
                    alert('Seleziona un documento per procedere');
                }
                return;
            }
            
            window.open(window.COREGRE.baseUrl + '/export/segnacolli/' + idDocumento, '_blank');
        };
        
        // Event listener per bottoni che aprono modal segnacolli
        const segnacolliButtons = document.querySelectorAll('[data-modal-target="segnacolli-modal"]');
        segnacolliButtons.forEach(button => {
            function clickHandler() {
                CoregreModals.openModal('segnacolli-modal');
                // Qui potresti caricare la lista dei documenti disponibili
            }
            button.addEventListener('click', clickHandler);
            eventListeners.push({ element: button, event: 'click', handler: clickHandler });
        });
        
        // Click fuori dal modal per chiudere
        const modals = document.querySelectorAll('[id$="-modal"]');
        modals.forEach(modal => {
            function clickHandler(e) {
                if (e.target === modal) {
                    CoregreModals.closeModal(modal.id);
                }
            }
            modal.addEventListener('click', clickHandler);
            eventListeners.push({ element: modal, event: 'click', handler: clickHandler });
        });
    }
    
    function deleteDocument(id) {
        fetch(window.COREGRE.baseUrl + '/export/delete', {
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
                    window.showAlert(data.message || 'Documento eliminato con successo', 'success');
                } else if (window.CoregreNotifications) {
                    window.CoregreNotifications.success(data.message || 'Documento eliminato con successo');
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
                window.showAlert('Errore durante l\'eliminazione del documento', 'error');
            } else if (window.CoregreNotifications) {
                window.CoregreNotifications.error('Errore durante l\'eliminazione del documento');
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
        window.COREGRE.onPageLoad(initExportIndex);
    }
    
    // Fallback primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initExportIndex);
    } else {
        initExportIndex();
    }
})();
</script>