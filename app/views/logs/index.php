<!-- Dashboard Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mr-4">
                    <i class="fas fa-clipboard-list text-xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Log Attività
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Monitor e analizza tutte le attività del sistema
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <?php if ($_SESSION['admin_type'] === 'admin'): ?>
                <button onclick="showCleanupModal()" 
                   class="inline-flex items-center rounded-lg border border-orange-300 bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-2 text-sm font-medium text-white hover:from-orange-600 hover:to-orange-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                    <i class="fas fa-broom mr-2"></i>
                    Pulisci Log
                </button>
            <?php endif; ?>
            <a href="<?= $this->url('/logs/export?' . http_build_query($_GET)) ?>" data-no-pjax 
               class="inline-flex items-center rounded-lg border border-green-300 bg-gradient-to-r from-green-500 to-green-600 px-4 py-2 text-sm font-medium text-white hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-file-csv mr-2"></i>
                Esporta CSV
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
                <span class="text-gray-700 dark:text-gray-300">Log Attività</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Statistiche Quick -->
<?php if (!empty($stats)): ?>
<div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Attività Oggi -->
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 dark:border-gray-800 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-calendar-day text-xl text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['today'] ?? 0 ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Oggi</p>
            </div>
        </div>
    </div>

    <!-- Attività Settimana -->
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 dark:border-gray-800 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                <i class="fas fa-calendar-week text-xl text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['week'] ?? 0 ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">7 Giorni</p>
            </div>
        </div>
    </div>

    <!-- Attività Mese -->
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 dark:border-gray-800 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 shadow-lg">
                <i class="fas fa-calendar-alt text-xl text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['month'] ?? 0 ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">30 Giorni</p>
            </div>
        </div>
    </div>

    <!-- Totale Log -->
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 dark:border-gray-800 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg">
                <i class="fas fa-database text-xl text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= number_format($totalCount ?? 0) ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Totale</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Filtri Avanzati -->
