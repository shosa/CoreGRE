<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Dashboard Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        <?= htmlspecialchars($material->material_code) ?>
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        <?= htmlspecialchars($material->description) ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <button onclick="openDeleteMaterialModal()"
                    class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i>
                Elimina Materiale
            </button>
            <a href="<?= $this->url('/mrp/materials') ?>"
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna all'Elenco
            </a>
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
                <a href="<?= $this->url('/mrp') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">MRP</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/mrp/materials') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">Materiali</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300"><?= htmlspecialchars($material->material_code) ?></span>
            </div>
        </li>
    </ol>
</nav>

<!-- Info Materiale -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm mb-8">
    <div class="px-6 py-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-barcode text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Codice Materiale</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-white font-mono"><?= htmlspecialchars($material->material_code) ?></dd>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-industry text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fornitore</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                            <?= htmlspecialchars($material->supplier_name) ?>
                            <?php if ($material->supplier_code): ?>
                                <div class="text-sm text-gray-500 dark:text-gray-400"><?= htmlspecialchars($material->supplier_code) ?></div>
                            <?php endif; ?>
                        </dd>
                    </div>
                </div>
            </div>

            <?php if ($material->category): ?>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-tags text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Categoria</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                <?= htmlspecialchars($material->category) ?>
                            </span>
                        </dd>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-<?= $material->has_sizes ? 'tags' : 'cube' ?> text-<?= $material->has_sizes ? 'blue' : 'gray' ?>-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo</dt>
                        <dd class="mt-1">
                            <?php if ($material->has_sizes): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <i class="fas fa-tags mr-1"></i> Con taglie
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    <i class="fas fa-cube mr-1"></i> Semplice
                                </span>
                            <?php endif; ?>
                        </dd>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Situazione MRP -->
<?php
$totFabbisogno = $requirements->sum('quantity_needed');
$totOrdinato = $orders->sum('quantity_ordered');
$totRicevuto = $arrivals->sum('quantity_received');
$daOrdinare = max(0, $totFabbisogno - $totOrdinato);
$daRicevere = max(0, $totOrdinato - $totRicevuto);
$mancante = $totFabbisogno - $totRicevuto;
?>

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-6 mb-8">
    <div class="bg-blue-50 dark:bg-blue-900/20 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-blue-700 dark:text-blue-300 truncate">Fabbisogno</dt>
                        <dd class="text-lg font-bold text-blue-900 dark:text-blue-100"><?= number_format($totFabbisogno, 0, ',', '.') ?></dd>
                        <dd class="text-xs text-blue-600 dark:text-blue-400"><?= htmlspecialchars($material->unit_measure ?: 'pz') ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-yellow-50 dark:bg-yellow-900/20 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-shopping-cart text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-yellow-700 dark:text-yellow-300 truncate">Ordinato</dt>
                        <dd class="text-lg font-bold text-yellow-900 dark:text-yellow-100"><?= number_format($totOrdinato, 0, ',', '.') ?></dd>
                        <dd class="text-xs text-yellow-600 dark:text-yellow-400"><?= htmlspecialchars($material->unit_measure ?: 'pz') ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-green-50 dark:bg-green-900/20 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-truck text-green-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-green-700 dark:text-green-300 truncate">Ricevuto</dt>
                        <dd class="text-lg font-bold text-green-900 dark:text-green-100"><?= number_format($totRicevuto, 0, ',', '.') ?></dd>
                        <dd class="text-xs text-green-600 dark:text-green-400"><?= htmlspecialchars($material->unit_measure ?: 'pz') ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-orange-50 dark:bg-orange-900/20 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-plus text-orange-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-orange-700 dark:text-orange-300 truncate">Da Ordinare</dt>
                        <dd class="text-lg font-bold text-orange-900 dark:text-orange-100"><?= number_format($daOrdinare, 0, ',', '.') ?></dd>
                        <dd class="text-xs text-orange-600 dark:text-orange-400"><?= htmlspecialchars($material->unit_measure ?: 'pz') ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-purple-50 dark:bg-purple-900/20 overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-purple-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-purple-700 dark:text-purple-300 truncate">Da Ricevere</dt>
                        <dd class="text-lg font-bold text-purple-900 dark:text-purple-100"><?= number_format($daRicevere, 0, ',', '.') ?></dd>
                        <dd class="text-xs text-purple-600 dark:text-purple-400"><?= htmlspecialchars($material->unit_measure ?: 'pz') ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="<?= $mancante > 0 ? 'bg-red-50 dark:bg-red-900/20' : ($mancante == 0 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20') ?> overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-balance-scale <?= $mancante > 0 ? 'text-red-600' : ($mancante == 0 ? 'text-green-600' : 'text-yellow-600') ?> text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium <?= $mancante > 0 ? 'text-red-700 dark:text-red-300' : ($mancante == 0 ? 'text-green-700 dark:text-green-300' : 'text-yellow-700 dark:text-yellow-300') ?> truncate">
                            <?= $mancante > 0 ? 'Mancante' : ($mancante == 0 ? 'Bilanciato' : 'Eccesso') ?>
                        </dt>
                        <dd class="text-lg font-bold <?= $mancante > 0 ? 'text-red-900 dark:text-red-100' : ($mancante == 0 ? 'text-green-900 dark:text-green-100' : 'text-yellow-900 dark:text-yellow-100') ?>">
                            <?= $mancante > 0 ? '+' : ($mancante < 0 ? '' : '') ?><?= number_format($mancante, 0, ',', '.') ?>
                        </dd>
                        <dd class="text-xs <?= $mancante > 0 ? 'text-red-600 dark:text-red-400' : ($mancante == 0 ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400') ?>"><?= htmlspecialchars($material->unit_measure ?: 'pz') ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pulsante Azioni -->
