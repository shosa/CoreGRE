<!-- Table Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mr-4">
                    <i class="fas fa-table text-xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Tabella: <?= strtoupper($tableName) ?>
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        <?= number_format($totalRecords) ?> record totali • <?= round($tableInfo['data_length']/1024/1024, 2) ?> MB
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <?php if ($this->hasPermission('dbsql')): ?>
                <div class="relative inline-block text-left">
                    <button onclick="toggleExportMenu()" 
                       class="inline-flex items-center rounded-lg border border-blue-300 bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                        <i class="fas fa-download mr-2"></i>
                        Export
                        <i class="fas fa-chevron-down ml-2"></i>
                    </button>
                    
                    <div id="export-menu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50 hidden">
                        <div class="py-1">
                            <button onclick="exportTable('csv')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-file-csv mr-2 text-green-500"></i>
                                Export CSV
                            </button>
                            <button onclick="exportTable('json')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-file-code mr-2 text-blue-500"></i>
                                Export JSON
                            </button>
                            <button onclick="exportTable('sql')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-database mr-2 text-purple-500"></i>
                                Export SQL
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($this->isAdmin()): ?>
                <div class="relative inline-block text-left">
                    <button onclick="toggleOperationsMenu()" 
                       class="inline-flex items-center rounded-lg border border-orange-300 bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-2 text-sm font-medium text-white hover:from-orange-600 hover:to-orange-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                        <i class="fas fa-tools mr-2"></i>
                        Operazioni
                        <i class="fas fa-chevron-down ml-2"></i>
                    </button>
                    
                    <div id="operations-menu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50 hidden">
                        <div class="py-1">
                            <button onclick="optimizeTable()" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-bolt mr-2 text-blue-500"></i>
                                Ottimizza Tabella
                            </button>
                            <button onclick="repairTable()" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-wrench mr-2 text-green-500"></i>
                                Ripara Tabella
                            </button>
                            <hr class="border-gray-200 dark:border-gray-600 my-1">
                            <button onclick="truncateTable()" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                                <i class="fas fa-trash-alt mr-2"></i>
                                Svuota Tabella
                            </button>
                        </div>
                    </div>
                </div>
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
                <a href="<?= $this->url('/database') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">Database Manager</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300"><?= strtoupper($tableName) ?></span>
            </div>
        </li>
    </ol>
</nav>

