<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div
                    class="mr-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 shadow-lg">
                    <i class="fas fa-building text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                      Reparti CQ
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Gestione Reparti Controllo Qualità
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
                <span class="text-gray-700 dark:text-gray-300">Reparti</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Form Gestione Reparto -->
    <div class="lg:col-span-1">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-plus-circle mr-3 text-orange-500"></i>
                    Gestione Reparto
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                    Aggiungi o modifica reparti del controllo qualità
                </p>
            </div>

            <form id="departmentForm" class="space-y-4">
                <input type="hidden" id="departmentId" name="id" value="">
                <input type="hidden" id="formAction" name="action" value="create">

                <div>
                    <label for="nome_reparto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nome Reparto <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nome_reparto" name="nome_reparto" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-orange-400"
                        placeholder="Es. Montaggio, Controllo, Finitura...">
                </div>

                <div>
                    <label for="ordine" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Ordine Visualizzazione
                    </label>
                    <input type="number" id="ordine" name="ordine" min="0" max="999"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-orange-400"
                        placeholder="0">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="attivo" name="attivo" checked
                        class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                    <label for="attivo" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Reparto Attivo
                    </label>
                </div>

                <div class="pt-4 space-y-3">
                    <button type="submit" id="submitBtn"
                        class="w-full rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-2 text-sm font-medium text-white hover:from-orange-600 hover:to-orange-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i>
                        <span id="submitText">Aggiungi Reparto</span>
                    </button>

                    <button type="button" id="cancelBtn" style="display: none;"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Annulla Modifica
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista Reparti -->
    <div class="lg:col-span-2">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="border-b border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-list mr-3 text-orange-500"></i>
                    Reparti Configurati (<?= count($departments) ?>)
                </h3>
            </div>

            <div class="p-6">
                <?php if (!empty($departments)): ?>
                    <div class="space-y-3" id="departmentsList">
                        <?php foreach ($departments as $dept): ?>
                            <div class="department-item flex items-center justify-between p-4 rounded-xl border border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50 transition-colors"
                                 data-id="<?= $dept->id ?>">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/20">
                                        <i class="fas fa-building text-orange-600 dark:text-orange-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white">
                                            <?= htmlspecialchars($dept->nome_reparto) ?>
                                        </h4>
                                        <div class="flex items-center space-x-3 text-sm text-gray-500 dark:text-gray-400">
                                            <span>Ordine: <?= $dept->ordine ?></span>
                                            <span class="px-2 py-1 rounded-full text-xs <?= $dept->attivo ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400' ?>">
                                                <?= $dept->attivo ? 'Attivo' : 'Disattivo' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <button onclick="editDepartment(<?= $dept->id ?>, '<?= addslashes($dept->nome_reparto) ?>', <?= $dept->ordine ?>, <?= $dept->attivo ?>)"
                                        class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg dark:text-blue-400 dark:hover:bg-blue-900/20 transition-colors"
                                        title="Modifica">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button onclick="deleteDepartment(<?= $dept->id ?>, '<?= addslashes($dept->nome_reparto) ?>')"
                                        class="p-2 text-red-600 hover:bg-red-100 rounded-lg dark:text-red-400 dark:hover:bg-red-900/20 transition-colors"
                                        title="Elimina">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fas fa-building text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessun reparto configurato</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Inizia aggiungendo il primo reparto per il controllo qualità.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript compatibile con PJAX per gestione reparti
(function() {
    let eventListeners = [];

    function initDepartmentsPage() {
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
        const form = document.getElementById('departmentForm');
        if (form) {
            function formSubmitHandler(e) {
                e.preventDefault();
                saveDepartment();
            }
            form.addEventListener('submit', formSubmitHandler);
            eventListeners.push({ element: form, event: 'submit', handler: formSubmitHandler });
        }

        const cancelBtn = document.getElementById('cancelBtn');
        if (cancelBtn) {
            function cancelHandler() {
                resetForm();
            }
            cancelBtn.addEventListener('click', cancelHandler);
            eventListeners.push({ element: cancelBtn, event: 'click', handler: cancelHandler });
        }
    }

    // Funzioni globali per la gestione reparti
    window.editDepartment = function(id, nome, ordine, attivo) {
        document.getElementById('departmentId').value = id;
        document.getElementById('formAction').value = 'update';
        document.getElementById('nome_reparto').value = nome;
        document.getElementById('ordine').value = ordine;
        document.getElementById('attivo').checked = attivo == 1;

        document.getElementById('submitText').textContent = 'Salva Modifiche';
        document.getElementById('cancelBtn').style.display = 'block';

        // Scroll al form
        document.getElementById('departmentForm').scrollIntoView({ behavior: 'smooth' });
    };

    window.deleteDepartment = function(id, nome) {
        if (confirm(`Sei sicuro di voler eliminare il reparto "${nome}"?`)) {
            apiCall('/quality/manage-department', {
                method: 'POST',
                body: {
                    action: 'delete',
                    id: id
                }
            }).then(response => {
                showAlert('Reparto eliminato con successo!', 'success');
                setTimeout(() => {
                    if (window.pjax) {
                        window.pjax.navigateTo(window.location.href);
                    } else {
                        location.reload();
                    }
                }, 1000);
            }).catch(error => {
                showAlert('Errore durante l\'eliminazione: ' + error.message, 'error');
            });
        }
    };

    function saveDepartment() {
        const formData = new FormData(document.getElementById('departmentForm'));
        const data = {};

        formData.forEach((value, key) => {
            if (key === 'attivo') {
                data[key] = document.getElementById('attivo').checked ? 1 : 0;
            } else {
                data[key] = value;
            }
        });

        apiCall('/quality/manage-department', {
            method: 'POST',
            body: data
        }).then(response => {
            const action = data.action === 'create' ? 'aggiunto' : 'modificato';
            showAlert(`Reparto ${action} con successo!`, 'success');
            resetForm();

            setTimeout(() => {
                if (window.pjax) {
                    window.pjax.navigateTo(window.location.href);
                } else {
                    location.reload();
                }
            }, 1000);
        }).catch(error => {
            showAlert('Errore durante il salvataggio: ' + error.message, 'error');
        });
    }

    function resetForm() {
        document.getElementById('departmentForm').reset();
        document.getElementById('departmentId').value = '';
        document.getElementById('formAction').value = 'create';
        document.getElementById('submitText').textContent = 'Aggiungi Reparto';
        document.getElementById('cancelBtn').style.display = 'none';
        document.getElementById('attivo').checked = true;
    }

    // Registra l'inizializzatore per PJAX
    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initDepartmentsPage);
    }

    // Inizializza anche al primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDepartmentsPage);
    } else {
        initDepartmentsPage();
    }
})();
</script>