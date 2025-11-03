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
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <input type="date" id="dateSelector" value="<?= $selectedDate ?>"
                   class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white">
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

<!-- Grafici e Calendario -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Calendario -->
    <div class="lg:col-span-1">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Calendario</h3>
                <div class="flex space-x-2">
                    <button id="prevMonth" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button id="nextMonth" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div id="calendarContainer">
                <div id="calendarMonthYear" class="text-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white"></h4>
                </div>
                <div id="calendarGrid" class="calendar-grid"></div>
            </div>
        </div>
    </div>

    <!-- Grafici -->
    <div class="lg:col-span-2">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Controlli Settimanali -->
            <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-chart-line mr-3 text-blue-500"></i>
                        Controlli Settimanali
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Trend controlli ultimi 7 giorni
                    </p>
                </div>
                <div class="h-80">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>

            <!-- Eccezioni per Reparto -->
            <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-chart-pie mr-3 text-orange-500"></i>
                        Eccezioni per Reparto
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Distribuzione percentuale eccezioni
                    </p>
                </div>
                <div class="h-80">
                    <canvas id="departmentChart"></canvas>
                </div>
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
                                    <i class="fas fa-building mr-1 w-3"></i><?= htmlspecialchars($record->reparto) ?>
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

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<style>
.calendar-grid {
    font-family: inherit;
}

.calendar-grid table {
    width: 100%;
    border-collapse: collapse;
}

.calendar-grid th,
.calendar-grid td {
    padding: 8px;
    text-align: center;
    border: 1px solid;
}

.calendar-grid th {
    font-weight: 600;
    background-color: rgb(243 244 246);
    color: rgb(55 65 81);
    border-color: rgb(229 231 235);
}

.calendar-grid td {
    cursor: pointer;
    transition: background-color 0.2s;
    color: rgb(17 24 39);
    border-color: rgb(229 231 235);
}

.calendar-grid td:hover {
    background-color: rgb(219 234 254);
}

.calendar-grid td.selected {
    background-color: rgb(59 130 246);
    color: white;
}

.calendar-grid td.other-month {
    color: rgb(156 163 175);
}

.calendar-grid td.today {
    background-color: rgb(254 243 199);
    font-weight: 600;
}

.dark .calendar-grid th {
    background-color: rgb(55 65 81);
    color: rgb(243 244 246);
    border-color: rgb(75 85 99);
}

.dark .calendar-grid td {
    border-color: rgb(75 85 99);
    color: rgb(243 244 246);
}

.dark .calendar-grid td:hover {
    background-color: rgb(30 64 175);
}

.dark .calendar-grid td.today {
    background-color: rgb(146 64 14);
}
</style>