<div class="mb-8">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm" x-data="{ showAdvanced: false }">
        <form method="GET" class="space-y-4">
            <!-- Riga principale di filtri -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-search mr-1"></i>
                        Ricerca
                    </label>
                    <input type="text" name="search" value="<?= htmlspecialchars($currentSearch ?? '') ?>"
                        placeholder="Descrizione, note, query..."
                        class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-tags mr-1"></i>
                        Categoria
                    </label>
                    <select name="category" class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
                        <option value="">Tutte le categorie</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat->category) ?>" <?= ($currentCategory ?? '') === $cat->category ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat->category) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-cogs mr-1"></i>
                        Tipo Attività
                    </label>
                    <select name="activity_type" class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
                        <option value="">Tutti i tipi</option>
                        <?php foreach ($activityTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type->activity_type) ?>" <?= ($currentActivityType ?? '') === $type->activity_type ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type->activity_type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" 
                            class="flex-1 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-filter mr-2"></i>
                        Filtra
                    </button>
                    <button type="button" @click="showAdvanced = !showAdvanced"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>
            </div>

            <!-- Filtri avanzati (collassabili) -->
            <div x-show="showAdvanced" x-collapse class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-user mr-1"></i>
                            Utente
                        </label>
                        <select name="user_id" class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
                            <option value="">Tutti gli utenti</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user->id ?>" <?= ($currentUserId ?? '') == $user->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user->nome ? $user->nome . ' (' . $user->user_name . ')' : $user->user_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar mr-1"></i>
                            Data Inizio
                        </label>
                        <input type="date" name="date_from" value="<?= htmlspecialchars($currentDateFrom ?? '') ?>"
                            class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar mr-1"></i>
                            Data Fine
                        </label>
                        <input type="date" name="date_to" value="<?= htmlspecialchars($currentDateTo ?? '') ?>"
                            class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
                    </div>
                </div>

                <div class="flex justify-end mt-4 space-x-2">
                    <a href="<?= $this->url('/logs') ?>" 
                       class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Reset Filtri
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabella Log -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-clipboard-list mr-3 text-blue-500"></i>
                    Log Attività
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    <?= number_format($totalCount) ?> log trovati
                    <?php if ($currentPage > 1 || $totalPages > 1): ?>
                        (pagina <?= $currentPage ?> di <?= $totalPages ?>)
                    <?php endif; ?>
                </p>
            </div>
            <?php if ($_SESSION['admin_type'] === 'admin'): ?>
            <div class="flex items-center space-x-3">
                <button id="select-all" type="button"
                    class="rounded-lg px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-check-square mr-1"></i>
                    Seleziona tutto
                </button>
                <button type="button" id="delete-selected" disabled
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-trash-alt mr-1"></i>
                    Elimina Selezionati
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-max table-auto border-collapse">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700 text-xs uppercase text-gray-500 dark:text-gray-400">
                    <?php if ($_SESSION['admin_type'] === 'admin'): ?>
                    <th class="px-4 py-3 text-left w-12">
                        <input type="checkbox" id="master-checkbox" 
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-2">
                    </th>
                    <?php endif; ?>
                    <th class="px-4 py-3 text-left w-16">ID</th>
                    <th class="px-4 py-3 text-left w-40">Data/Ora</th>
                    <th class="px-4 py-3 text-left w-32">Utente</th>
                    <th class="px-4 py-3 text-left w-32">Categoria</th>
                    <th class="px-4 py-3 text-left w-40">Tipo</th>
                    <th class="px-4 py-3 text-left">Descrizione</th>
                    <th class="px-4 py-3 text-center w-20">Azioni</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="<?= $_SESSION['admin_type'] === 'admin' ? '8' : '7' ?>" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                                    <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium mb-2">Nessun log trovato</h3>
                                <p class="text-sm">Prova a modificare i filtri di ricerca</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <?php if ($_SESSION['admin_type'] === 'admin'): ?>
                            <td class="px-4 py-3">
                                <input type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-2" 
                                       value="<?= $log->id ?>">
                            </td>
                            <?php endif; ?>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/20 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-300">
                                    #<?= $log->id ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        <?= date('d/m/Y', strtotime($log->created_at)) ?>
                                    </div>
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <?= date('H:i:s', strtotime($log->created_at)) ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-blue-600 shadow-sm mr-3">
                                        <i class="fas fa-user text-xs text-white"></i>
                                    </div>
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            <?= htmlspecialchars($log->nome ?? $log->user->user_name ?? 'Sistema') ?>
                                        </div>
                                        <?php if ($log->user_name && $log->nome): ?>
                                        <div class="text-gray-500 dark:text-gray-400">
                                            @<?= htmlspecialchars($log->user_name) ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <?php if ($log->category): ?>
                                    <?php
                                    $categoryColors = [
                                        'SYSTEM' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300',
                                        'USER' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
                                        'AUTH' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300',
                                        'ERROR' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300',
                                        'DATA' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300'
                                    ];
                                    $colorClass = $categoryColors[$log->category] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300';
                                    ?>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $colorClass ?>">
                                        <?= htmlspecialchars($log->category) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-900 dark:text-white font-mono bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded">
                                    <?= htmlspecialchars($log->activity_type ?? '-') ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($log->description ?? '') ?>
                                    <?php if ($log->note): ?>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <?= htmlspecialchars(strlen($log->note) > 100 ? substr($log->note, 0, 100) . '...' : $log->note) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <a href="<?= $this->url('/logs/' . $log->id) ?>"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/40 transition-colors"
                                       title="Visualizza dettagli">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <?php if ($_SESSION['admin_type'] === 'admin'): ?>
                                    <button onclick="deleteLog(<?= $log->id ?>)"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-700 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-800/40 transition-colors"
                                           title="Elimina log">
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

    <!-- Paginazione -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700 dark:text-gray-300">
                Mostrando <span class="font-medium"><?= ($currentPage - 1) * $perPage + 1 ?></span> - 
                <span class="font-medium"><?= min($currentPage * $perPage, $totalCount) ?></span> di 
                <span class="font-medium"><?= number_format($totalCount) ?></span> log
            </div>
            <div class="flex items-center space-x-2">
                <?php if ($currentPage > 1): ?>
                    <a href="<?= $this->url('/logs?' . http_build_query(array_merge($_GET, ['page' => 1]))) ?>" 
                       class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    <a href="<?= $this->url('/logs?' . http_build_query(array_merge($_GET, ['page' => $currentPage - 1]))) ?>" 
                       class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        <i class="fas fa-angle-left"></i>
                    </a>
                <?php endif; ?>

                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <?php if ($i == $currentPage): ?>
                        <span class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white">
                            <?= $i ?>
                        </span>
                    <?php else: ?>
                        <a href="<?= $this->url('/logs?' . http_build_query(array_merge($_GET, ['page' => $i]))) ?>" 
                           class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= $this->url('/logs?' . http_build_query(array_merge($_GET, ['page' => $currentPage + 1]))) ?>" 
                       class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="<?= $this->url('/logs?' . http_build_query(array_merge($_GET, ['page' => $totalPages]))) ?>" 
                       class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Cleanup (solo admin) -->
