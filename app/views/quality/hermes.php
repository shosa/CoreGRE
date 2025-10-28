<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="mr-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                    <i class="fas fa-search-plus text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Dashboard Hermes
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400 header-date">
                        Sistema di Controllo Qualità - <?= date('d/m/Y', strtotime($selectedDate)) ?>
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
                <a href="<?= $this->url('/quality') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                    Controllo Qualità
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Hermes</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-clipboard-check text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Controlli Oggi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white stats-value" id="controlli-oggi"><?= count($dailyRecords) ?></p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-yellow-500 to-yellow-600 shadow-lg">
                <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Eccezioni Oggi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white stats-value" id="eccezioni-oggi"><?= $dailyRecords->sum('numero_eccezioni') ?></p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                <i class="fas fa-building text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Reparti Attivi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white stats-value" id="reparti-attivi"><?= count($reparti) ?></p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-600 shadow-lg">
                <i class="fas fa-bug text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tipi Difetti</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white stats-value" id="tipi-difetti"><?= count($tipiDifetti) ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Grafici in Alto -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Calendar -->
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

    <!-- Charts -->
    <div class="lg:col-span-2">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Weekly Controls Chart -->
            <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-chart-line mr-3 text-blue-500"></i>
                        Controlli Settimanali
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Trend dei controlli negli ultimi 7 giorni
                    </p>
                </div>
                <div class="h-80">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>

            <!-- Department Exceptions Chart -->
            <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-chart-pie mr-3 text-orange-500"></i>
                        Eccezioni per Reparto
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Distribuzione percentuale delle eccezioni
                    </p>
                </div>
                <div class="h-80">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista Record a Tutta Larghezza -->
<div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 daily-records-title flex items-center">
        <i class="fas fa-list mr-3 text-green-500"></i>
        Record del <?= date('d/m/Y', strtotime($selectedDate)) ?>
    </h3>
    <div class="daily-records-list">
        <?php if (empty($dailyRecords)): ?>
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
                                <?php if ($record->tipi_difetti): ?>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Add Defect Modal -->
<div id="addDefectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Aggiungi Tipo Difetto</h3>
            <button onclick="closeDefectModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="defectForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descrizione</label>
                <input type="text" name="descrizione" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categoria (opzionale)</label>
                <input type="text" name="categoria"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDefectModal()"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Annulla
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Salva
                </button>
            </div>
        </form>
    </div>
