<?php
/**
 * SCM Admin - Monitoring Dashboard
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                Monitoring SCM
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Dashboard di monitoraggio in tempo reale dei lanci e laboratori
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button onclick="refreshData()" 
                    class="inline-flex items-center rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                <i class="fas fa-sync-alt mr-2"></i>
                Aggiorna
            </button>
           
        </div>
    </div>
</div>

<!-- Real-time Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Lanci Attivi -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-rocket text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lanci Attivi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white" id="active-launches">
                    <?= number_format($stats['active_launches'] ?? 0) ?>
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm">
                <span class="text-green-600 dark:text-green-400 font-medium">
                    <i class="fas fa-arrow-up mr-1"></i>
                    +<?= $stats['launches_today'] ?? 0 ?>
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-2">oggi</span>
            </div>
        </div>
    </div>

    <!-- Laboratori Online -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                <i class="fas fa-building text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lab. Online</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white" id="online-labs">
                    <?= number_format($stats['online_laboratories'] ?? 0) ?>
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm">
                <span class="text-blue-600 dark:text-blue-400 font-medium">
                    <?= $stats['total_laboratories'] ?? 0 ?> totali
                </span>
            </div>
        </div>
    </div>

    <!-- Fasi in Corso -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-orange-500 to-red-600 shadow-lg">
                <i class="fas fa-cogs text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Fasi in Corso</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white" id="active-phases">
                    <?= number_format($stats['active_phases'] ?? 0) ?>
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm">
                <span class="text-orange-600 dark:text-orange-400 font-medium">
                    <?= $stats['phases_completed_today'] ?? 0 ?>
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">completate oggi</span>
            </div>
        </div>
    </div>

    <!-- Paia in Lavorazione -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-pink-600 shadow-lg">
                <i class="fas fa-shoe-prints text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Paia in Lavorazione</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white" id="pairs-processing">
                    <?= number_format($stats['pairs_processing'] ?? 0) ?>
                </p>
            </div>
        </div>
        <div class="mt-4">
            <div class="flex items-center text-sm">
                <span class="text-purple-600 dark:text-purple-400 font-medium">
                    <?= number_format($stats['total_pairs'] ?? 0) ?>
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">totali</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Laboratori Attivi -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-building mr-2 text-blue-500"></i>
                Laboratori Attivi
            </h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">Online ora</span>
        </div>
        
        <div class="space-y-4" id="active-laboratories">
            <?php if (!empty($activeLaboratories)): ?>
                <?php foreach ($activeLaboratories as $lab): ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($lab['name']) ?>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <?= $lab['active_launches'] ?> lanci attivi
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                <?= $lab['completion_percentage'] ?>%
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Ultimo accesso: <?= $lab['last_activity'] ? date('H:i', strtotime($lab['last_activity'])) : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-building text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Nessun laboratorio online</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lanci Critici -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>
                Lanci Critici
            </h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">Attenzione richiesta</span>
        </div>
        
        <div class="space-y-4" id="critical-launches">
            <?php if (!empty($criticalLaunches)): ?>
                <?php foreach ($criticalLaunches as $launch): ?>
                    <div class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-400 rounded-full mr-3"></div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($launch['launch_number']) ?>
                                </div>
                                <div class="text-sm text-red-600 dark:text-red-400">
                                    <?= htmlspecialchars($launch['critical_reason']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <a href="<?= $this->url('/scm-admin/launches/' . $launch['id']) ?>" 
                               class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                Visualizza
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-green-400 text-3xl mb-3"></i>
                    <p class="text-green-600 dark:text-green-400">Nessun problema rilevato</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Performance Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Grafico Avanzamento -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-chart-line mr-2 text-green-500"></i>
                Avanzamento Giornaliero
            </h3>
        </div>
        
        <div class="h-64 flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
            <div class="text-center">
                <i class="fas fa-chart-line text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">Grafico avanzamento</p>
                <p class="text-sm text-gray-400 dark:text-gray-500">Implementazione Chart.js</p>
            </div>
        </div>
    </div>

    <!-- Distribuzione Workload -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-chart-pie mr-2 text-purple-500"></i>
                Distribuzione Carico Lavoro
            </h3>
        </div>
        
        <div class="space-y-4">
            <?php foreach ($workloadDistribution ?? [] as $lab): ?>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        <?= htmlspecialchars($lab['name']) ?>
                    </span>
                    <div class="flex items-center space-x-3">
                        <div class="w-32 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all" 
                                 style="width: <?= $lab['workload_percentage'] ?>%"></div>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400 w-12">
                            <?= $lab['workload_percentage'] ?>%
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-clock mr-2 text-indigo-500"></i>
            Attività Recenti
        </h3>
        <a href="<?= $this->url('/scm-admin/activity-log') ?>" 
           class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
            Vedi tutto
        </a>
    </div>
    
    <div class="space-y-4" id="recent-activity">
        <?php if (!empty($recentActivity)): ?>
            <?php foreach ($recentActivity as $activity): ?>
                <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex-shrink-0">
                        <?php 
                        $activityColors = [
                            'phase_completed' => 'bg-green-500',
                            'phase_started' => 'bg-blue-500',
                            'launch_created' => 'bg-purple-500',
                            'login' => 'bg-gray-500'
                        ];
                        $activityIcons = [
                            'phase_completed' => 'fas fa-check',
                            'phase_started' => 'fas fa-play',
                            'launch_created' => 'fas fa-plus',
                            'login' => 'fas fa-sign-in-alt'
                        ];
                        $activityColor = $activityColors[$activity['type']] ?? 'bg-gray-400';
                        $activityIcon = $activityIcons[$activity['type']] ?? 'fas fa-circle';
                        ?>
                        <div class="w-8 h-8 <?= $activityColor ?> rounded-full flex items-center justify-center">
                            <i class="<?= $activityIcon ?> text-white text-xs"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm text-gray-900 dark:text-white">
                            <strong><?= htmlspecialchars($activity['laboratory_name']) ?></strong>
                            <?= htmlspecialchars($activity['description']) ?>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <?= date('d/m/Y H:i', strtotime($activity['created_at'])) ?>
                        </div>
                    </div>
                    <?php if (!empty($activity['launch_number'])): ?>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/20 dark:text-blue-300">
                                <?= htmlspecialchars($activity['launch_number']) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-8">
                <i class="fas fa-clock text-gray-400 text-3xl mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400">Nessuna attività recente</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Auto-refresh ogni 30 secondi
setInterval(refreshData, 30000);

function refreshData() {
    // Indica che stiamo aggiornando
    const refreshBtn = document.querySelector('button[onclick="refreshData()"]');
    const originalText = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Aggiornamento...';
    refreshBtn.disabled = true;
    
    fetch('<?= $this->url('/scm-admin/monitoring/refresh') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Aggiorna i contatori
                document.getElementById('active-launches').textContent = data.stats.active_launches.toLocaleString();
                document.getElementById('online-labs').textContent = data.stats.online_laboratories.toLocaleString();
                document.getElementById('active-phases').textContent = data.stats.active_phases.toLocaleString();
                document.getElementById('pairs-processing').textContent = data.stats.pairs_processing.toLocaleString();
                
                // Aggiorna timestamp
                const now = new Date();
                const timeString = now.toLocaleTimeString('it-IT');
                
                // Mostra notifica di aggiornamento
                showNotification('Dati aggiornati alle ' + timeString, 'success');
            }
        })
        .catch(error => {
            console.error('Errore aggiornamento:', error);
            showNotification('Errore durante l\'aggiornamento', 'error');
        })
        .finally(() => {
            // Ripristina il bottone
            refreshBtn.innerHTML = originalText;
            refreshBtn.disabled = false;
        });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
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

// Aggiorna timestamp ogni minuto
setInterval(() => {
    const timestamps = document.querySelectorAll('[data-timestamp]');
    timestamps.forEach(el => {
        const timestamp = parseInt(el.dataset.timestamp);
        const now = Date.now();
        const diff = Math.floor((now - timestamp) / 1000);
        
        if (diff < 60) {
            el.textContent = 'Ora';
        } else if (diff < 3600) {
            el.textContent = Math.floor(diff / 60) + 'm fa';
        } else {
            el.textContent = Math.floor(diff / 3600) + 'h fa';
        }
    });
}, 60000);
</script>