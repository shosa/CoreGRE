<?php
/**
 * Riparazioni - Dashboard Principale
 * Gestione Riparazioni di Produzione
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Gestione Riparazioni
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Dashboard per la gestione delle riparazioni di produzione
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
                <span class="text-gray-700 dark:text-gray-300">Riparazioni</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Riparazioni Totali -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-tools text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Riparazioni Totali</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['totalRepairs']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Riparazioni Aperte -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-yellow-500 to-orange-500 shadow-lg">
                <i class="fas fa-folder-open text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Riparazioni Aperte</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['openRepairs']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Riparazioni Complete -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                <i class="fas fa-check-circle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Riparazioni Complete</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['completedRepairs']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Urgenza Alta -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-600 shadow-lg">
                <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Urgenza Alta</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['highUrgency']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Nuova Riparazione -->
    <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/10 dark:to-green-800/10"></div>
        <div class="relative p-8">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-plus text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Nuova Riparazione
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Crea una nuova riparazione inserendo cartellino o commessa
            </p>
            <div class="flex items-center justify-between">
                <a href="<?= $this->url('/riparazioni/create') ?>"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 font-medium transition-all duration-200">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Inizia
                </a>
            </div>
        </div>
    </div>

    <!-- Lista Riparazioni -->
    <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/10 dark:to-blue-800/10"></div>
        <div class="relative p-8">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-list text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Lista Riparazioni
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Visualizza e gestisci tutte le riparazioni esistenti
            </p>
            <div class="flex items-center justify-between">
                <a href="<?= $this->url('/riparazioni/list') ?>"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 font-medium transition-all duration-200">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Visualizza
                </a>
            </div>
        </div>
    </div>

    <!-- Statistiche -->
    <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/10 dark:to-purple-800/10"></div>
        <div class="relative p-8">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-chart-bar text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Riepilogo Urgenze
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                <span class="block"><span class="font-semibold text-red-600">Alta:</span> <?= $stats['highUrgency'] ?></span>
                <span class="block"><span class="font-semibold text-yellow-600">Media:</span> <?= $stats['mediumUrgency'] ?></span>
                <span class="block"><span class="font-semibold text-green-600">Bassa:</span> <?= $stats['lowUrgency'] ?></span>
            </p>
        </div>
    </div>
</div>

<!-- Ultime Riparazioni & Riparazioni per Reparto -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Ultime Riparazioni -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-clock mr-3 text-blue-600"></i>
                Ultime Riparazioni
            </h3>
        </div>
        <div class="p-6">
            <?php if (empty($recentRepairs)): ?>
                <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <i class="fas fa-info-circle text-4xl mb-4 block"></i>
                    Nessuna riparazione trovata
                </p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentRepairs as $repair): ?>
                        <?php
                        $urgencyColor = 'gray';
                        switch ($repair->URGENZA) {
                            case 'ALTA':
                                $urgencyColor = 'red';
                                break;
                            case 'MEDIA':
                                $urgencyColor = 'yellow';
                                break;
                            case 'BASSA':
                                $urgencyColor = 'green';
                                break;
                        }
                        $isComplete = $repair->COMPLETA == 1;
                        ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-gray-900 dark:text-white">ID: <?= htmlspecialchars($repair->IDRIP) ?></span>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-<?= $urgencyColor ?>-100 text-<?= $urgencyColor ?>-800 dark:bg-<?= $urgencyColor ?>-900/30 dark:text-<?= $urgencyColor ?>-400">
                                        <?= htmlspecialchars($repair->URGENZA) ?>
                                    </span>
                                    <?php if ($isComplete): ?>
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            Completa
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                    <?= htmlspecialchars($repair->ARTICOLO) ?>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">
                                    Cartellino: <?= htmlspecialchars($repair->CARTELLINO) ?> • <?= htmlspecialchars($repair->DATA) ?>
                                </p>
                            </div>
                            <a href="<?= $this->url('/riparazioni/' . $repair->IDRIP) ?>"
                               class="ml-4 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 text-center">
                    <a href="<?= $this->url('/riparazioni/list') ?>"
                       class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                        Vedi tutte le riparazioni <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Riparazioni per Reparto -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-industry mr-3 text-purple-600"></i>
                Riparazioni Aperte per Reparto
            </h3>
        </div>
        <div class="p-6">
            <?php if (empty($repairsByDepartment)): ?>
                <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <i class="fas fa-info-circle text-4xl mb-4 block"></i>
                    Nessuna riparazione aperta
                </p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($repairsByDepartment as $dept): ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                                    <i class="fas fa-building text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($dept->REPARTO ?: 'Non Specificato') ?>
                                </span>
                            </div>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                <?= number_format($dept->total) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
