<?php
/**
 * Export - Dashboard Principale
 * Gestione Documenti di Trasporto (DDT)
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Gestione Export DDT
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Dashboard per la gestione dei Documenti di Trasporto
                    </p>
                </div>
            </div>
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
                <span class="text-gray-700 dark:text-gray-300">Export</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Documenti Totali -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-file-alt text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Documenti Totali</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['totalDocuments']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Documenti Aperti -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                <i class="fas fa-folder-open text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Documenti Aperti</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['openDocuments']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Terzisti Attivi -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-yellow-500 to-orange-500 shadow-lg">
                <i class="fas fa-building text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Terzisti Attivi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['activeTerzisti']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Articoli Gestiti -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-pink-600 shadow-lg">
                <i class="fas fa-boxes text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Articoli Gestiti</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['totalArticles']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Nuovo Documento -->
    <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/10 dark:to-green-800/10"></div>
        <div class="relative p-8">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-plus text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Nuovo Documento
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Crea un nuovo documento DDT partendo da file Excel
            </p>
            <div class="flex items-center justify-between">
                <a href="<?= $this->url('/export/create') ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 font-medium">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Inizia
                </a>
            </div>
        </div>
    </div>

    <!-- Lista Documenti -->
    <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/10 dark:to-blue-800/10"></div>
        <div class="relative p-8">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-list text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Lista Documenti
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Visualizza e gestisci tutti i documenti DDT esistenti
            </p>
            <div class="flex items-center justify-between">
                <a href="<?= $this->url('/export') ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 font-medium">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Visualizza
                </a>
            </div>
        </div>
    </div>

    <!-- Gestione Terzisti -->
    <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/10 dark:to-orange-800/10"></div>
        <div class="relative p-8">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-building text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Gestione Terzisti
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Gestisci l'anagrafica dei terzisti e destinatari
            </p>
            <div class="flex items-center justify-between">
                <a href="<?= $this->url('/export/terzisti') ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 font-medium">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Gestisci
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Documenti Recenti -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-clock mr-2 text-blue-500"></i>
                Documenti Recenti
            </h3>
            <a href="<?= $this->url('/export') ?>" 
               class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Vedi tutti
            </a>
        </div>
        <div class="space-y-4">
            <?php if (!empty($recentDocuments)): ?>
                <?php foreach ($recentDocuments as $doc): ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/50">
                                <i class="fas fa-file-alt text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-gray-900 dark:text-white">DDT #<?= $doc->id ?></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($doc->terzista->ragione_sociale) ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?= $doc->stato === 'Aperto' ? 'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' ?>">
                                <?= $doc->stato ?>
                            </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><?= date('d/m/Y', strtotime($doc['data'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 dark:text-gray-400">Nessun documento recente</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-chart-pie mr-2 text-green-500"></i>
                Statistiche Veloci
            </h3>
        </div>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/50">
                        <i class="fas fa-calendar-week text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900 dark:text-white">Questa Settimana</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Nuovi documenti</p>
                    </div>
                </div>
                <span class="text-xl font-bold text-gray-900 dark:text-white"><?= $stats['weeklyDocuments'] ?></span>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/50">
                        <i class="fas fa-calendar-days text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900 dark:text-white">Questo Mese</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Documenti completati</p>
                    </div>
                </div>
                <span class="text-xl font-bold text-gray-900 dark:text-white"><?= $stats['monthlyCompleted'] ?></span>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/50">
                        <i class="fas fa-boxes text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900 dark:text-white">Media Colli</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Per documento</p>
                    </div>
                </div>
                <span class="text-xl font-bold text-gray-900 dark:text-white"><?= number_format($stats['avgColli'], 1) ?></span>
            </div>
        </div>
    </div>
</div>