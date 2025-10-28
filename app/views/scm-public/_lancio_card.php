<?php
$stato_colors = [
    'IN_PREPARAZIONE' => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'text' => 'text-yellow-800 dark:text-yellow-300', 'icon' => 'fa-clock'],
    'LANCIATO' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-800 dark:text-blue-300', 'icon' => 'fa-rocket'],
    'IN_LAVORAZIONE' => ['bg' => 'bg-orange-50 dark:bg-orange-900/20', 'text' => 'text-orange-800 dark:text-orange-300', 'icon' => 'fa-cogs'],
    'COMPLETO' => ['bg' => 'bg-green-50 dark:bg-green-900/20', 'text' => 'text-green-800 dark:text-green-300', 'icon' => 'fa-check-circle'],
    'SOSPESO' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-800 dark:text-red-300', 'icon' => 'fa-pause-circle']
];

$colors = $stato_colors[$lancio->status] ?? $stato_colors['IN_PREPARAZIONE'];
$percentuale_completamento = $lancio->percentuale_completamento ?? 0;
?>

<div
    class="bg-white dark:bg-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-rocket mr-2 text-blue-500"></i>
                <?= htmlspecialchars($lancio->launch_number) ?>
            </h3>
            <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                <span>
                    <i class="fas fa-calendar mr-1"></i>
                    <?= date('d/m/Y', strtotime($lancio->launch_date)) ?>
                </span>
                <span>
                    <i class="fas fa-box mr-1"></i>
                    <?= $lancio->totale_articoli ?> articoli
                </span>
                <span>
                    <i class="fas fa-shoe-prints mr-1"></i>
                    <?= number_format($lancio->totale_paia) ?> paia
                </span>
            </div>
        </div>
        <div class="flex flex-col items-end space-y-2">
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $colors['bg'] ?> <?= $colors['text'] ?>">
                <i class="fas <?= $colors['icon'] ?> mr-1"></i>
                <?= ucfirst(str_replace('_', ' ', strtolower($lancio->status))) ?>
            </span>
            <?php if ($lancio->numero_note > 0): ?>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200">
                    <i class="fas fa-sticky-note mr-1"></i>
                    <?= $lancio->numero_note ?> note
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Progress Bar -->
    <?php if ($lancio->totale_paia > 0): ?>
        <div class="mb-4">
            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                <span>Avanzamento</span>
                <span><?= $percentuale_completamento ?>%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-300"
                    style="width: <?= $percentuale_completamento ?>%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                <span>Avanzamento lavori</span>
                <span><?= number_format($lancio->totale_paia) ?> paia totali</span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Fasi del ciclo -->
    <?php if (!empty($lancio->phases_cycle)): ?>
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fasi del Ciclo</h4>
            <div class="flex flex-wrap gap-1">
                <?php foreach (explode(';', $lancio->phases_cycle) as $fase): ?>
                    <?php $fase = trim($fase);
                    if (empty($fase))
                        continue; ?>
                    <span
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                        <?= htmlspecialchars($fase) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Articoli del lancio -->
    <?php if (!empty($lancio->articoli)): ?>
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-list-ul mr-1"></i>
                Articoli (<?= count($lancio->articoli) ?>)
            </h4>
            <div class="space-y-2 max-h-32 overflow-y-auto">
                <?php foreach ($lancio->articoli->take(5) as $articolo): ?>
                    <div class="flex items-center justify-between text-xs bg-gray-50 dark:bg-gray-600 rounded-lg p-2">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 dark:text-white truncate">
                                <?= htmlspecialchars($articolo->article_name) ?>
                            </div>
                            <div class="text-gray-600 dark:text-gray-400 truncate">
                                Articolo del lancio
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 text-right">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    <?= number_format($articolo->completed_pairs) ?>/<?= number_format($articolo->total_pairs) ?>
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">
                                    <?= $articolo->total_pairs > 0 ? round(($articolo->completed_pairs / $articolo->total_pairs) * 100) : 0 ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (count($lancio->articoli) > 5): ?>
                    <div class="text-xs text-center text-gray-500 dark:text-gray-400 py-1">
                        ... e altri <?= count($lancio->articoli) - 5 ?> articoli
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Note -->
    <?php if (!empty($lancio->note)): ?>
        <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Note</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-600 rounded-lg p-2">
                <?= htmlspecialchars($lancio->note) ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-600">
        <div class="text-xs text-gray-500 dark:text-gray-400">
            <?php if ($lancio->ultimo_aggiornamento): ?>
                <i class="fas fa-clock mr-1"></i>
                Ultimo aggiornamento: <?= date('d/m/Y', strtotime($lancio->ultimo_aggiornamento)) ?>
            <?php else: ?>
                <i class="fas fa-info-circle mr-1"></i>
                Nessun aggiornamento
            <?php endif; ?>
        </div>

        <div class="flex space-x-2">
            <a href="<?= $this->url('/scm/lavora/' . $lancio->id) ?>"
                class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-eye mr-1"></i>
                Dettagli
            </a>

            <?php if (in_array($lancio->status, ['LANCIATO', 'IN_LAVORAZIONE'])): ?>
                <a href="<?= $this->url('/scm/lavora/' . $lancio->id) ?>"
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 transition-all duration-200 hover:shadow-md">
                    <i class="fas fa-cogs mr-1"></i>
                    Lavora
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>