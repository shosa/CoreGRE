<?php
/**
 * SCM Admin - Dashboard Principale
 * Supply Chain Management - Gestione Laboratori e Lanci
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-industry mr-3 text-purple-500"></i>
                SCM Admin Dashboard
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Gestione completa del Supply Chain Management
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?= $this->url('/scm-admin/launches/create') ?>"
                class="inline-flex items-center rounded-lg border border-transparent bg-gradient-to-r from-purple-500 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-purple-600 hover:to-purple-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-rocket mr-2"></i>
                Nuovo Lancio
            </a>
            <a href="<?= $this->url('/scm-admin/laboratories/create') ?>"
                class="inline-flex items-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm hover:shadow-md transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                Nuovo Laboratorio
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
                <span class="text-gray-700 dark:text-gray-300">SCM Admin</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Laboratori Attivi -->
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-industry text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Laboratori Attivi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['total_laboratories']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Lanci in Preparazione -->
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 shadow-lg">
                <i class="fas fa-clock text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['pending_launches']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Lanci in Lavorazione -->
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                <i class="fas fa-cogs text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In Lavorazione</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['active_launches']) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Lanci Bloccati -->
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-600 shadow-lg">
                <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bloccati</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['blocked_launches']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Menu Rapido -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-tachometer-alt mr-2 text-purple-500"></i>
            Gestione
        </h3>
        <div class="space-y-3">
            <a href="<?= $this->url('/scm-admin/laboratories') ?>"
                class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 group">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 shadow-sm group-hover:shadow-md transition-shadow">
                    <i class="fas fa-industry text-white"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Laboratori</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Gestisci terzisti</p>
                </div>
            </a>

            <a href="<?= $this->url('/scm-admin/launches') ?>"
                class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200 group">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 shadow-sm group-hover:shadow-md transition-shadow">
                    <i class="fas fa-rocket text-white"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Lanci</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Gestisci lanci</p>
                </div>
            </a>
            <a href="<?= $this->url('/scm-admin/settings') ?>"
                class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-yellow-300 dark:hover:border-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-all duration-200 group">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 shadow-sm group-hover:shadow-md transition-shadow">
                    <i class="fas fa-cog text-white"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Impostazioni</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Parametri generali</p>
                </div>
            </a>
        </div>
    </div>

    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-tasks mr-2 text-orange-500"></i>
            Azioni Rapide
        </h3>
        <div class="space-y-3">
            <a href="<?= $this->url('/scm-admin/launches/pending') ?>"
                class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-orange-300 dark:hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all duration-200 group">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 shadow-sm group-hover:shadow-md transition-shadow">
                    <i class="fas fa-clock text-white"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Lanci Pending</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Avvia lanci</p>
                </div>
            </a>
            <a href="<?= $this->url('/scm-admin/standard-phases') ?>"
                class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-teal-300 dark:hover:border-teal-500 hover:bg-teal-50 dark:hover:bg-teal-900/20 transition-all duration-200 group">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-teal-500 to-teal-600 shadow-sm group-hover:shadow-md transition-shadow">
                    <i class="fas fa-stairs text-white"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Gestione Cicli</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Fasi Standard</p>
                </div>
            </a>
        </div>

    </div>

    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-chart-line mr-2 text-green-500"></i>
            Monitoraggio Specifico
        </h3>
        <div class="space-y-3">
            <a href="<?= $this->url('/scm-admin/monitoring') ?>"
                class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200 group">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 shadow-sm group-hover:shadow-md transition-shadow">
                    <i class="fas fa-clock text-white"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Monitor Generale</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Statistiche generali</p>
                </div>
            </a>
            <a href="<?= $this->url('/scm-admin/monitoring/launches') ?>"
                class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-green-300 dark:hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 transition-all duration-200 group">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-green-600 shadow-sm group-hover:shadow-md transition-shadow">
                    <i class="fas fa-chart-area text-white"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Monitor Lanci</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Stato lanci</p>
                </div>
            </a>

            <a href="<?= $this->url('/scm-admin/monitoring/laboratories') ?>"
                class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-indigo-300 dark:hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-200 group">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-indigo-500 to-indigo-600 shadow-sm group-hover:shadow-md transition-shadow">
                    <i class="fas fa-chart-pie text-white"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Monitor Lab</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Performance</p>
                </div>
            </a>

        </div>
    </div>
</div>

<!-- Lanci Recenti -->
<div
    class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-history mr-2 text-purple-500"></i>
            Lanci Recenti
        </h3>
        <a href="<?= $this->url('/scm-admin/launches') ?>"
            class="inline-flex items-center px-3 py-2 text-sm font-medium text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-all duration-200">
            Vedi tutti <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>

    <div class="overflow-x-auto">
        <?php if (empty($recentLaunches)): ?>
            <div class="p-12 text-center">
                <div class="text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox text-4xl mb-4 opacity-50"></i>
                    <p class="text-lg font-medium">Nessun lancio presente</p>
                    <p class="text-sm mt-2">Inizia creando il primo lancio nel sistema</p>
                    <a href="<?= $this->url('/scm-admin/launches/create') ?>"
                        class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Crea il primo lancio
                    </a>
                </div>
            </div>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Lancio</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Laboratorio</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Data</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Stato</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Articoli</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Paia</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($recentLaunches as $launch): ?>
                        <?php
                        $statusColors = [
                            'IN_PREPARAZIONE' => 'bg-gray-100 text-gray-800 dark:bg-gray-800/20 dark:text-gray-300',
                            'IN_LAVORAZIONE' => 'bg-blue-100 text-blue-800 dark:bg-blue-800/20 dark:text-blue-300',
                            'BLOCCATO' => 'bg-red-100 text-red-800 dark:bg-red-800/20 dark:text-red-300',
                            'COMPLETATO' => 'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300'
                        ];
                        $statusClass = $statusColors[$launch->status] ?? $statusColors['IN_PREPARAZIONE'];
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($launch->launch_number) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($launch->laboratory_name ?? 'N/A') ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?= date('d/m/Y', strtotime($launch->launch_date)) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                    <?= htmlspecialchars($launch->status) ?>
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 dark:text-white">
                                <?= number_format($launch->total_articles) ?>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 dark:text-white">
                                <?= number_format($launch->total_pairs) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>