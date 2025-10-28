<?php
/**
 * SCM Admin - Monitoring Lanci Dettagliato
 * Vista dettagliata per il monitoraggio dei lanci con filtri e azioni
 */
?>

<?php
/**
 * SCM Admin - Monitoring Lanci Dettagliato
 * Vista dettagliata per il monitoraggio dei lanci con filtri e azioni
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                <?= htmlspecialchars($pageTitle) ?>
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                <?php if ($isLaboratoryView ?? false): ?>
                    Monitoring completo del laboratorio <?= htmlspecialchars($laboratoryDetails->name) ?> - dati in paia
                <?php else: ?>
                    Vista dettagliata di tutti i lanci in lavorazione, bloccati e completati
                <?php endif; ?>
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
        <?php if (isset($breadcrumb) && is_array($breadcrumb)): ?>
            <?php foreach ($breadcrumb as $index => $crumb): ?>
                <li class="<?= $index === 0 ? 'inline-flex items-center' : '' ?>">
                    <?php if ($index > 0): ?>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <?php endif; ?>

                    <?php if (isset($crumb['current']) && $crumb['current']): ?>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            <?= htmlspecialchars($crumb['title']) ?>
                        </span>
                    <?php else: ?>
                        <a href="<?= $this->url($crumb['url']) ?>"
                           class="<?= $index === 0 ? 'inline-flex items-center ' : '' ?>text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                            <?php if ($index === 0): ?>
                                <i class="fas fa-home mr-2"></i>
                            <?php endif; ?>
                            <?= htmlspecialchars($crumb['title']) ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($index > 0): ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ol>
</nav>

<!-- Statistiche Rapide -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <?php if ($isLaboratoryView ?? false): ?>
        <!-- Vista Laboratorio - Statistiche in PAIA -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800/40">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <i class="fas fa-play text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In Lavorazione</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        <?= number_format($stats['in_lavorazione'] ?? 0) ?> paia
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800/40">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30">
                    <i class="fas fa-ban text-red-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bloccate</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        <?= number_format($stats['bloccate'] ?? 0) ?> paia
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800/40">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completate</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        <?= number_format($stats['completate'] ?? 0) ?> paia
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800/40">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                    <i class="fas fa-flag-checkered text-purple-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Ultima Fase</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        <?= number_format($stats['ultima_fase'] ?? 0) ?> paia
                    </p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Vista Globale - Statistiche in LANCI -->
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800/40">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <i class="fas fa-rocket text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In Lavorazione</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        <?= $launches->where('status', 'IN_LAVORAZIONE')->count() ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800/40">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30">
                    <i class="fas fa-ban text-red-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bloccati</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        <?= $launches->where('status', 'BLOCCATO')->count() ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800/40">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completati</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        <?= $launches->where('status', 'COMPLETATO')->count() ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-800/40">
            <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/30">
                    <i class="fas fa-boxes text-orange-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Paia Totali</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        <?= number_format($launches->sum('total_pairs')) ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Filtri -->
<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-8">
    <form method="GET" class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-64">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Cerca per numero lancio
            </label>
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                   placeholder="Es. 7001..."
                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
        </div>

        <div class="min-w-48">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Laboratorio
            </label>
            <select name="laboratory_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">Tutti i laboratori</option>
                <?php foreach ($activeLaboratories as $lab): ?>
                    <option value="<?= $lab->id ?>" <?= ($_GET['laboratory_id'] ?? '') == $lab->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lab->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="min-w-48">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Stato
            </label>
            <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">Tutti gli stati</option>
                <option value="IN_LAVORAZIONE" <?= ($_GET['status'] ?? '') === 'IN_LAVORAZIONE' ? 'selected' : '' ?>>In Lavorazione</option>
                <option value="BLOCCATO" <?= ($_GET['status'] ?? '') === 'BLOCCATO' ? 'selected' : '' ?>>Bloccato</option>
                <option value="COMPLETATO" <?= ($_GET['status'] ?? '') === 'COMPLETATO' ? 'selected' : '' ?>>Completato</option>
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

<!-- Tabella Lanci -->
<div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-list mr-2 text-blue-500"></i>
           Lanci (<?= $launches->count() ?>)
        </h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Lancio
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Laboratorio
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Stato
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Progresso
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Articoli/Paia
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Data Lancio
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Azioni
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (!empty($launches)): ?>
                    <?php foreach ($launches as $launch): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        <a href="<?= $this->url('/scm-admin/launches/' . $launch->id) ?>"
                                           class="hover:text-blue-600 dark:hover:text-blue-400">
                                            #<?= htmlspecialchars($launch->launch_number) ?>
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <?= $launch->total_phases ?? 0 ?> fasi
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <a href="<?= $this->url('/scm-admin/monitoring/laboratories?lab=' . $launch->laboratory_id) ?>"
                                       class="hover:text-purple-600 dark:hover:text-purple-400">
                                        <?= htmlspecialchars($launch->laboratory_name) ?>
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $statusClasses = [
                                    'IN_PREPARAZIONE' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
                                    'IN_LAVORAZIONE' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                    'BLOCCATO' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                                    'COMPLETATO' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300'
                                ];
                                $statusClass = $statusClasses[$launch->status] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>">
                                    <?= ucfirst(strtolower(str_replace('_', ' ', $launch->status))) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                <?= $launch->completed_tasks ?? 0 ?>/<?= $launch->total_tasks ?? 0 ?>
                                            </span>
                                            <span class="text-xs font-medium text-gray-900 dark:text-white">
                                                <?= $launch->completion_percentage ?? 0 ?>%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full"
                                                 style="width: <?= $launch->completion_percentage ?? 0 ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        <?= $launch->total_articles ?? 0 ?> articoli
                                    </div>
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <?= number_format($launch->total_pairs ?? 0) ?> paia
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <?= date('d/m/Y', strtotime($launch->launch_date)) ?>
                                <div class="text-xs">
                                    <?php
                                    $days = (time() - strtotime($launch->launch_date)) / 86400;
                                    if ($days < 1) {
                                        echo "Oggi";
                                    } elseif ($days < 2) {
                                        echo "Ieri";
                                    } else {
                                        echo floor($days) . " giorni fa";
                                    }
                                    ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="<?= $this->url('/scm-admin/launches/' . $launch->id) ?>"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/40 transition-colors"
                                       title="Visualizza dettagli">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <?php if ($launch->status !== 'COMPLETATO'): ?>
                                        <a href="<?= $this->url('/scm-admin/launches/' . $launch->id . '/edit') ?>"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 hover:text-green-700 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-800/40 transition-colors"
                                           title="Modifica">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-400 dark:text-gray-500">
                                <i class="fas fa-search text-4xl mb-4"></i>
                                <p class="text-lg">Nessun lancio trovato</p>
                                <p class="text-sm">Prova a modificare i filtri di ricerca</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function refreshData() {
    location.reload();
}
</script>