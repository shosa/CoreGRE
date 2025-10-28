<!-- Dashboard Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                Dashboard
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Benvenuto, <?= htmlspecialchars($userName) ?>
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                <i class="far fa-calendar-alt mr-1"></i>
                <?= $currentDate ?>
            </span>
            <button onclick="openWidgetCustomizer()"
                class="rounded-lg border border-primary bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-cog mr-2"></i>
                Personalizza Widget
            </button>
        </div>
    </div>
</div>

<!-- Dynamic Widget Grid -->
<div class="mb-8">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4 md:gap-6" id="widget-grid">
        <?php if (!empty($widgets)): ?>
            <?php foreach ($widgets as $widget): ?>
                <?php $data = $widgetData[$widget->widget_key] ?? null; ?>
                <?php if ($data !== null): ?>
                    <?= $this->renderWidget($widget, $data) ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-12">
                <i class="fas fa-puzzle-piece text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessun widget attivo</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Personalizza la tua dashboard aggiungendo i widget che ti
                    interessano.</p>
                <button onclick="openWidgetCustomizer()" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Aggiungi Widget
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Additional Sections -->
<?php
// Check if quick_actions widget is enabled - if so, it will be rendered above
// Otherwise, show a simplified version here
$hasQuickActionsWidget = false;
foreach ($widgets as $widget) {
    if ($widget->widget_key === 'quick_actions') {
        $hasQuickActionsWidget = true;
        break;
    }
}
?>

<?php if (!$hasQuickActionsWidget): ?>
    <!-- Quick Actions Section (fallback when widget not enabled) -->
    <div class="mb-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Azioni Rapide -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-bolt mr-3 text-warning-500"></i>
                        Azioni Rapide
                    </h3>
                </div>
                <div class="space-y-3">
                    <?php if ($this->hasPermission('riparazioni')): ?>
                        <a href="<?= $this->url('/riparazioni/create') ?>"
                            class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-blue-50 hover:border-blue-300 dark:border-gray-700 dark:hover:bg-blue-900/20 dark:hover:border-blue-500 transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 shadow-md">
                                <i class="fas fa-hammer text-white"></i>
                            </div>
                            <span class="ml-3 font-medium text-gray-900 dark:text-white">Nuova Riparazione</span>
                        </a>
                    <?php endif; ?>

                    <?php if ($this->hasPermission('produzione')): ?>
                        <a href="<?= $this->url('/produzione/new') ?>"
                            class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-yellow-50 hover:border-yellow-300 dark:border-gray-700 dark:hover:bg-yellow-900/20 dark:hover:border-yellow-500 transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-yellow-500 to-orange-500 shadow-md">
                                <i class="fas fa-industry text-white"></i>
                            </div>
                            <span class="ml-3 font-medium text-gray-900 dark:text-white">Nuova Produzione</span>
                        </a>
                    <?php endif; ?>

                    <?php if ($this->hasPermission('export')): ?>
                        <a href="<?= $this->url('/export/create') ?>"
                            class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-purple-50 hover:border-purple-300 dark:border-gray-700 dark:hover:bg-purple-900/20 dark:hover:border-purple-500 transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 shadow-md">
                                <i class="fas fa-file-export text-white"></i>
                            </div>
                            <span class="ml-3 font-medium text-gray-900 dark:text-white">Nuovo Export/DDT</span>
                        </a>
                    <?php endif; ?>

                    <?php if ($this->hasPermission('scm')): ?>
                        <a href="<?= $this->url('/scm-admin/launches/create') ?>"
                            class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-orange-50 hover:border-orange-300 dark:border-gray-700 dark:hover:bg-orange-900/20 dark:hover:border-orange-500 transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-red-500 shadow-md">
                                <i class="fas fa-rocket text-white"></i>
                            </div>
                            <span class="ml-3 font-medium text-gray-900 dark:text-white">Nuovo Lancio SCM</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Attività Recenti -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-history mr-3 text-gray-500 dark:text-gray-400"></i>
                        Attività Recenti
                    </h3>
                </div>
                <div class="space-y-4" id="recent-activities">
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                        <p>Caricamento attività...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Include Widget Customizer Component -->
<?php include APP_ROOT . '/app/views/components/widget-customizer.php'; ?>

