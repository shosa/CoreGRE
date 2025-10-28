<?php
/**
 * SCM Admin - Gestione Lanci
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Gestione Lanci SCM
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Amministrazione lanci di produzione e monitoraggio avanzamenti
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?= $this->url('/scm-admin/launches/pending') ?>" 
               class="inline-flex items-center rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-amber-600 hover:to-amber-700 transition-all duration-200">
                <i class="fas fa-clock mr-2"></i>
                Lanci in Attesa
            </a>
            <a href="<?= $this->url('/scm-admin/launches/create') ?>" 
               class="inline-flex items-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i>
                Nuovo Lancio
            </a>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/') ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/scm-admin') ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    SCM Admin
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Lanci</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-rocket text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lanci Totali</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['total_launches'] ?? 0) ?>
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm">
                <span class="text-blue-600 dark:text-blue-400 font-medium">
                    <?= number_format($stats['total_pairs'] ?? 0) ?>
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">paia totali</span>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 shadow-lg">
                <i class="fas fa-clock text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In Preparazione</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['preparation_launches'] ?? 0) ?>
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm">
                <span class="text-amber-600 dark:text-amber-400 font-medium">
                    <?= number_format($stats['preparation_pairs'] ?? 0) ?>
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">paia</span>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-orange-500 to-red-600 shadow-lg">
                <i class="fas fa-cogs text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">In Lavorazione</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['processing_launches'] ?? 0) ?>
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm">
                <span class="text-orange-600 dark:text-orange-400 font-medium">
                    <?= number_format($stats['processing_pairs'] ?? 0) ?>
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">paia</span>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                <i class="fas fa-check-circle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completati</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['completed_launches'] ?? 0) ?>
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm">
                <span class="text-green-600 dark:text-green-400 font-medium">
                    <?= number_format($stats['completed_pairs'] ?? 0) ?>
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">paia</span>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-8">
    <form method="GET" action="<?= $this->url('/scm-admin/launches') ?>" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ricerca</label>
            <input type="text" 
                   name="search" 
                   value="<?= htmlspecialchars($search ?? '') ?>"
                   placeholder="Numero lancio..."
                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Laboratorio</label>
            <select name="laboratory_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-blue-500 focus:ring-blue-500">
                <option value="">Tutti i laboratori</option>
                <?php if (!empty($laboratories)): ?>
                    <?php foreach ($laboratories as $lab): ?>
                        <option value="<?= $lab->id ?>" <?= ($laboratory ?? '') == $lab->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($lab->name) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stato</label>
            <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-blue-500 focus:ring-blue-500">
                <option value="">Tutti gli stati</option>
                <option value="IN_PREPARAZIONE" <?= ($status ?? '') === 'IN_PREPARAZIONE' ? 'selected' : '' ?>>In Preparazione</option>
                <option value="IN_LAVORAZIONE" <?= ($status ?? '') === 'IN_LAVORAZIONE' ? 'selected' : '' ?>>In Lavorazione</option>
                <option value="COMPLETATO" <?= ($status ?? '') === 'COMPLETATO' ? 'selected' : '' ?>>Completato</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Periodo</label>
            <select name="period" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-blue-500 focus:ring-blue-500">
                <option value="">Tutti i periodi</option>
                <option value="today" <?= ($period ?? '') === 'today' ? 'selected' : '' ?>>Oggi</option>
                <option value="week" <?= ($period ?? '') === 'week' ? 'selected' : '' ?>>Questa settimana</option>
                <option value="month" <?= ($period ?? '') === 'month' ? 'selected' : '' ?>>Questo mese</option>
                <option value="year" <?= ($period ?? '') === 'year' ? 'selected' : '' ?>>Quest'anno</option>
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" 
                    class="flex-1 inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-2"></i>
                Cerca
            </button>
            <a href="<?= $this->url('/scm-admin/launches') ?>" 
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </form>
</div>

<!-- Launches List -->
<div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm overflow-hidden">
    <?php if (empty($launches) || $launches->count() === 0): ?>
        <div class="text-center py-12">
            <i class="fas fa-rocket text-gray-400 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessun lancio trovato</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
                <?= !empty($search ?? '') || ($status ?? '') !== '' || ($laboratory_id ?? '') !== '' ? 'Prova a modificare i filtri di ricerca.' : 'Inizia creando il tuo primo lancio.' ?>
            </p>
            <?php if (empty($search ?? '') && ($status ?? '') === '' && ($laboratory_id ?? '') === ''): ?>
                <a href="<?= $this->url('/scm-admin/launches/create') ?>" 
                   class="inline-flex items-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Crea Primo Lancio
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Lancio
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Laboratorio
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Articoli
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Fasi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Avanzamento
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Stato
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Azioni
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                    <?php if (!empty($launches) && $launches->count() > 0): ?>
                        <?php foreach ($launches as $launch): ?>
                        <?php
                        $statusColors = [
                            'IN_PREPARAZIONE' => 'bg-amber-100 text-amber-800 dark:bg-amber-800/20 dark:text-amber-300',
                            'IN_LAVORAZIONE' => 'bg-orange-100 text-orange-800 dark:bg-orange-800/20 dark:text-orange-300',
                            'COMPLETATO' => 'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300'
                        ];
                        $statusClass = $statusColors[$launch->status] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                            <!-- Lancio -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                                        <i class="fas fa-rocket text-white text-lg"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            <?= htmlspecialchars($launch->launch_number) ?>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <?= date('d/m/Y', strtotime($launch->launch_date)) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Laboratorio -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($launch->laboratory_name) ?>
                                    </div>
                                    <div class="text-gray-500 dark:text-gray-400">
                                        Laboratorio terzista
                                    </div>
                                </div>
                            </td>

                            <!-- Articoli -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        <?= $launch->total_articles ?> articoli
                                    </div>
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <?= number_format($launch->total_pairs) ?> paia
                                    </div>
                                </div>
                            </td>

                            <!-- Fasi -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        <?= $launch->total_phases ?> fasi
                                    </div>
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <?= $launch->completed_phases ?> completate
                                    </div>
                                </div>
                            </td>

                            <!-- Avanzamento -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="font-medium text-gray-900 dark:text-white"><?= $launch->completion_percentage ?>%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all" style="width: <?= $launch->completion_percentage ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Stato -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                    <?php
                                    $statusLabels = [
                                        'IN_PREPARAZIONE' => 'In Preparazione',
                                        'IN_LAVORAZIONE' => 'In Lavorazione',
                                        'COMPLETATO' => 'Completato'
                                    ];
                                    echo $statusLabels[$launch->status] ?? $launch->status;
                                    ?>
                                </span>
                            </td>

                            <!-- Azioni -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center space-x-3">
                                    <a href="<?= $this->url('/scm-admin/launches/' . $launch->id) ?>"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/40 transition-colors"
                                       title="Visualizza dettagli">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>

                                    <a href="<?= $this->url('/scm-admin/launches/' . $launch->id . '/edit') ?>"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 hover:text-green-700 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-800/40 transition-colors"
                                       title="Modifica lancio">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>

                                    <?php if ($launch->status === 'IN_PREPARAZIONE'): ?>
                                        <button onclick="deleteLaunch(<?= $launch->id ?>, '<?= htmlspecialchars($launch->launch_number) ?>')"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-700 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-800/40 transition-colors"
                                                title="Elimina lancio">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function deleteLaunch(id, launchNumber) {
    if (window.CoregreModals && window.CoregreModals.confirmDelete) {
        window.CoregreModals.confirmDelete(
            `Sei sicuro di voler eliminare il lancio "${launchNumber}"?\n\nQuesta operazione è irreversibile e eliminerà anche tutti gli articoli e i dati di avanzamento associati.`,
            () => {
                window.location.href = `<?= $this->url('/scm-admin/launches/') ?>${id}/delete`;
            },
            1
        );
    } else {
        // Fallback se CoregreModals non disponibile
        if (confirm(`Sei sicuro di voler eliminare il lancio "${launchNumber}"?\n\nQuesta operazione è irreversibile e eliminerà anche tutti gli articoli e i dati di avanzamento associati.`)) {
            window.location.href = `<?= $this->url('/scm-admin/launches/') ?>${id}/delete`;
        }
    }
}
</script>