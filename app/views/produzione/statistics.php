<?php
/**
 * Produzione - Dashboard Statistiche
 * Dashboard completa con grafici e KPI per analisi produzione
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-chart-line mr-3 text-blue-500"></i>
                Statistiche Produzione
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Analisi e monitoraggio performance produttiva
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="<?= $this->url('/produzione/calendar') ?>"
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                <i class="fas fa-calendar mr-2"></i>
                Calendario
            </a>
            <button onclick="openCustomChartModal()"
                    class="inline-flex items-center rounded-lg border border-purple-500 bg-white px-4 py-2 text-sm font-medium text-purple-600 hover:bg-purple-50 dark:border-purple-600 dark:bg-gray-700 dark:text-purple-400 dark:hover:bg-gray-600">
                <i class="fas fa-chart-bar mr-2"></i>
                Grafico Personalizzato
            </button>
            <button onclick="refreshData()"
                    class="inline-flex items-center rounded-lg border border-transparent bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200">
                <i class="fas fa-sync-alt mr-2"></i>
                Aggiorna
            </button>
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
                <a href="<?= $this->url('/produzione/calendar') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                    Produzione
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-500 dark:text-gray-400">Statistiche</span>
            </div>
        </li>
    </ol>
</nav>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Oggi -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Produzione Oggi</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="today-total">
                    <span class="animate-pulse">...</span>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="today-status">Caricamento...</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                <i class="fas fa-calendar-day text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Settimana -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Questa Settimana</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="week-total">
                    <span class="animate-pulse">...</span>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lun-Sab</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-calendar-week text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Mese -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Questo Mese</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2" id="month-total">
                    <span class="animate-pulse">...</span>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="month-avg">Media: ...</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg">
                <i class="fas fa-calendar-alt text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Confronto Mese Precedente -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">vs Mese Scorso</p>
                <p class="text-3xl font-bold mt-2" id="comparison-change">
                    <span class="animate-pulse text-gray-900 dark:text-white">...</span>
                </p>
                <p class="text-xs mt-1" id="comparison-status">Caricamento...</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl shadow-lg" id="comparison-icon">
                <i class="fas fa-chart-line text-white text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Grafici Principali -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Trend Ultimi 30 Giorni -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-chart-area mr-2 text-blue-500"></i>
                Trend Produzione (30gg)
            </h3>
            <select id="trend-days" class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500"
                    onchange="loadTrendData()">
                <option value="7">7 giorni</option>
                <option value="14">14 giorni</option>
                <option value="30" selected>30 giorni</option>
            </select>
        </div>
        <div class="relative" style="height: 300px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Distribuzione Reparti -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
            <i class="fas fa-chart-pie mr-2 text-purple-500"></i>
            Distribuzione per Reparto
        </h3>
        <div class="relative" style="height: 300px;">
            <canvas id="departmentChart"></canvas>
        </div>
    </div>
</div>

<!-- Performance reparti e Completamento Dati -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Performance reparti -->
    <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
            <i class="fas fa-industry mr-2 text-orange-500"></i>
            Performance Reparti (Mese Corrente)
        </h3>
        <div class="relative" style="height: 400px;">
            <canvas id="machineChart"></canvas>
        </div>
    </div>

    <!-- Statistiche Mensili -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
            <i class="fas fa-info-circle mr-2 text-green-500"></i>
            Dettagli Mese
        </h3>

        <div class="space-y-4">
            <!-- Giorni con Dati -->
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Giorni Lavorativi</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white" id="days-with-data">-/-</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-500"
                         id="completion-bar" style="width: 0%"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="completion-text">0% giorni inseriti</p>
            </div>

            <!-- Totali per Reparto -->
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-xs font-medium text-blue-700 dark:text-blue-300 mb-2">Montaggio</p>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100" id="month-montaggio">0</p>
            </div>

            <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                <p class="text-xs font-medium text-purple-700 dark:text-purple-300 mb-2">Orlatura</p>
                <p class="text-2xl font-bold text-purple-900 dark:text-purple-100" id="month-orlatura">0</p>
            </div>

            <div class="p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                <p class="text-xs font-medium text-orange-700 dark:text-orange-300 mb-2">Taglio</p>
                <p class="text-2xl font-bold text-orange-900 dark:text-orange-100" id="month-taglio">0</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Grafico Personalizzato -->
<div id="custom-chart-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-2xl bg-white dark:bg-gray-800">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-chart-bar mr-2 text-purple-500"></i>
                Crea Grafico Personalizzato
            </h3>
            <button onclick="closeCustomChartModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6">
            <form id="custom-chart-form" class="space-y-6">
                <!-- Periodo -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar mr-2"></i>Periodo
                        </label>
                        <select id="period-type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="custom">Personalizzato</option>
                            <option value="year">Anno Corrente</option>
                            <option value="last-year">Anno Precedente</option>
                            <option value="quarter">Trimestre Corrente</option>
                            <option value="semester">Semestre Corrente</option>
                        </select>
                    </div>

                    <div id="date-from-container">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Data Inizio
                        </label>
                        <input type="date" id="date-from" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" value="<?= date('Y-01-01') ?>">
                    </div>

                    <div id="date-to-container">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Data Fine
                        </label>
                        <input type="date" id="date-to" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <!-- Selezione Reparti -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-industry mr-2"></i>Seleziona Reparti/Macchine
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <input type="checkbox" name="departments[]" value="manovia1" class="rounded text-blue-600 mr-2" checked>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Manovia 1</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <input type="checkbox" name="departments[]" value="manovia2" class="rounded text-blue-600 mr-2" checked>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Manovia 2</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <input type="checkbox" name="departments[]" value="orlatura1" class="rounded text-purple-600 mr-2" checked>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Orlatura 1</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <input type="checkbox" name="departments[]" value="orlatura2" class="rounded text-purple-600 mr-2" checked>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Orlatura 2</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <input type="checkbox" name="departments[]" value="orlatura3" class="rounded text-purple-600 mr-2" checked>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Orlatura 3</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <input type="checkbox" name="departments[]" value="orlatura4" class="rounded text-purple-600 mr-2" checked>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Orlatura 4</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <input type="checkbox" name="departments[]" value="orlatura5" class="rounded text-purple-600 mr-2" checked>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Orlatura 5</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <input type="checkbox" name="departments[]" value="taglio1" class="rounded text-orange-600 mr-2" checked>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Taglio 1</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                            <input type="checkbox" name="departments[]" value="taglio2" class="rounded text-orange-600 mr-2" checked>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Taglio 2</span>
                        </label>
                    </div>
                    <div class="mt-2 flex space-x-2">
                        <button type="button" onclick="selectAllDepartments()" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400">
                            <i class="fas fa-check-double mr-1"></i>Seleziona Tutti
                        </button>
                        <button type="button" onclick="deselectAllDepartments()" class="text-sm text-gray-600 hover:text-gray-700 dark:text-gray-400">
                            <i class="fas fa-times mr-1"></i>Deseleziona Tutti
                        </button>
                    </div>
                </div>

                <!-- Tipo Grafico e Aggregazione -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-chart-line mr-2"></i>Tipo Grafico
                        </label>
                        <select id="chart-type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="line">Linea</option>
                            <option value="bar">Barre</option>
                            <option value="stacked-bar">Barre Sovrapposte</option>
                            <option value="area">Area</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar-alt mr-2"></i>Raggruppa per
                        </label>
                        <select id="group-by" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="day">Giorno</option>
                            <option value="week">Settimana</option>
                            <option value="month" selected>Mese</option>
                            <option value="quarter">Trimestre</option>
                        </select>
                    </div>
                </div>

                <!-- Azioni -->
                <div class="flex justify-end space-x-3 pt-4 border-t dark:border-gray-700">
                    <button type="button" onclick="closeCustomChartModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                        Annulla
                    </button>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Genera Grafico
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Visualizzazione Grafico Personalizzato -->
<div id="custom-chart-view-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-2xl bg-white dark:bg-gray-800">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white" id="custom-chart-title">
                    <i class="fas fa-chart-line mr-2 text-purple-500"></i>
                    Grafico Personalizzato
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" id="custom-chart-subtitle"></p>
            </div>
            <button onclick="closeCustomChartViewModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6">
            <!-- Loading -->
            <div id="custom-chart-loading" class="text-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500 mx-auto mb-4"></div>
                <p class="text-gray-600 dark:text-gray-400">Generazione grafico in corso...</p>
            </div>

            <!-- Chart Container -->
            <div id="custom-chart-container" class="hidden">
                <div class="relative" style="height: 500px;">
                    <canvas id="customChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
(function() {
    let trendChart, departmentChart, machineChart;

    function initStatistics() {
        // Cleanup eventuali grafici esistenti prima di ricaricare
        if (trendChart) {
            trendChart.destroy();
            trendChart = null;
        }
        if (departmentChart) {
            departmentChart.destroy();
            departmentChart = null;
        }
        if (machineChart) {
            machineChart.destroy();
            machineChart = null;
        }

        loadAllData();
    }

    // Carica tutti i dati
    window.refreshData = function() {
        loadAllData();
    };

    function loadAllData() {
        loadGeneralStats();
        loadTrendData();
        loadMachinePerformance();
        loadComparison();
    }

    // Carica statistiche generali
    function loadGeneralStats() {
        fetch(window.COREGRE.baseUrl + '/produzione/api/statistics')
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const data = result.data;

                    // Oggi
                    document.getElementById('today-total').textContent = formatNumber(data.today.total);
                    document.getElementById('today-status').textContent = data.today.has_data ? 'Dato inserito' : 'Nessun dato';

                    // Settimana
                    document.getElementById('week-total').textContent = formatNumber(data.week.total);

                    // Mese
                    document.getElementById('month-total').textContent = formatNumber(data.month.total);
                    document.getElementById('month-avg').textContent = `Media: ${formatNumber(data.month.avg_daily)}/giorno`;

                    document.getElementById('days-with-data').textContent = `${data.month.days_with_data}/${data.month.working_days}`;
                    document.getElementById('completion-bar').style.width = `${data.month.completion_rate}%`;
                    document.getElementById('completion-text').textContent = `${data.month.completion_rate}% giorni inseriti (su Lun-Ven)`;

                    document.getElementById('month-montaggio').textContent = formatNumber(data.month.montaggio);
                    document.getElementById('month-orlatura').textContent = formatNumber(data.month.orlatura);
                    document.getElementById('month-taglio').textContent = formatNumber(data.month.taglio);

                    // Grafico distribuzione reparti
                    updateDepartmentChart(data.month);
                }
            })
            .catch(error => console.error('Errore caricamento statistiche:', error));
    }

    // Carica trend
    window.loadTrendData = function() {
        const days = document.getElementById('trend-days').value;
        fetch(window.COREGRE.baseUrl + `/produzione/api/trend?days=${days}`)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    updateTrendChart(result.data);
                }
            })
            .catch(error => console.error('Errore caricamento trend:', error));
    };

    // Carica performance reparti
    function loadMachinePerformance() {
        fetch(window.COREGRE.baseUrl + '/produzione/api/machine-performance')
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    updateMachineChart(result.data);
                }
            })
            .catch(error => console.error('Errore caricamento performance:', error));
    }

    // Carica confronto
    function loadComparison() {
        fetch(window.COREGRE.baseUrl + '/produzione/api/comparison')
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const data = result.data;
                    const changeEl = document.getElementById('comparison-change');
                    const statusEl = document.getElementById('comparison-status');
                    const iconEl = document.getElementById('comparison-icon');

                    const changeText = data.change >= 0 ? `+${data.change}%` : `${data.change}%`;
                    changeEl.textContent = changeText;

                    if (data.trend === 'up') {
                        changeEl.className = 'text-3xl font-bold mt-2 text-green-600 dark:text-green-400';
                        statusEl.className = 'text-xs mt-1 text-green-600 dark:text-green-400';
                        statusEl.innerHTML = '<i class="fas fa-arrow-up mr-1"></i>In crescita';
                        iconEl.className = 'flex h-12 w-12 items-center justify-center rounded-xl shadow-lg bg-gradient-to-r from-green-500 to-green-600';
                    } else {
                        changeEl.className = 'text-3xl font-bold mt-2 text-red-600 dark:text-red-400';
                        statusEl.className = 'text-xs mt-1 text-red-600 dark:text-red-400';
                        statusEl.innerHTML = '<i class="fas fa-arrow-down mr-1"></i>In calo';
                        iconEl.className = 'flex h-12 w-12 items-center justify-center rounded-xl shadow-lg bg-gradient-to-r from-red-500 to-red-600';
                    }
                }
            })
            .catch(error => console.error('Errore caricamento confronto:', error));
    }

    // Aggiorna grafico trend
    function updateTrendChart(data) {
        const ctx = document.getElementById('trendChart');

        if (trendChart) {
            trendChart.destroy();
        }

        trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Totale',
                    data: data.datasets.total,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Aggiorna grafico reparti
    function updateDepartmentChart(data) {
        const ctx = document.getElementById('departmentChart');

        if (departmentChart) {
            departmentChart.destroy();
        }

        departmentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Montaggio', 'Orlatura', 'Taglio'],
                datasets: [{
                    data: [data.montaggio, data.orlatura, data.taglio],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(249, 115, 22, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + formatNumber(context.parsed) + ' pz';
                            }
                        }
                    }
                }
            }
        });
    }

    // Aggiorna grafico reparti
    function updateMachineChart(data) {
        const ctx = document.getElementById('machineChart');

        if (machineChart) {
            machineChart.destroy();
        }

        const labels = data.map(m => m.name);
        const values = data.map(m => m.value);

        machineChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Produzione',
                    data: values,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderRadius: 8,
                    barThickness: 30
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return formatNumber(context.parsed.x) + ' pz';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Formatta numero
    function formatNumber(num) {
        return new Intl.NumberFormat('it-IT').format(num);
    }

    // Registrazione PJAX
    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initStatistics);
    }

    // Fallback primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStatistics);
    } else {
        initStatistics();
    }
})();

// Custom Chart Modal Management
let customChart = null;

function openCustomChartModal() {
    document.getElementById('custom-chart-modal').classList.remove('hidden');
}

function closeCustomChartModal() {
    document.getElementById('custom-chart-modal').classList.add('hidden');
}

function closeCustomChartViewModal() {
    document.getElementById('custom-chart-view-modal').classList.add('hidden');
    if (customChart) {
        customChart.destroy();
        customChart = null;
    }
}

function selectAllDepartments() {
    document.querySelectorAll('input[name="departments[]"]').forEach(cb => cb.checked = true);
}

function deselectAllDepartments() {
    document.querySelectorAll('input[name="departments[]"]').forEach(cb => cb.checked = false);
}

// Period type change handler
document.getElementById('period-type').addEventListener('change', function() {
    const periodType = this.value;
    const dateFromContainer = document.getElementById('date-from-container');
    const dateToContainer = document.getElementById('date-to-container');
    const dateFrom = document.getElementById('date-from');
    const dateTo = document.getElementById('date-to');

    const today = new Date();
    const year = today.getFullYear();

    switch(periodType) {
        case 'year':
            dateFrom.value = `${year}-01-01`;
            dateTo.value = `${year}-12-31`;
            dateFromContainer.style.display = 'none';
            dateToContainer.style.display = 'none';
            break;
        case 'last-year':
            dateFrom.value = `${year-1}-01-01`;
            dateTo.value = `${year-1}-12-31`;
            dateFromContainer.style.display = 'none';
            dateToContainer.style.display = 'none';
            break;
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            const quarterStart = new Date(year, quarter * 3, 1);
            const quarterEnd = new Date(year, (quarter + 1) * 3, 0);
            dateFrom.value = quarterStart.toISOString().split('T')[0];
            dateTo.value = quarterEnd.toISOString().split('T')[0];
            dateFromContainer.style.display = 'none';
            dateToContainer.style.display = 'none';
            break;
        case 'semester':
            const semester = today.getMonth() < 6 ? 0 : 1;
            const semesterStart = new Date(year, semester * 6, 1);
            const semesterEnd = new Date(year, (semester + 1) * 6, 0);
            dateFrom.value = semesterStart.toISOString().split('T')[0];
            dateTo.value = semesterEnd.toISOString().split('T')[0];
            dateFromContainer.style.display = 'none';
            dateToContainer.style.display = 'none';
            break;
        case 'custom':
        default:
            dateFromContainer.style.display = 'block';
            dateToContainer.style.display = 'block';
            break;
    }
});

// Custom Chart Form Submit
document.getElementById('custom-chart-form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Raccogli parametri
    const dateFrom = document.getElementById('date-from').value;
    const dateTo = document.getElementById('date-to').value;
    const chartType = document.getElementById('chart-type').value;
    const groupBy = document.getElementById('group-by').value;

    const departments = [];
    document.querySelectorAll('input[name="departments[]"]:checked').forEach(cb => {
        departments.push(cb.value);
    });

    if (departments.length === 0) {
        alert('Seleziona almeno un reparto/macchina');
        return;
    }

    // Chiudi modal configurazione e apri modal visualizzazione
    closeCustomChartModal();
    document.getElementById('custom-chart-view-modal').classList.remove('hidden');
    document.getElementById('custom-chart-loading').classList.remove('hidden');
    document.getElementById('custom-chart-container').classList.add('hidden');

    // Genera grafico
    generateCustomChart(dateFrom, dateTo, departments, chartType, groupBy);
});

function generateCustomChart(dateFrom, dateTo, departments, chartType, groupBy) {
    // Costruisci URL con parametri
    const params = new URLSearchParams({
        date_from: dateFrom,
        date_to: dateTo,
        departments: departments.join(','),
        group_by: groupBy
    });

    fetch(window.COREGRE.baseUrl + '/produzione/api/custom-chart?' + params.toString())
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                renderCustomChart(result.data, chartType, groupBy, dateFrom, dateTo);
            } else {
                alert('Errore nel caricamento dati: ' + (result.message || 'Errore sconosciuto'));
                closeCustomChartViewModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Errore nel caricamento dati');
            closeCustomChartViewModal();
        });
}

function renderCustomChart(data, chartType, groupBy, dateFrom, dateTo) {
    // Nascondi loading
    document.getElementById('custom-chart-loading').classList.add('hidden');
    document.getElementById('custom-chart-container').classList.remove('hidden');

    // Aggiorna titolo
    const groupByLabel = {
        'day': 'Giornaliera',
        'week': 'Settimanale',
        'month': 'Mensile',
        'quarter': 'Trimestrale'
    }[groupBy] || 'Personalizzata';

    document.getElementById('custom-chart-subtitle').textContent =
        `Analisi ${groupByLabel} dal ${formatDate(dateFrom)} al ${formatDate(dateTo)}`;

    // Distruggi grafico precedente
    if (customChart) {
        customChart.destroy();
    }

    const ctx = document.getElementById('customChart');

    // Configurazione colori per reparto
    const colors = {
        'manovia1': { bg: 'rgba(59, 130, 246, 0.2)', border: 'rgb(59, 130, 246)' },
        'manovia2': { bg: 'rgba(99, 102, 241, 0.2)', border: 'rgb(99, 102, 241)' },
        'orlatura1': { bg: 'rgba(168, 85, 247, 0.2)', border: 'rgb(168, 85, 247)' },
        'orlatura2': { bg: 'rgba(217, 70, 239, 0.2)', border: 'rgb(217, 70, 239)' },
        'orlatura3': { bg: 'rgba(236, 72, 153, 0.2)', border: 'rgb(236, 72, 153)' },
        'orlatura4': { bg: 'rgba(244, 114, 182, 0.2)', border: 'rgb(244, 114, 182)' },
        'orlatura5': { bg: 'rgba(251, 146, 60, 0.2)', border: 'rgb(251, 146, 60)' },
        'taglio1': { bg: 'rgba(249, 115, 22, 0.2)', border: 'rgb(249, 115, 22)' },
        'taglio2': { bg: 'rgba(234, 88, 12, 0.2)', border: 'rgb(234, 88, 12)' }
    };

    const labels = {
        'manovia1': 'Manovia 1',
        'manovia2': 'Manovia 2',
        'orlatura1': 'Orlatura 1',
        'orlatura2': 'Orlatura 2',
        'orlatura3': 'Orlatura 3',
        'orlatura4': 'Orlatura 4',
        'orlatura5': 'Orlatura 5',
        'taglio1': 'Taglio 1',
        'taglio2': 'Taglio 2'
    };

    // Prepara datasets
    const datasets = Object.keys(data.datasets).map(dept => {
        const config = {
            label: labels[dept] || dept,
            data: data.datasets[dept],
            backgroundColor: colors[dept]?.bg || 'rgba(100, 100, 100, 0.2)',
            borderColor: colors[dept]?.border || 'rgb(100, 100, 100)',
            borderWidth: 2
        };

        if (chartType === 'line') {
            config.tension = 0.4;
            config.fill = false;
        } else if (chartType === 'area') {
            config.tension = 0.4;
            config.fill = true;
        }

        return config;
    });

    // Configurazione grafico
    const chartConfig = {
        type: chartType === 'stacked-bar' ? 'bar' : (chartType === 'area' ? 'line' : chartType),
        data: {
            labels: data.labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                x: {
                    stacked: chartType === 'stacked-bar',
                    grid: {
                        display: false
                    }
                },
                y: {
                    stacked: chartType === 'stacked-bar',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return formatNumber(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + formatNumber(context.parsed.y);
                        }
                    }
                }
            }
        }
    };

    customChart = new Chart(ctx, chartConfig);
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('it-IT');
}

function formatNumber(num) {
    return new Intl.NumberFormat('it-IT').format(num);
}
</script>
