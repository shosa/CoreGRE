<?php if (empty($lanci)): ?>
    <div class="px-6 py-8 text-center text-gray-500">
        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
        <p>Nessun lancio da visualizzare</p>
    </div>
<?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lancio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Articoli</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stato</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avanzamento
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($lanci as $lancio): ?>
                    <?php
                    // Colori stati
                    $stato_colors = [
                        'IN_PREPARAZIONE' => 'bg-yellow-100 text-yellow-800',
                        'LANCIATO' => 'bg-blue-100 text-blue-800',
                        'IN_LAVORAZIONE' => 'bg-orange-100 text-orange-800',
                        'COMPLETO' => 'bg-green-100 text-green-800',
                        'SOSPESO' => 'bg-red-100 text-red-800'
                    ];
                    $stato_class = $stato_colors[$lancio->status] ?? 'bg-gray-100 text-gray-800';
                    $percentuale = $lancio->percentuale_completamento ?? 0;

                    // Fasi
                    $fasi = array_filter(explode(';', $lancio->phases_cycle));
                    $totaleFasi = count($fasi);
                    ?>
                    <tr class="hover:bg-gray-50">
                        <!-- Numero Lancio -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($lancio->launch_number) ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($lancio->launch_date)) ?>
                                </div>
                            </div>
                        </td>

                        <!-- Articoli -->
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <div class="font-medium text-gray-900 mb-1">
                                    <?= $lancio->totale_articoli ?> articoli - <?= number_format($lancio->totale_paia) ?> paia
                                </div>
                                <?php if (!empty($lancio->articoli)): ?>
                                    <div class="space-y-1">
                                        <?php foreach ($lancio->articoli->take(2) as $i => $articolo): ?>
                                            <div class="text-xs bg-gray-100 rounded px-2 py-1">
                                                <span
                                                    class="font-medium"><?= htmlspecialchars(substr($articolo->article_name, 0, 30)) ?><?= strlen($articolo->article_name) > 30 ? '...' : '' ?></span>
                                                <span class="text-gray-600">(<?= number_format($articolo->total_pairs) ?>)</span>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php if (count($lancio->articoli) > 2): ?>
                                            <div class="text-xs text-gray-500 italic">
                                                ... e altri <?= count($lancio->articoli) - 2 ?> articoli
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Stato -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $stato_class ?>">
                                <?= str_replace('_', ' ', $lancio->status) ?>
                            </span>
                        </td>

                        <!-- Fasi -->
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <div class="font-medium text-gray-900 mb-1">
                                    <?= $totaleFasi ?> fasi totali
                                </div>
                                <?php if ($totaleFasi > 0): ?>
                                    <div class="text-xs text-gray-500">
                                        <?php foreach (array_slice($fasi, 0, 3) as $i => $fase): ?>
                                            <?php if ($i > 0)
                                                echo ' • '; ?>
                                            <?= htmlspecialchars(trim($fase)) ?>
                                        <?php endforeach; ?>
                                        <?php if ($totaleFasi > 3): ?>
                                            <br><span class="text-gray-400">+<?= $totaleFasi - 3 ?> altre</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Avanzamento -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-900"><?= $percentuale ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all"
                                            style="width: <?= $percentuale ?>%"></div>
                                    </div>
                                    <?php if ($lancio->ultimo_aggiornamento): ?>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Agg: <?= date('d/m/Y', strtotime($lancio->ultimo_aggiornamento)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>

                        <!-- Note -->
                        <td class="px-6 py-4 text-center">
                            <?php if ($lancio->numero_note > 0): ?>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-sticky-note mr-1"></i>
                                    <?= $lancio->numero_note ?>
                                </span>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>

                        <!-- Azioni -->
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="<?= $this->url('/scm/lavora/' . $lancio->id) ?>"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>
                                    Dettagli
                                </a>

                                <?php if (in_array($lancio->status, ['LANCIATO', 'IN_LAVORAZIONE'])): ?>
                                    <a href="<?= $this->url('/scm/lavora/' . $lancio->id) ?>"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-cogs mr-1"></i>
                                        Lavora
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>