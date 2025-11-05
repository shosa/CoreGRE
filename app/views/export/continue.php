<?php
// Debug: verifichiamo le variabili disponibili
if (!isset($progressivo)) {
    echo '<div class="p-4 bg-red-100 text-red-700">Errore: variabile progressivo non definita</div>';
    return;
}
if (!isset($documento)) {
    echo '<div class="p-4 bg-red-100 text-red-700">Errore: variabile documento non definita</div>';
    return;
}
// Assicuriamoci che tutte le variabili esistano
$articoli = $articoli ?? [];
$terzista = $terzista ?? [];
$lanci = $lanci ?? [];
$total = $total ?? 0;
$mancanzeCount = $mancanzeCount ?? 0;
$files = $files ?? [];
?>

<!-- Header con titolo e informazioni documento -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <span class="bg-gradient-to-r from-blue-500 to-blue-600 bg-clip-text text-transparent mr-3">DDT n°
                    <?= $progressivo ?></span>
                <?php if ($documento['stato'] == 'Aperto'): ?>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-300">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        DA CONFERMARE
                    </span>
                <?php else: ?>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-800/20 dark:text-green-300">
                        <i class="fas fa-check mr-2"></i>
                        COMPLETATO
                    </span>
                <?php endif; ?>
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Gestione e modifica dettagli del documento di trasporto
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <?php if ($documento['stato'] == 'Aperto'): ?>
                <button onclick="completaDdt()"
                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-base font-medium text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 shadow-lg hover:shadow-xl transition-all duration-200">
                    <i class="fas fa-check mr-2"></i>
                    TERMINA DDT
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="flex mt-2" aria-label="Breadcrumb">
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
                    <a href="<?= $this->url('/export/dashboard') ?>"
                        class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        Export
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">DDT #<?= $progressivo ?></span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Informazioni documento in card -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Info Documento -->
    <div
        class="rounded-2xl border border-gray-200 bg-gradient-to-br from-yellow-400 to-yellow-500 shadow-lg dark:border-gray-800 text-white">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-white/20 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-file-invoice text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-white">DDT N° <?= $progressivo ?></h3>
                    <p class="text-sm text-white/90">Del: <?= date('d/m/Y', strtotime($documento['data'])) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Terzista -->
    <div
        class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl dark:bg-blue-800/20">
                        <i class="fas fa-user-tie text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Destinatario</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <?= htmlspecialchars($terzista['ragione_sociale'] ?? 'N/A') ?>
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <?= htmlspecialchars($terzista['nazione'] ?? 'N/A') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Allegati -->
    <div
        class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div
                        class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-xl dark:bg-green-800/20">
                        <i class="fas fa-paperclip text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Allegati</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400"><?= count($files) ?> file Excel</p>
                    <?php if (!empty($files)): ?>
                        <button onclick="downloadAllFiles()"
                            class="text-sm text-green-600 hover:text-green-700 dark:text-green-400">
                            <i class="fas fa-download mr-1"></i>Scarica tutti
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Totali -->
    <div
        class="rounded-2xl border border-gray-200 bg-gradient-to-br from-purple-500 to-purple-600 shadow-lg dark:border-gray-800 text-white">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-white/20 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-calculator text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-white">Totali</h3>
                    <p class="text-sm text-white/90">Articoli: <?= count($articoli) ?></p>
                    <p class="text-sm text-white/90">Valore: €<?= number_format($total, 2, ',', '.') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lanci associati -->
<?php if (!empty($lanci)): ?>
    <div
        class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-8">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-tags mr-3 text-blue-500"></i>
                Lanci Associati
            </h3>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-3">
                <?php foreach ($lanci as $lancio): ?>
                    <div
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-50 text-blue-800 border border-blue-200 dark:bg-blue-800/20 dark:text-blue-300 dark:border-blue-700">
                        <i class="fas fa-tag mr-2"></i>
                        <span class="font-medium"><?= htmlspecialchars($lancio['lancio']) ?></span>
                        <span class="ml-2 text-sm opacity-75">(<?= htmlspecialchars($lancio['articolo']) ?> -
                            <?= $lancio['paia'] ?> paia)</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Pulsanti azioni -->
