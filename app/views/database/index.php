<!-- Database Manager Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg mr-4">
                    <i class="fas fa-database text-xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Database Manager
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Gestione diretta database: query, modifica dati e operazioni CRUD
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <?php if ($this->isAdmin()): ?>
                <button onclick="refreshDatabase()"
                    class="inline-flex items-center rounded-lg border border-green-300 bg-gradient-to-r from-green-500 to-green-600 px-4 py-2 text-sm font-medium text-white hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                    <i class="fas fa-sync mr-2"></i>
                    Aggiorna
                </button>
                <a href="<?= $this->url('/database/console') ?>"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-900 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                    <i class="fas fa-terminal mr-2"></i>
                    SQL Console
                </a>
                <button onclick="createBackup()"
                    class="inline-flex items-center rounded-lg border border-orange-300 bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-2 text-sm font-medium text-white hover:from-orange-600 hover:to-orange-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                    <i class="fas fa-download mr-2"></i>
                    Backup DB
                </button>
            <?php endif; ?>
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
                <span class="text-gray-700 dark:text-gray-300">Database Manager</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Statistiche Database -->
<div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Tabelle -->
    <div
        class="rounded-2xl border border-gray-200 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 dark:border-gray-800 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg">
                <i class="fas fa-table text-xl text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['tables_count'] ?? 0 ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Tabelle</p>
            </div>
        </div>
    </div>

    <!-- Dimensione -->
    <div
        class="rounded-2xl border border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 dark:border-gray-800 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-hdd text-xl text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['size'] ?? 'N/A' ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Dimensione</p>
            </div>
        </div>
    </div>

    <!-- MySQL Version -->
    <div
        class="rounded-2xl border border-gray-200 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 dark:border-gray-800 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                <i class="fas fa-server text-xl text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-lg font-bold text-gray-900 dark:text-white">
                    <?= explode('-', $stats['mysql_version'] ?? 'N/A')[0] ?>
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">MySQL</p>
            </div>
        </div>
    </div>

    <!-- Engine -->
    <div
        class="rounded-2xl border border-gray-200 bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 dark:border-gray-800 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 shadow-lg">
                <i class="fas fa-cog text-xl text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-lg font-bold text-gray-900 dark:text-white"><?= $stats['main_engine'] ?? 'N/A' ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Engine</p>
            </div>
        </div>
    </div>
</div>

