<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div
                    class="mr-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                    <i class="fas fa-chevron-down text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Controllo Qualità
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Consultazione dati controllo qualità
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
                <span class="text-gray-700 dark:text-gray-300">Controllo qualità</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 shadow-lg">
                <i class="fas fa-calendar-day text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Record Oggi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['today_records']) ?>
                </p>
            </div>
        </div>
    </div>

    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-calendar-week text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Record Settimana</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['week_records']) ?>
                </p>
            </div>
        </div>
    </div>

    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-600 shadow-lg">
                <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Eccezioni Mese</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['month_exceptions']) ?>
                </p>
            </div>
        </div>
    </div>

    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-indigo-500 to-blue-600 shadow-lg">
                <i class="fas fa-chart-line text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Reparti Attivi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['active_departments']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Sezioni Principali -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Dashboard Hermes -->
    <div
        class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div
            class="absolute inset-0 bg-gradient-to-br from-orange-50 to-red-100 dark:from-orange-900/10 dark:to-red-800/10">
        </div>
        <div class="relative p-8">
            <div
                class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-500 to-red-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-gem text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Dashboard Hermes
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Visualizza i dati dei controlli qualità premium per griffe Hermes con grafici e analisi
            </p>
            <a href="<?= $this->url('/quality/hermes') ?>"
                class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium group-hover:translate-x-1 transition-transform">
                Apri Dashboard
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>

    <!-- Report CQ -->
    <div
        class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg hover:shadow-xl transition-all duration-300 dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm hover:-translate-y-1">
        <div
            class="absolute inset-0 bg-gradient-to-br from-green-50 to-teal-100 dark:from-green-900/10 dark:to-teal-800/10">
        </div>
        <div class="relative p-8">
            <div
                class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-green-500 to-teal-600 shadow-lg mb-6 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-file-alt text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                Report CQ
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Genera report giornalieri e periodici in formato PDF ed Excel
            </p>
            <a href="<?= $this->url('/quality/reports') ?>"
                class="inline-flex items-center text-green-600 hover:text-green-700 font-medium group-hover:translate-x-1 transition-transform">
                Genera Report
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</div>

<!-- Gestione e Configurazione -->
<div class="mb-8">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
        <i class="fas fa-cogs mr-3 text-gray-500"></i>
        Gestione e Configurazione
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Reparti CQ -->
        <div
            class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md hover:shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm transition-all duration-200 hover:-translate-y-1">
            <div class="relative p-6">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-yellow-600 shadow-lg mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-building text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Reparti CQ
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Gestisci i reparti del controllo qualità
                </p>
                <a href="<?= $this->url('/quality/departments') ?>"
                    class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium text-sm group-hover:translate-x-1 transition-transform">
                    Gestisci Reparti
                    <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Tipi Difetti -->
        <div
            class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md hover:shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm transition-all duration-200 hover:-translate-y-1">
            <div class="relative p-6">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-r from-red-500 to-red-600 shadow-lg mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Tipi Difetti
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Gestisci i tipi di difetti per il controllo qualità
                </p>
                <a href="<?= $this->url('/quality/defects') ?>"
                    class="inline-flex items-center text-red-600 hover:text-red-700 font-medium text-sm group-hover:translate-x-1 transition-transform">
                    Gestisci Difetti
                    <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript compatibile con PJAX per Quality Dashboard
    (function () {
        let eventListeners = [];

        function initQualityDashboard() {
            cleanupEventListeners();
            setupEventListeners();
        }

        function cleanupEventListeners() {
            eventListeners.forEach(({ element, event, handler }) => {
                if (element) {
                    element.removeEventListener(event, handler);
                }
            });
            eventListeners = [];
        }

        function setupEventListeners() {
            // Animazioni al hover per le navigation cards
            const navigationCards = document.querySelectorAll('.group.relative.overflow-hidden');
            navigationCards.forEach(card => {
                function enterHandler() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                }
                function leaveHandler() {
                    this.style.transform = 'translateY(0) scale(1)';
                }

                card.addEventListener('mouseenter', enterHandler);
                card.addEventListener('mouseleave', leaveHandler);

                eventListeners.push({ element: card, event: 'mouseenter', handler: enterHandler });
                eventListeners.push({ element: card, event: 'mouseleave', handler: leaveHandler });
            });
        }

        // Registra l'inizializzatore per PJAX
        if (window.COREGRE && window.COREGRE.onPageLoad) {
            window.COREGRE.onPageLoad(initQualityDashboard);
        }

        // Inizializza anche al primo caricamento
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initQualityDashboard);
        } else {
            initQualityDashboard();
        }
    })();
</script>