<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div
                    class="mr-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-500 to-indigo-600 shadow-lg">
                    <i class="fas fa-users text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                      Operatori CQ
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Gestione Team Controllo Qualità
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/') ?>" class="hover:text-gray-700">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/quality') ?>" class="hover:text-gray-700">CQ Hermes</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700">Operatori</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Operators List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Team Operatori CQ</h3>
                    <p class="text-gray-600 mt-1">Gestione completa del personale qualità</p>
                </div>
                <button onclick="showAddOperatorModal()"
                    class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-6 py-3 rounded-lg hover:from-purple-600 hover:to-indigo-700 transition-all duration-200 font-semibold shadow-lg">
                    <i class="fas fa-user-plus mr-2"></i>
                    Nuovo Operatore
                </button>
            </div>

            <!-- Search and Filters -->
            <div class="mb-6 flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" id="searchOperators" placeholder="Cerca operatore..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                <select id="filterByReparto"
                    class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Tutti i reparti</option>
                    <option value="MONTAGGIO">Montaggio</option>
                    <option value="FINITURA">Finitura</option>
                    <option value="CONTROLLO">Controllo</option>
                    <option value="SPEDIZIONE">Spedizione</option>
                </select>
            </div>

            <!-- Operators Table -->
            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Operatore</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Username</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                PIN</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reparto</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Azioni</th>
                        </tr>
                    </thead>
                    <tbody id="operatorsTableBody" class="bg-white divide-y divide-gray-200">
                        <?php foreach ($operators as $operator): ?>
                            <tr class="hover:bg-gray-50 transition-colors" data-operator-id="<?= $operator->id ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gradient-to-r from-purple-400 to-indigo-500 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    <?= strtoupper(substr($operator->full_name, 0, 2)) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($operator->full_name) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= htmlspecialchars($operator->user) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <?= htmlspecialchars($operator->pin) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= htmlspecialchars($operator->reparto) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="editOperator(<?= $operator->id ?>)"
                                            class="text-indigo-600 hover:text-indigo-900 p-2 rounded-lg hover:bg-indigo-50 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteOperator(<?= $operator->id ?>)"
                                            class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Stats -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Stats Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <h4 class="text-lg font-bold text-gray-800 mb-4">Statistiche Team</h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Totale Operatori</span>
                    <span class="text-2xl font-bold text-purple-600"><?= count($operators) ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Reparti Attivi</span>
                    <span class="text-2xl font-bold text-blue-600">
                        <?= $operators->pluck('reparto')->unique()->count() ?>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">PIN Attivi</span>
                    <span class="text-2xl font-bold text-emerald-600">
                        <?= $operators->where('pin', '!=', null)->where('pin', '!=', '')->count() ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Department Distribution -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <h4 class="text-lg font-bold text-gray-800 mb-4">Distribuzione Reparti</h4>
            <div class="space-y-3">
                <?php
                $departments = $operators->groupBy('reparto')->map(function($items) { return $items->count(); })->toArray();
                $colors = ['bg-purple-500', 'bg-blue-500', 'bg-emerald-500', 'bg-amber-500', 'bg-red-500'];
                $colorIndex = 0;
                ?>
                <?php foreach ($departments as $dept => $count): ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full <?= $colors[$colorIndex % count($colors)] ?> mr-3">
                            </div>
                            <span class="text-sm text-gray-700"><?= htmlspecialchars($dept) ?></span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900"><?= $count ?></span>
                    </div>
                    <?php $colorIndex++; ?>
                <?php endforeach; ?>
            </div>
        </div>

      
    </div>
</div>



