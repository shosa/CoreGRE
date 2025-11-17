<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="mr-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                    <i class="fas fa-gem text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Controllo Qualità
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400 header-date">
                        Sistema CQ Hermes - <?= date('d/m/Y', strtotime($selectedDate)) ?>
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
                <span class="text-gray-700 dark:text-gray-300">Controllo Qualità</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 shadow-lg">
                <i class="fas fa-calendar-day text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Record Oggi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white stats-value" id="today-records">
                    <?= number_format($stats['today_records']) ?>
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-calendar-week text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Record Settimana</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white stats-value" id="week-records">
                    <?= number_format($stats['week_records']) ?>
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-600 shadow-lg">
                <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Eccezioni Mese</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white stats-value" id="month-exceptions">
                    <?= number_format($stats['month_exceptions']) ?>
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-indigo-500 to-blue-600 shadow-lg">
                <i class="fas fa-chart-line text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Reparti Attivi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white stats-value" id="active-departments">
                    <?= number_format($stats['active_departments']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Consulto Record -->
    <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/10 dark:to-indigo-800/10"></div>
        <div class="relative p-8">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-list text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Consulto Record
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Visualizza e filtra tutti i record di controllo qualità
            </p>
            <a href="<?= $this->url('/quality/records') ?>"
                class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium group-hover:translate-x-1 transition-transform">
                Consulta Record
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>

    <!-- Report CQ -->
    <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-teal-100 dark:from-green-900/10 dark:to-teal-800/10"></div>
        <div class="relative p-8">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-green-500 to-teal-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-file-alt text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Report CQ
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Genera report giornalieri e periodici PDF ed Excel
            </p>
            <a href="<?= $this->url('/quality/reports') ?>"
                class="inline-flex items-center text-green-600 hover:text-green-700 font-medium group-hover:translate-x-1 transition-transform">
                Genera Report
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>

    <!-- Gestione -->
    <div class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-red-100 dark:from-orange-900/10 dark:to-red-800/10"></div>
        <div class="relative p-8">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-500 to-red-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-cogs text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Gestione
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Gestisci reparti e tipi difetti del controllo qualità
            </p>
            <div class="flex space-x-4">
                <a href="<?= $this->url('/quality/departments') ?>"
                    class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium text-sm group-hover:translate-x-1 transition-transform">
                    Reparti
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
                <a href="<?= $this->url('/quality/defects') ?>"
                    class="inline-flex items-center text-red-600 hover:text-red-700 font-medium text-sm group-hover:translate-x-1 transition-transform">
                    Difetti
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Record Giornalieri -->
<div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 daily-records-title flex items-center">
        <i class="fas fa-clipboard-check mr-3 text-green-500"></i>
        Record del <?= date('d/m/Y', strtotime($selectedDate)) ?>
    </h3>
    <div class="daily-records-list">
        <?php if (empty($dailyRecords) || count($dailyRecords) === 0): ?>
            <div class="text-center py-12">
                <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400 text-lg">Nessun controllo effettuato in questa data</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <?php foreach ($dailyRecords as $record): ?>
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:shadow-md transition-shadow <?= $record->numero_eccezioni > 0 ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : 'bg-gray-50 dark:bg-gray-700' ?>">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($record->numero_cartellino) ?></span>
                                <?php if ($record->numero_eccezioni > 0): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        <?= $record->numero_eccezioni ?> eccezioni
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        OK
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    <i class="fas fa-building mr-1 w-3"></i><?= htmlspecialchars($record->reparto_display ?? $record->reparto) ?>
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    <i class="fas fa-user mr-1 w-3"></i><?= htmlspecialchars($record->operatore) ?>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-clock mr-1 w-3"></i><?= date('H:i', strtotime($record->data_controllo)) ?>
                                </p>
                                <?php if (isset($record->tipi_difetti) && $record->tipi_difetti): ?>
                                    <p class="text-xs text-red-600 dark:text-red-400">
                                        <i class="fas fa-bug mr-1 w-3"></i><?= htmlspecialchars($record->tipi_difetti) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Dashboard Quality - PJAX Compatible
(function() {
    'use strict';

    let isInitialized = false;

    function initQualityDashboard() {
        if (isInitialized) {
            console.log('[Quality Dashboard] Already initialized, skipping');
            return;
        }
        isInitialized = true;
        console.log('[Quality Dashboard] Initializing...');
    }

    function cleanupQualityDashboard() {
        console.log('[Quality Dashboard] Cleaning up...');
        isInitialized = false;
    }

    // Register with PJAX
    document.addEventListener('pjax:beforeNavigate', cleanupQualityDashboard);

    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initQualityDashboard);
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQualityDashboard);
    } else {
        initQualityDashboard();
    }
})();
</script>
