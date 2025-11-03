<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                <i class="fas fa-mobile-alt mr-2 text-purple-500"></i>
                Dashboard InWork Mobile
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Gestisci gli operatori mobile e i permessi per i moduli dell'app CoreInWork
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="<?= $this->url('/inwork-admin/create') ?>"
                class="inline-flex items-center rounded-lg border border-primary bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-user-plus mr-2"></i>
                Nuovo Operatore
            </a>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-8" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/') ?>"
                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                    Operatori InWork
                </span>
            </div>
        </li>
    </ol>
</nav>

<!-- Stats Cards -->
<div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-users text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Totale Operatori</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $totalOperators ?></p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 shadow-lg">
                <i class="fas fa-check-circle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Operatori Attivi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $activeOperators ?></p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-rose-600 shadow-lg">
                <i class="fas fa-user-slash text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Operatori Inattivi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $inactiveOperators ?></p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
                <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Senza Moduli</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $noModules ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Grid -->
<div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
    <!-- Distribuzione Permessi Moduli -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="mb-4 flex items-center text-lg font-semibold text-gray-900 dark:text-white">
            <i class="fas fa-puzzle-piece mr-2 text-purple-500"></i>
            Distribuzione Moduli
        </h3>
        <div class="space-y-4">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>Controllo Qualità
                    </span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white"><?= $qualityCount ?></span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700">
                    <div class="h-2 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full" style="width: <?= $totalOperators > 0 ? ($qualityCount / $totalOperators * 100) : 0 ?>%"></div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        <i class="fas fa-tools text-amber-500 mr-2"></i>Riparazioni Interne
                    </span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white"><?= $repairsCount ?></span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700">
                    <div class="h-2 bg-gradient-to-r from-amber-500 to-orange-600 rounded-full" style="width: <?= $totalOperators > 0 ? ($repairsCount / $totalOperators * 100) : 0 ?>%"></div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        <i class="fas fa-layer-group text-blue-500 mr-2"></i>Entrambi i Moduli
                    </span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white"><?= $bothModules ?></span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700">
                    <div class="h-2 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full" style="width: <?= $totalOperators > 0 ? ($bothModules / $totalOperators * 100) : 0 ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribuzione per Reparto -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="mb-4 flex items-center text-lg font-semibold text-gray-900 dark:text-white">
            <i class="fas fa-building mr-2 text-indigo-500"></i>
            Distribuzione per Reparto
        </h3>
        <div class="space-y-3">
            <?php $colors = ['blue', 'green', 'purple', 'amber', 'red', 'indigo']; $index = 0; ?>
            <?php foreach (array_slice($operatorsByDepartment, 0, 5, true) as $dept => $count): ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <?= htmlspecialchars($dept) ?>
                        </span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white"><?= $count ?></span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700">
                        <div class="h-2 bg-gradient-to-r from-<?= $colors[$index % count($colors)] ?>-500 to-<?= $colors[$index % count($colors)] ?>-600 rounded-full" style="width: <?= $totalOperators > 0 ? ($count / $totalOperators * 100) : 0 ?>%"></div>
                    </div>
                </div>
                <?php $index++; ?>
            <?php endforeach; ?>
            <?php if (count($operatorsByDepartment) > 5): ?>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    + altri <?= count($operatorsByDepartment) - 5 ?> reparti
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Alerts e Azioni Rapide -->
<?php if ($operatorsWithoutContacts > 0 || $noModules > 0): ?>
<div class="mb-8">
    <?php if ($operatorsWithoutContacts > 0): ?>
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow dark:border-amber-800 dark:bg-amber-900/20 mb-4">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400 mr-3"></i>
            <div>
                <p class="text-sm font-medium text-amber-800 dark:text-amber-200">
                    <strong><?= $operatorsWithoutContacts ?></strong> operatori senza contatti (email/telefono)
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($noModules > 0): ?>
    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 shadow dark:border-red-800 dark:bg-red-900/20">
        <div class="flex items-center">
            <i class="fas fa-ban text-red-600 dark:text-red-400 mr-3"></i>
            <div>
                <p class="text-sm font-medium text-red-800 dark:text-red-200">
                    <strong><?= $noModules ?></strong> operatori senza accesso a nessun modulo
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Operatori Table -->
<div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            <i class="fas fa-list mr-2"></i>
            Elenco Operatori
        </h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 dark:bg-gray-800/60">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Username
                    </th>
                    <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Nome Completo
                    </th>
                    <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Reparto
                    </th>
                    <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Email
                    </th>
                    <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Moduli
                    </th>
                    <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                        Status
                    </th>
                    <th class="px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 text-right">
                        Azioni
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                <?php if (empty($operators) || $operators->count() === 0): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-users text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400">Nessun operatore trovato</p>
                                <a href="<?= $this->url('/inwork-admin/create') ?>"
                                    class="mt-4 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    Crea il primo operatore
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($operators as $operator): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors duration-150" data-operator-id="<?= $operator->id ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-gray-500 to-gray-600 shadow">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            <?= htmlspecialchars($operator->user) ?>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-900 dark:text-white"><?= htmlspecialchars($operator->full_name) ?></p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($operator->reparto) ?></p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($operator->email): ?>
                                    <p class="text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($operator->email) ?></p>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400 dark:text-gray-600">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-1 flex-wrap">
                                    <?php
                                    $enabledModules = $operator->modulePermissions()->where('enabled', 1)->pluck('module')->toArray();
                                    foreach ($enabledModules as $module):
                                        $colors = [
                                            'quality' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                            'repairs' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'
                                        ];
                                        $icons = [
                                            'quality' => 'fa-clipboard-check',
                                            'repairs' => 'fa-tools'
                                        ];
                                    ?>
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $colors[$module] ?>">
                                            <i class="fas <?= $icons[$module] ?> mr-1"></i>
                                            <?= ucfirst($module) ?>
                                        </span>
                                    <?php endforeach; ?>
                                    <?php if (empty($enabledModules)): ?>
                                        <span class="text-xs text-gray-400 dark:text-gray-600">Nessun modulo</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($operator->active): ?>
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Attivo
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Disattivato
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Toggle Active -->
                                    <button
                                        onclick="toggleOperator(<?= $operator->id ?>, <?= $operator->active ? 'true' : 'false' ?>)"
                                        class="rounded-lg p-2 transition-colors duration-150 <?= $operator->active ? 'text-amber-600 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-900/20' : 'text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-900/20' ?>"
                                        title="<?= $operator->active ? 'Disattiva' : 'Attiva' ?>">
                                        <i class="fas <?= $operator->active ? 'fa-toggle-on' : 'fa-toggle-off' ?> text-lg"></i>
                                    </button>

                                    <!-- Edit -->
                                    <a href="<?= $this->url('/inwork-admin/' . $operator->id . '/edit') ?>"
                                        class="rounded-lg p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 transition-colors duration-150"
                                        title="Modifica">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Delete -->
                                    <button
                                        onclick="deleteOperator(<?= $operator->id ?>, '<?= htmlspecialchars($operator->user) ?>')"
                                        class="rounded-lg p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors duration-150"
                                        title="Elimina">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript per azioni -->
