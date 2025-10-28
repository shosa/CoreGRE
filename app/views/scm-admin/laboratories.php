<?php
/**
 * SCM Admin - Gestione Laboratori
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Gestione Laboratori SCM
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Amministrazione laboratori terzisti e controllo accessi
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?= $this->url('/scm-admin/laboratories/create') ?>" 
               class="inline-flex items-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i>
                Nuovo Laboratorio
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
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Laboratori</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Search and Filter -->
<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-8">
    <form method="GET" action="<?= $this->url('/scm-admin/laboratories') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ricerca</label>
            <input type="text" 
                   name="search" 
                   value="<?= htmlspecialchars($search) ?>"
                   placeholder="Nome o email laboratorio..."
                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stato</label>
            <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-blue-500 focus:ring-blue-500">
                <option value="">Tutti gli stati</option>
                <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Attivi</option>
                <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Inattivi</option>
            </select>
        </div>
        <div class="md:col-span-2 flex items-end space-x-3">
            <button type="submit" 
                    class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-2"></i>
                Cerca
            </button>
            <a href="<?= $this->url('/scm-admin/laboratories') ?>" 
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-times mr-2"></i>
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Laboratories List -->
<div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm overflow-hidden">
    <?php if (empty($laboratories)): ?>
        <div class="text-center py-12">
            <i class="fas fa-building text-gray-400 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessun laboratorio trovato</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
                <?= !empty($search) || $status !== '' ? 'Prova a modificare i filtri di ricerca.' : 'Inizia creando il tuo primo laboratorio.' ?>
            </p>
            <?php if (empty($search) && $status === ''): ?>
                <a href="<?= $this->url('/scm-admin/laboratories/create') ?>" 
                   class="inline-flex items-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Crea Primo Laboratorio
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Laboratorio
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Accesso
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Attività
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Lanci
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
                    <?php foreach ($laboratories as $lab): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                            <!-- Laboratorio -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                                        <i class="fas fa-building text-white text-lg"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            <?= htmlspecialchars($lab['name']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <?= htmlspecialchars($lab['email']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Accesso -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($lab['username']) ?>
                                    </div>
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <?php if ($lab['last_login']): ?>
                                            Ultimo: <?= date('d/m/Y H:i', strtotime($lab['last_login'])) ?>
                                        <?php else: ?>
                                            Mai collegato
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <!-- Attività -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="text-gray-900 dark:text-white">
                                    <?php if ($lab['last_login']): ?>
                                        <?php 
                                        $daysAgo = floor((time() - strtotime($lab['last_login'])) / 86400);
                                        if ($daysAgo == 0): ?>
                                            <span class="text-green-600 dark:text-green-400">Oggi</span>
                                        <?php elseif ($daysAgo == 1): ?>
                                            <span class="text-yellow-600 dark:text-yellow-400">Ieri</span>
                                        <?php elseif ($daysAgo <= 7): ?>
                                            <span class="text-orange-600 dark:text-orange-400"><?= $daysAgo ?> giorni fa</span>
                                        <?php else: ?>
                                            <span class="text-red-600 dark:text-red-400"><?= $daysAgo ?> giorni fa</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">Inattivo</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Lanci -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/20 dark:text-blue-300">
                                        <?= $lab['total_launches'] ?> totali
                                    </span>
                                    <?php if ($lab['active_launches'] > 0): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-800/20 dark:text-orange-300">
                                            <?= $lab['active_launches'] ?> attivi
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Stato -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($lab['is_active']): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300">
                                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></div>
                                        Attivo
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800/20 dark:text-red-300">
                                        <div class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1"></div>
                                        Inattivo
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Azioni -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center space-x-3">
                                    <a href="<?= $this->url('/scm-admin/laboratories/' . $lab['id'] . '/edit') ?>"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 hover:text-green-700 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-800/40 transition-colors"
                                       title="Modifica laboratorio">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>

                                    <button onclick="toggleLaboratory(<?= $lab['id'] ?>, <?= $lab['is_active'] ? 'false' : 'true' ?>)"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg <?= $lab['is_active'] ? 'bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-700 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-800/40' : 'bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/40' ?> transition-colors"
                                            title="<?= $lab['is_active'] ? 'Disattiva' : 'Attiva' ?> laboratorio">
                                        <i class="fas fa-<?= $lab['is_active'] ? 'pause' : 'play' ?> text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleLaboratory(id, activate) {
    const action = activate ? 'attivare' : 'disattivare';

    if (window.WebgreModals && window.WebgreModals.confirm) {
        window.WebgreModals.confirm({
            title: activate ? 'Attiva Laboratorio' : 'Disattiva Laboratorio',
            message: `Sei sicuro di voler ${action} questo laboratorio?`,
            type: activate ? 'info' : 'warning',
            confirmText: activate ? 'Attiva' : 'Disattiva',
            onConfirm: () => {
                window.location.href = `<?= $this->url('/scm-admin/laboratories/') ?>${id}/toggle`;
            }
        });
    } else {
        // Fallback usando WebgreModals
        WebgreModals.confirm({
            message: `Sei sicuro di voler ${action} questo laboratorio?`,
            onConfirm: () => {
                window.location.href = `<?= $this->url('/scm-admin/laboratories/') ?>${id}/toggle`;
            }
        });
    }
}
</script>

<style>
.table-scroll {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}
.table-scroll::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.table-scroll::-webkit-scrollbar-track {
    background: transparent;
}
.table-scroll::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 3px;
}
</style>