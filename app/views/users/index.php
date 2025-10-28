<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                Gestione Utenti
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Gestisci gli utenti del sistema e i loro permessi
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="<?= $this->url('/users/create') ?>"
                class="inline-flex items-center rounded-lg border border-primary bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-user-plus mr-2"></i>
                Nuovo Utente
            </a>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-8" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/') ?>"
                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                    Gestione Utenti
                </span>
            </div>
        </li>
    </ol>
</nav>
<!-- Stats Cards -->
<div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-users text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Totale Utenti</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $totalUsers ?></p>
            </div>
        </div>
    </div>

    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 shadow-lg">
                <i class="fas fa-user-shield text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Amministratori</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= $users->filter(fn($u) => $u->admin_type === 'admin')->count() ?>
                </p>
            </div>
        </div>
    </div>

    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 shadow-lg">
                <i class="fas fa-user-tie text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Manager</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= $users->filter(fn($u) => $u->admin_type === 'manager')->count() ?>
                </p>
            </div>
        </div>
    </div>

    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-yellow-500 to-orange-500 shadow-lg">
                <i class="fas fa-user text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Utenti Standard</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
<?= $users->filter(fn($u) => in_array($u->admin_type, ['user', 'viewer']))->count() ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Utenti Table -->
<div
    class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
    <!-- Table Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-users mr-3 text-blue-500"></i>
            Lista Utenti
        </h3>
        <div class="flex items-center space-x-3">
            <div class="flex items-center">
                <input type="checkbox" id="master-checkbox"
                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="master-checkbox" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Seleziona
                    tutto</label>
            </div>
            <button type="button" id="delete-selected" disabled
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-trash-alt mr-1"></i>
                Elimina Selezionati
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        <input type="checkbox"
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Utente
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Email
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Ruolo
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Ultimo Accesso
                    </th>
                    <th
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Azioni
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox"
                                    class="row-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    value="<?= $user->id ?>">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            <?= htmlspecialchars($user->nome) ?>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            @<?= htmlspecialchars($user->user_name) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($user->mail ?: 'Non specificata') ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $badgeColors = [
                                    'admin' => 'bg-red-100 text-red-800 dark:bg-red-800/20 dark:text-red-300',
                                    'manager' => 'bg-purple-100 text-purple-800 dark:bg-purple-800/20 dark:text-purple-300',
                                    'user' => 'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300',
                                    'viewer' => 'bg-gray-100 text-gray-800 dark:bg-gray-800/20 dark:text-gray-300'
                                ];
                                $badgeClass = $badgeColors[$user->admin_type] ?? $badgeColors['user'];
                                ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $badgeClass ?>">
                                    <?= ucfirst($user->admin_type) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <?= $user->last_login ? date('d/m/Y', strtotime($user->last_login)) : 'Mai' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="<?= $this->url('/users/' . $user->id . '/permissions') ?>"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-purple-100 text-purple-600 hover:bg-purple-200 hover:text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 dark:hover:bg-purple-800/40 transition-colors"
                                        title="Gestisci Permessi">
                                        <i class="fas fa-user-cog"></i>
                                    </a>
                                    <a href="<?= $this->url('/users/' . $user->id . '/edit') ?>"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/40 transition-colors"
                                        title="Modifica">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteUser(<?= $user->id ?>)"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-700 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-800/40 transition-colors"
                                        title="Elimina">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500 dark:text-gray-400">
                                <i class="fas fa-users text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Nessun utente trovato</p>
                                <p class="mt-2">Inizia aggiungendo il primo utente al sistema.</p>
                                <a href="<?= $this->url('/users/create') ?>"
                                    class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Aggiungi Utente
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Users Index - JavaScript compatibile con PJAX
    (function () {
        // Variabili globali per cleanup
        let eventListeners = [];

        // Funzione di inizializzazione
        function initUsersIndex() {
            // Cleanup precedenti event listeners
            cleanupEventListeners();

            const masterCheckbox = document.getElementById('master-checkbox');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const deleteBtn = document.getElementById('delete-selected');

            if (!masterCheckbox || !deleteBtn) return;

            if (rowCheckboxes.length === 0) {
                deleteBtn.disabled = true;
                return;
            }

            // Seleziona/deseleziona tutto
            function masterCheckboxHandler() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = masterCheckbox.checked;
                });
                updateDeleteButton();
                updateRowHighlights();
            }
            masterCheckbox.addEventListener('change', masterCheckboxHandler);
            eventListeners.push({ element: masterCheckbox, event: 'change', handler: masterCheckboxHandler });

            // Gestione selezione singola riga
            rowCheckboxes.forEach(checkbox => {
                function checkboxHandler() {
                    updateMasterCheckbox();
                    updateDeleteButton();
                    updateRowHighlight(this);
                }
                checkbox.addEventListener('change', checkboxHandler);
                eventListeners.push({ element: checkbox, event: 'change', handler: checkboxHandler });
            });

            function updateMasterCheckbox() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                masterCheckbox.checked = checkedCount === rowCheckboxes.length;
                masterCheckbox.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
            }

            function updateDeleteButton() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                deleteBtn.disabled = checkedCount === 0;
            }

            function updateRowHighlight(checkbox) {
                const row = checkbox.closest('tr');
                if (checkbox.checked) {
                    row.classList.add('bg-blue-50', 'dark:bg-blue-900/20');
                } else {
                    row.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
                }
            }

            function updateRowHighlights() {
                rowCheckboxes.forEach(updateRowHighlight);
            }

            // Gestione eliminazione multipla
            function deleteSelectedHandler() {
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                    .map(cb => cb.value);

                if (selectedIds.length === 0) return;

                const count = selectedIds.length;
                const message = count === 1
                    ? 'Sei sicuro di voler eliminare questo utente?'
                    : `Sei sicuro di voler eliminare ${count} utenti?`;

                if (window.WebgreModals) {
                    window.WebgreModals.confirm({
                        title: 'Conferma Eliminazione',
                        message: message,
                        type: 'danger',
                        confirmText: 'Elimina',
                        cancelText: 'Annulla',
                        onConfirm: () => confirmDelete(selectedIds)
                    });
                } else {
                    // Fallback
                    if (confirm(message)) {
                        confirmDelete(selectedIds);
                    }
                }
            }
            deleteBtn.addEventListener('click', deleteSelectedHandler);
            eventListeners.push({ element: deleteBtn, event: 'click', handler: deleteSelectedHandler });
        }

        function cleanupEventListeners() {
            eventListeners.forEach(({ element, event, handler }) => {
                element.removeEventListener(event, handler);
            });
            eventListeners = [];
        }

        // Gestione eliminazione singola - funzione globale
        window.deleteUser = function (id) {
            if (window.WebgreModals) {
                window.WebgreModals.confirm({
                    title: 'Conferma Eliminazione',
                    message: 'Sei sicuro di voler eliminare questo utente?',
                    type: 'danger',
                    confirmText: 'Elimina',
                    cancelText: 'Annulla',
                    onConfirm: () => confirmDelete([id])
                });
            } else {
                // Fallback
                if (confirm('Sei sicuro di voler eliminare questo utente?')) {
                    confirmDelete([id]);
                }
            }
        };

        // Funzione di eliminazione
        async function confirmDelete(ids) {
            try {
                const response = await fetch(`<?= $this->url('/users/delete') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': window.WEBGRE ? window.WEBGRE.csrfToken : ''
                    },
                    body: JSON.stringify({ ids: ids })
                });

                if (!response.ok) {
                    throw new Error(`Errore server: ${response.status}`);
                }

                const data = await response.json();

                if (!data.success || data.error) {
                    throw new Error(data.error || 'Errore sconosciuto durante l\'eliminazione');
                }

                // Mostra notifica di successo
                WebgreNotifications.success(data.message || 'Utenti eliminati con successo');

                // Ricarica la pagina per aggiornare la lista
                setTimeout(() => {
                    if (window.WEBGRE && window.WEBGRE.pjax) {
                        window.WEBGRE.pjax.reload();
                    } else {
                        location.reload();
                    }
                }, 1500);

            } catch (error) {
                console.error('Error deleting users:', error);

                // Mostra errore con sistema notifiche
                const errorMessage = error.message || 'Errore sconosciuto durante l\'eliminazione';
                WebgreNotifications.error(errorMessage);
            }
        }

        // Registra l'inizializzatore per PJAX
        if (window.WEBGRE && window.WEBGRE.onPageLoad) {
            window.WEBGRE.onPageLoad(initUsersIndex);
        }

        // Inizializza anche al primo caricamento
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initUsersIndex);
        } else {
            initUsersIndex();
        }
    })();
</script>