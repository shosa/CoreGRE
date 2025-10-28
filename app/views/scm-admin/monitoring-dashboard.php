<?php
/**
 * SCM Admin - Monitoring Dashboard
 * Dashboard panoramica con statistiche generali e link ai dettagli
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                Monitoring SCM - Dashboard
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Panoramica generale del sistema SCM con statistiche in tempo reale
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button onclick="refreshDashboard()"
                class="inline-flex items-center rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                <i class="fas fa-sync-alt mr-2"></i>
                Aggiorna
            </button>

        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/') ?>"
                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/scm-admin') ?>"
                    class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    SCM Admin
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Monitoring</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Statistiche Principali -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Lanci Attivi -->
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-rocket text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lanci Attivi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($generalStats['active_launches'] ?? 0) ?>
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm">
                <span class="text-green-600 dark:text-green-400 font-medium">
                    +<?= $generalStats['launches_today'] ?? 0 ?>
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">oggi</span>
            </div>
        </div>
    </div>

    <!-- Laboratori Attivi -->
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg">
                <i class="fas fa-building text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Laboratori Attivi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($generalStats['active_laboratories'] ?? 0) ?>
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm">
                <span class="text-gray-900 dark:text-white font-medium">
                    <?= $generalStats['total_laboratories'] ?? 0 ?>
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">totali</span>
            </div>
        </div>
    </div>

    <!-- Task Completate -->
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                <i class="fas fa-check-circle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Task Completate</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($generalStats['completed_tasks'] ?? 0) ?>
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm">
                <span class="text-gray-900 dark:text-white font-medium">
                    <?= number_format($generalStats['total_tasks'] ?? 0) ?>
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">totali</span>
            </div>
        </div>
    </div>

    <!-- Paia in Produzione -->
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 shadow-lg">
                <i class="fas fa-boxes text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Paia in Produzione</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($generalStats['total_pairs_in_production'] ?? 0) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Azioni Principali -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Link ai Dettagli -->
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-chart-line mr-2 text-blue-500"></i>
            Monitoring Dettagliato
        </h3>
        <div class="space-y-3">
            <a href="<?= $this->url('/scm-admin/monitoring/launches') ?>"
                class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors group">
                <div class="flex items-center">
                    <i class="fas fa-rocket text-blue-500 mr-3"></i>
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">Monitor Lanci</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Vista dettagliata dei lanci attivi</div>
                    </div>
                </div>
                <i class="fas fa-arrow-right text-blue-500 group-hover:translate-x-1 transition-transform"></i>
            </a>

            <a href="<?= $this->url('/scm-admin/monitoring/laboratories') ?>"
                class="flex items-center justify-between p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors group">
                <div class="flex items-center">
                    <i class="fas fa-building text-purple-500 mr-3"></i>
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">Monitor Laboratori</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Vista dettagliata dei laboratori</div>
                    </div>
                </div>
                <i class="fas fa-arrow-right text-purple-500 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>

    <!-- Attività Recenti -->
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-clock mr-2 text-green-500"></i>
            Attività Recenti (24h)
        </h3>
        <div class="space-y-3" style="max-height: 300px; overflow-y: auto;">
            <?php if (!empty($recentActivity)): ?>
                <?php foreach ($recentActivity as $activity): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">
                                Lancio <?= htmlspecialchars($activity['launch_number']) ?>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <?= htmlspecialchars($activity['laboratory_name']) ?>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                +<?= $activity['tasks_completed_24h'] ?> task
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-clock text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Nessuna attività nelle ultime 24h</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Sezione Criticità -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Top Laboratori -->
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-trophy mr-2 text-yellow-500"></i>
                Top 5 Laboratori
            </h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">Per attività</span>
        </div>

        <div class="space-y-4">
            <?php if (!empty($topLaboratories)): ?>
                <?php foreach ($topLaboratories as $index => $lab): ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center">
                            <div
                                class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-yellow-400 to-yellow-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                <?= $index + 1 ?>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    <a href="<?= $this->url('/scm-admin/monitoring/laboratories?lab=' . $lab['id']) ?>"
                                        class="hover:text-blue-600 dark:hover:text-blue-400">
                                        <?= htmlspecialchars($lab['name']) ?>
                                    </a>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <?= $lab['active_launches'] ?> lanci • <?= number_format($lab['total_pairs']) ?> paia
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                <?= $lab['avg_completion'] ?>%
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-building text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Nessun laboratorio attivo</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lanci Critici -->
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>
                Lanci Critici
            </h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">Bloccati o in ritardo</span>
        </div>

        <div class="space-y-4" style="max-height: 400px; overflow-y: auto;">
            <?php if (!empty($criticalLaunches)): ?>
                <?php foreach ($criticalLaunches as $launch): ?>
                    <div class="flex items-center justify-between p-4
                        <?= $launch['status'] === 'BLOCCATO' ? 'bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500' : 'bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500' ?>
                        rounded-lg">
                        <div class="flex items-center">
                            <div class="mr-3">
                                <?php if ($launch['status'] === 'BLOCCATO'): ?>
                                    <i class="fas fa-ban text-red-500"></i>
                                <?php else: ?>
                                    <i class="fas fa-clock text-yellow-500"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    <a href="<?= $this->url('/scm-admin/launches/' . $launch['id']) ?>"
                                        class="hover:text-blue-600 dark:hover:text-blue-400">
                                        Lancio <?= htmlspecialchars($launch['launch_number']) ?>
                                    </a>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <?= htmlspecialchars($launch['laboratory_name']) ?> •
                                    <?= date('d/m/Y', strtotime($launch['launch_date'])) ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                <?= $launch['completed_tasks'] ?>/<?= $launch['total_tasks'] ?>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <?= ucfirst(strtolower(str_replace('_', ' ', $launch['status']))) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-green-400 text-3xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Nessun lancio critico</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function refreshDashboard() {
        location.reload();
    }

    // Auto-refresh ogni 5 minuti
    setInterval(refreshDashboard, 300000);
</script>