</div>

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
// Sistema Dashboard Hermes - JavaScript compatibile con PJAX
(function() {
    let eventListeners = [];
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let selectedDate = '<?= $selectedDate ?>';

    const monthNames = [
        "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno",
        "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"
    ];

    function initHermesDashboard() {
        cleanupEventListeners();
        setupEventListeners();
        initializeCalendar();
        initializeCharts();
    }

    function cleanupEventListeners() {
        // Cleanup event listeners
        eventListeners.forEach(({ element, event, handler }) => {
            if (element) {
                element.removeEventListener(event, handler);
            }
        });
        eventListeners = [];

        // Distruggi chart esistenti per evitare conflitti PJAX
        if (window.weeklyChartInstance) {
            window.weeklyChartInstance.destroy();
            window.weeklyChartInstance = null;
        }
        if (window.deptChartInstance) {
            window.deptChartInstance.destroy();
            window.deptChartInstance = null;
        }
    }

    function addEventListenerWithCleanup(element, event, handler) {
        if (element) {
            element.addEventListener(event, handler);
            eventListeners.push({ element, event, handler });
        }
    }

    function setupEventListeners() {
        // Date selector
        const dateSelector = document.getElementById('dateSelector');
        addEventListenerWithCleanup(dateSelector, 'change', function() {
            updateDashboardData(this.value);
        });

        // Calendar navigation
        addEventListenerWithCleanup(document.getElementById('prevMonth'), 'click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        });

        addEventListenerWithCleanup(document.getElementById('nextMonth'), 'click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        });

        // Defect form
        const defectForm = document.getElementById('defectForm');
        if (defectForm) {
            addEventListenerWithCleanup(defectForm, 'submit', function(e) {
                e.preventDefault();
                saveDefect(new FormData(this));
            });
        }

        // Modal outside click
        const modal = document.getElementById('addDefectModal');
        if (modal) {
            addEventListenerWithCleanup(modal, 'click', function(e) {
                if (e.target === this) {
                    closeDefectModal();
                }
            });
        }
    }

    function initializeCalendar() {
        const today = new Date();
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
        const firstDayWeek = firstDay.getDay() === 0 ? 7 : firstDay.getDay(); // Monday = 1

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

                    html += `<td class="${classes.join(' ')}" onclick="selectDate('${dateStr}')">${date}</td>`;
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
        // Distruggi chart esistenti prima di crearne di nuovi (sicurezza PJAX)
        if (window.weeklyChartInstance) {
            window.weeklyChartInstance.destroy();
            window.weeklyChartInstance = null;
        }
        if (window.deptChartInstance) {
            window.deptChartInstance.destroy();
            window.deptChartInstance = null;
        }

        // Verifica se Chart.js è caricato
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js non caricato, salto inizializzazione grafici');
            // Mostra messaggio nei canvas
            const weeklyCtx = document.getElementById('weeklyChart');
            const deptCtx = document.getElementById('departmentChart');

            if (weeklyCtx) {
                weeklyCtx.parentElement.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-8">Caricamento grafico...</p>';
            }
            if (deptCtx) {
                deptCtx.parentElement.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-8">Caricamento grafico...</p>';
            }

            // Riprova dopo 1 secondo
            setTimeout(initializeCharts, 1000);
            return;
        }

        // Weekly Controls Chart
        const weeklyData = <?= json_encode($weeklyControls) ?>;
        const weeklyLabels = [];
        const weeklyValues = [];

        // Genera 7 giorni della settimana
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
            window.weeklyChartInstance = new Chart(weeklyCtx, {
                type: 'line',
                data: {
                    labels: weeklyLabels,
                    datasets: [{
                        label: 'Controlli',
                        data: weeklyValues,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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
            window.deptChartInstance = new Chart(deptCtx, {
                type: 'doughnut',
                data: {
                    labels: deptLabels,
                    datasets: [{
                        data: deptValues,
                        backgroundColor: [
                            '#ef4444', '#f97316', '#eab308', '#22c55e',
                            '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    return data.labels.map((label, i) => ({
                                        text: `${label}: ${data.datasets[0].data[i]}%`,
                                        fillStyle: data.datasets[0].backgroundColor[i]
                                    }));
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    function selectDate(date) {
        updateDashboardData(date);
    }

    function updateDashboardData(date) {
        // Aggiorna la data selezionata
        selectedDate = date;

        // Aggiorna il date selector
        const dateSelector = document.getElementById('dateSelector');
        if (dateSelector) {
            dateSelector.value = date;
        }

        // Mostra loading
        showLoadingStates();

        // Chiama l'API per recuperare i nuovi dati
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
        // Stats cards
        document.querySelectorAll('.stats-value').forEach(el => {
            el.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        });

        // Daily records
        const recordsList = document.querySelector('.daily-records-list');
        if (recordsList) {
            recordsList.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></div>';
        }

        // Charts
        const weeklyChart = document.getElementById('weeklyChart');
        const deptChart = document.getElementById('departmentChart');
        if (weeklyChart) weeklyChart.style.opacity = '0.5';
        if (deptChart) deptChart.style.opacity = '0.5';
    }

    function hideLoadingStates() {
        const weeklyChart = document.getElementById('weeklyChart');
        const deptChart = document.getElementById('departmentChart');
        if (weeklyChart) weeklyChart.style.opacity = '1';
        if (deptChart) deptChart.style.opacity = '1';
    }

    function updateDashboardUI(data) {
        // Aggiorna header date
        const headerDate = document.querySelector('.header-date');
        if (headerDate) {
            const dateObj = new Date(data.selectedDate);
            headerDate.textContent = `Sistema di Controllo Qualità - ${dateObj.toLocaleDateString('it-IT')}`;
        }

        // Aggiorna stats cards
        const statsElements = {
            'controlli-oggi': data.stats.controlliOggi,
            'eccezioni-oggi': data.stats.eccezioniOggi,
            'reparti-attivi': data.stats.reparti,
            'tipi-difetti': data.stats.tipiDifetti
        };

        Object.entries(statsElements).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        });

        // Aggiorna record giornalieri
        updateDailyRecords(data.dailyRecords, data.selectedDate);

        // Aggiorna calendario
        updateCalendarForDate(data.selectedDate);

        // Aggiorna grafici
        updateCharts(data);

        hideLoadingStates();
    }

    function updateDailyRecords(records, date) {
        const container = document.querySelector('.daily-records-list');
        if (!container) return;

        const dateObj = new Date(date);
        const formattedDate = dateObj.toLocaleDateString('it-IT');

        // Aggiorna titolo
        const title = document.querySelector('.daily-records-title');
        if (title) {
            title.textContent = `Record del ${formattedDate}`;
        }

        if (records.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-calendar-times text-3xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Nessun controllo effettuato in questa data</p>
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
                ? `<p class="text-xs text-red-600 dark:text-red-400 mt-1"><i class="fas fa-bug mr-1"></i>${record.tipi_difetti}</p>`
                : '';

            const recordTime = new Date(record.data_controllo).toLocaleTimeString('it-IT', {hour: '2-digit', minute: '2-digit'});

            return `
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 ${bgClass}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="font-semibold text-gray-900 dark:text-white">${record.numero_cartellino}</span>
                                ${statusBadge}
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <i class="fas fa-building mr-1"></i>${record.reparto}
                                <i class="fas fa-user ml-3 mr-1"></i>${record.operatore}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <i class="fas fa-clock mr-1"></i>${recordTime}
                            </p>
                            ${defectTypes}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = recordsHTML;
    }

    function updateCalendarForDate(date) {
        const dateObj = new Date(date);
        currentMonth = dateObj.getMonth();
        currentYear = dateObj.getFullYear();
        selectedDate = date;
        renderCalendar();
    }

    function updateCharts(data) {
        // Distruggi grafici esistenti se presenti
        if (window.weeklyChartInstance) {
            window.weeklyChartInstance.destroy();
        }
        if (window.deptChartInstance) {
            window.deptChartInstance.destroy();
        }

        // Ricrea i grafici con i nuovi dati
        createWeeklyChart(data.weeklyControls, data.weekStart);
        createDepartmentChart(data.departmentExceptions);
    }

    function createWeeklyChart(weeklyData, weekStart) {
        const weeklyLabels = [];
        const weeklyValues = [];

        for (let i = 0; i < 7; i++) {
            const date = new Date(weekStart);
            date.setDate(date.getDate() + i);
            const dateStr = date.toISOString().split('T')[0];
            const dayName = date.toLocaleDateString('it-IT', { weekday: 'short' });

            weeklyLabels.push(dayName);
            const found = weeklyData.find(d => d.data === dateStr);
            weeklyValues.push(found ? parseInt(found.controlli) : 0);
        }

        const weeklyCtx = document.getElementById('weeklyChart');
        if (weeklyCtx) {
            window.weeklyChartInstance = new Chart(weeklyCtx, {
                type: 'line',
                data: {
                    labels: weeklyLabels,
                    datasets: [{
                        label: 'Controlli',
                        data: weeklyValues,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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
    }

    function createDepartmentChart(departmentData) {
        const deptLabels = departmentData.map(d => d.reparto || 'Sconosciuto');
        const deptValues = departmentData.map(d => parseFloat(d.percentuale_eccezioni) || 0);

        const deptCtx = document.getElementById('departmentChart');
        if (deptCtx && deptLabels.length > 0) {
            window.deptChartInstance = new Chart(deptCtx, {
                type: 'doughnut',
                data: {
                    labels: deptLabels,
                    datasets: [{
                        data: deptValues,
                        backgroundColor: [
                            '#ef4444', '#f97316', '#eab308', '#22c55e',
                            '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    return data.labels.map((label, i) => ({
                                        text: `${label}: ${data.datasets[0].data[i]}%`,
                                        fillStyle: data.datasets[0].backgroundColor[i]
                                    }));
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    function showAddDefectModal() {
        document.getElementById('addDefectModal').classList.remove('hidden');
    }

    function closeDefectModal() {
        document.getElementById('addDefectModal').classList.add('hidden');
        document.getElementById('defectForm').reset();
    }

    function saveDefect(formData) {
        fetch('<?= $this->url('/quality/save-defect-type') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeDefectModal();
                WebgreNotifications.success('Difetto salvato con successo');
                location.reload();
            } else {
                WebgreNotifications.error('Errore: ' + (data.message || 'Errore sconosciuto'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            WebgreNotifications.error('Errore di connessione');
        });
    }

    function editDefect(id) {
        console.log('Edit defect:', id);
        // TODO: Implementa logica di modifica
    }

    function deleteDefect(id) {
        WebgreModals.confirmDelete(
            'Sei sicuro di voler eliminare questo tipo di difetto?',
            () => {
                fetch('<?= $this->url('/quality/delete-defect-type') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        WebgreNotifications.success('Difetto eliminato con successo');
                        location.reload();
                    } else {
                        WebgreNotifications.error('Errore: ' + (data.message || 'Errore sconosciuto'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    WebgreNotifications.error('Errore di connessione');
                });
            }
        );
    }

    // Esponi funzioni globalmente per i click inline HTML
    window.selectDate = selectDate;
    window.showAddDefectModal = showAddDefectModal;
    window.closeDefectModal = closeDefectModal;
    window.editDefect = editDefect;
    window.deleteDefect = deleteDefect;

    // Registra inizializzatore PJAX
    if (window.WEBGRE && window.WEBGRE.onPageLoad) {
        window.WEBGRE.onPageLoad(initHermesDashboard);
    }

    // Inizializzazione
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHermesDashboard);
    } else {
        initHermesDashboard();
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', cleanupEventListeners);

})();
</script>