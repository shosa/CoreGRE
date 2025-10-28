<?php
/**
 * SCM Admin - Monitoring Laboratori Dettagliato
 * Vista dettagliata per il monitoraggio dei laboratori con performance e statistiche
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                Monitoring Laboratori - Dettaglio
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Vista dettagliata delle performance e attività di tutti i laboratori
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?= $this->url('/scm-admin/monitoring') ?>"
                class="inline-flex items-center rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Dashboard
            </a>
            <button onclick="refreshData()"
                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
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
                <a href="<?= $this->url('/scm-admin/monitoring') ?>"
                    class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    Monitoring
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Laboratori</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Statistiche Rapide Laboratori -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800/40">
        <div class="flex items-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                <i class="fas fa-building text-purple-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Totali</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">
                    <?= count($laboratories) ?>
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800/40">
        <div class="flex items-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                <i class="fas fa-play text-green-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Attivi</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">
                    <?= $laboratories->where('active_launches', '>', 0)->count() ?>

                </p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800/40">
        <div class="flex items-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/30">
                <i class="fas fa-rocket text-orange-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lanci Totali</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">
                    <?= $laboratories->sum('total_launches') ?>

                </p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800/40">
        <div class="flex items-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                <i class="fas fa-boxes text-blue-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Paia Totali</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">
                    <?= $laboratories->sum('total_pairs') ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Filtri -->
<div
    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-8">
    <form method="GET" class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-64">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Cerca laboratorio
            </label>
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                placeholder="Nome laboratorio..."
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
        </div>

        <div class="min-w-48">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Stato Attività
            </label>
            <select name="activity"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">Tutti</option>
                <option value="active" <?= ($_GET['activity'] ?? '') === 'active' ? 'selected' : '' ?>>Solo Attivi</option>
                <option value="inactive" <?= ($_GET['activity'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inattivi
                </option>
            </select>
        </div>

        <div class="min-w-48">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Ordina per
            </label>
            <select name="sort"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="name" <?= ($_GET['sort'] ?? 'name') === 'name' ? 'selected' : '' ?>>Nome</option>
                <option value="active_launches" <?= ($_GET['sort'] ?? '') === 'active_launches' ? 'selected' : '' ?>>Lanci
                    Attivi</option>
                <option value="completion" <?= ($_GET['sort'] ?? '') === 'completion' ? 'selected' : '' ?>>% Completamento
                </option>
                <option value="total_pairs" <?= ($_GET['sort'] ?? '') === 'total_pairs' ? 'selected' : '' ?>>Paia Totali
                </option>
            </select>
        </div>

        <div class="flex items-end">
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-2"></i>
                Filtra
            </button>
        </div>
    </form>
</div>

<!-- Grid Laboratori -->
<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
    <?php if (!empty($laboratories)): ?>
        <?php foreach ($laboratories as $lab): ?>
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:shadow-xl transition-shadow">
                <!-- Header Laboratorio -->
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg">
                            <i class="fas fa-building text-white"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <?= htmlspecialchars($lab['name']) ?>
                            </h3>
                            <div class="flex items-center mt-1">
                                <?php if ($lab['active_launches'] > 0): ?>
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">Attivo</span>
                                <?php else: ?>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full mr-2"></div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Inattivo</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Azioni -->
                    <div class="flex items-center space-x-3">
                        <a href="<?= $this->url('/scm-admin/monitoring/launches?laboratory_id=' . $lab['id']) ?>"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/40 transition-colors"
                            title="Visualizza lanci">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                        <a href="<?= $this->url('/scm-admin/laboratories/' . $lab['id'] . '/edit') ?>"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 hover:text-green-700 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-800/40 transition-colors"
                            title="Modifica laboratorio">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- Statistiche Principali -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            <?= $lab['active_launches'] ?>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Lanci Attivi</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            <?= $lab['completed_launches'] ?>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Completati</div>
                    </div>
                </div>

                <!-- Progresso Medio -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Progresso Medio
                        </span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                            <?= number_format($lab['completion_percentage'] ?? 0, 1) ?>%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                        <div class="bg-gradient-to-r from-purple-500 to-blue-500 h-2 rounded-full transition-all duration-300"
                            style="width: <?= $lab['completion_percentage'] ?? 0 ?>%"></div>
                    </div>
                </div>

                <!-- Dettagli Produzione -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">
                            <i class="fas fa-boxes mr-2 text-orange-500"></i>
                            Paia Totali
                        </span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            <?= number_format($lab['total_pairs']) ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">
                            <i class="fas fa-tasks mr-2 text-blue-500"></i>
                            Task Completate
                        </span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            <?= $lab['completed_tasks'] ?>/<?= $lab['total_tasks'] ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">
                            <i class="fas fa-clock mr-2 text-green-500"></i>
                            Ultimo Accesso
                        </span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            <?php if (isset($lab['last_activity']) && $lab['last_activity']): ?>
                                <?php
                                $lastActivity = strtotime($lab['last_activity']);
                                $now = time();
                                $diff = $now - $lastActivity;

                                if ($diff < 3600) {
                                    echo floor($diff / 60) . 'm fa';
                                } elseif ($diff < 86400) {
                                    echo floor($diff / 3600) . 'h fa';
                                } else {
                                    echo floor($diff / 86400) . 'g fa';
                                }
                                ?>
                            <?php else: ?>
                                <span class="text-gray-400">Mai</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- Performance Indicator -->
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <?php
                    $performance = $lab['completion_percentage'] ?? 0;
                    if ($performance >= 80) {
                        $performanceClass = 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20';
                        $performanceIcon = 'fa-trophy';
                        $performanceText = 'Eccellente';
                    } elseif ($performance >= 60) {
                        $performanceClass = 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20';
                        $performanceIcon = 'fa-thumbs-up';
                        $performanceText = 'Buona';
                    } elseif ($performance >= 40) {
                        $performanceClass = 'text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/20';
                        $performanceIcon = 'fa-hand-paper';
                        $performanceText = 'Media';
                    } else {
                        $performanceClass = 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20';
                        $performanceIcon = 'fa-exclamation-triangle';
                        $performanceText = 'Attenzione';
                    }
                    ?>
                    <div class="flex items-center justify-center p-2 rounded-lg <?= $performanceClass ?>">
                        <i class="fas <?= $performanceIcon ?> mr-2"></i>
                        <span class="text-sm font-medium">Performance: <?= $performanceText ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Empty State -->
        <div class="col-span-full">
            <div class="text-center py-12">
                <div class="text-gray-400 dark:text-gray-500">
                    <i class="fas fa-building text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium mb-2">Nessun laboratorio trovato</h3>
                    <p class="text-sm">Prova a modificare i filtri di ricerca oppure aggiungi un nuovo laboratorio</p>
                    <div class="mt-6">
                        <a href="<?= $this->url('/scm-admin/laboratories/create') ?>"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Aggiungi Laboratorio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Performance Summary (se ci sono laboratori) -->
<?php if (!empty($laboratories)): ?>
    <div
        class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
            Riepilogo Performance
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    <?= $laboratories->filter(fn($l) => ($l->completion_percentage ?? 0) >= 80)->count() ?>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Eccellenti (≥80%)</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    <?= $laboratories->filter(fn($l) => ($l->completion_percentage ?? 0) >= 60 && ($l->completion_percentage ?? 0) < 80)->count() ?>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Buone (60-79%)</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                    <?= $laboratories->filter(fn($l) => ($l->completion_percentage ?? 0) >= 40 && ($l->completion_percentage ?? 0) < 60)->count() ?>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Medie (40-59%)</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                    <?= $laboratories->filter(fn($l) => ($l->completion_percentage ?? 0) < 40)->count() ?>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Da Migliorare (<40%)< /div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function refreshData() {
            location.reload();
        }
    </script>