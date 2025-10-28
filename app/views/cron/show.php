<!-- Job Detail Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg mr-4">
                    <i class="fas fa-cog text-xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        <?= htmlspecialchars($job->name()) ?>
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        <?= htmlspecialchars($job->description() ?? '') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="<?= $this->url('/cron') ?>"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-arrow-left mr-2"></i>
                Indietro
            </a>
            <button onclick="runJobManually('<?= htmlspecialchars($job_class) ?>')"
                class="inline-flex items-center rounded-lg border border-blue-300 bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-play mr-2"></i>
                Esegui Ora
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
                <a href="<?= $this->url('/cron') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                    Cron Jobs
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300"><?= htmlspecialchars($job->name()) ?></span>
            </div>
        </li>
    </ol>
</nav>

<!-- Job Info Card -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm mb-6">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-info-circle mr-3 text-purple-500"></i>
            Informazioni Job
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Classe</label>
                <p class="mt-1 text-sm font-mono text-gray-900 dark:text-white"><?= htmlspecialchars($job_class) ?></p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Schedule</label>
                <p class="mt-1 text-sm font-mono text-gray-900 dark:text-white"><?= htmlspecialchars($job->schedule() ?? 'Sempre attivo') ?></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><?= htmlspecialchars($schedule_description) ?></p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Prossima Esecuzione</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                    <?php if ($next_run): ?>
                        <?= $next_run->format('d/m/Y H:i:s') ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Timeout</label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                    <?= $job->timeout() > 0 ? $job->timeout() . 's' : 'Nessuno' ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Statistiche -->
<div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Esecuzioni totali -->
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 dark:border-gray-800 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-play text-xl text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['total_runs'] ?? 0 ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Esecuzioni (30gg)</p>
            </div>
        </div>
    </div>

    <!-- Successi -->
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 dark:border-gray-800 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                <i class="fas fa-check-circle text-xl text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['successful'] ?? 0 ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Successi</p>
            </div>
        </div>
    </div>

    <!-- Fallimenti -->
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 dark:border-gray-800 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-600 shadow-lg">
                <i class="fas fa-exclamation-triangle text-xl text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= $stats['failed'] ?? 0 ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Fallimenti</p>
            </div>
        </div>
    </div>

    <!-- Durata media -->
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 dark:border-gray-800 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 shadow-lg">
                <i class="fas fa-clock text-xl text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-white"><?= round($stats['avg_duration'] ?? 0, 2) ?>s</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Durata media</p>
            </div>
        </div>
    </div>
</div>