<div class="mb-4 flex justify-end">
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
            <i class="fas fa-plus mr-2"></i>
            Azioni
            <i class="fas fa-chevron-down ml-2" :class="{ 'rotate-180': open }"></i>
        </button>

        <div x-show="open" @click.away="open = false"
             class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10">
            <div class="py-1">
                <button onclick="openOrderModal(); closeDropdown()"
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                    <i class="fas fa-shopping-cart mr-3 text-yellow-600"></i>
                    Aggiungi Ordine
                </button>
                <button onclick="openArrivalModal(); closeDropdown()"
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                    <i class="fas fa-truck mr-3 text-green-600"></i>
                    Registra Arrivo
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Dettaglio Fabbisogno -->
<?php if (!$requirements->isEmpty() && !$material->has_sizes): ?>
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm mb-8">
    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-clipboard-list mr-3 text-blue-600"></i>
            Dettaglio Fabbisogno
        </h3>
    </div>
    <div class="p-6">
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Data ultimo import: <?= $requirements->first()->import_date ? date('d/m/Y', strtotime($requirements->first()->import_date)) : 'N/D' ?>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Descrizione</th>
                        <th class="text-center py-2 px-3 text-sm font-medium text-gray-900 dark:text-white min-w-32">Quantità</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Riga FABBISOGNO -->
                    <tr class="bg-white dark:bg-gray-700">
                        <td class="py-3 px-3 text-sm font-medium text-gray-900 dark:text-white">Fabbisogno</td>
                        <td class="text-center py-3 px-3 text-sm font-bold text-blue-600 dark:text-blue-400 font-mono">
                            <?= number_format($requirements->first()->quantity_needed, 0, ',', '.') ?>
                        </td>
                    </tr>

                    <!-- Riga TOTALE ORDINATO -->
                    <tr class="bg-yellow-50 dark:bg-yellow-900/20 border-t border-gray-200 dark:border-gray-600">
                        <td class="py-3 px-3 text-sm font-medium text-gray-900 dark:text-white">Tot. Ordinato</td>
                        <td class="text-center py-3 px-3 text-sm font-bold text-yellow-600 dark:text-yellow-400 font-mono">
                            <?= number_format($totOrdinato, 0, ',', '.') ?>
                        </td>
                    </tr>

                    <!-- Riga TOTALE CONSEGNATO -->
                    <tr class="bg-green-50 dark:bg-green-900/20">
                        <td class="py-3 px-3 text-sm font-medium text-gray-900 dark:text-white">Tot. Consegnato</td>
                        <td class="text-center py-3 px-3 text-sm font-bold text-green-600 dark:text-green-400 font-mono">
                            <?= number_format($totRicevuto, 0, ',', '.') ?>
                        </td>
                    </tr>

                    <!-- Riga DIFFERENZA -->
                    <tr class="bg-gray-100 dark:bg-gray-600 border-t border-gray-300 dark:border-gray-500">
                        <td class="py-3 px-3 text-sm font-bold text-gray-900 dark:text-white">Differenza</td>
                        <td class="text-center py-3 px-3 text-sm font-bold font-mono <?= $mancante > 0 ? 'text-red-600 dark:text-red-400' : ($mancante < 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400') ?>">
                            <?= $mancante > 0 ? '' : ($mancante < 0 ? '' : '') ?><?= number_format($mancante, 0, ',', '.') ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php elseif (!$requirements->isEmpty() && $material->has_sizes): ?>
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm mb-8">
    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-clipboard-list mr-3 text-blue-600"></i>
            Dettaglio Fabbisogni per Taglie
            <span class="ml-3 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 py-1 px-2 rounded-full text-sm font-semibold">
                <?= $requirements->count() ?> taglie
            </span>
        </h3>
    </div>
    <div class="p-6">
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Data ultimo import: <?= $requirements->first()->import_date ? date('d/m/Y', strtotime($requirements->first()->import_date)) : 'N/D' ?>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Taglia</th>
                        <?php foreach ($requirements as $req): ?>
                            <?php if ($req->size): ?>
                                <th class="text-center py-2 px-3 text-sm font-medium text-gray-900 dark:text-white min-w-16">
                                    <?= htmlspecialchars($req->size) ?>
                                </th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <th class="text-center py-2 px-3 text-sm font-medium text-blue-600 dark:text-blue-400 min-w-20">
                            <i class="fas fa-calculator mr-1"></i>TOTALE
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white dark:bg-gray-700">
                        <td class="py-3 px-3 text-sm font-medium text-gray-900 dark:text-white">Quantità</td>
                        <?php foreach ($requirements as $req): ?>
                            <?php if ($req->size): ?>
                                <td class="text-center py-3 px-3 text-sm text-gray-900 dark:text-white font-mono">
                                    <?= number_format($req->quantity_needed, 0, ',', '.') ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <td class="text-center py-3 px-3 text-sm font-bold text-blue-600 dark:text-blue-400">
                            <?= number_format($totFabbisogno, 0, ',', '.') ?>
                        </td>
                    </tr>

                    <?php
                    $orderedBySize = $orders->groupBy('size')->map(function ($group) {
                        return $group->sum('quantity_ordered');
                    });
                    $receivedBySize = $arrivals->groupBy('size')->map(function ($group) {
                        return $group->sum('quantity_received');
                    });
                    ?>

                    <!-- Riga TOTALE ORDINATO -->
                    <tr class="bg-yellow-50 dark:bg-yellow-900/20 border-t border-gray-200 dark:border-gray-600">
                        <td class="py-3 px-3 text-sm font-medium text-gray-900 dark:text-white">Tot. Ordinato</td>
                        <?php foreach ($requirements as $req): ?>
                            <?php if ($req->size): ?>
                                <td class="text-center py-3 px-3 text-sm text-gray-900 dark:text-white font-mono">
                                    <?php $orderedQty = $orderedBySize[$req->size] ?? 0; ?>
                                    <?= $orderedQty > 0 ? number_format($orderedQty, 0, ',', '.') : '-' ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <td class="text-center py-3 px-3 text-sm font-bold text-yellow-600 dark:text-yellow-400">
                            <?= number_format($totOrdinato, 0, ',', '.') ?>
                        </td>
                    </tr>

                    <!-- Riga TOTALE CONSEGNATO -->
                    <tr class="bg-green-50 dark:bg-green-900/20">
                        <td class="py-3 px-3 text-sm font-medium text-gray-900 dark:text-white">Tot. Consegnato</td>
                        <?php foreach ($requirements as $req): ?>
                            <?php if ($req->size): ?>
                                <td class="text-center py-3 px-3 text-sm text-gray-900 dark:text-white font-mono">
                                    <?php $receivedQty = $receivedBySize[$req->size] ?? 0; ?>
                                    <?= $receivedQty > 0 ? number_format($receivedQty, 0, ',', '.') : '-' ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <td class="text-center py-3 px-3 text-sm font-bold text-green-600 dark:text-green-400">
                            <?= number_format($totRicevuto, 0, ',', '.') ?>
                        </td>
                    </tr>

                    <!-- Riga DIFFERENZA -->
                    <tr class="bg-gray-100 dark:bg-gray-600 border-t border-gray-300 dark:border-gray-500">
                        <td class="py-3 px-3 text-sm font-bold text-gray-900 dark:text-white">Differenza</td>
                        <?php foreach ($requirements as $req): ?>
                            <?php if ($req->size): ?>
                                <?php
                                $needed = $req->quantity_needed;
                                $received = $receivedBySize[$req->size] ?? 0;
                                $diff = $needed - $received;
                                ?>
                                <td class="text-center py-3 px-3 text-sm font-bold font-mono <?= $diff > 0 ? 'text-red-600 dark:text-red-400' : ($diff < 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400') ?>">
                                    <?php if ($diff != 0): ?>
                                        <?= $diff > 0 ? '' : '' ?><?= number_format($diff, 0, ',', '.') ?>
                                    <?php else: ?>
                                        0
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <td class="text-center py-3 px-3 text-sm font-bold <?= $mancante > 0 ? 'text-red-600 dark:text-red-400' : ($mancante < 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400') ?>">
                            <?= $mancante > 0 ? '' : ($mancante < 0 ? '' : '') ?><?= number_format($mancante, 0, ',', '.') ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Dettagli -->
