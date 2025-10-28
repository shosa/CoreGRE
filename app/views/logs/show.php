<!-- Dashboard Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mr-4">
                    <i class="fas fa-file-alt text-xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Dettaglio Log #<?= $log->id ?>
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Visualizza i dettagli completi del log attività
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="<?= $this->url('/logs') ?>" 
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna ai Log
            </a>
            <?php if ($_SESSION['admin_type'] === 'ADMIN'): ?>
            <button onclick="deleteLog(<?= $log->id ?>)" 
                    class="inline-flex items-center rounded-lg border border-red-300 bg-gradient-to-r from-red-500 to-red-600 px-4 py-2 text-sm font-medium text-white hover:from-red-600 hover:to-red-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-trash mr-2"></i>
                Elimina Log
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
                <a href="<?= $this->url('/logs') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                    Log Attività
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Log #<?= $log->id ?></span>
            </div>
        </li>
    </ol>
</nav>

<!-- Dettaglio Log -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Colonna Principale -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Informazioni Generali -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-info-circle mr-3 text-blue-500"></i>
                    Informazioni Generali
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <!-- ID e Timestamp -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-hashtag mr-1"></i>
                            ID Log
                        </label>
                        <div class="flex items-center">
                            <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/20 px-3 py-1 text-sm font-medium text-blue-800 dark:text-blue-300">
                                #<?= $log->id ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-clock mr-1"></i>
                            Data e Ora
                        </label>
                        <div class="text-sm text-gray-900 dark:text-white">
                            <div class="font-medium"><?= date('d/m/Y H:i:s', strtotime($log->created_at)) ?></div>
                            <div class="text-gray-500 dark:text-gray-400">
                                <?= $this->timeAgo($log->created_at) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categoria e Tipo -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-tags mr-1"></i>
                            Categoria
                        </label>
                        <?php if ($log->category): ?>
                            <?php
                            $categoryColors = [
                                'SYSTEM' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                'USER' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                'AUTH' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300 border-green-200 dark:border-green-800',
                                'ERROR' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300 border-red-200 dark:border-red-800',
                                'DATA' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800'
                            ];
                            $colorClass = $categoryColors[$log->category] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300 border-gray-200 dark:border-gray-800';
                            ?>
                            <span class="inline-flex items-center rounded-lg border px-3 py-2 text-sm font-medium <?= $colorClass ?>">
                                <i class="fas fa-tag mr-2"></i>
                                <?= htmlspecialchars($log->category) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-gray-400 dark:text-gray-500 italic">Non specificata</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-cogs mr-1"></i>
                            Tipo Attività
                        </label>
                        <?php if ($log->activity_type): ?>
                            <span class="inline-flex items-center rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm font-mono text-gray-900 dark:text-white">
                                <?= htmlspecialchars($log->activity_type) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-gray-400 dark:text-gray-500 italic">Non specificato</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Descrizione -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-file-text mr-1"></i>
                        Descrizione
                    </label>
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-4">
                        <?php if ($log->description): ?>
                            <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap"><?= htmlspecialchars($log->description) ?></p>
                        <?php else: ?>
                            <p class="text-sm text-gray-400 dark:text-gray-500 italic">Nessuna descrizione</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Note e Query -->
        <?php if ($log->note || $log->text_query): ?>
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-sticky-note mr-3 text-yellow-500"></i>
                    Note e Dettagli Tecnici
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <!-- Note -->
                <?php if ($log->note): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-sticky-note mr-1"></i>
                        Note
                    </label>
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-4">
                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap"><?= htmlspecialchars($log->note) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Query/Dettagli Tecnici -->
                <?php if ($log->text_query): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-code mr-1"></i>
                        Query / Dettagli Tecnici
                    </label>
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-mono">TECHNICAL DATA</span>
                            <button onclick="copyToClipboard(this)" data-text="<?= htmlspecialchars($log->text_query) ?>" 
                                    class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                                <i class="fas fa-copy mr-1"></i>
                                Copia
                            </button>
                        </div>
                        <pre class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap break-all font-mono bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-600 rounded p-3 overflow-x-auto"><?= htmlspecialchars($log->text_query) ?></pre>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="space-y-8">
        <!-- Informazioni Utente -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-user mr-3 text-green-500"></i>
                    Utente
                </h3>
            </div>
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mr-4">
                        <i class="fas fa-user text-xl text-white"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">
                            <?= htmlspecialchars($log->nome ?? $log->user->user_name ?? 'Sistema') ?>
                        </div>
                        <?php if ($log->user_name && $log->nome): ?>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            @<?= htmlspecialchars($log->user_name) ?>
                        </div>
                        <?php endif; ?>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            ID: <?= $log->user_id ?? 'N/A' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Azioni Rapide -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-bolt mr-3 text-orange-500"></i>
                    Azioni Rapide
                </h3>
            </div>
            <div class="p-6 space-y-3">
                <?php if ($log->user_id): ?>
                <a href="<?= $this->url('/logs?user_id=' . $log->user_id) ?>"
                   class="flex items-center w-full px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                    <i class="fas fa-search mr-3 text-blue-500"></i>
                    Altri log di questo utente
                </a>
                <?php endif; ?>
                
                <?php if ($log->category): ?>
                <a href="<?= $this->url('/logs?category=' . urlencode($log->category)) ?>"
                   class="flex items-center w-full px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                    <i class="fas fa-tags mr-3 text-purple-500"></i>
                    Log della stessa categoria
                </a>
                <?php endif; ?>
                
                <?php if ($log->activity_type): ?>
                <a href="<?= $this->url('/logs?activity_type=' . urlencode($log->activity_type)) ?>"
                   class="flex items-center w-full px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                    <i class="fas fa-cogs mr-3 text-green-500"></i>
                    Log dello stesso tipo
                </a>
                <?php endif; ?>
                
                <a href="<?= $this->url('/logs?date_from=' . date('Y-m-d', strtotime($log->created_at)) . '&date_to=' . date('Y-m-d', strtotime($log->created_at))) ?>"
                   class="flex items-center w-full px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                    <i class="fas fa-calendar mr-3 text-yellow-500"></i>
                    Log dello stesso giorno
                </a>
            </div>
        </div>

        <!-- Statistiche Correlate -->
        <?php if ($log->user_id): ?>
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-title-md font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-chart-bar mr-3 text-indigo-500"></i>
                    Statistiche Utente
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Attività totali</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white"><?= number_format($userStats['total']) ?></span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Oggi</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white"><?= number_format($userStats['today']) ?></span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Questa settimana</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white"><?= number_format($userStats['week']) ?></span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Log Show - JavaScript compatibile con PJAX
    (function() {
        function initLogShow() {
            // Nessun event listener specifico necessario per questa pagina
        }

        // Funzione per copiare testo
        window.copyToClipboard = function(button) {
            const text = button.getAttribute('data-text');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-check mr-1"></i>Copiato!';
                    button.classList.add('text-green-500');
                    
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.classList.remove('text-green-500');
                    }, 2000);
                });
            } else {
                // Fallback per browser più vecchi
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                if (window.showAlert) {
                    window.showAlert('Testo copiato negli appunti', 'success');
                }
            }
        };

        // Funzione per eliminare log
        window.deleteLog = function(id) {
            if (window.CoregreModals && window.CoregreModals.confirmDelete) {
                window.CoregreModals.confirmDelete(
                    'Sei sicuro di voler eliminare questo log?',
                    async () => {
                        try {
                            let loadingId = null;
                            if (window.CoregreNotifications && window.CoregreNotifications.loading) {
                                loadingId = window.CoregreNotifications.loading('Eliminazione in corso...');
                            }

                            const response = await fetch(`<?= $this->url('/logs/delete') ?>`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                                },
                                body: JSON.stringify({ ids: [id] })
                            });

                            if (loadingId && window.CoregreNotifications && window.CoregreNotifications.remove) {
                                window.CoregreNotifications.remove(loadingId);
                            }

                            if (!response.ok) {
                                throw new Error(`Errore server: ${response.status}`);
                            }

                            const data = await response.json();

                            if (data.error) {
                                throw new Error(data.error);
                            }

                            if (data.success) {
                                if (window.showAlert) {
                                    window.showAlert('Log eliminato con successo', 'success');
                                }

                                // Reindirizza alla lista log
                                setTimeout(() => {
                                    if (window.pjax) {
                                        window.pjax.navigateTo('<?= $this->url('/logs') ?>');
                                    } else {
                                        window.location.href = '<?= $this->url('/logs') ?>';
                                    }
                                }, 1500);
                            }

                        } catch (error) {
                            console.error('Error deleting log:', error);
                            
                            const errorMsg = `Errore durante l'eliminazione: ${error.message}`;
                            if (window.showAlert) {
                                window.showAlert(errorMsg, 'error');
                            }
                        }
                    },
                    1
                );
            } else {
                if (confirm('Sei sicuro di voler eliminare questo log?')) {
                    window.location.href = '<?= $this->url('/logs/delete/' . $log->id) ?>';
                }
            }
        };

        // Registra l'inizializzatore per PJAX
        if (window.COREGRE && window.COREGRE.onPageLoad) {
            window.COREGRE.onPageLoad(initLogShow);
        }

        // Inizializza anche al primo caricamento
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initLogShow);
        } else {
            initLogShow();
        }
    })();
</script>