<!-- Contenuto Principale -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Lista Tabelle -->
    <div class="lg:col-span-4">
        <div
            class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-table mr-3 text-purple-500"></i>
                    Tabelle Database
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    <?= count($tables) ?> tabelle trovate
                </p>
            </div>

            <!-- Search -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="relative">
                    <input type="text" id="table-search" placeholder="Cerca tabella..."
                        class="w-full rounded-lg border-gray-300 px-4 py-2.5 pl-10 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 ml-6 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-2">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <button id="clear-search"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Tabelle List -->
            <div class="max-h-96 overflow-y-auto">
                <div id="tables-list">
                    <?php foreach ($tables as $table): ?>
                        <div class="table-item border-b border-gray-100 dark:border-gray-700 last:border-b-0 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors"
                            data-table="<?= $table['table_name'] ?>">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30 mr-3">
                                        <i class="fas fa-table text-purple-600 dark:text-purple-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            <?= strtoupper($table['table_name']) ?>
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            <?= number_format($table['table_rows'] ?? 0) ?> righe •
                                            <?= round($table['size_mb'] ?? 0, 2) ?> MB
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?=
                                        $table['engine'] === 'InnoDB' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' :
                                        'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300' ?>">
                                        <?= $table['engine'] ?? 'N/A' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

      
    </div>

    <!-- Area Principale - Dettagli Tabella -->
    <div class="lg:col-span-8">
        <div
            class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-info-circle mr-3 text-blue-500"></i>
                            <span id="current-section">Seleziona una tabella</span>
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Clicca su una tabella per visualizzare struttura e dati
                        </p>
                    </div>
                </div>
            </div>

            <div id="main-content" class="p-6">
                <!-- Welcome Content (hidden when table loaded) -->
                <div id="welcome-content" class="text-center py-20">
                    <div
                        class="flex h-24 w-24 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/20 mx-auto mb-6">
                        <i class="fas fa-mouse-pointer text-4xl text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Nessuna Tabella Selezionata</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">
                        Seleziona una tabella dalla lista a sinistra per visualizzarne i dettagli
                    </p>

                    <!-- Quick Actions -->
                    <div class="flex items-center justify-center gap-4">
                        <?php if ($this->isAdmin()): ?>
                        <a href="<?= $this->url('/database/console') ?>"
                            class="inline-flex items-center px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors shadow-md hover:shadow-lg">
                            <i class="fas fa-terminal mr-2"></i>
                            Apri SQL Console
                        </a>
                        <button onclick="createBackup()"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all shadow-md hover:shadow-lg">
                            <i class="fas fa-download mr-2"></i>
                            Crea Backup
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Table Preview Content (hidden initially) -->
                <div id="table-preview-content" class="hidden">
                    <!-- Loading -->
                    <div id="table-loading" class="text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">Caricamento dati...</p>
                    </div>

                    <!-- Table Data -->
                    <div id="table-data-container" class="hidden">
                        <!-- Search and Actions Bar -->
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="flex-1 max-w-md">
                                <input type="text" id="table-search-input" placeholder="Cerca nei dati..."
                                    class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="table-records-count" class="text-sm text-gray-600 dark:text-gray-400"></span>
                                <a id="manage-table-btn" href="#"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 shadow-md hover:shadow-lg transition-all">
                                    <i class="fas fa-cog mr-2"></i>
                                    Gestione
                                </a>
                            </div>
                        </div>

                        <!-- Table Wrapper with Dev Style -->
                        <div class="rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/50 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table id="preview-table" class="w-full text-sm font-mono">
                                    <thead class="bg-gray-200 dark:bg-gray-800 border-b-2 border-gray-300 dark:border-gray-600">
                                        <tr id="table-headers">
                                            <!-- Headers loaded dynamically -->
                                        </tr>
                                    </thead>
                                    <tbody id="table-body" class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <!-- Data loaded dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div id="table-pagination" class="mt-4 flex items-center justify-between">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Pagina <span id="current-page">1</span> di <span id="total-pages">1</span>
                            </div>
                            <div class="flex gap-2">
                                <button id="prev-page" onclick="loadTablePage(currentTablePage - 1)"
                                    class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button id="next-page" onclick="loadTablePage(currentTablePage + 1)"
                                    class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    // Database Manager Index - JavaScript compatibile con PJAX
    (function () {
        let eventListeners = [];

        function initDatabaseIndex() {
            cleanupEventListeners();

            // Ricerca tabelle nella lista
            const tableSearchInput = document.getElementById('table-search');
            const clearSearch = document.getElementById('clear-search');
            const tableItems = document.querySelectorAll('.table-item');

            if (tableSearchInput) {
                function searchHandler() {
                    const query = this.value.toLowerCase();
                    tableItems.forEach(item => {
                        const tableName = item.querySelector('p').textContent.toLowerCase();
                        item.style.display = tableName.includes(query) ? 'block' : 'none';
                    });
                }
                tableSearchInput.addEventListener('input', searchHandler);
                eventListeners.push({ element: tableSearchInput, event: 'input', handler: searchHandler });
            }

            if (clearSearch) {
                function clearHandler() {
                    tableSearchInput.value = '';
                    tableItems.forEach(item => item.style.display = 'block');
                }
                clearSearch.addEventListener('click', clearHandler);
                eventListeners.push({ element: clearSearch, event: 'click', handler: clearHandler });
            }

            // Click su tabelle - carica preview inline
            tableItems.forEach(item => {
                function clickHandler() {
                    const tableName = this.dataset.table;
                    if (tableName) {
                        loadTablePreview(tableName);

                        // Highlight selected table
                        tableItems.forEach(t => t.classList.remove('bg-purple-50', 'dark:bg-purple-900/20'));
                        this.classList.add('bg-purple-50', 'dark:bg-purple-900/20');
                    }
                }
                item.addEventListener('click', clickHandler);
                eventListeners.push({ element: item, event: 'click', handler: clickHandler });
            });

            // Search table data input
            const tableDataSearchInput = document.getElementById('table-search-input');
            if (tableDataSearchInput) {
                let searchTimeout;
                function dataSearchHandler() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        if (window.currentTableName) {
                            loadTablePreview(window.currentTableName, 1, this.value);
                        }
                    }, 500);
                }
                tableDataSearchInput.addEventListener('input', dataSearchHandler);
                eventListeners.push({ element: tableDataSearchInput, event: 'input', handler: dataSearchHandler });
            }
        }

        // Global variables for table preview
        window.currentTableName = null;
        window.currentTablePage = 1;
        window.currentTableSearch = '';

        // Load table preview via AJAX
        window.loadTablePreview = async function(tableName, page = 1, search = '') {
            window.currentTableName = tableName;
            window.currentTablePage = page;
            window.currentTableSearch = search;

            const welcomeContent = document.getElementById('welcome-content');
            const previewContent = document.getElementById('table-preview-content');
            const loadingDiv = document.getElementById('table-loading');
            const dataContainer = document.getElementById('table-data-container');
            const currentSection = document.getElementById('current-section');

            // Show preview area
            welcomeContent.classList.add('hidden');
            previewContent.classList.remove('hidden');
            loadingDiv.classList.remove('hidden');
            dataContainer.classList.add('hidden');

            // Update title
            if (currentSection) {
                currentSection.textContent = tableName.toUpperCase();
            }

            try {
                const response = await fetch('<?= $this->url('/database/table-preview') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                    },
                    body: `table=${encodeURIComponent(tableName)}&page=${page}&search=${encodeURIComponent(search)}`
                });

                const result = await response.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                // Hide loading, show data
                loadingDiv.classList.add('hidden');
                dataContainer.classList.remove('hidden');

                // Render table
                renderTablePreview(result);

            } catch (error) {
                console.error('Error loading table preview:', error);
                if (window.showAlert) {
                    window.showAlert(`Errore: ${error.message}`, 'error');
                }
                // Show welcome back on error
                previewContent.classList.add('hidden');
                welcomeContent.classList.remove('hidden');
            }
        };

        window.loadTablePage = function(page) {
            if (page < 1 || page > window.totalTablePages) return;
            loadTablePreview(window.currentTableName, page, window.currentTableSearch);
        };

        function renderTablePreview(data) {
            const headersRow = document.getElementById('table-headers');
            const tbody = document.getElementById('table-body');
            const recordsCount = document.getElementById('table-records-count');
            const manageBtn = document.getElementById('manage-table-btn');
            const currentPageSpan = document.getElementById('current-page');
            const totalPagesSpan = document.getElementById('total-pages');
            const prevBtn = document.getElementById('prev-page');
            const nextBtn = document.getElementById('next-page');

            // Update manage button link
            if (manageBtn) {
                manageBtn.href = `<?= $this->url('/database/table/') ?>${data.table}`;
            }

            // Update records count
            if (recordsCount) {
                recordsCount.textContent = `${data.totalRecords} record totali`;
            }

            // Render headers
            headersRow.innerHTML = '';
            if (data.structure && data.structure.length > 0) {
                data.structure.forEach(col => {
                    const th = document.createElement('th');
                    th.className = 'px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider';
                    th.textContent = col.Field;
                    headersRow.appendChild(th);
                });
            }

            // Render data
            tbody.innerHTML = '';
            if (data.data && data.data.length > 0) {
                data.data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors';

                    Object.values(row).forEach(value => {
                        const td = document.createElement('td');
                        td.className = 'px-4 py-2 text-gray-900 dark:text-gray-100 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs';
                        td.textContent = value !== null ? String(value) : 'NULL';
                        td.title = value !== null ? String(value) : 'NULL';
                        tr.appendChild(td);
                    });

                    tbody.appendChild(tr);
                });
            } else {
                const tr = document.createElement('tr');
                const td = document.createElement('td');
                td.colSpan = data.structure.length;
                td.className = 'px-4 py-8 text-center text-gray-500 dark:text-gray-400';
                td.textContent = 'Nessun dato trovato';
                tr.appendChild(td);
                tbody.appendChild(tr);
            }

            // Update pagination
            window.totalTablePages = data.totalPages;
            if (currentPageSpan) currentPageSpan.textContent = data.currentPage;
            if (totalPagesSpan) totalPagesSpan.textContent = data.totalPages;

            if (prevBtn) {
                prevBtn.disabled = data.currentPage <= 1;
            }
            if (nextBtn) {
                nextBtn.disabled = data.currentPage >= data.totalPages;
            }
        }

        function cleanupEventListeners() {
            eventListeners.forEach(({ element, event, handler }) => {
                element.removeEventListener(event, handler);
            });
            eventListeners = [];
        }

        // Funzioni globali
        window.createBackup = async function () {
            if (!confirm('Sei sicuro di voler creare un backup del database? Questa operazione può richiedere del tempo.')) {
                return;
            }

            try {
                if (window.showAlert) {
                    window.showAlert('Creazione backup in corso...', 'info');
                }

                window.location.href = '<?= $this->url('/database/backup') ?>';

            } catch (error) {
                console.error('Backup error:', error);
                if (window.showAlert) {
                    window.showAlert('Errore durante la creazione del backup', 'error');
                }
            }
        };

        window.refreshDatabase = function () {
            if (window.pjax) {
                window.pjax.navigateTo(window.location.href);
            } else {
                window.location.reload();
            }
        };


        // Registra l'inizializzatore per PJAX
        if (window.COREGRE && window.COREGRE.onPageLoad) {
            window.COREGRE.onPageLoad(initDatabaseIndex);
        }

        // Inizializza anche al primo caricamento
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDatabaseIndex);
        } else {
            initDatabaseIndex();
        }
    })();
</script>