<!-- Add/Edit Operator Modal -->
<div id="operatorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 id="modalTitle" class="text-lg font-bold text-gray-900">Nuovo Operatore</h3>
                <button onclick="closeOperatorModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="operatorForm" class="p-6">
            <input type="hidden" id="operatorId" name="id">
            <input type="hidden" id="operatorAction" name="action" value="create">

            <div class="space-y-4">
                <div>
                    <label for="operatorUser" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="operatorUser" name="user" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div>
                    <label for="operatorFullName" class="block text-sm font-medium text-gray-700 mb-1">Nome
                        Completo</label>
                    <input type="text" id="operatorFullName" name="full_name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div>
                    <label for="operatorPin" class="block text-sm font-medium text-gray-700 mb-1">PIN</label>
                    <input type="number" id="operatorPin" name="pin" required min="1000" max="9999"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <div>
                    <label for="operatorReparto" class="block text-sm font-medium text-gray-700 mb-1">Reparto</label>
                    <select id="operatorReparto" name="reparto" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Seleziona reparto</option>
                        <option value="MONTAGGIO">Montaggio</option>
                        <option value="FINITURA">Finitura</option>
                        <option value="CONTROLLO">Controllo</option>
                        <option value="SPEDIZIONE">Spedizione</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex space-x-3">
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-purple-500 to-indigo-600 text-white py-2 px-4 rounded-lg hover:from-purple-600 hover:to-indigo-700 transition-all font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Salva
                </button>
                <button type="button" onclick="closeOperatorModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Annulla
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Operator management functions
    let operatorsData = <?= json_encode($operators) ?>;

    function showAddOperatorModal() {
        document.getElementById('modalTitle').textContent = 'Nuovo Operatore';
        document.getElementById('operatorAction').value = 'create';
        document.getElementById('operatorForm').reset();
        document.getElementById('operatorId').value = '';
        document.getElementById('operatorModal').classList.remove('hidden');
    }

    function editOperator(operatorId) {
        const operator = operatorsData.find(op => op.id == operatorId);
        if (!operator) return;

        document.getElementById('modalTitle').textContent = 'Modifica Operatore';
        document.getElementById('operatorAction').value = 'update';
        document.getElementById('operatorId').value = operator.id;
        document.getElementById('operatorUser').value = operator.user;
        document.getElementById('operatorFullName').value = operator.full_name;
        document.getElementById('operatorPin').value = operator.pin;
        document.getElementById('operatorReparto').value = operator.reparto;
        document.getElementById('operatorModal').classList.remove('hidden');
    }

    function closeOperatorModal() {
        document.getElementById('operatorModal').classList.add('hidden');
        document.getElementById('operatorForm').reset();
    }

    function deleteOperator(operatorId) {
        WebgreModals.confirmDelete(
            'Sei sicuro di voler eliminare questo operatore?',
            () => {

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', operatorId);

        fetch('<?= $this->url('/quality/manage-operator') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    WebgreNotifications.success('Operatore eliminato con successo');
                    location.reload();
                } else {
                    WebgreNotifications.error('Errore durante l\'eliminazione: ' + (data.error || 'Errore sconosciuto'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                WebgreNotifications.error('Errore di connessione');
            });
        });
    }

    // Form submission
    document.getElementById('operatorForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('<?= $this->url('/quality/manage-operator') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    WebgreNotifications.success('Operatore salvato con successo');
                    location.reload();
                } else {
                    WebgreNotifications.error('Errore durante il salvataggio: ' + (data.error || 'Errore sconosciuto'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                WebgreNotifications.error('Errore di connessione');
            });
    });

    // Search and filter functionality
    document.getElementById('searchOperators').addEventListener('input', filterOperators);
    document.getElementById('filterByReparto').addEventListener('change', filterOperators);

    function filterOperators() {
        const searchTerm = document.getElementById('searchOperators').value.toLowerCase();
        const selectedReparto = document.getElementById('filterByReparto').value;
        const rows = document.querySelectorAll('#operatorsTableBody tr');

        rows.forEach(row => {
            const name = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const username = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const reparto = row.querySelector('td:nth-child(4) span').textContent;

            const matchesSearch = name.includes(searchTerm) || username.includes(searchTerm);
            const matchesReparto = !selectedReparto || reparto === selectedReparto;

            row.style.display = matchesSearch && matchesReparto ? '' : 'none';
        });
    }

    // Quick actions
    function exportOperators() {
        window.open('<?= $this->url('/quality/operators/export') ?>', '_blank');
    }

    function printOperators() {
        window.print();
    }

    function bulkActions() {
        WebgreNotifications.info('Funzionalità in sviluppo');
    }

    // Close modal on outside click
    document.getElementById('operatorModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeOperatorModal();
        }
    });
</script>