<script>
    // Dashboard - JavaScript compatibile con PJAX
    (function () {
        // Variabili globali per cleanup
        let eventListeners = [];
        let refreshInterval = null;

        // Funzione di inizializzazione dashboard
        function initDashboard() {
            // Cleanup precedenti listeners e intervals
            cleanupEventListeners();
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }

            // Inizializza form personalizzazione
            initCustomizeForm();

            // Carica dati iniziali
            loadRecentActivities();

            // Avvia animazioni contatori dopo un breve delay
            setTimeout(animateCounters, 500);

            // Auto refresh stats ogni 5 minuti
            refreshInterval = setInterval(refreshStats, 5 * 60 * 1000);
        }

        function initCustomizeForm() {
            const customizeForm = document.getElementById('customizeForm');
            if (customizeForm) {
                function formSubmitHandler(e) {
                    e.preventDefault();

                    const formData = new FormData(customizeForm);
                    const data = {};

                    // Converti checkboxes in 1/0
                    const checkboxes = customizeForm.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(checkbox => {
                        data[checkbox.name] = checkbox.checked ? 1 : 0;
                    });

                    apiCall('/api/dashboard/update-preferences', {
                        method: 'POST',
                        body: data
                    })
                        .then(response => {
                            closeCustomizeModal();
                            showAlert('Preferenze aggiornate con successo!', 'success');

                            // Ricarica la pagina dopo un breve delay
                            setTimeout(() => {
                                if (window.pjax) {
                                    window.pjax.navigateTo(window.location.href);
                                } else {
                                    location.reload();
                                }
                            }, 1000);
                        })
                        .catch(error => {
                            showAlert('Errore durante l\'aggiornamento delle preferenze: ' + error.message, 'error');
                        });
                }

                customizeForm.addEventListener('submit', formSubmitHandler);
                eventListeners.push({ element: customizeForm, event: 'submit', handler: formSubmitHandler });
            }
        }

        function cleanupEventListeners() {
            eventListeners.forEach(({ element, event, handler }) => {
                element.removeEventListener(event, handler);
            });
            eventListeners = [];
        }

        // Funzioni globali per widget customizer
        window.openWidgetCustomizer = function () {
            if (typeof showWidgetCustomizer === 'function') {
                showWidgetCustomizer();
            } else if (typeof openCustomizerModal === 'function') {
                openCustomizerModal();
            }
        };

        // Funzioni di navigazione per i widget
        window.navigateToRiparazioni = function () {
            if (window.pjax && typeof window.pjax.navigateTo === 'function') {
                window.pjax.navigateTo('<?= $this->url('/riparazioni') ?>');
            } else {
                window.location.href = '<?= $this->url('/riparazioni') ?>';
            }
        };

        window.navigateToQuality = function () {
            if (window.pjax && typeof window.pjax.navigateTo === 'function') {
                window.pjax.navigateTo('<?= $this->url('/quality') ?>');
            } else {
                window.location.href = '<?= $this->url('/quality') ?>';
            }
        };

        window.navigateToProduzione = function () {
            if (window.pjax && typeof window.pjax.navigateTo === 'function') {
                window.pjax.navigateTo('<?= $this->url('/produzione') ?>');
            } else {
                window.location.href = '<?= $this->url('/produzione') ?>';
            }
        };

        // Carica attività recenti
        async function loadRecentActivities() {
            try {
                const response = await fetch(window.WEBGRE.baseUrl + '/api/dashboard/recent-activities');
                const activities = await response.json();

                const container = document.getElementById('recent-activities');

                if (!container) return;

                if (activities.length === 0) {
                    container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="far fa-calendar-times text-2xl mb-2"></i>
                        <p>Nessuna attività recente</p>
                    </div>
                `;
                    return;
                }

                const activitiesHTML = activities.map(activity => `
                <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 rounded-xl transition-colors">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/20">
                            <i class="fas fa-${activity.icon} text-sm text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate uppercase">
                            ${activity.description}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            ${activity.time}
                        </p>
                    </div>
                </div>
            `).join('');

                container.innerHTML = activitiesHTML;

            } catch (error) {
                console.error('Error loading activities:', error);
                const container = document.getElementById('recent-activities');
                if (container) {
                    container.innerHTML = `
                    <div class="text-center py-8 text-red-500">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <p>Errore nel caricamento delle attività</p>
                    </div>
                `;
                }
            }
        }

        // Funzione per animare i contatori
        function animateCounters() {
            const counters = document.querySelectorAll('.counter-animate');

            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                if (isNaN(target)) return;

                const duration = 2000; // 2 secondi
                const increment = target / (duration / 16); // 60fps
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 16);
            });
        }

        // Refresh intelligente dei dati senza ricaricare la pagina
        async function refreshStats() {
            try {
                const response = await fetch(window.WEBGRE.baseUrl + '/api/dashboard/stats');
                const newStats = await response.json();

                // Aggiorna i contatori con i nuovi valori
                if (newStats.riparazioni) {
                    const riparazioniCounter = document.querySelector('[data-target*="riparazioni"][data-target*="totali"]');
                    if (riparazioniCounter) {
                        riparazioniCounter.setAttribute('data-target', newStats.riparazioni.totali);
                        riparazioniCounter.textContent = newStats.riparazioni.totali;
                    }

                    const mieRiparazioniCounter = document.querySelector('[data-target*="riparazioni"][data-target*="mie"]');
                    if (mieRiparazioniCounter) {
                        mieRiparazioniCounter.setAttribute('data-target', newStats.riparazioni.mie);
                        mieRiparazioniCounter.textContent = newStats.riparazioni.mie;
                    }
                }

                if (newStats.quality) {
                    const qualityCounter = document.querySelector('[data-target*="quality"]');
                    if (qualityCounter) {
                        qualityCounter.setAttribute('data-target', newStats.quality.oggi);
                        qualityCounter.textContent = newStats.quality.oggi;
                    }
                }

                if (newStats.produzione) {
                    const prodWeekCounter = document.querySelector('[data-target*="produzione"][data-target*="settimana"]');
                    if (prodWeekCounter) {
                        prodWeekCounter.setAttribute('data-target', newStats.produzione.settimana);
                        prodWeekCounter.textContent = newStats.produzione.settimana;
                    }

                    const prodMonthCounter = document.querySelector('[data-target*="produzione"][data-target*="mese"]');
                    if (prodMonthCounter) {
                        prodMonthCounter.setAttribute('data-target', newStats.produzione.mese);
                        prodMonthCounter.textContent = newStats.produzione.mese;
                    }
                }

                // Ricarica anche le attività recenti
                loadRecentActivities();

            } catch (error) {
                console.error('Stats refresh failed:', error);
                // Fallback a reload completo in caso di errore critico
                if (error.status === 401 || error.status === 403) {
                    if (window.pjax) {
                        window.pjax.navigateTo(window.location.href);
                    } else {
                        location.reload();
                    }
                }
            }
        }

        // Registra l'inizializzatore per PJAX
        if (window.WEBGRE && window.WEBGRE.onPageLoad) {
            window.WEBGRE.onPageLoad(initDashboard);
        }

        // Inizializza anche al primo caricamento
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDashboard);
        } else {
            initDashboard();
        }
    })();
</script>