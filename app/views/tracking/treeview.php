<?php
/**
 * Tracking - Albero Dettagli
 * Replica esatta del sistema legacy treeView.php
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="mr-4 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white p-3 rounded-2xl shadow-lg">
                    <i class="fas fa-sitemap fa-2x"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Albero Dettagli
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Visualizza e gestisce la struttura ad albero dei collegamenti di tracking
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?= $this->url('/tracking') ?>" 
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alla Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500 dark:text-gray-400">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/tracking') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-home mr-2"></i>
                Tracking
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Albero Dettagli</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Search Form -->
<div class="max-w-2xl mx-auto mb-6">
    <form id="searchForm" class="space-y-4">
        <div class="flex space-x-2">
            <input type="text" name="search_query" 
                   placeholder="Inserisci cartellino, commessa o numero lotto (usa * per visualizzare tutto)"
                   class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            <button type="submit" 
                    class="inline-flex items-center rounded-lg bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-2.5 text-sm font-medium text-white shadow-lg hover:from-indigo-600 hover:to-indigo-700 transition-all duration-200">
                <i class="fas fa-search mr-2"></i>
                Cerca
            </button>
        </div>
    </form>
</div>

<!-- Stats and Export -->
<div class="max-w-2xl mx-auto mb-6">
    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
        <span class="text-gray-600 dark:text-gray-300 text-sm">
            Sono presenti <span class="font-bold text-green-600 dark:text-green-400"><?= $stats['totalLinks'] ?></span> Associazioni
            per <span class="font-bold text-indigo-600 dark:text-indigo-400"><?= $stats['totalCartels'] ?></span> Cartellini.
        </span>
        <a href="<?= $this->url('/tracking/export-excel') ?>" 
           class="inline-flex items-center rounded-lg bg-gradient-to-r from-green-500 to-green-600 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200">
            <i class="fas fa-download mr-2"></i>
            EXCEL
        </a>
    </div>
</div>

<!-- Tree View Container -->
<div class="max-w-4xl mx-auto">
    <div class="rounded-lg border border-gray-200 bg-white shadow-lg">
        <!-- Loader -->
        <div id="loader" class="hidden p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-blue-600 text-sm">Caricamento...</p>
        </div>

        <!-- Tree Content -->
        <div class="p-4">
            <div id="treeViewPlaceholder">
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-4 text-blue-600">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <p class="text-lg mb-2">Struttura Tracking</p>
                    <p class="text-sm">Effettua una ricerca per visualizzare i risultati</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Clean Tree View */
#treeViewPlaceholder {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    background: #ffffff;
    color: #334155;
    border-radius: 8px;
    padding: 1rem;
}

#treeViewPlaceholder ul {
    list-style-type: none;
    padding-left: 0;
    margin-bottom: 0;
}

#treeViewPlaceholder ul ul {
    display: none;
    margin-left: 1.5rem;
    padding-left: 1rem;
    border-left: 1px solid #d1d5db;
}

#treeViewPlaceholder li {
    cursor: pointer;
    padding: 0.5rem 0.75rem;
    margin: 0.25rem 0;
    position: relative;
    transition: background-color 0.2s ease;
    border-radius: 4px;
}

#treeViewPlaceholder li:hover {
    background-color: #f3f4f6;
}

.dark #treeViewPlaceholder li:hover {
    background-color: #374151;
}

#treeViewPlaceholder li:before {
    content: "▶";
    margin-right: 0.5rem;
    color: #6b7280;
    font-size: 0.75rem;
    display: inline-block;
}

#treeViewPlaceholder li.collapsed:before {
    content: "▶";
}

#treeViewPlaceholder li.expanded:before {
    content: "▼";
}

#treeViewPlaceholder li.leaf:before {
    content: "•";
    color: #10b981;
}

#treeViewPlaceholder li.leaf {
    cursor: default !important;
    background-color: #f0fdf4;
}

#treeViewPlaceholder li.leaf:hover {
    background-color: #f0fdf4;
}

#treeViewPlaceholder li .timestamp {
    color: #64748b;
    font-size: 0.75rem;
    float: right;
    margin-left: 0.75rem;
    font-family: monospace;
    opacity: 0.8;
}

#treeViewPlaceholder li .lot-actions {
    display: inline-block;
    margin-left: 1rem;
    opacity: 0;
    transition: opacity 0.2s ease;
}

#treeViewPlaceholder li:hover .lot-actions {
    opacity: 1;
}

.lot-actions button {
    background: none;
    border: none;
    cursor: pointer;
    color: #64748b;
    padding: 0.25rem;
}

.lot-actions button:hover {
    color: #1e293b;
}

.dark .lot-actions button {
    color: #94a3b8;
}

.dark .lot-actions button:hover {
    color: #e2e8f0;
}


/* Simple scrollbar */
#treeViewPlaceholder::-webkit-scrollbar {
    width: 6px;
}

#treeViewPlaceholder::-webkit-scrollbar-track {
    background: #f9fafb;
}

#treeViewPlaceholder::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

#treeViewPlaceholder::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>

<!-- Edit Lot Modal -->
<div id="editLotModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 100000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Modifica Lotto</h3>
            <div class="mt-2 px-7 py-3">
                <input type="text" id="editLotInput" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <input type="hidden" id="editLotId">
            </div>
            <div class="items-center px-4 py-3">
                <button id="saveLotBtn" class="px-4 py-2 bg-indigo-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    Salva
                </button>
                 <button id="cancelEditLotBtn" class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Annulla
                </button>
            </div>
        </div>
    </div>
</div>

<script>
<?= $pageScripts ?>
</script>