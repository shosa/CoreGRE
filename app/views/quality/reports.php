<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div
                    class="mr-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                    <i class="fas fa-file-alt text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                      Report CQ Hermes
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Generazione Report Controllo Qualità
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
                <a href="<?= $this->url('/quality') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">CQ Hermes</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Report</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Report Cards Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

    <!-- Report Giornaliero -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-calendar-day mr-3 text-blue-500"></i>
                Report Giornaliero
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                Genera report per una data specifica
            </p>
        </div>

        <form id="dailyReportForm" class="space-y-4">
            <div>
                <label for="daily_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Data Report <span class="text-red-500">*</span>
                </label>
                <input type="date" id="daily_date" name="report_date" required
                    value="<?= date('Y-m-d') ?>"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-blue-400">
            </div>

            <div>
                <label for="daily_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Formato Output
                </label>
                <select id="daily_format" name="format"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-blue-400">
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                </select>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                    <i class="fas fa-download mr-2"></i>
                    Genera Report Giornaliero
                </button>
            </div>
        </form>
    </div>

    <!-- Report Periodo -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-calendar-alt mr-3 text-purple-500"></i>
                Report Periodo
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                Genera report per un intervallo di date
            </p>
        </div>

        <form id="periodReportForm" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data Inizio <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="start_date" name="start_date" required
                        value="<?= date('Y-m-d', strtotime('-7 days')) ?>"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-purple-400">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Data Fine <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="end_date" name="end_date" required
                        value="<?= date('Y-m-d') ?>"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-purple-400">
                </div>
            </div>

            <div>
                <label for="period_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Formato Output
                </label>
                <select id="period_format" name="format"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-purple-400">
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                </select>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-purple-600 hover:to-purple-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                    <i class="fas fa-download mr-2"></i>
                    Genera Report Periodo
                </button>
            </div>
        </form>
    </div>
</div>


<script>
// JavaScript compatibile con PJAX per gestione report
(function() {
    let eventListeners = [];

    function initReportsPage() {
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
        // Form report giornaliero
        const dailyForm = document.getElementById('dailyReportForm');
        if (dailyForm) {
            function dailyFormHandler(e) {
                e.preventDefault();
                generateDailyReport();
            }
            dailyForm.addEventListener('submit', dailyFormHandler);
            eventListeners.push({ element: dailyForm, event: 'submit', handler: dailyFormHandler });
        }

        // Form report periodo
        const periodForm = document.getElementById('periodReportForm');
        if (periodForm) {
            function periodFormHandler(e) {
                e.preventDefault();
                generatePeriodReport();
            }
            periodForm.addEventListener('submit', periodFormHandler);
            eventListeners.push({ element: periodForm, event: 'submit', handler: periodFormHandler });
        }

        // Validazione date
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        if (startDate && endDate) {
            function dateValidation() {
                if (startDate.value && endDate.value) {
                    if (new Date(startDate.value) > new Date(endDate.value)) {
                        endDate.value = startDate.value;
                    }
                }
            }
            startDate.addEventListener('change', dateValidation);
            endDate.addEventListener('change', dateValidation);
            eventListeners.push({ element: startDate, event: 'change', handler: dateValidation });
            eventListeners.push({ element: endDate, event: 'change', handler: dateValidation });
        }
    }

    function generateDailyReport() {
        const formData = new FormData(document.getElementById('dailyReportForm'));
        const params = new URLSearchParams(formData);
        params.append('action', 'daily_report');

        showAlert('Generazione report in corso...', 'info');

        // Apri in nuova finestra per il download
        window.open(`<?= $this->url('/quality/generate-report') ?>?${params.toString()}`, '_blank');
    }

    function generatePeriodReport() {
        const formData = new FormData(document.getElementById('periodReportForm'));
        const params = new URLSearchParams(formData);
        params.append('action', 'period_report');

        showAlert('Generazione report in corso...', 'info');

        // Apri in nuova finestra per il download
        window.open(`<?= $this->url('/quality/generate-report') ?>?${params.toString()}`, '_blank');
    }


    // Registra l'inizializzatore per PJAX
    if (window.WEBGRE && window.WEBGRE.onPageLoad) {
        window.WEBGRE.onPageLoad(initReportsPage);
    }

    // Inizializza anche al primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReportsPage);
    } else {
        initReportsPage();
    }
})();
</script>