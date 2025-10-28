<?php
/**
 * SCM Admin - Dettaglio Lancio
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                Lancio <?= htmlspecialchars($launch->launch_number) ?>
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Dettagli completi e monitoraggio avanzamento
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="<?= $this->url('/scm-admin/launches') ?>" 
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alla Lista
            </a>
            <?php if ($launch->status === 'IN_PREPARAZIONE'): ?>
                <a href="<?= $this->url('/scm-admin/launches/' . $launch->id . '/edit') ?>" 
                   class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                    <i class="fas fa-edit mr-2"></i>
                    Modifica
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/') ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/scm-admin') ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    SCM Admin
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/scm-admin/launches') ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    Lanci
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Lancio <?= htmlspecialchars($launch->launch_number) ?></span>
            </div>
        </li>
    </ol>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- Informazioni Generali -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="flex items-center mb-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                    <i class="fas fa-rocket text-white text-lg"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informazioni Generali</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Dati base del lancio</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Numero Lancio</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($launch->launch_number) ?></dd>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data Lancio</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white"><?= date('d/m/Y', strtotime($launch->launch_date)) ?></dd>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Laboratorio</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($launch->laboratory_name) ?></dd>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Stato</dt>
                    <dd class="mt-1">
                        <?php
                        $statusColors = [
                            'IN_PREPARAZIONE' => 'bg-amber-100 text-amber-800 dark:bg-amber-800/20 dark:text-amber-300',
                            'IN_LAVORAZIONE' => 'bg-orange-100 text-orange-800 dark:bg-orange-800/20 dark:text-orange-300',
                            'COMPLETATO' => 'bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300'
                        ];
                        $statusClass = $statusColors[$launch->status] ?? 'bg-gray-100 text-gray-800';
                        $statusLabels = [
                            'IN_PREPARAZIONE' => 'In Preparazione',
                            'IN_LAVORAZIONE' => 'In Lavorazione',
                            'COMPLETATO' => 'Completato'
                        ];
                        ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $statusClass ?>">
                            <?= $statusLabels[$launch->status] ?? $launch->status ?>
                        </span>
                    </dd>
                </div>
            </div>

            <?php if (!empty($launch->notes)): ?>
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Note Generali</h4>
                    <p class="text-sm text-blue-700 dark:text-blue-200"><?= nl2br(htmlspecialchars($launch->notes)) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Avanzamento Globale -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="flex items-center mb-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                    <i class="fas fa-chart-pie text-white text-lg"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Avanzamento Globale</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Percentuale completamento generale</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400"><?= $stats['completion_percentage'] ?>%</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Completamento</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-600 dark:text-orange-400"><?= $stats['completed_phases'] ?>/<?= $stats['total_phases'] ?></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Fasi Completate</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400"><?= number_format($stats['total_pairs']) ?></div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Paia Totali</div>
                </div>
            </div>

            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-4 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-green-500 h-4 rounded-full transition-all duration-500 shadow-lg" 
                     style="width: <?= $stats['completion_percentage'] ?>%"></div>
            </div>
        </div>

       <!-- Matrice Fasi/Articoli -->
<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
    <div class="flex items-center mb-6">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg">
            <i class="fas fa-table text-white text-lg"></i>
        </div>
        <div class="ml-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Matrice Avanzamento</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Stato delle fasi per ogni articolo</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Articolo</th>
                    <?php foreach ($phases as $phase): ?>
                        <th class="px-3 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                            <?= htmlspecialchars($phase->phase_name) ?>
                        </th>
                    <?php endforeach; ?>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">%</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php 
                // Riorganizza progress in una matrice [article_id][phase_id]
                $progressMatrix = [];
                foreach ($progress as $p) {
                    $progressMatrix[$p->article_id][$p->phase_id] = $p;
                }
                ?>

                <?php foreach ($articles as $article): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-900 dark:text-white">
                                <?= htmlspecialchars($article->article_name) ?>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <?= number_format($article->total_pairs) ?> paia
                            </div>
                        </td>
                        <?php foreach ($phases as $phase): ?>
                            <td class="px-3 py-4 text-center">
                                <?php 
                                $cell = $progressMatrix[$article->id][$phase->id] ?? null;
                                if ($cell): 
                                    $statusColors = [
                                        'NON_INIZIATA' => 'bg-gray-200 text-gray-700',
                                        'IN_CORSO' => 'bg-yellow-200 text-yellow-800',
                                        'COMPLETATA' => 'bg-green-200 text-green-800',
                                        'BLOCCATA' => 'bg-red-200 text-red-800'
                                    ];
                                    $statusIcons = [
                                        'NON_INIZIATA' => 'fas fa-circle',
                                        'IN_CORSO' => 'fas fa-clock',
                                        'COMPLETATA' => 'fas fa-check-circle',
                                        'BLOCCATA' => 'fas fa-ban'
                                    ];
                                    $statusClass = $statusColors[$cell->status] ?? 'bg-gray-200 text-gray-700';
                                    $statusIcon = $statusIcons[$cell->status] ?? 'fas fa-circle';
                                ?>
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full <?= $statusClass ?>" 
                                          title="<?= $cell->status ?>">
                                        <i class="<?= $statusIcon ?> text-xs"></i>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-400">
                                        <i class="fas fa-minus text-xs"></i>
                                    </span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <td class="px-4 py-4 text-center">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                <?= $article->completion_percentage ?>%
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

        <!-- Timeline -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="flex items-center mb-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 shadow-lg">
                    <i class="fas fa-history text-white text-lg"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Timeline Attività</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Cronologia degli aggiornamenti</p>
                </div>
            </div>

            <?php if (!empty($timeline)): ?>
                <div class="space-y-4">
                    <?php foreach ($timeline as $event): ?>
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <?php 
                                $eventColors = [
                                    'created' => 'bg-blue-500',
                                    'phase_completed' => 'bg-green-500',
                                    'phase_started' => 'bg-yellow-500',
                                    'status_changed' => 'bg-purple-500',
                                    'note_added' => 'bg-gray-500'
                                ];
                                $eventColor = $eventColors[$event['event_type']] ?? 'bg-gray-400';
                                ?>
                                <div class="w-3 h-3 <?= $eventColor ?> rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($event['description']) ?>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <?= date('d/m/Y H:i', strtotime($event['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-history text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Nessuna attività registrata</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Azioni Rapide</h3>
            <div class="space-y-3">
                <a href="<?= $this->url('/scm/lavora/' . $launch->id) ?>" 
                   target="_blank"
                   class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    Vista Laboratorio
                </a>
                
                <?php if ($launch->status === 'IN_PREPARAZIONE'): ?>
                    <button onclick="startLaunch()" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition-all duration-200">
                        <i class="fas fa-play mr-2"></i>
                        Avvia Lancio
                    </button>
                <?php elseif ($launch->status === 'IN_LAVORAZIONE'): ?>
                    <button onclick="completeLaunch()" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 transition-all duration-200">
                        <i class="fas fa-flag-checkered mr-2"></i>
                        Completa Lancio
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Statistics -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistiche</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Creato il</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        <?= date('d/m/Y', strtotime($launch->created_at)) ?>
                    </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Ultimo aggiornamento</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        <?= date('d/m/Y H:i', strtotime($launch->updated_at)) ?>
                    </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Totale articoli</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        <?= $articles->count() ?>
                    </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Totale fasi</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        <?= $phases->count() ?>
                    </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Operazioni totali</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        <?= $articles->count() * $phases->count() ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function startLaunch() {
    if (window.CoregreModals && window.CoregreModals.confirm) {
        window.CoregreModals.confirm({
            title: 'Avvia Lancio',
            message: 'Sei sicuro di voler avviare questo lancio?\n\nDopo l\'avvio non sarà più possibile modificare articoli e fasi.',
            type: 'warning',
            confirmText: 'Avvia',
            onConfirm: () => {
                window.location.href = '<?= $this->url('/scm-admin/launches/' . $launch->id . '/start') ?>';
            }
        });
    } else {
        // Fallback usando CoregreModals
        CoregreModals.confirm({
            message: 'Sei sicuro di voler avviare questo lancio?\n\nDopo l\'avvio non sarà più possibile modificare articoli e fasi.',
            onConfirm: () => {
                window.location.href = '<?= $this->url('/scm-admin/launches/' . $launch->id . '/start') ?>';
            }
        });
    }
}

function completeLaunch() {
    if (window.CoregreModals && window.CoregreModals.confirm) {
        window.CoregreModals.confirm({
            title: 'Completa Lancio',
            message: 'Sei sicuro di voler completare questo lancio?\n\nQuesta operazione segnerà il lancio come terminato.',
            type: 'info',
            confirmText: 'Completa',
            onConfirm: () => {
                window.location.href = '<?= $this->url('/scm-admin/launches/' . $launch->id . '/complete') ?>';
            }
        });
    } else {
        // Fallback usando CoregreModals
        CoregreModals.confirm({
            message: 'Sei sicuro di voler completare questo lancio?\n\nQuesta operazione segnerà il lancio come terminato.',
            onConfirm: () => {
                window.location.href = '<?= $this->url('/scm-admin/launches/' . $launch->id . '/complete') ?>';
            }
        });
    }
}
</script>