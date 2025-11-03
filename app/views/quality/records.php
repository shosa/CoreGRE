<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="mr-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-500 to-blue-600 shadow-lg">
                    <i class="fas fa-list text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Consulto Record CQ
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Visualizza e filtra i record di controllo qualità
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
                <a href="<?= $this->url('/quality') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                    Controllo Qualità
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Consulto Record</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Filtri -->
<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-8">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
        <i class="fas fa-filter mr-2 text-blue-500"></i>
        Filtri Ricerca
    </h3>
    <form method="GET" action="<?= $this->url('/quality/records') ?>" id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Inizio</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Fine</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>"
                   class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reparto</label>
            <select name="reparto" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                <option value="">Tutti i reparti</option>
                <?php foreach ($reparti as $reparto): ?>
                    <option value="<?= htmlspecialchars($reparto->nome_reparto) ?>" <?= $selectedReparto === $reparto->nome_reparto ? 'selected' : '' ?>>
                        <?= htmlspecialchars($reparto->nome_reparto) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Operatore</label>
            <select name="operatore" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
                <option value="">Tutti gli operatori</option>
                <?php foreach ($operatori as $operatore): ?>
                    <option value="<?= htmlspecialchars($operatore) ?>" <?= $selectedOperatore === $operatore ? 'selected' : '' ?>>
                        <?= htmlspecialchars($operatore) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="lg:col-span-4 flex justify-end space-x-3">
            <button type="reset" onclick="window.location.href='<?= $this->url('/quality/records') ?>'"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-redo mr-2"></i>
                Reset
            </button>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-2"></i>
                Cerca
            </button>
        </div>
    </form>
</div>

<!-- Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow dark:border-gray-800 dark:bg-gray-800/40">
        <div class="flex items-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                <i class="fas fa-list text-blue-600 dark:text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Totale Record</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white"><?= count($records) ?></p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow dark:border-gray-800 dark:bg-gray-800/40">
        <div class="flex items-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Controlli OK</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                    <?php
                        $okCount = 0;
                        foreach ($records as $record) {
                            if ($record->qualityExceptions->count() === 0) $okCount++;
                        }
                        echo $okCount;
                    ?>
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow dark:border-gray-800 dark:bg-gray-800/40">
        <div class="flex items-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30">
                <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Con Eccezioni</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                    <?php
                        $exceptionCount = 0;
                        foreach ($records as $record) {
                            if ($record->qualityExceptions->count() > 0) $exceptionCount++;
                        }
                        echo $exceptionCount;
                    ?>
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow dark:border-gray-800 dark:bg-gray-800/40">
        <div class="flex items-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/30">
                <i class="fas fa-percentage text-orange-600 dark:text-orange-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">% Eccezioni</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                    <?php
                        $totalRecords = count($records);
                        $percentage = $totalRecords > 0 ? round(($exceptionCount / $totalRecords) * 100, 1) : 0;
                        echo $percentage . '%';
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Tabella Record -->
<div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm overflow-hidden">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-table mr-2 text-indigo-500"></i>
            Record Periodo: <?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?>
        </h3>
    </div>

    <?php if (empty($records) || count($records) === 0): ?>
        <div class="p-12 text-center">
            <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400 text-lg">Nessun record trovato con i filtri selezionati</p>
            <button onclick="window.location.href='<?= $this->url('/quality/records') ?>'"
                    class="mt-4 px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-redo mr-2"></i>
                Reset Filtri
            </button>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cartellino</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Articolo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reparto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Operatore</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Paia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Eccezioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($records as $record): ?>
                        <?php
                            $hasExceptions = $record->qualityExceptions->count() > 0;
                            $rowClass = $hasExceptions ? 'bg-red-50 dark:bg-red-900/10' : '';
                        ?>
                        <tr class="<?= $rowClass ?> hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                #<?= htmlspecialchars($record->id) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                <?= date('d/m/Y H:i', strtotime($record->data_controllo)) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                <?= htmlspecialchars($record->numero_cartellino) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                <?= htmlspecialchars($record->articolo) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                <?= htmlspecialchars($record->reparto) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                <?= htmlspecialchars($record->operatore) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                <?= htmlspecialchars($record->paia_totali) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($hasExceptions): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Con Eccezioni
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        OK
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                <?php if ($hasExceptions): ?>
                                    <div class="space-y-1">
                                        <?php foreach ($record->qualityExceptions as $exception): ?>
                                            <div class="flex items-center text-xs text-red-600 dark:text-red-400">
                                                <i class="fas fa-bug mr-1"></i>
                                                <?= htmlspecialchars($exception->tipo_difetto) ?>
                                                <?php if ($exception->paia_difettose): ?>
                                                    <span class="ml-1 text-gray-500">(<?= $exception->paia_difettose ?> paia)</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
// Consulto Record CQ - PJAX Compatible
(function() {
    'use strict';

    let formController = null;
    let isInitialized = false;

    function initRecordsPage() {
        if (isInitialized) {
            console.log('[Records Page] Already initialized, skipping');
            return;
        }
        isInitialized = true;

        if (formController) {
            formController.abort();
        }
        formController = new AbortController();
        const signal = formController.signal;

        console.log('[Records Page] Initializing...');

        // Setup form auto-submit on select change
        const form = document.getElementById('filterForm');
        if (form) {
            const selects = form.querySelectorAll('select');
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    form.submit();
                }, { signal });
            });
        }
    }

    function cleanupRecordsPage() {
        console.log('[Records Page] Cleaning up...');
        isInitialized = false;

        if (formController) {
            formController.abort();
            formController = null;
        }
    }

    // Register with PJAX
    document.addEventListener('pjax:beforeNavigate', cleanupRecordsPage);

    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initRecordsPage);
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRecordsPage);
    } else {
        initRecordsPage();
    }
})();
</script>