<!-- Table Info Cards -->
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Records -->
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 dark:border-gray-800 p-6 shadow-lg">
        <div class="flex items-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500 shadow-lg">
                <i class="fas fa-list text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-xl font-bold text-gray-900 dark:text-white"><?= number_format($totalRecords) ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Record</p>
            </div>
        </div>
    </div>

    <!-- Engine -->
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 dark:border-gray-800 p-6 shadow-lg">
        <div class="flex items-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-500 shadow-lg">
                <i class="fas fa-cog text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-lg font-bold text-gray-900 dark:text-white"><?= $tableInfo['engine'] ?? 'N/A' ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Engine</p>
            </div>
        </div>
    </div>

    <!-- Collation -->
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 dark:border-gray-800 p-6 shadow-lg">
        <div class="flex items-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-500 shadow-lg">
                <i class="fas fa-font text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-bold text-gray-900 dark:text-white"><?= explode('_', $tableInfo['table_collation'] ?? 'N/A')[0] ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Charset</p>
            </div>
        </div>
    </div>

    <!-- Auto Increment -->
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 dark:border-gray-800 p-6 shadow-lg">
        <div class="flex items-center">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-500 shadow-lg">
                <i class="fas fa-plus-circle text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-lg font-bold text-gray-900 dark:text-white"><?= $tableInfo['auto_increment'] ?? 'N/A' ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Next AI</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
            <button onclick="showTab('data')" id="tab-data" 
                    class="tab-button active border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                <i class="fas fa-table mr-2"></i>
                Dati (<?= count($tableData) ?>)
            </button>
            <button onclick="showTab('structure')" id="tab-structure"
                    class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <i class="fas fa-database mr-2"></i>
                Struttura (<?= count($structure) ?>)
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div id="tab-content-data" class="tab-content">
        <!-- Search and Filters -->
        <?php if ($totalRecords > 0): ?>
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col gap-4">
                <!-- Prima riga: Ricerca -->
                <div class="flex items-center justify-between">
                    <form method="GET" class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>"
                                   placeholder="Cerca nei record..."
                                   class="w-full ml-6 sm:w-64 rounded-lg border-gray-300 px-4 py-2 pl-10 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                        <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>
                            Cerca
                        </button>
                        <?php if ($search): ?>
                        <a href="<?= $this->url("/database/table/{$tableName}") ?>"
                           class="inline-flex items-center rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                            <i class="fas fa-times mr-2"></i>
                            Reset
                        </a>
                        <?php endif; ?>
                    </form>

                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <?php if ($search): ?>
                            Trovati <?= number_format($totalRecords) ?> record per "<?= htmlspecialchars($search) ?>"
                        <?php else: ?>
                            Mostrando <?= (($currentPage - 1) * $perPage) + 1 ?>-<?= min($currentPage * $perPage, $totalRecords) ?> di <?= number_format($totalRecords) ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Seconda riga: Pulsanti CRUD (solo icone) -->
                <?php if ($this->hasPermission('dbsql')): ?>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                    <button onclick="showAddRecordModal()" title="Nuovo Record"
                       class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-green-300 bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transition-all">
                        <i class="fas fa-plus"></i>
                    </button>

                    <button id="edit-selected-btn" onclick="editSelectedRecord()" disabled title="Modifica Record Selezionato"
                       class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-blue-300 bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-edit"></i>
                    </button>

                    <button id="delete-selected-btn" onclick="deleteSelectedRecords()" disabled title="Elimina Record Selezionati"
                       class="relative inline-flex items-center justify-center w-10 h-10 rounded-lg border border-red-300 bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 shadow-md hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-trash"></i>
                        <span id="delete-count" class="absolute -top-2 -right-2 bg-red-700 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center hidden"></span>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <?php if (empty($tableData)): ?>
                <div class="text-center py-12">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 mx-auto mb-6">
                        <i class="fas fa-inbox text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nessun Record</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        <?= $search ? "Nessun risultato per la ricerca." : "La tabella è vuota." ?>
                    </p>
                    <?php if ($this->hasPermission('dbsql')): ?>
                    <button onclick="showAddRecordModal()" 
                            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>
                        Aggiungi Primo Record
                    </button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <?php if ($this->hasPermission('dbsql')): ?>
                            <th class="px-4 py-3 text-center w-12">
                                <input type="checkbox" id="select-all-records" onchange="toggleAllRecords()"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            </th>
                            <?php endif; ?>
                            <?php foreach (array_keys($tableData[0]) as $column): ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <?= $column ?>
                            </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800/40 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php 
                        $primaryKey = null;
                        foreach ($structure as $col) {
                            if ($col['Key'] === 'PRI') {
                                $primaryKey = $col['Field'];
                                break;
                            }
                        }
                        ?>
                        
                        <?php foreach ($tableData as $row): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors record-row">
                            <?php if ($this->hasPermission('dbsql')): ?>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <input type="checkbox" class="record-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    data-id="<?= htmlspecialchars($row[$primaryKey] ?? '') ?>"
                                    onchange="updateActionButtons()">
                            </td>
                            <?php endif; ?>
                            
                            <?php foreach ($row as $value): ?>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                                <?php if ($value === null): ?>
                                    <span class="text-gray-400 italic">NULL</span>
                                <?php elseif (is_string($value) && strlen($value) > 100): ?>
                                    <span title="<?= htmlspecialchars($value) ?>">
                                        <?= htmlspecialchars(substr($value, 0, 100)) ?>...
                                    </span>
                                <?php else: ?>
                                    <?= htmlspecialchars($value) ?>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Pagina <?= $currentPage ?> di <?= $totalPages ?>
            </div>
            <div class="flex items-center space-x-2">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        <i class="fas fa-chevron-left mr-2"></i>
                        Precedente
                    </a>
                <?php endif; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                        Successiva
                        <i class="fas fa-chevron-right ml-2"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Structure Tab Content -->
    <div id="tab-content-structure" class="tab-content hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Campo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Null</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Chiave</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Default</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Extra</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800/40 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($structure as $column): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
                            <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-sm">
                                <?= $column['Field'] ?>
                            </code>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= 
                                strpos($column['Type'], 'int') !== false ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300' :
                                (strpos($column['Type'], 'varchar') !== false ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' :
                                (strpos($column['Type'], 'text') !== false ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300' :
                                (strpos($column['Type'], 'date') !== false ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300' :
                                'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300'))) ?>">
                                <?= $column['Type'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            <?= $column['Null'] === 'YES' ? 
                                '<span class="text-green-600 dark:text-green-400">SI</span>' : 
                                '<span class="text-red-600 dark:text-red-400">NO</span>' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            <?php if ($column['Key'] === 'PRI'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300">PRIMARY</span>
                            <?php elseif ($column['Key'] === 'UNI'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300">UNIQUE</span>
                            <?php elseif ($column['Key'] === 'MUL'): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">INDEX</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            <?= $column['Default'] === null ? 
                                '<span class="text-gray-400 italic">NULL</span>' : 
                                htmlspecialchars($column['Default']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            <?php if ($column['Extra']): ?>
                                <span class="text-xs font-medium text-blue-600 dark:text-blue-400">
                                    <?= strtoupper($column['Extra']) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Record -->
<div id="add-record-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-99999 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-plus-circle text-green-500 mr-3"></i>
                        Nuovo Record - <?= strtoupper($tableName) ?>
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Inserisci i dati per il nuovo record
                    </p>
                </div>
                <button onclick="hideAddRecordModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[60vh]" id="add-record-form-container">
            <form id="add-record-form">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($structure as $column): ?>
                        <?php if ($column['Extra'] !== 'auto_increment'): ?>
                            <div class="form-group">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <?= $column['Field'] ?>
                                    <?php if ($column['Null'] === 'NO'): ?>
                                        <span class="text-red-500">*</span>
                                    <?php endif; ?>
                                    <span class="text-xs text-gray-500 ml-2">(<?= $column['Type'] ?>)</span>
                                </label>
                                
                                <?php if (strpos($column['Type'], 'text') !== false): ?>
                                    <textarea name="<?= $column['Field'] ?>" 
                                            <?= $column['Null'] === 'NO' ? 'required' : '' ?>
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                            rows="3" placeholder="<?= $column['Default'] ? 'Default: ' . $column['Default'] : 'Inserisci ' . $column['Field'] ?>"></textarea>
                                <?php elseif (strpos($column['Type'], 'date') !== false): ?>
                                    <input type="<?= strpos($column['Type'], 'datetime') !== false ? 'datetime-local' : 'date' ?>" 
                                           name="<?= $column['Field'] ?>" 
                                           <?= $column['Null'] === 'NO' ? 'required' : '' ?>
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <?php elseif (strpos($column['Type'], 'int') !== false): ?>
                                    <input type="number" 
                                           name="<?= $column['Field'] ?>" 
                                           <?= $column['Null'] === 'NO' ? 'required' : '' ?>
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                           placeholder="<?= $column['Default'] ? 'Default: ' . $column['Default'] : 'Inserisci ' . $column['Field'] ?>">
                                <?php else: ?>
                                    <input type="text" 
                                           name="<?= $column['Field'] ?>" 
                                           <?= $column['Null'] === 'NO' ? 'required' : '' ?>
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                           placeholder="<?= $column['Default'] ? 'Default: ' . $column['Default'] : 'Inserisci ' . $column['Field'] ?>">
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </form>
        </div>
        
        <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
            <button onclick="hideAddRecordModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                Annulla
            </button>
            <button onclick="saveNewRecord()" 
                    class="px-6 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                <i class="fas fa-save mr-2"></i>
                Salva Record
            </button>
        </div>
    </div>
</div>

<!-- Modal Edit Record -->
<div id="edit-record-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-99999 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-edit text-blue-500 mr-3"></i>
                        Modifica Record - <?= strtoupper($tableName) ?>
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" id="edit-record-subtitle">
                        Modifica i dati del record selezionato
                    </p>
                </div>
                <button onclick="hideEditRecordModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[60vh]" id="edit-record-form-container">
            <form id="edit-record-form">
                <input type="hidden" id="edit-record-id" name="record_id">
                <div id="edit-record-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- I campi verranno popolati dinamicamente -->
                </div>
            </form>
        </div>
        
        <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
            <button onclick="hideEditRecordModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                Annulla
            </button>
            <button onclick="saveEditedRecord()" 
                    class="px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                <i class="fas fa-save mr-2"></i>
                Aggiorna Record
            </button>
        </div>
    </div>
</div>

<!-- Modal Delete Confirm -->
<div id="delete-record-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-99999 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20 mr-4">
                    <i class="fas fa-trash text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Elimina Record</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Questa operazione è irreversibile</p>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-4" id="delete-record-message">
                    Sei sicuro di voler eliminare questo record?
                </p>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3" id="delete-record-preview">
                    <!-- Preview del record da eliminare -->
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button onclick="hideDeleteRecordModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Annulla
                </button>
                <button onclick="confirmDeleteRecord()" 
                        class="px-6 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    <i class="fas fa-trash mr-2"></i>
                    Elimina Record
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Database Table - JavaScript compatibile con PJAX
    (function() {
        let eventListeners = [];
        const tableName = '<?= $tableName ?>';

        function initDatabaseTable() {
            cleanupEventListeners();
            closeAllModals();

            // Gestione click fuori per chiudere menu
            function documentClickHandler(e) {
                if (!e.target.closest('#export-menu') && !e.target.closest('[onclick="toggleExportMenu()"]')) {
                    hideExportMenu();
                }
                if (!e.target.closest('#operations-menu') && !e.target.closest('[onclick="toggleOperationsMenu()"]')) {
                    hideOperationsMenu();
                }
            }
            document.addEventListener('click', documentClickHandler);
            eventListeners.push({ element: document, event: 'click', handler: documentClickHandler });

            // Inizializza stato pulsanti
            updateActionButtons();
        }

        function cleanupEventListeners() {
            eventListeners.forEach(({ element, event, handler }) => {
                element.removeEventListener(event, handler);
            });
            eventListeners = [];
        }

        function closeAllModals() {
            // Chiudi tutti i modali
            const modals = [
                'add-record-modal',
                'edit-record-modal',
                'delete-record-modal',
                'import-modal',
                'sql-modal'
            ];

            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                }
            });

            // Ripristina overflow del body
            document.body.classList.remove('overflow-hidden');

            // Reset variabili globali
            if (typeof currentEditId !== 'undefined') {
                currentEditId = null;
            }
            if (typeof currentDeleteId !== 'undefined') {
                currentDeleteId = null;
            }

            // Reset forms
            const forms = ['add-record-form', 'edit-record-form'];
            forms.forEach(formId => {
                const form = document.getElementById(formId);
                if (form) {
                    form.reset();
                }
            });

            // Pulisci contenuti dinamici
            const editFields = document.getElementById('edit-record-fields');
            if (editFields) {
                editFields.innerHTML = '';
            }
        }

        // Tab Management
        window.showTab = function(tabName) {
            // Reset all tabs
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });
            
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Activate selected tab
            const activeTab = document.getElementById(`tab-${tabName}`);
            if (activeTab) {
                activeTab.classList.add('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                activeTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            }

            const activeContent = document.getElementById(`tab-content-${tabName}`);
            if (activeContent) {
                activeContent.classList.remove('hidden');
            }
        };

        // Checkbox Management
        window.toggleAllRecords = function() {
            const selectAll = document.getElementById('select-all-records');
            const checkboxes = document.querySelectorAll('.record-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateActionButtons();
        };

        window.updateActionButtons = function() {
            const checkboxes = document.querySelectorAll('.record-checkbox:checked');
            const count = checkboxes.length;

            const editBtn = document.getElementById('edit-selected-btn');
            const deleteBtn = document.getElementById('delete-selected-btn');
            const deleteCount = document.getElementById('delete-count');

            if (count === 0) {
                // Nessuna selezione: disabilita tutti
                if (editBtn) editBtn.disabled = true;
                if (deleteBtn) deleteBtn.disabled = true;
                if (deleteCount) {
                    deleteCount.textContent = '';
                    deleteCount.classList.add('hidden');
                }
            } else if (count === 1) {
                // Una selezione: abilita modifica e elimina
                if (editBtn) editBtn.disabled = false;
                if (deleteBtn) deleteBtn.disabled = false;
                if (deleteCount) {
                    deleteCount.textContent = '';
                    deleteCount.classList.add('hidden');
                }
            } else {
                // Selezione multipla: disabilita modifica, abilita elimina
                if (editBtn) editBtn.disabled = true;
                if (deleteBtn) deleteBtn.disabled = false;
                if (deleteCount) {
                    deleteCount.textContent = count;
                    deleteCount.classList.remove('hidden');
                }
            }

            // Aggiorna checkbox "select all"
            const selectAll = document.getElementById('select-all-records');
            const allCheckboxes = document.querySelectorAll('.record-checkbox');
            if (selectAll && allCheckboxes.length > 0) {
                selectAll.checked = allCheckboxes.length === count;
                selectAll.indeterminate = count > 0 && count < allCheckboxes.length;
            }
        };

        window.editSelectedRecord = function() {
            const selected = document.querySelector('.record-checkbox:checked');
            if (!selected) {
                return;
            }

            const id = selected.dataset.id;
            if (!id || id === '') {
                if (window.showAlert) {
                    window.showAlert('Errore: ID del record non valido', 'error');
                }
                return;
            }

            editRecord(id);
        };

        window.deleteSelectedRecords = function() {
            const selected = document.querySelectorAll('.record-checkbox:checked');
            const ids = Array.from(selected).map(cb => cb.dataset.id);

            if (ids.length === 0) return;

            // Se c'è solo un record selezionato, usa il modale con preview
            if (ids.length === 1) {
                deleteRecord(ids[0]);
            } else {
                // Per eliminazioni multiple, usa il modale con messaggio personalizzato
                const messageEl = document.getElementById('delete-record-message');
                const previewEl = document.getElementById('delete-record-preview');

                messageEl.textContent = `Sei sicuro di voler eliminare ${ids.length} record?`;
                previewEl.innerHTML = `<p class="text-sm text-gray-600 dark:text-gray-400">Questa operazione eliminerà ${ids.length} record ed è irreversibile.</p>`;

                // Imposta un array di ID invece di un singolo ID
                currentDeleteId = ids;

                window.COREGRE.openModal('delete-record-modal');
            }
        };

        async function deleteMultipleRecords(ids) {
            try {
                const promises = ids.map(id =>
                    fetch('<?= $this->url('/database/delete-record') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                        },
                        body: `table=${tableName}&id=${encodeURIComponent(id)}`
                    }).then(r => r.json())
                );

                const results = await Promise.all(promises);
                const errors = results.filter(r => r.error);

                if (errors.length > 0) {
                    throw new Error(`${errors.length} eliminazioni fallite`);
                }

                if (window.showAlert) {
                    window.showAlert(`${ids.length} record eliminati con successo`, 'success');
                }

                // Ricarica pagina
                setTimeout(() => {
                    if (window.pjax) {
                        window.pjax.navigateTo(window.location.href);
                    } else {
                        window.location.reload();
                    }
                }, 1500);

            } catch (error) {
                console.error('Delete error:', error);
                if (window.showAlert) {
                    window.showAlert(`Errore: ${error.message}`, 'error');
                }
            }
        }

        // Menu Management
        window.toggleExportMenu = function() {
            const menu = document.getElementById('export-menu');
            menu.classList.toggle('hidden');
        };

        window.hideExportMenu = function() {
            document.getElementById('export-menu')?.classList.add('hidden');
        };

        window.toggleOperationsMenu = function() {
            const menu = document.getElementById('operations-menu');
            menu.classList.toggle('hidden');
        };

        window.hideOperationsMenu = function() {
            document.getElementById('operations-menu')?.classList.add('hidden');
        };

        // Export Functions
        window.exportTable = function(format) {
            window.location.href = `<?= $this->url('/database/export') ?>?table=${tableName}&format=${format}`;
            hideExportMenu();
        };

        // Table Operations
        window.optimizeTable = function() {
            CoregreModals.confirm({
                message: `Sei sicuro di voler ottimizzare la tabella ${tableName.toUpperCase()}?`,
                onConfirm: () => performTableOperation('optimize')
            });
        };

        window.repairTable = function() {
            CoregreModals.confirm({
                message: `Sei sicuro di voler riparare la tabella ${tableName.toUpperCase()}?`,
                onConfirm: () => performTableOperation('repair')
            });
        };

        window.truncateTable = function() {
            CoregreModals.confirm({
                title: 'ATTENZIONE - Operazione Irreversibile',
                message: `Sei sicuro di voler svuotare completamente la tabella ${tableName.toUpperCase()}? Tutti i dati saranno eliminati definitivamente!\n\nQuesta operazione è IRREVERSIBILE.`,
                type: 'danger',
                confirmText: 'Sì, svuota tabella',
                cancelText: 'Annulla',
                onConfirm: () => performTableOperation('truncate')
            });
        };

        async function performTableOperation(operation) {
            try {
                const response = await fetch('<?= $this->url('/database/table-operation') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                    },
                    body: `table=${tableName}&operation=${operation}`
                });

                const result = await response.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                if (window.showAlert) {
                    window.showAlert(result.message, 'success');
                }

                // Ricarica la pagina dopo truncate
                if (operation === 'truncate') {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }

            } catch (error) {
                console.error('Table operation error:', error);
                if (window.showAlert) {
                    window.showAlert(`Errore: ${error.message}`, 'error');
                }
            }

            hideOperationsMenu();
        }

        // Record Management - CRUD Operations
        let currentEditId = null;
        let currentDeleteId = null;
        const tableStructure = <?= json_encode($structure) ?>;
        const primaryKey = '<?= array_filter($structure, function($col) { return $col['Key'] === 'PRI'; })[0]['Field'] ?? 'id' ?>';

        window.showAddRecordModal = function() {
            window.COREGRE.openModal('add-record-modal');
        };

        window.hideAddRecordModal = function() {
            window.COREGRE.closeModal('add-record-modal', function() {
                const form = document.getElementById('add-record-form');
                if (form) form.reset();
            });
        };

        window.saveNewRecord = async function() {
            const form = document.getElementById('add-record-form');
            const formData = new FormData(form);
            
            // Validazione
            const requiredFields = form.querySelectorAll('[required]');
            for (let field of requiredFields) {
                if (!field.value.trim()) {
                    field.focus();
                    if (window.showAlert) {
                        window.showAlert(`Il campo "${field.name}" è obbligatorio`, 'error');
                    }
                    return;
                }
            }

            try {
                const data = {};
                for (let [key, value] of formData.entries()) {
                    if (value.trim() !== '') {
                        data[key] = value;
                    }
                }

                const response = await fetch('<?= $this->url('/database/record/create') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                    },
                    body: `table=${tableName}&data=${encodeURIComponent(JSON.stringify(data))}`
                });

                const result = await response.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                if (window.showAlert) {
                    window.showAlert(result.message || 'Record creato con successo', 'success');
                }

                window.hideAddRecordModal();

                // Ricarica la pagina
                setTimeout(() => {
                    window.location.reload();
                }, 1500);

            } catch (error) {
                console.error('Create record error:', error);
                if (window.showAlert) {
                    window.showAlert(`Errore: ${error.message}`, 'error');
                }
            }
        };

        window.editRecord = async function(id) {
            if (!id || id === '' || id === 'undefined') {
                if (window.showAlert) {
                    window.showAlert('Errore: ID del record non valido', 'error');
                }
                return;
            }

            currentEditId = id;

            try {
                const url = `<?= $this->url('/database/record/get') ?>?table=${tableName}&id=${encodeURIComponent(id)}`;

                // Recupera i dati del record
                const response = await fetch(url, {
                    headers: {
                        'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                    }
                });

                const result = await response.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                // Popola il modal di edit
                populateEditModal(result.data);
                showEditRecordModal();

            } catch (error) {
                console.error('Load record error:', error);
                if (window.showAlert) {
                    window.showAlert(`Errore: ${error.message}`, 'error');
                }
            }
        };

        function populateEditModal(data) {
            const fieldsContainer = document.getElementById('edit-record-fields');
            const recordIdInput = document.getElementById('edit-record-id');
            const subtitle = document.getElementById('edit-record-subtitle');

            recordIdInput.value = data[primaryKey];
            subtitle.textContent = `ID: ${data[primaryKey]} - Modifica i dati del record`;

            let fieldsHtml = '';
            tableStructure.forEach(column => {
                const fieldName = column.Field;
                const fieldValue = data[fieldName] || '';
                const isRequired = column.Null === 'NO' ? 'required' : '';
                const isAutoIncrement = column.Extra === 'auto_increment';

                fieldsHtml += `
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            ${fieldName}
                            ${column.Null === 'NO' ? '<span class="text-red-500">*</span>' : ''}
                            <span class="text-xs text-gray-500 ml-2">(${column.Type})</span>
                            ${isAutoIncrement ? '<span class="text-xs text-blue-500 ml-1">AUTO</span>' : ''}
                        </label>
                `;

                if (isAutoIncrement) {
                    fieldsHtml += `
                        <input type="text" value="${fieldValue}" disabled
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-500 cursor-not-allowed">
                        <input type="hidden" name="${fieldName}" value="${fieldValue}">
                    `;
                } else if (column.Type.includes('text')) {
                    fieldsHtml += `
                        <textarea name="${fieldName}" ${isRequired}
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                  rows="3">${fieldValue}</textarea>
                    `;
                } else if (column.Type.includes('date')) {
                    const inputType = column.Type.includes('datetime') ? 'datetime-local' : 'date';
                    let formattedValue = fieldValue;
                    if (inputType === 'datetime-local' && fieldValue) {
                        formattedValue = fieldValue.replace(' ', 'T').substring(0, 16);
                    }
                    fieldsHtml += `
                        <input type="${inputType}" name="${fieldName}" ${isRequired} value="${formattedValue}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    `;
                } else if (column.Type.includes('int')) {
                    fieldsHtml += `
                        <input type="number" name="${fieldName}" ${isRequired} value="${fieldValue}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    `;
                } else {
                    fieldsHtml += `
                        <input type="text" name="${fieldName}" ${isRequired} value="${fieldValue}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    `;
                }

                fieldsHtml += '</div>';
            });

            fieldsContainer.innerHTML = fieldsHtml;
        }

        function showEditRecordModal() {
            window.COREGRE.openModal('edit-record-modal');
        }

        window.hideEditRecordModal = function() {
            window.COREGRE.closeModal('edit-record-modal', function() {
                currentEditId = null;
                const editFields = document.getElementById('edit-record-fields');
                if (editFields) editFields.innerHTML = '';
            });
        };

        window.saveEditedRecord = async function() {
            if (!currentEditId) return;

            const form = document.getElementById('edit-record-form');
            const formData = new FormData(form);
            
            // Validazione
            const requiredFields = form.querySelectorAll('[required]');
            for (let field of requiredFields) {
                if (!field.value.trim()) {
                    field.focus();
                    if (window.showAlert) {
                        window.showAlert(`Il campo "${field.name}" è obbligatorio`, 'error');
                    }
                    return;
                }
            }

            try {
                const data = {};
                for (let [key, value] of formData.entries()) {
                    // Escludi solo il campo record_id, includi tutti gli altri anche se vuoti
                    if (key !== 'record_id') {
                        data[key] = value;
                    }
                }

                // Verifica che ci siano dati da aggiornare
                if (Object.keys(data).length === 0) {
                    if (window.showAlert) {
                        window.showAlert('Nessun dato da aggiornare', 'error');
                    }
                    return;
                }

                const payload = {
                    table: tableName,
                    id: currentEditId,
                    data: data
                };

                const response = await fetch('<?= $this->url('/database/record/update') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                if (window.showAlert) {
                    window.showAlert(result.message || 'Record aggiornato con successo', 'success');
                }

                window.hideEditRecordModal();

                // Ricarica la pagina
                setTimeout(() => {
                    window.location.reload();
                }, 1500);

            } catch (error) {
                console.error('Update record error:', error);
                if (window.showAlert) {
                    window.showAlert(`Errore: ${error.message}`, 'error');
                }
            }
        };

        window.deleteRecord = async function(id) {
            currentDeleteId = id;
            
            try {
                // Recupera i dati del record per preview
                const response = await fetch(`<?= $this->url('/database/record/get') ?>?table=${tableName}&id=${id}`, {
                    headers: {
                        'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                    }
                });

                const result = await response.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                // Popola il modal di delete
                populateDeleteModal(result.data);
                showDeleteRecordModal();

            } catch (error) {
                console.error('Load record for delete error:', error);
                if (window.showAlert) {
                    window.showAlert(`Errore: ${error.message}`, 'error');
                }
            }
        };

        function populateDeleteModal(data) {
            const messageEl = document.getElementById('delete-record-message');
            const previewEl = document.getElementById('delete-record-preview');

            messageEl.textContent = `Sei sicuro di voler eliminare il record con ${primaryKey}: ${data[primaryKey]}?`;

            let previewHtml = '<div class="space-y-2">';
            Object.entries(data).forEach(([key, value]) => {
                let displayValue = value;
                if (value === null) {
                    displayValue = '<span class="text-gray-400 italic">NULL</span>';
                } else if (typeof value === 'string' && value.length > 50) {
                    displayValue = value.substring(0, 50) + '...';
                }
                
                previewHtml += `
                    <div class="flex justify-between text-xs">
                        <span class="font-medium text-gray-600 dark:text-gray-400">${key}:</span>
                        <span class="text-gray-900 dark:text-gray-200">${displayValue}</span>
                    </div>
                `;
            });
            previewHtml += '</div>';

            previewEl.innerHTML = previewHtml;
        }

        function showDeleteRecordModal() {
            window.COREGRE.openModal('delete-record-modal');
        }

        window.hideDeleteRecordModal = function() {
            window.COREGRE.closeModal('delete-record-modal', function() {
                currentDeleteId = null;
            });
        };

        window.confirmDeleteRecord = async function() {
            if (!currentDeleteId) return;

            try {
                // Se currentDeleteId è un array, elimina multipli, altrimenti singolo
                if (Array.isArray(currentDeleteId)) {
                    await deleteMultipleRecords(currentDeleteId);
                    window.hideDeleteRecordModal();
                } else {
                    // Elimina singolo record
                    const response = await fetch('<?= $this->url('/database/record/delete') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                        },
                        body: `table=${tableName}&id=${currentDeleteId}`
                    });

                    const result = await response.json();

                    if (result.error) {
                        throw new Error(result.error);
                    }

                    if (window.showAlert) {
                        window.showAlert(result.message || 'Record eliminato con successo', 'success');
                    }

                    window.hideDeleteRecordModal();

                    // Ricarica la pagina
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }

            } catch (error) {
                console.error('Delete record error:', error);
                if (window.showAlert) {
                    window.showAlert(`Errore: ${error.message}`, 'error');
                }
            }
        };

        // Registra l'inizializzatore per PJAX
        if (window.COREGRE && window.COREGRE.onPageLoad) {
            window.COREGRE.onPageLoad(initDatabaseTable);
        }

        // Inizializza anche al primo caricamento
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDatabaseTable);
        } else {
            initDatabaseTable();
        }
    })();
</script>