<!-- Log esecuzioni -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-history mr-3 text-orange-500"></i>
            Log Esecuzioni
        </h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Ultimi 50 log per questo job
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        ID
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Stato
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Data Esecuzione
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Completato
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Durata
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Azioni
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800/40 divide-y divide-gray-200 dark:divide-gray-700">
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-4 block"></i>
                            Nessun log disponibile per questo job
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-mono text-gray-900 dark:text-white">#<?= $log->id ?></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php
                                $statusClass = match($log->status) {
                                    'success' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                    'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                    'running' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'
                                };
                                $statusIcon = match($log->status) {
                                    'success' => 'fa-check-circle',
                                    'failed' => 'fa-times-circle',
                                    'running' => 'fa-spinner fa-spin',
                                    default => 'fa-question-circle'
                                };
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                    <i class="fas <?= $statusIcon ?> mr-1"></i>
                                    <?= htmlspecialchars(ucfirst($log->status)) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-900 dark:text-white">
                                    <?= $log->started_at->format('d/m/Y H:i:s') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-900 dark:text-white">
                                    <?= $log->completed_at ? $log->completed_at->format('H:i:s') : '-' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-900 dark:text-white font-mono">
                                    <?= $log->duration_formatted ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="viewLogDetail(<?= $log->id ?>)"
                                    class="inline-flex items-center px-2 py-1.5 rounded-lg bg-purple-100 text-purple-600 hover:bg-purple-200 hover:text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 dark:hover:bg-purple-800/40 transition-colors text-xs font-medium">
                                    <i class="fas fa-eye mr-1.5"></i>
                                    Dettagli
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Dettagli Log -->
<div id="log-detail-modal" class="hidden fixed inset-0 z-[99999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-4 text-center">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="WebgreModals.closeModal('log-detail-modal')"></div>

        <div class="relative inline-block align-middle bg-white dark:bg-gray-800 rounded-2xl px-6 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all max-w-4xl w-full mx-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/30 mr-3">
                        <i class="fas fa-file-alt text-purple-600 dark:text-purple-400"></i>
                    </div>
                    Dettagli Log
                </h3>
                <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" onclick="WebgreModals.closeModal('log-detail-modal')">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mt-6">
                <div id="log-detail-content" class="text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500 mx-auto"></div>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">Caricamento dettagli...</p>
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700 mt-6">
                <button onclick="WebgreModals.closeModal('log-detail-modal')"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500 transition-colors">
                    Chiudi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    let eventListeners = [];

    function initCronShow() {
        cleanupEventListeners();

        window.runJobManually = async function(jobClass) {
            if (!confirm('Sei sicuro di voler eseguire manualmente questo job?')) {
                return;
            }

            try {
                const response = await fetch('<?= $this->url('/cron/run') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': window.WEBGRE ? window.WEBGRE.csrfToken : ''
                    },
                    body: 'job_class=' + encodeURIComponent(jobClass)
                });

                const result = await response.json();

                if (result.success) {
                    if (window.showAlert) {
                        window.showAlert(result.message || 'Job eseguito con successo', 'success');
                    }
                    setTimeout(() => {
                        if (window.pjax) {
                            window.pjax.navigateTo(window.location.href);
                        } else {
                            window.location.reload();
                        }
                    }, 1500);
                } else {
                    if (window.showAlert) {
                        window.showAlert(result.message || 'Errore durante esecuzione job', 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                if (window.showAlert) {
                    window.showAlert('Errore durante esecuzione job', 'error');
                }
            }
        };

        window.viewLogDetail = async function(logId) {
            const content = document.getElementById('log-detail-content');

            WebgreModals.openModal('log-detail-modal');

            content.innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500 mx-auto"></div>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">Caricamento dettagli...</p>
                </div>
            `;

            try {
                const response = await fetch('<?= $this->url('/cron/log-detail') ?>?id=' + logId, {
                    headers: {
                        'X-CSRF-TOKEN': window.WEBGRE ? window.WEBGRE.csrfToken : ''
                    }
                });

                const result = await response.json();

                if (result.success) {
                    const log = result.log;
                    content.innerHTML = `
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Job</label>
                                    <p class="text-sm text-gray-900 dark:text-white">${log.job_name}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Stato</label>
                                    <p class="text-sm text-gray-900 dark:text-white capitalize">${log.status}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Inizio</label>
                                    <p class="text-sm text-gray-900 dark:text-white">${log.started_at}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Fine</label>
                                    <p class="text-sm text-gray-900 dark:text-white">${log.completed_at || 'N/A'}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Durata</label>
                                    <p class="text-sm text-gray-900 dark:text-white">${log.duration_formatted}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Schedule</label>
                                    <p class="text-sm text-gray-900 dark:text-white font-mono">${log.schedule || 'N/A'}</p>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Output</label>
                                <pre class="mt-2 p-4 bg-gray-100 dark:bg-gray-900 rounded-lg text-xs overflow-auto max-h-96 font-mono text-gray-900 dark:text-gray-100">${log.output || 'Nessun output'}</pre>
                            </div>
                        </div>
                    `;
                } else {
                    throw new Error(result.error || 'Errore caricamento log');
                }
            } catch (error) {
                console.error('Error:', error);
                if (window.showAlert) {
                    window.showAlert('Errore durante esecuzione log', 'error');
                }
            }
        };
    }

    function cleanupEventListeners() {
        eventListeners.forEach(({ element, event, handler }) => {
            element.removeEventListener(event, handler);
        });
        eventListeners = [];
    }

    // Registrazione PJAX
    if (window.WEBGRE && window.WEBGRE.onPageLoad) {
        window.WEBGRE.onPageLoad(initCronShow);
    }

    // Inizializza al primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCronShow);
    } else {
        initCronShow();
    }
})();
</script>