<?php if ($_SESSION['admin_type'] === 'admin'): ?>
<div id="cleanup-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-99999 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/20 mr-3">
                    <i class="fas fa-broom text-orange-600 dark:text-orange-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pulizia Log</h3>
            </div>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Elimina i log più vecchi del numero di giorni specificato. 
                <strong>Questa operazione non può essere annullata.</strong>
            </p>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Elimina log più vecchi di (giorni)
                </label>
                <input type="number" id="cleanup-days" value="90" min="30" max="365"
                       class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Minimo 30 giorni per sicurezza
                </p>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="hideCleanupModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Annulla
                </button>
                <button onclick="performCleanup()" 
                        class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors">
                    <i class="fas fa-broom mr-2"></i>
                    Pulisci Log
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<script>
    // Activity Log Index - JavaScript compatibile con PJAX
    (function() {
        // Variabili globali per cleanup
        let eventListeners = [];

        // Funzione di inizializzazione
        function initActivityLogIndex() {
            // Cleanup precedenti event listeners
            cleanupEventListeners();

            const masterCheckbox = document.getElementById('master-checkbox');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const selectAllBtn = document.getElementById('select-all');
            const deleteBtn = document.getElementById('delete-selected');

            // Solo se è admin e esistono elementi
            if (masterCheckbox && deleteBtn) {
                // Seleziona/deseleziona tutto
                function masterCheckboxHandler() {
                    rowCheckboxes.forEach(checkbox => {
                        checkbox.checked = masterCheckbox.checked;
                    });
                    updateDeleteButton();
                    updateRowHighlights();
                }
                masterCheckbox.addEventListener('change', masterCheckboxHandler);
                eventListeners.push({ element: masterCheckbox, event: 'change', handler: masterCheckboxHandler });

                // Seleziona tutto button
                if (selectAllBtn) {
                    function selectAllHandler() {
                        masterCheckbox.checked = !masterCheckbox.checked;
                        masterCheckbox.dispatchEvent(new Event('change'));
                    }
                    selectAllBtn.addEventListener('click', selectAllHandler);
                    eventListeners.push({ element: selectAllBtn, event: 'click', handler: selectAllHandler });
                }

                // Gestione selezione singola riga
                rowCheckboxes.forEach(checkbox => {
                    function checkboxHandler() {
                        updateMasterCheckbox();
                        updateDeleteButton();
                        updateRowHighlight(this);
                    }
                    checkbox.addEventListener('change', checkboxHandler);
                    eventListeners.push({ element: checkbox, event: 'change', handler: checkboxHandler });
                });

                function updateMasterCheckbox() {
                    const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                    masterCheckbox.checked = checkedCount === rowCheckboxes.length;
                    masterCheckbox.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
                }

                function updateDeleteButton() {
                    const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                    deleteBtn.disabled = checkedCount === 0;
                }

                function updateRowHighlight(checkbox) {
                    const row = checkbox.closest('tr');
                    if (checkbox.checked) {
                        row.classList.add('bg-blue-50', 'dark:bg-blue-900/20');
                    } else {
                        row.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
                    }
                }

                function updateRowHighlights() {
                    rowCheckboxes.forEach(updateRowHighlight);
                }

                // Gestione eliminazione multipla
                function deleteSelectedHandler() {
                    const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                        .map(cb => cb.value);

                    if (selectedIds.length === 0) return;

                    const count = selectedIds.length;
                    if (window.CoregreModals && window.CoregreModals.confirmDelete) {
                        window.CoregreModals.confirmDelete(
                            count === 1
                                ? 'Sei sicuro di voler eliminare questo log?'
                                : `Sei sicuro di voler eliminare ${count} log?`,
                            () => confirmDelete(selectedIds),
                            count
                        );
                    } else {
                        // Fallback semplice se CoregreModals non disponibile
                        if (confirm(count === 1
                            ? 'Sei sicuro di voler eliminare questo log?'
                            : `Sei sicuro di voler eliminare ${count} log?`)) {
                            confirmDelete(selectedIds);
                        }
                    }
                }
                deleteBtn.addEventListener('click', deleteSelectedHandler);
                eventListeners.push({ element: deleteBtn, event: 'click', handler: deleteSelectedHandler });
            }
        }

        function cleanupEventListeners() {
            eventListeners.forEach(({ element, event, handler }) => {
                element.removeEventListener(event, handler);
            });
            eventListeners = [];
        }

        // Funzione unificata per ricaricare la pagina
        function reloadPage() {
            // Prova prima con PJAX globale
            if (window.pjax && typeof window.pjax.navigateTo === 'function') {
                console.log('Reloading with PJAX...');
                window.pjax.navigateTo(window.location.href);
                return;
            }

            // Fallback per PJAX legacy
            if (window.CoregrePjax && typeof window.CoregrePjax.navigateTo === 'function') {
                console.log('Reloading with CoregrePjax...');
                window.CoregrePjax.navigateTo(window.location.href);
                return;
            }

            // Prova con evento PJAX custom
            const pjaxEvent = new CustomEvent('pjax:reload', {
                detail: { url: window.location.href },
                bubbles: true
            });
            
            if (document.dispatchEvent(pjaxEvent) && pjaxEvent.defaultPrevented) {
                console.log('Reloading with custom PJAX event...');
                return;
            }

            // Ultima risorsa: reload normale
            console.log('Reloading with standard reload...');
            window.location.reload();
        }

        // Gestione eliminazione singola - funzione globale
        window.deleteLog = function (id) {
            if (window.CoregreModals && window.CoregreModals.confirmDelete) {
                window.CoregreModals.confirmDelete(
                    'Sei sicuro di voler eliminare questo log?',
                    () => confirmDelete([id]),
                    1
                );
            } else {
                // Fallback semplice se CoregreModals non disponibile
                if (confirm('Sei sicuro di voler eliminare questo log?')) {
                    confirmDelete([id]);
                }
            }
        };

        // Funzione di eliminazione
        async function confirmDelete(ids) {
            try {
                // Mostra notifica di caricamento
                let loadingId = null;
                if (window.CoregreNotifications && window.CoregreNotifications.loading) {
                    loadingId = window.CoregreNotifications.loading('Eliminazione in corso...');
                }

                const response = await fetch(`<?= $this->url('/logs/delete') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                    },
                    body: JSON.stringify({ ids: ids })
                });

                // Rimuovi notifica di caricamento
                if (loadingId && window.CoregreNotifications && window.CoregreNotifications.remove) {
                    window.CoregreNotifications.remove(loadingId);
                }

                if (!response.ok) {
                    throw new Error(`Errore server: ${response.status}`);
                }

                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                if (data.success) {
                    const count = ids.length;
                    const message = data.message || (count === 1
                        ? 'Log eliminato con successo'
                        : `${count} log eliminati con successo`);

                    // Usa sistema di notifiche globale se disponibile
                    if (window.showAlert) {
                        window.showAlert(message, 'success');
                    } else if (window.CoregreNotifications && window.CoregreNotifications.success) {
                        window.CoregreNotifications.success(message, 3000);
                    }

                    // Rimuovi le righe eliminate visivamente per feedback immediato
                    ids.forEach(id => {
                        const row = document.querySelector(`.row-checkbox[value="${id}"]`)?.closest('tr');
                        if (row) {
                            row.style.transition = 'opacity 0.3s ease';
                            row.style.opacity = '0.3';
                            setTimeout(() => row.remove(), 300);
                        }
                    });

                    // Ricarica la pagina per aggiornare contatori e paginazione
                    setTimeout(() => {
                        reloadPage();
                    }, 800);
                } else {
                    throw new Error('Errore durante l\'eliminazione');
                }

            } catch (error) {
                console.error('Error deleting logs:', error);

                // Rimuovi eventuali notifiche di caricamento
                if (window.CoregreNotifications && window.CoregreNotifications.removeByText) {
                    window.CoregreNotifications.removeByText('Eliminazione in corso');
                }

                const errorMsg = `Errore durante l'eliminazione: ${error.message}`;
                if (window.showAlert) {
                    window.showAlert(errorMsg, 'error');
                } else if (window.CoregreNotifications && window.CoregreNotifications.error) {
                    window.CoregreNotifications.error(errorMsg);
                }
            }
        }

        // Modal cleanup functions - solo per admin
        window.showCleanupModal = function() {
            CoregreModals.openModal('cleanup-modal');
        };

        window.hideCleanupModal = function() {
            CoregreModals.closeModal('cleanup-modal');
        };

        window.performCleanup = async function() {
            const daysInput = document.getElementById('cleanup-days');
            const days = parseInt(daysInput?.value) || 90;

            if (days < 30) {
                if (window.showAlert) {
                    window.showAlert('Minimo 30 giorni richiesti per sicurezza', 'error');
                } else {
                    alert('Minimo 30 giorni richiesti per sicurezza');
                }
                return;
            }

            try {
                let loadingId = null;
                if (window.CoregreNotifications && window.CoregreNotifications.loading) {
                    loadingId = window.CoregreNotifications.loading('Pulizia in corso...');
                }

                const response = await fetch(`<?= $this->url('/logs/cleanup') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                    },
                    body: JSON.stringify({ days: days })
                });

                if (loadingId && window.CoregreNotifications && window.CoregreNotifications.remove) {
                    window.CoregreNotifications.remove(loadingId);
                }

                if (!response.ok) {
                    throw new Error(`Errore server: ${response.status}`);
                }

                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                const message = data.message || 'Pulizia completata con successo';
                if (window.showAlert) {
                    window.showAlert(message, 'success');
                } else if (window.CoregreNotifications && window.CoregreNotifications.success) {
                    window.CoregreNotifications.success(message, 3000);
                }

                // Chiudi modal e ricarica
                window.hideCleanupModal();
                setTimeout(() => {
                    reloadPage();
                }, 1000);

            } catch (error) {
                console.error('Error during cleanup:', error);
                
                if (window.CoregreNotifications && window.CoregreNotifications.removeByText) {
                    window.CoregreNotifications.removeByText('Pulizia in corso');
                }

                const errorMsg = `Errore durante la pulizia: ${error.message}`;
                if (window.showAlert) {
                    window.showAlert(errorMsg, 'error');
                } else if (window.CoregreNotifications && window.CoregreNotifications.error) {
                    window.CoregreNotifications.error(errorMsg);
                }
            }
        };

        // Registra l'inizializzatore per PJAX
        if (window.COREGRE && window.COREGRE.onPageLoad) {
            window.COREGRE.onPageLoad(initActivityLogIndex);
        }

        // Inizializza anche al primo caricamento
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initActivityLogIndex);
        } else {
            initActivityLogIndex();
        }
    })();
</script>