<script>
async function toggleOperator(id, currentActive) {
    const action = currentActive ? 'disattivare' : 'attivare';

    if (!confirm(`Sei sicuro di voler ${action} questo operatore?`)) {
        return;
    }

    try {
        const response = await fetch('<?= $this->url('/inwork-admin/toggle') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.COREGRE?.csrfToken || ''
            },
            body: JSON.stringify({ id: id })
        });

        const data = await response.json();

        if (data.success) {
            showAlert(data.message, 'success');
            // Reload con PJAX
            if (typeof PJAX !== 'undefined') {
                PJAX.load(window.location.href);
            } else {
                location.reload();
            }
        } else {
            showAlert(data.message || 'Errore durante l\'operazione', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Errore di connessione', 'error');
    }
}

async function deleteOperator(id, username) {
    if (!confirm(`Sei sicuro di voler eliminare l'operatore "${username}"?\n\nQuesta azione non può essere annullata.`)) {
        return;
    }

    try {
        const response = await fetch('<?= $this->url('/inwork-admin/delete') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.COREGRE?.csrfToken || ''
            },
            body: JSON.stringify({ id: id })
        });

        const data = await response.json();

        if (data.success) {
            showAlert(data.message, 'success');
            // Reload con PJAX
            if (typeof PJAX !== 'undefined') {
                PJAX.load(window.location.href);
            } else {
                location.reload();
            }
        } else {
            showAlert(data.message || 'Errore durante l\'eliminazione', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Errore di connessione', 'error');
    }
}
</script>
