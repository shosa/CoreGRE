<?php
/**
 * Tracking - Dashboard Principale
 * Monitoraggio Lotti di Produzione
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
               
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Monitoraggio Lotti di Produzione
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Tracking e gestione di lotti e cartellini
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-link text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Collegamenti Totali</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['totalLinks']) ?>
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                <i class="fas fa-cube text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lotti Attivi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['totalLots']) ?>
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-yellow-500 to-orange-500 shadow-lg">
                <i class="fas fa-tags text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tipi Tracking</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['totalTypes']) ?>
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-pink-600 shadow-lg">
                <i class="fas fa-clock text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Attività (7gg)</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['recentActivity']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Associa per Ricerca -->
    <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/10 dark:to-green-800/10"></div>
        <div class="relative p-8">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-search-plus text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Associa per Ricerca
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Utilizza i campi di ricerca per selezionare i cartellini automaticamente
            </p>
            <a href="<?= $this->url('/tracking/multisearch') ?>" 
               class="inline-flex items-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
                Inizia Ricerca
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>

    <!-- Associa Cartellini -->
    <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/10 dark:to-blue-800/10"></div>
        <div class="relative p-8">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-link text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Associa Cartellini
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Inserisci manualmente i singoli cartellini per l'associazione
            </p>
            <a href="<?= $this->url('/tracking/ordersearch') ?>" 
               class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
                Associa Manuale
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>

    <!-- Albero Dettagli -->
    <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/10 dark:to-purple-800/10"></div>
        <div class="relative p-8">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-folder-tree text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Albero Dettagli
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Visualizza la struttura gerarchica dei lotti e collegamenti
            </p>
            <a href="<?= $this->url('/tracking/treeview') ?>" 
               class="inline-flex items-center rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
                Visualizza Albero
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</div>

<!-- Secondary Functions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Gestione Lotti -->
    <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-file-invoice mr-3 text-white flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mr-3"></i>
                Gestione Lotti
            </h3>
        </div>
        <div class="p-6">
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                Gestisci dettagli lotti, info ordini e SKU mapping
            </p>
            <div class="flex space-x-3">
                <a href="<?= $this->url('/tracking/lotdetail') ?>" 
                   class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-edit mr-2 "></i>
                    Dettagli Lotti
                </a>
            </div>
        </div>
    </div>

    <!-- Report e Export -->
    <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-file-export mr-3 text-white flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 shadow-lg mr-3"></i>
                Report e Export
            </h3>
        </div>
        <div class="p-6">
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                Genera packing list, fiches cartellini e report PDF/Excel
            </p>
            <div class="flex space-x-3">
                <a href="<?= $this->url('/tracking/packinglist') ?>" 
                   class="inline-flex items-center rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700 transition-colors">
                    <i class="fas fa-list mr-2"></i>
                    Packing List
                </a>
                <a href="<?= $this->url('/tracking/makefiches') ?>" 
                   class="inline-flex items-center rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-file-alt mr-2"></i>
                    Fiches
                </a>
            </div>
        </div>
    </div>
</div>

<script>
<?= $pageScripts ?>
</script>