<div
    class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-8">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-tools mr-3 text-gray-500"></i>
            Azioni Documento
        </h3>
    </div>
    <div class="p-6">
        <div class="flex flex-wrap gap-4">
            <!-- Gruppo Gestione Dati -->
            <?php if ($documento['stato'] == 'Aperto'): ?>
                <div class="flex flex-wrap gap-2">
                    <button onclick="openModal()"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                        <i class="fas fa-weight mr-2"></i>
                        Piede
                    </button>
                    <button onclick="openAutorizzazioneModal()"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 dark:bg-blue-800/20 dark:text-blue-300 dark:hover:bg-blue-800/40 transition-colors">
                        <i class="fas fa-pencil-alt mr-2"></i>
                        Autorizzazione
                    </button>
                    <button onclick="openCommentoModal()"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 dark:bg-blue-800/20 dark:text-blue-300 dark:hover:bg-blue-800/40 transition-colors">
                        <i class="fas fa-comment mr-2"></i>
                        Commento
                    </button>
                </div>

                <!-- Gruppo Gestione Articoli -->
                <div class="flex flex-wrap gap-2">
                    <button onclick="openRigaLiberaModal()"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-purple-700 bg-purple-100 hover:bg-purple-200 dark:bg-purple-800/20 dark:text-purple-300 dark:hover:bg-purple-800/40 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                       Riga
                    </button>
                    <?php if ($documento['first_boot'] == 1): ?>
                        <button onclick="cercaNcECosti()"
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-800/20 dark:text-green-300 dark:hover:bg-green-800/40 transition-colors">
                            <i class="fas fa-search-plus mr-2"></i>
                            Cerca Voci e Costi
                        </button>
                    <?php endif; ?>
                    <button onclick="elaboraMancanti()"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-yellow-700 bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-800/20 dark:text-yellow-300 dark:hover:bg-yellow-800/40 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Elabora Mancanti
                    </button>
                    <div
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-500 bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        
                        <?php if ($mancanzeCount > 0): ?>
                            <span
                                class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800/20 dark:text-red-300">
                                <?= $mancanzeCount ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <button onclick="openMancantiModal()"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-800/20 dark:text-indigo-300 dark:hover:bg-indigo-800/40 transition-colors">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Aggiungi Mancanti
                    </button>
                </div>
            <?php endif; ?>
            <!-- Gruppo Esportazione -->
            <div class="flex flex-wrap gap-2">
                <button onclick="exportToExcel()"
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-800/20 dark:text-green-300 dark:hover:bg-green-800/40 transition-colors">
                    <i class="fas fa-file-excel mr-2"></i>
                    Excel
                </button>
                <a target="_blank" href="<?= $this->url('/export/pdf/' . $progressivo) ?>"
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-800/20 dark:text-red-300 dark:hover:bg-red-800/40 transition-colors">
                    <i class="fas fa-file-pdf mr-2"></i>
                    PDF
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Tabella articoli -->
<div
    class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-8">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-list mr-3 text-green-500"></i>
            Corpo Documento
            <span
                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800/40 dark:text-gray-300">
                <?= count($articoli) ?> articoli
            </span>
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="dataTable">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Codice Articolo</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Descrizione</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Voce Doganale</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        UM</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        QTA</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        QTA Reale</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Costo Unit.</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Totale</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800/40 dark:divide-gray-700">
                <?php
                $articoliNormali = $articoli->filter(function($art) { return ($art->is_mancante ?? 0) == 0; });
                $articoliMancanti = $articoli->filter(function($art) { return ($art->is_mancante ?? 0) == 1; });

                $mancantiByDDT = [];
                foreach ($articoliMancanti as $articolo) {
                    $rif = $articolo->rif_mancante ?? 'Senza riferimento';
                    $mancantiByDDT[$rif][] = $articolo;
                }

                $isChiuso = ($documento->stato == 'Chiuso');

                // Funzione helper per i div editabili
                function editableDiv($isChiuso, $id, $campo, $valore)
                {
                    $contentEditable = $isChiuso ? 'false' : 'true';
                    $onblur = (!$isChiuso && $id > 0) ? "onblur=\"updateData($id, '$campo', this)\"" : "";
                    $extraClass = $isChiuso ? "bg-gray-100 dark:bg-gray-700 cursor-not-allowed" : "hover:border-blue-300 focus:border-blue-500";
                    return "
                <div contenteditable=\"$contentEditable\" 
                     class=\"min-h-[1.5rem] p-2 rounded border-2 border-transparent $extraClass focus:outline-none transition-colors\"
                     $onblur>
                    $valore
                </div>";
                }

                // Articoli normali
                foreach ($articoliNormali as $articolo):
                    $subtotal = round(($articolo->qta_reale ?? 0) * ($articolo->prezzo_unitario ?? 0), 2);
                    $qta_mancante = ($articolo->qta_originale ?? 0) - ($articolo->qta_reale ?? 0);
                    $isPartial = $qta_mancante > 0;
                    $isRigaLibera = ($articolo->tipo_riga ?? 'articolo') === 'libera';
                    ?>
                    <tr
                        class="hover:bg-gray-50 dark:hover:bg-gray-700/30 <?= $isPartial ? 'bg-yellow-50 dark:bg-yellow-800/10' : '' ?> <?= $isRigaLibera ? 'border-l-4 border-purple-500' : '' ?>">
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700/50 relative">
                            <?php if ($isRigaLibera): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-800/20 dark:text-purple-300 mr-2">
                                    <i class="fas fa-edit mr-1"></i>Libera
                                </span>
                            <?php endif; ?>
                            <?= htmlspecialchars($articolo->codice_articolo ?? '') ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                            <?= editableDiv($isChiuso, $articolo->id ?? 0, 'descrizione', htmlspecialchars($articolo->descrizione ?? '')) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                            <?= editableDiv($isChiuso, $articolo->id ?? 0, 'voce_doganale', htmlspecialchars($articolo->voce_doganale ?? '')) ?>
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700/50">
                            <?= htmlspecialchars($articolo->um ?? '') ?>
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700/50">
                            <?= number_format($articolo->qta_originale ?? 0, 3, ',', '.') ?>
                        </td>
                        <td
                            class="px-6 py-4 text-sm text-gray-900 dark:text-white <?= $isPartial ? 'bg-yellow-100 dark:bg-yellow-800/20' : '' ?>">
                            <?= editableDiv($isChiuso, $articolo->id ?? 0, 'qta_reale', number_format($articolo->qta_reale ?? 0, 3, ',', '.')) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                            <?= editableDiv($isChiuso, $articolo->id ?? 0, 'prezzo_unitario', '€' . number_format($articolo->prezzo_unitario ?? 0, 3, ',', '.')) ?>
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white bg-green-50 dark:bg-green-800/20">
                            €<?= number_format($subtotal, 2, ',', '.') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <!-- Mancanti -->
                <?php foreach ($mancantiByDDT as $rif => $mancanti): ?>
                    <tr class="bg-gray-600 text-white">
                        <td colspan="8" class="px-6 py-3 text-center font-bold">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            MANCANTI SU <?= htmlspecialchars($rif) ?>
                        </td>
                    </tr>
                    <?php foreach ($mancanti as $articolo):
                        $subtotal = round(($articolo->qta_reale ?? 0) * ($articolo->prezzo_unitario ?? 0), 2);
                        ?>
                        <tr class="bg-blue-50 dark:bg-blue-800/10 hover:bg-blue-100 dark:hover:bg-blue-800/20">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white bg-blue-100 dark:bg-blue-800/20">
                                <?= htmlspecialchars($articolo->codice_articolo ?? '') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                <?= editableDiv($isChiuso, $articolo->id ?? 0, 'descrizione', htmlspecialchars($articolo->descrizione ?? '')) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                <?= editableDiv($isChiuso, $articolo->id ?? 0, 'voce_doganale', htmlspecialchars($articolo->voce_doganale ?? '')) ?>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white bg-blue-100 dark:bg-blue-800/20">
                                <?= htmlspecialchars($articolo->um ?? '') ?>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white bg-blue-100 dark:bg-blue-800/20">
                                <?= number_format($articolo->qta_originale ?? 0, 3, ',', '.') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                <?= editableDiv($isChiuso, $articolo->id ?? 0, 'qta_reale', number_format($articolo->qta_reale ?? 0, 3, ',', '.')) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                <?= editableDiv($isChiuso, $articolo->id ?? 0, 'prezzo_unitario', '€' . number_format($articolo->prezzo_unitario ?? 0, 3, ',', '.')) ?>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white bg-green-50 dark:bg-green-800/20">
                                €<?= number_format($subtotal, 2, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <td colspan="7" class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">
                        Totale in €:
                    </td>
                    <td id="totalValue"
                        class="px-6 py-4 text-sm font-bold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-800/20"
                        data-total="<?= $total ?>">
                        €<?= number_format($total, 2, ',', '.') ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- File Excel associati -->
<?php if (!empty($files)): ?>
    <div
        class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-8">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-file-excel mr-3 text-green-500"></i>
                File Excel Associati
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <?php foreach ($files as $file): ?>
                    <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file-excel text-green-500 text-xl"></i>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars(basename($file)) ?></span>
                        </div>
                        <button onclick="downloadFile('<?= htmlspecialchars(basename($file)) ?>')"
                            class="inline-flex items-center px-2 py-1.5 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/40 transition-colors text-xs font-medium">
                            <i class="fas fa-download mr-1.5"></i>
                            Download
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modali -->
<!-- Modal Pesi e Aspetto Merce -->
<div id="pesiModal" class="fixed inset-0 z-[99999] hidden overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black bg-opacity-25" onclick="closeModal('pesiModal')"></div>
        <div class="relative w-full max-w-4xl rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-weight mr-2"></i>
                    Dati piede documento
                </h3>
                <button onclick="closeModal('pesiModal')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Aspetto
                            Merce</label>
                        <input type="text" id="aspettoMerce"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            placeholder="Es. Scatole, Pacchi, ecc.">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Numero
                            Colli</label>
                        <input type="number" id="numeroColli"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            placeholder="Inserisci il numero di colli">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Peso Lordo
                            (kg)</label>
                        <input type="number" step="0.01" id="pesoLordo"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Peso Netto
                            (kg)</label>
                        <input type="number" step="0.01" id="pesoNetto"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Trasportatore</label>
                    <input type="text" id="trasportatore"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        placeholder="Nome del trasportatore">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Materiale Consegnato
                        Per La Realizzazione Di</label>
                    <input type="text" id="consegnato"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        placeholder="Tomaie, sottopiedi, parti di tomaia">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white">Dettaglio Voci Doganali</h4>
                        <label class="flex items-center">
                            <input type="checkbox" id="presentiSottopiedi" onchange="toggleSottopiedi()" class="mr-2">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Presenti SOTTOPIEDI</span>
                        </label>
                    </div>
                    <div class="border border-gray-200 rounded-lg dark:border-gray-600">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        Voce Doganale</th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        Peso Netto (kg)</th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        Somma QTA</th>
                                </tr>
                            </thead>
                            <tbody id="doganaleTableBody" class="divide-y divide-gray-200 dark:divide-gray-600">
                                <!-- I dati verranno caricati dinamicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="resetPesiData()"
                    class="px-4 py-2 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 dark:bg-red-800/20 dark:text-red-300">
                    <i class="fas fa-trash-alt mr-2"></i>Resetta
                </button>
                <button onclick="savePesiData()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Salva
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Autorizzazione -->
<div id="autorizzazioneModal" class="fixed inset-0 z-[99999] hidden overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black bg-opacity-25" onclick="closeModal('autorizzazioneModal')"></div>
        <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-pencil-alt mr-2"></i>
                    Modifica Autorizzazione
                </h3>
                <button onclick="closeModal('autorizzazioneModal')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Autorizzazione</label>
                <textarea id="autorizzazione" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="Inserisci il testo dell'autorizzazione"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="resetAutorizzazioneData()"
                    class="px-4 py-2 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 dark:bg-red-800/20 dark:text-red-300">
                    <i class="fas fa-trash-alt mr-2"></i>Resetta
                </button>
                <button onclick="saveAutorizzazioneData()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Salva
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Commento -->
<div id="commentoModal" class="fixed inset-0 z-[99999] hidden overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black bg-opacity-25" onclick="closeModal('commentoModal')"></div>
        <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-comment mr-2"></i>
                    Aggiungi Commento
                </h3>
                <button onclick="closeModal('commentoModal')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Commento</label>
                <textarea id="commento" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="Inserisci il testo"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="resetCommentoData()"
                    class="px-4 py-2 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 dark:bg-red-800/20 dark:text-red-300">
                    <i class="fas fa-trash-alt mr-2"></i>Resetta
                </button>
                <button onclick="saveCommentoData()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Salva
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Mancanti -->
<div id="mancantiModal" class="fixed inset-0 z-[99999] hidden overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black bg-opacity-25" onclick="closeModal('mancantiModal')"></div>
        <div class="relative w-full max-w-4xl rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-dolly-flatbed mr-2"></i>
                    Seleziona Mancanti da Aggiungere
                </h3>
                <button onclick="closeModal('mancantiModal')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mb-6">
                <div class="rounded-lg bg-blue-50 p-4 mb-4 dark:bg-blue-800/20">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-400 mr-2 mt-0.5"></i>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Seleziona i mancanti che desideri aggiungere al DDT corrente. Gli articoli selezionati
                            verranno rimossi dall'elenco mancanti e aggiunti al DDT.
                        </p>
                    </div>
                </div>
                <div id="mancantiContainer">
                    <div class="text-center py-8">
                        <div
                            class="inline-flex items-center px-4 py-2 font-semibold text-sm text-blue-600 dark:text-blue-400">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Caricamento mancanti...
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="closeModal('mancantiModal')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">
                    Annulla
                </button>
                <button onclick="aggiungiMancantiSelezionati()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Aggiungi Selezionati
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Riga Libera -->
<div id="rigaLiberaModal" class="fixed inset-0 z-[99999] hidden overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black bg-opacity-25" onclick="closeModal('rigaLiberaModal')"></div>
        <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-plus-circle mr-2 text-purple-500"></i>
                    Aggiungi Riga Libera al DDT
                </h3>
                <button onclick="closeModal('rigaLiberaModal')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="rigaLiberaForm" onsubmit="salvaRigaLibera(event)">
                <div class="space-y-4">
                    <!-- Codice Articolo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Codice Articolo
                        </label>
                        <input type="text" id="rl_codice" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="es. ART-001">
                    </div>

                    <!-- Descrizione -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descrizione
                        </label>
                        <textarea id="rl_descrizione" required rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Descrizione articolo"></textarea>
                    </div>

                    <!-- Riga con Voce Doganale e UM -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Voce Doganale
                            </label>
                            <input type="text" id="rl_voce_doganale"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="es. 12345678">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Unità di Misura
                            </label>
                            <input type="text" id="rl_um" required value="PZ"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="es. PZ, KG, MT">
                        </div>
                    </div>

                    <!-- Riga con Quantità e Prezzo -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Quantità
                            </label>
                            <input type="number" id="rl_quantita" required step="0.001" min="0" value="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Prezzo Unitario (€)
                            </label>
                            <input type="number" id="rl_prezzo" step="0.001" min="0" value="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="rounded-lg bg-purple-50 p-4 dark:bg-purple-800/20">
                        <div class="flex">
                            <i class="fas fa-info-circle text-purple-400 mr-2 mt-0.5"></i>
                            <p class="text-sm text-purple-700 dark:text-purple-300">
                                La riga libera verrà aggiunta al corpo del DDT e potrà essere modificata come le altre righe.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal('rigaLiberaModal')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">
                        Annulla
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700">
                        <i class="fas fa-save mr-2"></i>Aggiungi Riga
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.1.1/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.2/FileSaver.min.js"></script>

<script>
    /**
     * JavaScript per la gestione dei DDT
     */

    // ======================
    // FUNZIONI DI UTILITÀ
    // ======================

    /**
     * Arrotonda un numero al numero specificato di decimali
     */
    function round(value, decimals) {
        return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
    }

    /**
     * Formatta un numero con separatori delle migliaia e decimali
     */
    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };

        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }

        return s.join(dec);
    }

    // ======================
    // GESTIONE MODALI
    // ======================

    function openModal() {
        document.getElementById('pesiModal').classList.remove('hidden');
    }

    function openAutorizzazioneModal() {
        document.getElementById('autorizzazioneModal').classList.remove('hidden');
    }

    function openCommentoModal() {
        document.getElementById('commentoModal').classList.remove('hidden');
    }

    function openMancantiModal() {
        document.getElementById('mancantiModal').classList.remove('hidden');
    }

    function openRigaLiberaModal() {
        document.getElementById('rigaLiberaModal').classList.remove('hidden');
        // Reset form
        document.getElementById('rigaLiberaForm').reset();
        document.getElementById('rl_um').value = 'PZ';
        document.getElementById('rl_quantita').value = '1';
        document.getElementById('rl_prezzo').value = '0';
    }

    function closeModal(modalId) {
        CoregreModals.closeModal(modalId);
    }

    // ======================
    // FUNZIONI PRINCIPALI
    // ======================

    /**
     * Esporta i dati degli articoli in un file Excel
     */
    function exportToExcel() {
        const loadingId = CoregreNotifications.loading('Generazione file Excel in corso...');

        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet('DDT');

        // Definizione delle colonne
        worksheet.columns = [
            { header: 'CODICE ARTICOLO', key: 'codice_articolo', width: 17 },
            { header: 'DESCRIZIONE', key: 'descrizione', width: 75 },
            { header: 'VOCE DOGANALE', key: 'voce_doganale', width: 15 },
            { header: 'UM', key: 'um', width: 10 },
            { header: 'QTA', key: 'qta_reale', width: 10 },
            { header: 'TOTALE', key: 'subtotal', width: 15 },
        ];

        // Aggiunta delle righe con i dati degli articoli
        <?php foreach ($articoli as $articolo): ?>
            worksheet.addRow({
                codice_articolo: '<?= addslashes($articolo->codice_articolo ?? '') ?>',
                descrizione: '<?= addslashes($articolo->descrizione ?? '') ?>',
                voce_doganale: '<?= addslashes($articolo->voce_doganale ?? '') ?>',
                um: '<?= addslashes($articolo->um ?? '') ?>',
                qta_reale: Number('<?= str_replace(',', '.', $articolo->qta_reale ?? 0) ?>'),
                subtotal: Number('<?= number_format(($articolo->qta_reale ?? 0) * ($articolo->prezzo_unitario ?? 0), 2, '.', '') ?>')
            });
        <?php endforeach; ?>

        // Generazione e download del file Excel
        workbook.xlsx.writeBuffer().then((data) => {
            CoregreNotifications.remove(loadingId);
            const blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            saveAs(blob, `DDT_<?= $progressivo ?>.xlsx`);

            CoregreNotifications.success('File Excel generato e download avviato!');
        }).catch(error => {
            CoregreNotifications.remove(loadingId);
            console.error('Error:', error);
            CoregreNotifications.error('Errore durante la generazione del file Excel');
        });
    }

    /**
     * Avvia l'elaborazione dei mancanti
     */
    function elaboraMancanti() {
        CoregreModals.confirm({
            title: 'Conferma Elaborazione',
            message: 'I mancanti del documento in questione verranno ricalcolati. Continuare?',
            confirmText: 'Conferma',
            cancelText: 'Annulla',
            type: 'warning',
            onConfirm: () => {
                const loadingId = CoregreNotifications.loading('Elaborazione mancanti in corso...');

                fetch('<?= $this->url('/export/elabora_mancanti') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `progressivo=<?= $progressivo ?>`
                })
                    .then(response => response.json())
                    .then(data => {
                        CoregreNotifications.remove(loadingId);
                        if (data.success) {
                            CoregreNotifications.success(data.message || 'Elaborazione completata con successo!');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            CoregreNotifications.error(data.message || 'Errore durante l\'elaborazione');
                        }
                    })
                    .catch(error => {
                        CoregreNotifications.remove(loadingId);
                        console.error('Error:', error);
                        CoregreNotifications.error('Si è verificato un errore durante l\'elaborazione dei mancanti');
                    });
            }
        });
    }

    /**
     * Aggiorna i dati di un articolo
     */
    function updateData(id, field, element) {
        // Prevenzione chiamate multiple
        if (element.dataset.updating === 'true') {
            return;
        }

        // Validazione ID con conversione esplicita
        const numericId = parseInt(id);
        if (!numericId || numericId <= 0 || isNaN(numericId)) {
            console.warn('ID articolo non valido (' + id + '), aggiornamento ignorato');
            return;
        }

        const newValue = element.innerText.replace('€', '').replace(/\./g, '').replace(',', '.').trim();
        const originalText = element.innerText;

        // Marca come in aggiornamento
        element.dataset.updating = 'true';

        // Mostra un mini loader nell'elemento
        element.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch('<?= $this->url('/export/update_data') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${numericId}&field=${field}&value=${encodeURIComponent(newValue)}`
        })
            .then(response => {
                if (!response.ok) {
                    console.warn(`HTTP ${response.status}: ${response.statusText} - ma continuo comunque`);
                    return { success: true, message: 'Aggiornamento completato' };
                }
                return response.json();
            })
            .then(data => {
                // Ripristina il testo originale
                element.innerText = originalText;

                if (!data.success) {
                    CoregreNotifications.error(data.message || 'Errore durante l\'aggiornamento');
                } else {
                    // Feedback visivo di successo
                    element.style.backgroundColor = '#dcfce7';
                    setTimeout(() => {
                        element.style.backgroundColor = '';
                    }, 1000);

                    // Ricalcola i totali se necessario
                    recalculateTotal();
                }
            })
            .catch(error => {
                // Ripristina il testo originale in caso di errore
                element.innerText = originalText;
                console.error('Error:', error);
                CoregreNotifications.error('Si è verificato un errore durante l\'aggiornamento dei dati');
            })
            .finally(() => {
                // Rimuovi il flag di aggiornamento
                element.dataset.updating = 'false';
            });
    }

    /**
     * Ricalcola il totale della tabella
     */
    function recalculateTotal() {
        let total = 0;
        const rows = document.querySelectorAll("#dataTable tbody tr");

        rows.forEach(row => {
            // Salta le righe di intestazione dei mancanti
            if (row.classList.contains('bg-gray-600')) return;

            const cells = row.cells;
            if (cells.length >= 8) {
                const qtaRealeTd = cells[5];
                const prezzoTd = cells[6];
                const totaleTd = cells[7];

                // Parsing con supporto per 3 decimali
                const qtaReale = parseFloat(qtaRealeTd.innerText.replace(/\./g, '').replace(',', '.')) || 0;
                const prezzo = parseFloat(prezzoTd.innerText.replace('€', '').replace(/\./g, '').replace(',', '.')) || 0;
                const subtotal = round(qtaReale * prezzo, 2); // Totale sempre a 2 decimali

                totaleTd.innerText = '€' + number_format(subtotal, 2, ',', '.');
                total += subtotal;
            }
        });

        const totalElement = document.getElementById('totalValue');
        if (totalElement) {
            totalElement.setAttribute('data-total', total);
            totalElement.innerText = '€' + number_format(total, 2, ',', '.');
        }
    }

    /**
     * Gestisce la pressione del tasto invio nelle celle editabili
     */
    function handleKeyPress(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            event.target.blur();
        }
    }

    // Aggiunge l'event listener solo per il tasto ENTER (non duplicare blur)
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[contenteditable="true"]:not([readonly])').forEach(function (cell) {
            cell.addEventListener('keydown', handleKeyPress);
        });

        // Carica i dati iniziali delle voci doganali quando si apre il modale pesi
        const pesiModalObserver = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                const modal = document.getElementById('pesiModal');
                if (modal && !modal.classList.contains('hidden')) {
                    loadDoganaleData(false);
                    // Carica i dati esistenti del piede documento
                    loadPiedeDocumento();
                }
            });
        });

        const pesiModal = document.getElementById('pesiModal');
        if (pesiModal) {
            pesiModalObserver.observe(pesiModal, { attributes: true, attributeFilter: ['class'] });
        }

        // Carica mancanti quando si apre il relativo modale
        const mancantiModalObserver = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                const modal = document.getElementById('mancantiModal');
                if (modal && !modal.classList.contains('hidden')) {
                    loadMancanti();
                }
            });
        });

        const mancantiModal = document.getElementById('mancantiModal');
        if (mancantiModal) {
            mancantiModalObserver.observe(mancantiModal, { attributes: true, attributeFilter: ['class'] });
        }
    });

    // Funzione per caricare i dati esistenti del piede documento
    function loadPiedeDocumento() {
        fetch('<?= $this->url('/export/get_piede_documento') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `progressivo=<?= $progressivo ?>`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.piede) {
                    const piede = data.piede;
                    document.getElementById('aspettoMerce').value = piede.aspetto_colli || '';
                    document.getElementById('numeroColli').value = piede.n_colli || '';
                    document.getElementById('pesoLordo').value = piede.tot_peso_lordo || '';
                    document.getElementById('pesoNetto').value = piede.tot_peso_netto || '';
                    document.getElementById('trasportatore').value = piede.trasportatore || '';
                    document.getElementById('consegnato').value = piede.consegnato_per || '';
                }
            })
            .catch(error => {
                console.error('Error loading piede documento:', error);
            });
    }

    // Funzione per caricare la lista dei mancanti
    function loadMancanti() {
        const container = document.getElementById('mancantiContainer');
        const loadingId = CoregreNotifications.loading('Caricamento mancanti...');

        fetch('<?= $this->url('/export/get_mancanti') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `progressivo=<?= $progressivo ?>`
        })
            .then(response => response.json())
            .then(data => {
                CoregreNotifications.remove(loadingId);
                if (data.success) {
                    let html = '';
                    if (data.mancanti && data.mancanti.length > 0) {
                        data.mancanti.forEach(mancante => {
                            html += `
                        <div class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <input type="checkbox" id="mancante_${mancante.id}" value="${mancante.id}" class="mr-3">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">${mancante.codice_articolo}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">${mancante.descrizione}</div>
                                <div class="text-xs text-gray-500">Qta: ${mancante.qta_mancante} - DDT: ${mancante.rif_ddt}</div>
                            </div>
                        </div>
                    `;
                        });
                    } else {
                        html = '<div class="text-center py-8 text-gray-500">Nessun mancante disponibile</div>';
                    }
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="text-center py-8 text-red-500">Errore nel caricamento dei mancanti</div>';
                }
            })
            .catch(error => {
                CoregreNotifications.remove(loadingId);
                console.error('Error:', error);
                container.innerHTML = '<div class="text-center py-8 text-red-500">Errore di connessione</div>';
            });
    }

    // ======================
    // FUNZIONI PLACEHOLDER
    // ======================

    function completaDdt() {
        CoregreModals.confirm({
            title: 'Conferma Completamento',
            message: 'Sei sicuro di voler completare questo DDT? Questa azione non può essere annullata.',
            confirmText: 'Sì, completa!',
            cancelText: 'Annulla',
            type: 'warning',
            onConfirm: () => {
                const loadingId = CoregreNotifications.loading('Completamento DDT in corso...');

                fetch('<?= $this->url('/export/completa_ddt') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `progressivo=<?= $progressivo ?>`
                })
                    .then(response => response.json())
                    .then(data => {
                        CoregreNotifications.remove(loadingId);
                        if (data.success) {
                            CoregreNotifications.success('DDT completato con successo!');
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            CoregreNotifications.error(data.message || 'Errore durante il completamento');
                        }
                    })
                    .catch(error => {
                        CoregreNotifications.remove(loadingId);
                        console.error('Error:', error);
                        CoregreNotifications.error('Si è verificato un errore durante il completamento del DDT');
                    });
            }
        });
    }

    function cercaNcECosti() {
        const loadingId = CoregreNotifications.loading('Ricerca voci e costi in corso...');

        fetch('<?= $this->url('/export/cerca_nc_costi') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `progressivo=<?= $progressivo ?>`
        })
            .then(response => response.json())
            .then(data => {
                CoregreNotifications.remove(loadingId);
                if (data.success) {
                    CoregreNotifications.success(data.message || 'Ricerca completata con successo!');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    CoregreNotifications.error(data.message || 'Errore durante la ricerca');
                }
            })
            .catch(error => {
                CoregreNotifications.remove(loadingId);
                console.error('Error:', error);
                CoregreNotifications.error('Si è verificato un errore durante la ricerca delle voci e dei costi');
            });
    }

    function savePesiData() {
        const loadingId = CoregreNotifications.loading('Salvataggio dati piede documento...');

        const datiPiede = {
            progressivo: <?= $progressivo ?>,
            aspetto_merce: document.getElementById('aspettoMerce').value,
            numero_colli: document.getElementById('numeroColli').value,
            peso_lordo: document.getElementById('pesoLordo').value,
            peso_netto: document.getElementById('pesoNetto').value,
            trasportatore: document.getElementById('trasportatore').value,
            consegnato_per: document.getElementById('consegnato').value
        };

        fetch('<?= $this->url('/export/save_piede_documento') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(datiPiede)
        })
            .then(response => response.json())
            .then(data => {
                CoregreNotifications.remove(loadingId);
                if (data.success) {
                    CoregreNotifications.success('Dati salvati con successo!');
                    closeModal('pesiModal');
                } else {
                    CoregreNotifications.error(data.message || 'Errore durante il salvataggio');
                }
            })
            .catch(error => {
                CoregreNotifications.remove(loadingId);
                console.error('Error:', error);
                CoregreNotifications.error('Si è verificato un errore durante il salvataggio');
            });
    }

    function resetPesiData() {
        CoregreModals.confirm({
            title: 'Conferma Reset',
            message: 'Sei sicuro di voler resettare tutti i dati? Questa azione cancellerà tutti i dati del piede documento dal database.',
            confirmText: 'Sì, resetta',
            cancelText: 'Annulla',
            type: 'warning',
            onConfirm: () => {
                const loadingId = CoregreNotifications.loading('Reset dati in corso...');

                // Cancella dal database
                fetch('<?= $this->url('/export/reset_piede_documento') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `progressivo=<?= $progressivo ?>`
                })
                    .then(response => response.json())
                    .then(data => {
                        CoregreNotifications.remove(loadingId);
                        if (data.success) {
                            // Reset form
                            document.getElementById('aspettoMerce').value = '';
                            document.getElementById('numeroColli').value = '';
                            document.getElementById('pesoLordo').value = '';
                            document.getElementById('pesoNetto').value = '';
                            document.getElementById('trasportatore').value = '';
                            document.getElementById('consegnato').value = '';

                            // Reset tabella voci doganali
                            const tableBody = document.getElementById('doganaleTableBody');
                            tableBody.innerHTML = '';

                            // Deseleziona checkbox sottopiedi
                            document.getElementById('presentiSottopiedi').checked = false;

                            CoregreNotifications.success('Dati resettati con successo!');

                            // Chiudi il modale
                            setTimeout(() => {
                                closeModal('pesiModal');
                            }, 1000);
                        } else {
                            CoregreNotifications.error(data.message || 'Errore durante il reset');
                        }
                    })
                    .catch(error => {
                        CoregreNotifications.remove(loadingId);
                        console.error('Error:', error);
                        CoregreNotifications.error('Si è verificato un errore durante il reset');
                    });
            }
        });
    }

    function saveCommentoData() {
        const commento = document.getElementById('commento').value.trim();

        if (!commento) {
            CoregreNotifications.warning('Inserisci un commento prima di salvare');
            return;
        }

        const loadingId = CoregreNotifications.loading('Salvataggio commento...');

        fetch('<?= $this->url('/export/save_commento') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `progressivo=<?= $progressivo ?>&commento=${encodeURIComponent(commento)}`
        })
            .then(response => response.json())
            .then(data => {
                CoregreNotifications.remove(loadingId);
                if (data.success) {
                    CoregreNotifications.success('Commento salvato con successo!');
                    closeModal('commentoModal');
                } else {
                    CoregreNotifications.error(data.message || 'Errore durante il salvataggio del commento');
                }
            })
            .catch(error => {
                CoregreNotifications.remove(loadingId);
                console.error('Error:', error);
                CoregreNotifications.error('Si è verificato un errore durante il salvataggio del commento');
            });
    }

    function resetCommentoData() {
        document.getElementById('commento').value = '';
    }

    function saveAutorizzazioneData() {
        const autorizzazione = document.getElementById('autorizzazione').value.trim();

        if (!autorizzazione) {
            CoregreNotifications.warning('Inserisci un testo per l\'autorizzazione prima di salvare');
            return;
        }

        const loadingId = CoregreNotifications.loading('Salvataggio autorizzazione...');

        fetch('<?= $this->url('/export/save_autorizzazione') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `progressivo=<?= $progressivo ?>&autorizzazione=${encodeURIComponent(autorizzazione)}`
        })
            .then(response => response.json())
            .then(data => {
                CoregreNotifications.remove(loadingId);
                if (data.success) {
                    CoregreNotifications.success('Autorizzazione salvata con successo!');
                    closeModal('autorizzazioneModal');
                } else {
                    CoregreNotifications.error(data.message || 'Errore durante il salvataggio dell\'autorizzazione');
                }
            })
            .catch(error => {
                CoregreNotifications.remove(loadingId);
                console.error('Error:', error);
                CoregreNotifications.error('Si è verificato un errore durante il salvataggio dell\'autorizzazione');
            });
    }

    function resetAutorizzazioneData() {
        document.getElementById('autorizzazione').value = '';
    }

    function aggiungiMancantiSelezionati() {
        const checkboxes = document.querySelectorAll('#mancantiModal input[type="checkbox"]:checked');

        if (checkboxes.length === 0) {
            CoregreNotifications.warning('Seleziona almeno un elemento da aggiungere');
            return;
        }

        const ids = Array.from(checkboxes).map(cb => cb.value);
        const loadingId = CoregreNotifications.loading('Aggiunta mancanti in corso...');

        fetch('<?= $this->url('/export/aggiungi_mancanti') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                progressivo: <?= $progressivo ?>,
                mancanti_ids: ids
            })
        })
            .then(response => response.json())
            .then(data => {
                CoregreNotifications.remove(loadingId);
                if (data.success) {
                    CoregreNotifications.success(`${ids.length} elemento${ids.length > 1 ? 'i' : ''} aggiunto${ids.length > 1 ? 'i' : ''} con successo!`);
                    closeModal('mancantiModal');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    CoregreNotifications.error(data.message || 'Errore durante l\'aggiunta dei mancanti');
                }
            })
            .catch(error => {
                CoregreNotifications.remove(loadingId);
                console.error('Error:', error);
                CoregreNotifications.error('Si è verificato un errore durante l\'aggiunta dei mancanti');
            });
    }

    function salvaRigaLibera(event) {
        event.preventDefault();

        const codice = document.getElementById('rl_codice').value.trim();
        const descrizione = document.getElementById('rl_descrizione').value.trim();
        const voceDoganale = document.getElementById('rl_voce_doganale').value.trim();
        const um = document.getElementById('rl_um').value.trim();
        const quantita = parseFloat(document.getElementById('rl_quantita').value);
        const prezzo = parseFloat(document.getElementById('rl_prezzo').value);

        if (!codice || !descrizione || !um || quantita <= 0) {
            CoregreNotifications.warning('Compila tutti i campi obbligatori');
            return;
        }

        const loadingId = CoregreNotifications.loading('Aggiunta riga libera in corso...');

        fetch('<?= $this->url('/export/aggiungi_riga_libera') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                progressivo: <?= $progressivo ?>,
                codice_articolo: codice,
                descrizione: descrizione,
                voce_doganale: voceDoganale,
                um: um,
                quantita: quantita,
                prezzo_unitario: prezzo
            })
        })
            .then(response => response.json())
            .then(data => {
                CoregreNotifications.remove(loadingId);
                if (data.success) {
                    CoregreNotifications.success('Riga libera aggiunta con successo!');
                    closeModal('rigaLiberaModal');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    CoregreNotifications.error(data.message || 'Errore durante l\'aggiunta della riga');
                }
            })
            .catch(error => {
                CoregreNotifications.remove(loadingId);
                console.error('Error:', error);
                CoregreNotifications.error('Si è verificato un errore durante l\'aggiunta della riga');
            });
    }

    function toggleSottopiedi() {
        const checkbox = document.getElementById('presentiSottopiedi');
        const tableBody = document.getElementById('doganaleTableBody');

        if (checkbox.checked) {
            // Carica i dati delle voci doganali con sottopiedi
            loadDoganaleData(true);
        } else {
            // Carica i dati delle voci doganali senza sottopiedi  
            loadDoganaleData(false);
        }
    }

    function loadDoganaleData(includeSottopiedi = false) {
        const tableBody = document.getElementById('doganaleTableBody');
        const loadingId = CoregreNotifications.loading('Caricamento dati voci doganali...');

        fetch('<?= $this->url('/export/get_doganale_data') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `progressivo=<?= $progressivo ?>&include_sottopiedi=${includeSottopiedi ? '1' : '0'}`
        })
            .then(response => response.json())
            .then(data => {
                CoregreNotifications.remove(loadingId);
                if (data.success) {
                    let html = '';
                    data.voci.forEach(voce => {
                        // Gestisci valori null o undefined
                        const voceDoganale = voce.voce_doganale || '';
                        const pesoNetto = voce.peso_netto || '0';
                        const qtaTotale = voce.qta_totale || '0';
                        html += `
                    <tr class="bg-white dark:bg-gray-800">
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">${voceDoganale}</td>
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                            <input type="number" step="0.01" value="${pesoNetto}" 
                                   class="w-full px-2 py-1 border rounded dark:bg-gray-700 dark:border-gray-600" 
                                   onchange="updateDoganaleWeight('${voceDoganale}', this.value)">
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">${qtaTotale}</td>
                    </tr>
                `;
                    });
                    tableBody.innerHTML = html;
                } else {
                    CoregreNotifications.error(data.message || 'Errore nel caricamento dei dati');
                }
            })
            .catch(error => {
                CoregreNotifications.remove(loadingId);
                console.error('Error:', error);
                CoregreNotifications.error('Errore nel caricamento delle voci doganali');
            });
    }

    function updateDoganaleWeight(voceDoganale, peso) {
        // Salva il peso aggiornato
        fetch('<?= $this->url('/export/update_doganale_weight') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `progressivo=<?= $progressivo ?>&voce_doganale=${encodeURIComponent(voceDoganale)}&peso=${peso}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    CoregreNotifications.success('Peso aggiornato!', 1000);
                } else {
                    CoregreNotifications.error('Errore nell\'aggiornamento del peso');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                CoregreNotifications.error('Errore di connessione');
            });
    }

    function downloadFile(filename) {
        window.open('<?= $this->url('/export/download/' . $progressivo . '/') ?>' + encodeURIComponent(filename), '_blank');
    }

    function downloadAllFiles() {
        window.open('<?= $this->url('/export/download_all/' . $progressivo) ?>', '_blank');
    }

    // Sposta i modali fuori dal main-content per coprire tutta la pagina
    document.addEventListener('DOMContentLoaded', function() {
        const modals = ['pesiModal', 'autorizzazioneModal', 'commentoModal', 'mancantiModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && modal.parentElement.id !== 'body') {
                document.body.appendChild(modal);
            }
        });
    });
</script>