<!-- Dettaglio Ordini -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm mb-8">
    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-shopping-cart mr-3 text-yellow-600"></i>
                Dettaglio Ordini
                <span class="ml-3 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 py-1 px-2 rounded-full text-sm font-semibold">
                    <?= $orders->count() ?>
                </span>
            </h3>
            <button onclick="openOrderModal()"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                <i class="fas fa-plus mr-2"></i>
                Aggiungi Ordine
            </button>
        </div>
    </div>
    <div class="p-6">
        <?php if ($orders->isEmpty()): ?>
            <div class="text-center py-12">
                <i class="fas fa-shopping-cart text-gray-400 text-4xl mb-4"></i>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessun ordine registrato</h4>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Registra i primi ordini per questo materiale.</p>
                <button onclick="openOrderModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                    <i class="fas fa-plus mr-2"></i>
                    Registra Primo Ordine
                </button>
            </div>
        <?php elseif ($material->has_sizes): ?>
            <!-- Tabella ordini con taglie orizzontali -->
            <?php
            $groupedOrders = $orders->groupBy(function($order) {
                return $order->order_number . '_' . $order->order_date;
            })->map(function($group) {
                $first = $group->first();
                return (object)[
                    'order_number' => $first->order_number,
                    'order_date' => $first->order_date,
                    'notes' => $first->notes,
                    'sizes' => $group->pluck('quantity_ordered', 'size'),
                    'ids' => $group->pluck('id'),
                ];
            });
            $sizes = $requirements->pluck('size')->filter()->unique()->sort(SORT_NATURAL);
            ?>

            <div class="space-y-4">
                <?php foreach ($groupedOrders as $groupedOrder): ?>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                                    Ordine: <?= htmlspecialchars($groupedOrder->order_number) ?>
                                </h5>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Data: <?= date('d/m/Y', strtotime($groupedOrder->order_date)) ?>
                                    <?php if ($groupedOrder->notes): ?>
                                        - Note: <?= htmlspecialchars($groupedOrder->notes) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <button onclick="deleteOrder(<?= $groupedOrder->ids->first() ?>)"
                                    class="inline-flex items-center justify-center w-6 h-6 rounded bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-700 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-800/40 transition-colors text-xs"
                                    title="Elimina ordine">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-600">
                                        <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Taglia</th>
                                        <?php foreach ($sizes as $size): ?>
                                            <th class="text-center py-2 px-3 text-sm font-medium text-gray-900 dark:text-white min-w-16">
                                                <?= htmlspecialchars($size) ?>
                                            </th>
                                        <?php endforeach; ?>
                                        <th class="text-center py-2 px-3 text-sm font-medium text-yellow-600 dark:text-yellow-400 min-w-20">
                                            <i class="fas fa-calculator mr-1"></i>TOTALE
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white dark:bg-gray-600">
                                        <td class="py-3 px-3 text-sm font-medium text-gray-900 dark:text-white">Quantità</td>
                                        <?php
                                        $totalOrdered = 0;
                                        foreach ($sizes as $size):
                                            $qty = $groupedOrder->sizes[$size] ?? 0;
                                            $totalOrdered += $qty;
                                        ?>
                                            <td class="text-center py-3 px-3 text-sm text-gray-900 dark:text-white font-mono">
                                                <?= $qty > 0 ? number_format($qty, 0, ',', '.') : '-' ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="text-center py-3 px-3 text-sm font-bold text-yellow-600 dark:text-yellow-400">
                                            <?= number_format($totalOrdered, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Tabella semplice per materiali senza taglie -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ordine</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantità</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Note</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($order->order_number) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?= date('d/m/Y', strtotime($order->order_date)) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-mono text-gray-900 dark:text-white">
                                    <?= number_format($order->quantity_ordered, 0, ',', '.') ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white max-w-xs">
                                    <?= $order->notes ? htmlspecialchars($order->notes) : '-' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="deleteOrder(<?= $order->id ?>)"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-700 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-800/40 transition-colors"
                                            title="Elimina ordine">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Dettaglio Arrivi -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm mb-8">
    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-truck mr-3 text-green-600"></i>
                Dettaglio Arrivi
                <span class="ml-3 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 py-1 px-2 rounded-full text-sm font-semibold">
                    <?= $arrivals->count() ?>
                </span>
            </h3>
            <button onclick="openArrivalModal()"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>
                Aggiungi Arrivo
            </button>
        </div>
    </div>
    <div class="p-6">
        <?php if ($arrivals->isEmpty()): ?>
            <div class="text-center py-12">
                <i class="fas fa-truck text-gray-400 text-4xl mb-4"></i>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nessun arrivo registrato</h4>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Registra i primi arrivi per questo materiale.</p>
                <button onclick="openArrivalModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>
                    Registra Primo Arrivo
                </button>
            </div>
        <?php elseif ($material->has_sizes): ?>
            <!-- Tabella arrivi con taglie orizzontali -->
            <?php
            $groupedArrivals = $arrivals->groupBy(function($arrival) {
                return $arrival->document_number . '_' . $arrival->arrival_date;
            })->map(function($group) {
                $first = $group->first();
                return (object)[
                    'document_number' => $first->document_number,
                    'arrival_date' => $first->arrival_date,
                    'notes' => $first->notes,
                    'sizes' => $group->pluck('quantity_received', 'size'),
                    'ids' => $group->pluck('id'),
                ];
            });
            $sizes = $requirements->pluck('size')->filter()->unique()->sort(SORT_NATURAL);
            ?>

            <div class="space-y-4">
                <?php foreach ($groupedArrivals as $groupedArrival): ?>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                                    Documento: <?= htmlspecialchars($groupedArrival->document_number) ?>
                                </h5>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Data: <?= date('d/m/Y', strtotime($groupedArrival->arrival_date)) ?>
                                    <?php if ($groupedArrival->notes): ?>
                                        - Note: <?= htmlspecialchars($groupedArrival->notes) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <button onclick="deleteArrival(<?= $groupedArrival->ids->first() ?>)"
                                    class="inline-flex items-center justify-center w-6 h-6 rounded bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-700 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-800/40 transition-colors text-xs"
                                    title="Elimina arrivo">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-600">
                                        <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Taglia</th>
                                        <?php foreach ($sizes as $size): ?>
                                            <th class="text-center py-2 px-3 text-sm font-medium text-gray-900 dark:text-white min-w-16">
                                                <?= htmlspecialchars($size) ?>
                                            </th>
                                        <?php endforeach; ?>
                                        <th class="text-center py-2 px-3 text-sm font-medium text-green-600 dark:text-green-400 min-w-20">
                                            <i class="fas fa-calculator mr-1"></i>TOTALE
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white dark:bg-gray-600">
                                        <td class="py-3 px-3 text-sm font-medium text-gray-900 dark:text-white">Quantità</td>
                                        <?php
                                        $totalReceived = 0;
                                        foreach ($sizes as $size):
                                            $qty = $groupedArrival->sizes[$size] ?? 0;
                                            $totalReceived += $qty;
                                        ?>
                                            <td class="text-center py-3 px-3 text-sm text-gray-900 dark:text-white font-mono">
                                                <?= $qty > 0 ? number_format($qty, 0, ',', '.') : '-' ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="text-center py-3 px-3 text-sm font-bold text-green-600 dark:text-green-400">
                                            <?= number_format($totalReceived, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Tabella semplice per materiali senza taglie -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantità</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Note</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        <?php foreach ($arrivals as $arrival): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($arrival->document_number) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?= date('d/m/Y', strtotime($arrival->arrival_date)) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-mono text-gray-900 dark:text-white">
                                    <?= number_format($arrival->quantity_received, 0, ',', '.') ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white max-w-xs">
                                    <?= $arrival->notes ? htmlspecialchars($arrival->notes) : '-' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="deleteArrival(<?= $arrival->id ?>)"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-700 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-800/40 transition-colors"
                                            title="Elimina arrivo">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Ordini -->
<div id="orderModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center" style="z-index: 99999;">
    <div class="relative mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800 my-8">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    <i class="fas fa-shopping-cart mr-2 text-yellow-600"></i>
                    Aggiungi Ordine - <?= htmlspecialchars($material->material_code) ?>
                </h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="orderForm" method="POST" action="<?= $this->url('/mrp/material/' . $material->id . '/add-order') ?>" x-data="{ orderRows: [{}] }">
                <?php if (!$material->has_sizes): ?>
                    <!-- Materiali senza taglie -->
                    <div class="space-y-6">
                        <!-- Righe ordini multipli -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Ordini da Inserire</h4>
                                <button type="button" onclick="addOrderRow()" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-plus mr-1"></i> Aggiungi Ordine
                                </button>
                            </div>
                            <div id="orderRowsContainer">
                                <div class="order-row bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Numero Ordine *
                                            </label>
                                            <input type="text" name="order_numbers[]" required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Data Ordine *
                                            </label>
                                            <input type="date" name="order_dates[]" required value="<?= date('Y-m-d') ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Quantità *
                                            </label>
                                            <input type="number" name="quantities[]" required min="1"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        </div>
                                        <div class="flex items-end">
                                            <button type="button" onclick="removeOrderRow(this)" class="text-red-600 hover:text-red-800 p-2">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Note
                                        </label>
                                        <input type="text" name="notes[]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Materiali con taglie -->
                    <div class="space-y-6">
                        <!-- Ordini multipli con taglie -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Ordini da Inserire</h4>
                                <button type="button" onclick="addOrderRowWithSizes()" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-plus mr-1"></i> Aggiungi Ordine
                                </button>
                            </div>
                            <div id="orderSizeRowsContainer">
                                <div class="order-size-row bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                    <!-- Dati ordine -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Numero Ordine *
                                            </label>
                                            <input type="text" name="order_numbers[0]" required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Data Ordine *
                                            </label>
                                            <input type="date" name="order_dates[0]" required value="<?= date('Y-m-d') ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        </div>
                                    </div>

                                    <!-- Tabella taglie orizzontale -->
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead>
                                                <tr class="border-b border-gray-200 dark:border-gray-600">
                                                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Taglia</th>
                                                    <?php
                                                    $sizes = $requirements->pluck('size')->filter()->unique()->sort(SORT_NATURAL);
                                                    foreach ($sizes as $size): ?>
                                                        <th class="text-center py-2 px-3 text-sm font-medium text-gray-900 dark:text-white min-w-16">
                                                            <?= htmlspecialchars($size) ?>
                                                        </th>
                                                    <?php endforeach; ?>
                                                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-40">Note</th>
                                                    <th class="w-10"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="py-2 px-3 text-sm text-gray-900 dark:text-white">Quantità</td>
                                                    <?php foreach ($sizes as $size): ?>
                                                        <td class="py-2 px-3">
                                                            <input type="number" name="size_quantities[0][<?= htmlspecialchars($size) ?>]" min="0"
                                                                   class="w-full px-2 py-1 text-center border border-gray-300 rounded focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                                        </td>
                                                    <?php endforeach; ?>
                                                    <td class="py-2 px-3">
                                                        <input type="text" name="row_notes[0]"
                                                               class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                                    </td>
                                                    <td class="py-2 px-3">
                                                        <button type="button" onclick="removeOrderSizeRow(this)" class="text-red-600 hover:text-red-800">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeOrderModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Annulla
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                        <i class="fas fa-save mr-2"></i>
                        Salva Ordine
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Arrivi -->
<div id="arrivalModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center" style="z-index: 99999;">
    <div class="relative mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800 my-8">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    <i class="fas fa-truck mr-2 text-green-600"></i>
                    Registra Arrivo - <?= htmlspecialchars($material->material_code) ?>
                </h3>
                <button onclick="closeArrivalModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="arrivalForm" method="POST" action="<?= $this->url('/mrp/material/' . $material->id . '/add-arrival') ?>">
                <?php if (!$material->has_sizes): ?>
                    <!-- Materiali senza taglie -->
                    <div class="space-y-6">
                        <!-- Righe arrivi multipli -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Arrivi da Registrare</h4>
                                <button type="button" onclick="addArrivalRow()" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-plus mr-1"></i> Aggiungi Arrivo
                                </button>
                            </div>
                            <div id="arrivalRowsContainer">
                                <div class="arrival-row bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Numero Documento *
                                            </label>
                                            <input type="text" name="document_numbers[]" required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Data Arrivo *
                                            </label>
                                            <input type="date" name="arrival_dates[]" required value="<?= date('Y-m-d') ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Quantità *
                                            </label>
                                            <input type="number" name="quantities[]" required min="1"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        </div>
                                        <div class="flex items-end">
                                            <button type="button" onclick="removeArrivalRow(this)" class="text-red-600 hover:text-red-800 p-2">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Note
                                        </label>
                                        <input type="text" name="notes[]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Materiali con taglie -->
                    <div class="space-y-6">
                        <!-- Arrivi multipli con taglie -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Arrivi da Registrare</h4>
                                <button type="button" onclick="addArrivalRowWithSizes()" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-plus mr-1"></i> Aggiungi Arrivo
                                </button>
                            </div>
                            <div id="arrivalSizeRowsContainer">
                                <div class="arrival-size-row bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                    <!-- Dati arrivo -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Numero Documento *
                                            </label>
                                            <input type="text" name="document_numbers[0]" required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Data Arrivo *
                                            </label>
                                            <input type="date" name="arrival_dates[0]" required value="<?= date('Y-m-d') ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        </div>
                                    </div>

                                    <!-- Tabella taglie orizzontale -->
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead>
                                                <tr class="border-b border-gray-200 dark:border-gray-600">
                                                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Taglia</th>
                                                    <?php foreach ($sizes as $size): ?>
                                                        <th class="text-center py-2 px-3 text-sm font-medium text-gray-900 dark:text-white min-w-16">
                                                            <?= htmlspecialchars($size) ?>
                                                        </th>
                                                    <?php endforeach; ?>
                                                    <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-40">Note</th>
                                                    <th class="w-10"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="py-2 px-3 text-sm text-gray-900 dark:text-white">Quantità</td>
                                                    <?php foreach ($sizes as $size): ?>
                                                        <td class="py-2 px-3">
                                                            <input type="number" name="size_quantities[0][<?= htmlspecialchars($size) ?>]" min="0"
                                                                   class="w-full px-2 py-1 text-center border border-gray-300 rounded focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                                        </td>
                                                    <?php endforeach; ?>
                                                    <td class="py-2 px-3">
                                                        <input type="text" name="row_notes[0]"
                                                               class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                                    </td>
                                                    <td class="py-2 px-3">
                                                        <button type="button" onclick="removeArrivalSizeRow(this)" class="text-red-600 hover:text-red-800">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeArrivalModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Annulla
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-save mr-2"></i>
                        Salva Arrivo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Elimina Materiale -->
<div id="deleteMaterialModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center" style="z-index: 99999;">
    <div class="relative mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800 my-8">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-red-600 dark:text-red-400">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Elimina Materiale - <?= htmlspecialchars($material->material_code) ?>
                </h3>
                <button onclick="closeDeleteMaterialModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-red-800 dark:text-red-400">
                            Attenzione! Questa azione eliminerà permanentemente:
                        </h4>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <ul class="list-disc list-inside space-y-1">
                                <li><strong><?= $requirements->count() ?></strong> record di fabbisogno</li>
                                <li><strong><?= $orders->count() ?></strong> ordini registrati</li>
                                <li><strong><?= $arrivals->count() ?></strong> arrivi registrati</li>
                                <li>Il materiale <strong><?= htmlspecialchars($material->material_code) ?></strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <form id="deleteMaterialForm" method="POST" action="<?= $this->url('/mrp/material/' . $material->id . '/delete') ?>">
                <input type="hidden" name="material_id" value="<?= $material->id ?>">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Per confermare l'eliminazione, digita <strong>ELIMINA</strong> nel campo sottostante:
                        </label>
                        <input type="text" id="deleteConfirmation" required oninput="validateDeleteConfirmation()"
                               placeholder="Digita ELIMINA per confermare"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Motivo eliminazione (opzionale):
                        </label>
                        <textarea name="reason" id="deleteReason" rows="2"
                                  placeholder="Es: Materiale non più in uso..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeDeleteMaterialModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Annulla
                    </button>
                    <button type="submit" id="confirmDeleteButton" disabled
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <i class="fas fa-trash mr-2"></i>
                        Elimina Definitivamente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Cleanup per PJAX
function cleanupEventListeners() {
    // Cleanup eventuali event listener se necessario
}

// Dropdown management
function closeDropdown() {
    // Find the dropdown and close it
    const dropdownElement = document.querySelector('[x-data*="open"]');
    if (dropdownElement && dropdownElement.__x) {
        dropdownElement.__x.$data.open = false;
    }
}

// Modal management
function openOrderModal() {
    const modal = document.getElementById('orderModal');
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
    WebgreModals.openModal('orderModal');
    document.getElementById('orderForm').reset();
    resetOrderRows();
}

function closeOrderModal() {
    WebgreModals.closeModal('orderModal');
}

function openArrivalModal() {
    WebgreModals.openModal('arrivalModal');
    document.getElementById('arrivalForm').reset();
    resetArrivalRows();
}

function closeArrivalModal() {
    WebgreModals.closeModal('arrivalModal');
}

// Row management for materials without sizes
function addOrderRow() {
    const container = document.getElementById('orderRowsContainer');
    const firstRow = container.querySelector('.order-row');
    const newRow = firstRow.cloneNode(true);
    newRow.querySelectorAll('input').forEach(input => input.value = '');
    container.appendChild(newRow);
}

function removeOrderRow(button) {
    const container = document.getElementById('orderRowsContainer');
    if (container.children.length > 1) {
        button.closest('.order-row').remove();
    }
}

function resetOrderRows() {
    const container = document.getElementById('orderRowsContainer');
    if (!container) return;
    const firstRow = container.querySelector('.order-row');
    if (firstRow) {
        firstRow.querySelectorAll('input').forEach(input => input.value = '');
        while (container.children.length > 1) {
            container.lastElementChild.remove();
        }
    }
}

function addArrivalRow() {
    const container = document.getElementById('arrivalRowsContainer');
    const firstRow = container.querySelector('.arrival-row');
    const newRow = firstRow.cloneNode(true);
    newRow.querySelectorAll('input').forEach(input => input.value = '');
    container.appendChild(newRow);
}

function removeArrivalRow(button) {
    const container = document.getElementById('arrivalRowsContainer');
    if (container.children.length > 1) {
        button.closest('.arrival-row').remove();
    }
}

function resetArrivalRows() {
    const container = document.getElementById('arrivalRowsContainer');
    if (!container) return;
    const firstRow = container.querySelector('.arrival-row');
    if (firstRow) {
        firstRow.querySelectorAll('input').forEach(input => input.value = '');
        while (container.children.length > 1) {
            container.lastElementChild.remove();
        }
    }
}

// Row management for materials with sizes
let orderSizeRowIndex = 1;
function addOrderRowWithSizes() {
    const container = document.getElementById('orderSizeRowsContainer');
    const firstRow = container.querySelector('.order-size-row');
    const newRow = firstRow.cloneNode(true);

    newRow.querySelectorAll('input').forEach(input => {
        input.name = input.name.replace(/\ \[\d+\]/, `[${orderSizeRowIndex}]`);
        input.value = '';
    });
    
    newRow.querySelector('input[name*="order_dates"]').value = new Date().toISOString().slice(0, 10);

    container.appendChild(newRow);
    orderSizeRowIndex++;
}

function removeOrderSizeRow(button) {
    const row = button.closest('.order-size-row');
    if (row.parentElement.children.length > 1) {
        row.remove();
    }
}

let arrivalSizeRowIndex = 1;
function addArrivalRowWithSizes() {
    const container = document.getElementById('arrivalSizeRowsContainer');
    const firstRow = container.querySelector('.arrival-size-row');
    const newRow = firstRow.cloneNode(true);

    newRow.querySelectorAll('input').forEach(input => {
        input.name = input.name.replace(/\ \[\d+\]/, `[${arrivalSizeRowIndex}]`);
        input.value = '';
    });
    
    newRow.querySelector('input[name*="arrival_dates"]').value = new Date().toISOString().slice(0, 10);

    container.appendChild(newRow);
    arrivalSizeRowIndex++;
}

function removeArrivalSizeRow(button) {
    const row = button.closest('.arrival-size-row');
    if (row.parentElement.children.length > 1) {
        row.remove();
    }
}

// Delete functions
function deleteOrder(orderId) {
    WebgreModals.confirmDelete(
        'Sei sicuro di voler eliminare questo ordine?',
        () => {
            const loadingId = WebgreNotifications.loading('Eliminazione in corso...');

            fetch('<?= $this->url('/mrp/order/delete') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'id=' + encodeURIComponent(orderId)
            })
            .then(response => response.json())
            .then(data => {
                WebgreNotifications.remove(loadingId);

                if (data.success) {
                    WebgreNotifications.success('Ordine eliminato con successo');
                    setTimeout(() => { window.location.reload(); }, 1200);
                } else {
                    WebgreNotifications.error(data.error || 'Errore durante l\'eliminazione');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                WebgreNotifications.remove(loadingId);
                WebgreNotifications.error('Errore di connessione durante l\'eliminazione');
            });
        }
    );
}

function deleteArrival(arrivalId) {
    WebgreModals.confirmDelete(
        'Sei sicuro di voler eliminare questo arrivo?',
        () => {
            const loadingId = WebgreNotifications.loading('Eliminazione in corso...');

            fetch('<?= $this->url('/mrp/arrival/delete') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'id=' + encodeURIComponent(arrivalId)
            })
            .then(response => response.json())
            .then(data => {
                WebgreNotifications.remove(loadingId);

                if (data.success) {
                    WebgreNotifications.success('Arrivo eliminato con successo');
                    setTimeout(() => { window.location.reload(); }, 1200);
                } else {
                    WebgreNotifications.error(data.error || 'Errore durante l\'eliminazione');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                WebgreNotifications.remove(loadingId);
                WebgreNotifications.error('Errore di connessione durante l\'eliminazione');
            });
        }
    );
}

// Modal Delete Material
function openDeleteMaterialModal() {
    WebgreModals.openModal('deleteMaterialModal');
    document.getElementById('deleteConfirmation').value = '';
    document.getElementById('deleteReason').value = '';
    document.getElementById('confirmDeleteButton').disabled = true;
}

function closeDeleteMaterialModal() {
    WebgreModals.closeModal('deleteMaterialModal');
}

function validateDeleteConfirmation() {
    const input = document.getElementById('deleteConfirmation');
    const button = document.getElementById('confirmDeleteButton');
    button.disabled = input.value.toUpperCase() !== 'ELIMINA';
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('MRP Material Detail loaded');
});
</script>