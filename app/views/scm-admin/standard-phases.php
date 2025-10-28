<?php
/**
 * SCM Admin - Gestione Fasi Standard
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                Fasi Standard
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Gestione delle fasi predefinite per i cicli produttivi
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button onclick="addStandardPhase()"
                class="inline-flex items-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i>
                Nuova Fase
            </button>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/') ?>"
                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/scm-admin') ?>"
                    class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    SCM Admin
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Cicli Standard</span>
            </div>
        </li>
    </ol>
</nav>


<!-- Standard Phases List -->
<div
    class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm overflow-hidden">
    <?php if (empty($standardPhases)): ?>
        <div class="text-center py-12">
            <i class="fas fa-list-ol text-gray-400 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessuna fase standard definita</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
                Crea le prime fasi standard per velocizzare la creazione dei lanci
            </p>
            <button onclick="addStandardPhase()"
                class="inline-flex items-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                Crea Prima Fase
            </button>
        </div>
    <?php else: ?>
        <div class="p-6">
            <div id="phases-container" class="space-y-4">
                <?php foreach ($standardPhases as $index => $phase): ?>
                    <div class="phase-item flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600"
                        data-phase-id="<?= $phase['id'] ?>">
                        <!-- Drag Handle -->
                        <div class="cursor-move text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-grip-vertical"></i>
                        </div>

                        <!-- Order Number -->
                        <div
                            class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                            <?= $phase['phase_order'] ?>
                        </div>

                        <!-- Phase Info -->
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <input type="text"
                                    class="phase-name-input w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white focus:border-blue-500 focus:ring-blue-500 font-medium"
                                    value="<?= htmlspecialchars($phase['phase_name']) ?>" placeholder="Nome fase..."
                                    onchange="updatePhase(<?= $phase['id'] ?>, 'phase_name', this.value)">
                            </div>
                            <div>
                                <input type="text"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                    value="<?= htmlspecialchars($phase['description'] ?? '') ?>"
                                    placeholder="Descrizione (opzionale)..."
                                    onchange="updatePhase(<?= $phase['id'] ?>, 'description', this.value)">
                            </div>
                            <div>
                                <select
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                    onchange="updatePhase(<?= $phase['id'] ?>, 'category', this.value)">
                                    <option value="production" <?= ($phase['category'] ?? 'production') === 'production' ? 'selected' : '' ?>>Produzione</option>
                                    <option value="quality" <?= ($phase['category'] ?? '') === 'quality' ? 'selected' : '' ?>>
                                        Controllo Qualità</option>
                                    <option value="packaging" <?= ($phase['category'] ?? '') === 'packaging' ? 'selected' : '' ?>>
                                        Confezionamento</option>
                                    <option value="finishing" <?= ($phase['category'] ?? '') === 'finishing' ? 'selected' : '' ?>>
                                        Finitura</option>
                                    <option value="other" <?= ($phase['category'] ?? '') === 'other' ? 'selected' : '' ?>>Altro
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-2">
                            <button onclick="duplicatePhase(<?= $phase['id'] ?>)"
                                class="p-2 text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 transition-colors rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                title="Duplica fase">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button onclick="deletePhase(<?= $phase['id'] ?>)"
                                class="p-2 text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-colors rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20"
                                title="Elimina fase">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-info-circle mr-2"></i>
                    Trascina le fasi per riordinarle
                </div>
                <div class="flex space-x-3">
                    <button onclick="addStandardPhase()"
                        class="inline-flex items-center px-4 py-2 border border-green-300 text-sm font-medium rounded-lg text-green-700 bg-green-50 hover:bg-green-100 dark:border-green-600 dark:text-green-300 dark:bg-green-900/20 dark:hover:bg-green-900/40 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Aggiungi Fase
                    </button>
                    <button onclick="saveOrder()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-sm font-medium rounded-lg text-white hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Salva Ordine
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>



<script>
    // Sortable phases
    let phasesSortable;

    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('phases-container');
        if (container) {
            phasesSortable = new Sortable(container, {
                handle: '.fa-grip-vertical',
                animation: 150,
                onEnd: function (evt) {
                    updatePhaseNumbers();
                }
            });
        }
    });

    function updatePhaseNumbers() {
        const phases = document.querySelectorAll('.phase-item');
        phases.forEach((phase, index) => {
            const numberElement = phase.querySelector('.rounded-full');
            numberElement.textContent = index + 1;
        });
    }

    function addStandardPhase() {
        const container = document.getElementById('phases-container');
        const phaseCount = container ? container.children.length + 1 : 1;

        fetch('<?= $this->url('/scm-admin/standard-phases/create') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                phase_name: '',
                description: '',
                category: 'production',
                phase_order: phaseCount
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Errore durante la creazione della fase');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Errore durante la creazione della fase');
            });
    }

    function updatePhase(phaseId, field, value) {
        fetch(`<?= $this->url('/scm-admin/standard-phases/') ?>${phaseId}/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                [field]: value
            })
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Errore durante l\'aggiornamento della fase');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Errore durante l\'aggiornamento della fase');
            });
    }

    function duplicatePhase(phaseId) {
        fetch(`<?= $this->url('/scm-admin/standard-phases/') ?>${phaseId}/duplicate`, {
            method: 'POST'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Errore durante la duplicazione della fase');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Errore durante la duplicazione della fase');
            });
    }

    function deletePhase(phaseId) {
        if (window.CoregreModals && window.CoregreModals.confirmDelete) {
            window.CoregreModals.confirmDelete(
                'Sei sicuro di voler eliminare questa fase standard?\n\nQuesta operazione è irreversibile.',
                () => confirmDeletePhase(phaseId),
                1
            );
        } else {
            // Fallback se CoregreModals non disponibile
            if (confirm('Sei sicuro di voler eliminare questa fase standard?\n\nQuesta operazione è irreversibile.')) {
                confirmDeletePhase(phaseId);
            }
        }
    }

    function confirmDeletePhase(phaseId) {
        fetch(`<?= $this->url('/scm-admin/standard-phases/') ?>${phaseId}/delete`, {
            method: 'DELETE'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Errore durante l\'eliminazione della fase');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Errore durante l\'eliminazione della fase');
            });
    }


    function saveOrder() {
        const phases = document.querySelectorAll('.phase-item');
        const order = Array.from(phases).map((phase, index) => ({
            id: parseInt(phase.dataset.phaseId),
            order: index + 1
        }));

        fetch('<?= $this->url('/scm-admin/standard-phases/reorder') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ order })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Ordine salvato con successo', 'success');
                } else {
                    alert('Errore durante il salvataggio dell\'ordine');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Errore durante il salvataggio dell\'ordine');
            });
    }

    function loadTemplate(templateName) {
        const templates = {
            'basic': 'Ciclo Base (4 fasi)',
            'advanced': 'Ciclo Avanzato (7 fasi)',
            'quality': 'Focus Qualità (6 fasi)'
        };

        const templateTitle = templates[templateName];

        if (confirm(`Vuoi caricare il template "${templateTitle}"?\n\nQuesta operazione sostituirà tutte le fasi attuali.`)) {
            fetch(`<?= $this->url('/scm-admin/standard-phases/load-template/') ?>${templateName}`, {
                method: 'POST'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Errore durante il caricamento del template');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Errore durante il caricamento del template');
                });
        }
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
        notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'} mr-2"></i>
            ${message}
        </div>
    `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
</script>

<!-- Sortable.js per drag & drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>