<script>
// Dashboard Quality - PJAX Compatible
(function() {
    'use strict';

    let formController = null;
    let isInitialized = false;
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let selectedDate = '<?= $selectedDate ?>';

    const monthNames = [
        "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno",
        "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"
    ];

    function initQualityDashboard() {
        if (isInitialized) {
            console.log('[Quality Dashboard] Already initialized, skipping');
            return;
        }
        isInitialized = true;

        if (formController) {
            formController.abort();
        }
        formController = new AbortController();
        const signal = formController.signal;

        console.log('[Quality Dashboard] Initializing...');

        setupEventListeners(signal);
        initializeCalendar();
        initializeCharts();
    }

    function cleanupQualityDashboard() {
        console.log('[Quality Dashboard] Cleaning up...');
        isInitialized = false;

        if (formController) {
            formController.abort();
            formController = null;
        }

        // Destroy charts
        if (window.qualityWeeklyChart) {
            window.qualityWeeklyChart.destroy();
            window.qualityWeeklyChart = null;
        }
        if (window.qualityDeptChart) {
            window.qualityDeptChart.destroy();
            window.qualityDeptChart = null;
        }
    }

    function setupEventListeners(signal) {
        // Date selector
        const dateSelector = document.getElementById('dateSelector');
        if (dateSelector) {
            dateSelector.addEventListener('change', function() {
                updateDashboardData(this.value);
            }, { signal });
        }

        // Calendar navigation
        const prevMonth = document.getElementById('prevMonth');
        const nextMonth = document.getElementById('nextMonth');

        if (prevMonth) {
            prevMonth.addEventListener('click', function() {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                renderCalendar();
            }, { signal });
        }

        if (nextMonth) {
            nextMonth.addEventListener('click', function() {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                renderCalendar();
            }, { signal });
        }
    }

    function initializeCalendar() {
        const selected = new Date(selectedDate);
        currentMonth = selected.getMonth();
        currentYear = selected.getFullYear();
        renderCalendar();
    }

    function renderCalendar() {
        const monthYearEl = document.getElementById('calendarMonthYear');
        const gridEl = document.getElementById('calendarGrid');

        if (!monthYearEl || !gridEl) return;

        monthYearEl.innerHTML = `<h4 class="text-lg font-semibold text-gray-900 dark:text-white">${monthNames[currentMonth]} ${currentYear}</h4>`;

        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const firstDayWeek = firstDay.getDay() === 0 ? 7 : firstDay.getDay();

        let html = `
            <table class="w-full">
                <thead>
                    <tr>
                        <th>Lun</th><th>Mar</th><th>Mer</th><th>Gio</th><th>Ven</th><th>Sab</th><th>Dom</th>
                    </tr>
                </thead>
                <tbody>
        `;

        let date = 1;
        for (let week = 0; week < 6; week++) {
            html += '<tr>';
            for (let day = 1; day <= 7; day++) {
                if (week === 0 && day < firstDayWeek) {
                    html += '<td class="other-month"></td>';
                } else if (date > lastDay.getDate()) {
                    html += '<td class="other-month"></td>';
                } else {
                    const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
                    const isToday = dateStr === new Date().toISOString().split('T')[0];
                    const isSelected = dateStr === selectedDate;

                    let classes = [];
                    if (isToday) classes.push('today');
                    if (isSelected) classes.push('selected');

                    html += `<td class="${classes.join(' ')}" onclick="window.selectQualityDate('${dateStr}')">${date}</td>`;
                    date++;
                }
            }
            html += '</tr>';
            if (date > lastDay.getDate()) break;
        }

        html += '</tbody></table>';
        gridEl.innerHTML = html;
    }

    function initializeCharts() {
        // Destroy existing charts
        if (window.qualityWeeklyChart) {
            window.qualityWeeklyChart.destroy();
            window.qualityWeeklyChart = null;
        }
        if (window.qualityDeptChart) {
            window.qualityDeptChart.destroy();
            window.qualityDeptChart = null;
        }

        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.warn('[Quality Dashboard] Chart.js not loaded, retrying...');
            setTimeout(initializeCharts, 500);
            return;
        }

        console.log('[Quality Dashboard] Initializing charts...');

        // Weekly Controls Chart
        const weeklyData = <?= json_encode($weeklyControls) ?>;
        const weeklyLabels = [];
        const weeklyValues = [];

        for (let i = 0; i < 7; i++) {
            const date = new Date('<?= $weekStart ?>');
            date.setDate(date.getDate() + i);
            const dateStr = date.toISOString().split('T')[0];
            const dayName = date.toLocaleDateString('it-IT', { weekday: 'short' });

            weeklyLabels.push(dayName);
            const found = weeklyData.find(d => d.data === dateStr);
            weeklyValues.push(found ? parseInt(found.controlli) : 0);
        }

        const weeklyCtx = document.getElementById('weeklyChart');
        if (weeklyCtx) {
            window.qualityWeeklyChart = new Chart(weeklyCtx, {
                type: 'line',
                data: {
                    labels: weeklyLabels,
                    datasets: [{
                        label: 'Controlli',
                        data: weeklyValues,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
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
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // Department Exceptions Chart
        const deptData = <?= json_encode($departmentExceptions) ?>;
        const deptLabels = deptData.map(d => d.reparto || 'Sconosciuto');
        const deptValues = deptData.map(d => parseFloat(d.percentuale_eccezioni) || 0);

        const deptCtx = document.getElementById('departmentChart');
        if (deptCtx && deptLabels.length > 0) {
            window.qualityDeptChart = new Chart(deptCtx, {
                type: 'doughnut',
                data: {
                    labels: deptLabels,
                    datasets: [{
                        data: deptValues,
                        backgroundColor: [
                            '#ef4444', '#f97316', '#eab308', '#22c55e',
                            '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 11
                                },
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    return data.labels.map((label, i) => ({
                                        text: `${label}: ${data.datasets[0].data[i]}%`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    }));
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    }
                }
            });
        } else if (deptCtx) {
            deptCtx.parentElement.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-8">Nessun dato disponibile</p>';
        }
    }

    function updateDashboardData(date) {
        selectedDate = date;

        const dateSelector = document.getElementById('dateSelector');
        if (dateSelector) {
            dateSelector.value = date;
        }

        showLoadingStates();

        fetch(`<?= $this->url('/quality/hermes-data') ?>?date=${date}`)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    updateDashboardUI(result.data);
                } else {
                    console.error('Errore:', result.message);
                    hideLoadingStates();
                }
            })
            .catch(error => {
                console.error('Errore di rete:', error);
                hideLoadingStates();
            });
    }

    function showLoadingStates() {
        document.querySelectorAll('.stats-value').forEach(el => {
            el.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        });

        const recordsList = document.querySelector('.daily-records-list');
        if (recordsList) {
            recordsList.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></div>';
        }
    }

    function hideLoadingStates() {
        // States will be updated by updateDashboardUI
    }

    function updateDashboardUI(data) {
        // Update header date
        const headerDate = document.querySelector('.header-date');
        if (headerDate) {
            const dateObj = new Date(data.selectedDate);
            headerDate.textContent = `Sistema CQ Hermes - ${dateObj.toLocaleDateString('it-IT')}`;
        }

        // Update stats
        const statsMap = {
            'today-records': data.stats.controlliOggi,
            'week-records': data.weeklyControls.reduce((sum, d) => sum + parseInt(d.controlli || 0), 0),
            'month-exceptions': data.stats.eccezioniOggi,
            'active-departments': data.stats.reparti
        };

        Object.entries(statsMap).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        });

        // Update daily records
        updateDailyRecords(data.dailyRecords, data.selectedDate);

        // Update calendar
        const dateObj = new Date(data.selectedDate);
        currentMonth = dateObj.getMonth();
        currentYear = dateObj.getFullYear();
        selectedDate = data.selectedDate;
        renderCalendar();

        // Update charts
        updateCharts(data);

        hideLoadingStates();
    }

    function updateDailyRecords(records, date) {
        const container = document.querySelector('.daily-records-list');
        if (!container) return;

        const dateObj = new Date(date);
        const formattedDate = dateObj.toLocaleDateString('it-IT');

        const title = document.querySelector('.daily-records-title');
        if (title) {
            title.innerHTML = `<i class="fas fa-clipboard-check mr-3 text-green-500"></i>Record del ${formattedDate}`;
        }

        if (records.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">Nessun controllo effettuato in questa data</p>
                </div>
            `;
            return;
        }

        const recordsHTML = records.map(record => {
            const hasExceptions = record.numero_eccezioni > 0;
            const bgClass = hasExceptions ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : 'bg-gray-50 dark:bg-gray-700';
            const statusBadge = hasExceptions
                ? `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">${record.numero_eccezioni} eccezioni</span>`
                : `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">OK</span>`;

            const defectTypes = record.tipi_difetti
                ? `<p class="text-xs text-red-600 dark:text-red-400"><i class="fas fa-bug mr-1 w-3"></i>${record.tipi_difetti}</p>`
                : '';

            const recordTime = new Date(record.data_controllo).toLocaleTimeString('it-IT', {hour: '2-digit', minute: '2-digit'});

            return `
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:shadow-md transition-shadow ${bgClass}">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-900 dark:text-white">${record.numero_cartellino}</span>
                            ${statusBadge}
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <i class="fas fa-building mr-1 w-3"></i>${record.reparto}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <i class="fas fa-user mr-1 w-3"></i>${record.operatore}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-clock mr-1 w-3"></i>${recordTime}
                            </p>
                            ${defectTypes}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = `<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">${recordsHTML}</div>`;
    }

    function updateCharts(data) {
        if (window.qualityWeeklyChart) {
            window.qualityWeeklyChart.destroy();
        }
        if (window.qualityDeptChart) {
            window.qualityDeptChart.destroy();
        }

        // Recreate charts with new data
        setTimeout(() => initializeCharts(), 100);
    }

    // Global function for calendar date selection
    window.selectQualityDate = function(date) {
        updateDashboardData(date);
    };

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
