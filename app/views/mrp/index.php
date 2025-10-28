<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Dashboard Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        MRP - Material Requirements Planning
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Gestione fabbisogni materiali e pianificazione ordini
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="<?= $this->url('/mrp/import') ?>"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <i class="fas fa-upload mr-2"></i>
                Import Dati ERP
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
                <span class="text-gray-700 dark:text-gray-300">MRP Dashboard</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                <i class="fas fa-cubes text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    Materiali Totali
                </p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['total_materials']) ?>
                </p>
            </div>
        </div>
    </div>

    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                <i class="fas fa-tags text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    Con Taglie
                </p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['materials_with_sizes']) ?>
                </p>
            </div>
        </div>
    </div>

    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-yellow-500 to-yellow-600 shadow-lg">
                <i class="fas fa-shopping-cart text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    Ordini Totali
                </p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['total_orders']) ?>
                </p>
            </div>
        </div>
    </div>

    <div
        class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg">
                <i class="fas fa-truck text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    Arrivi Totali
                </p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    <?= number_format($stats['total_arrivals']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php if ($stats['last_import']): ?>
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-8">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    Ultimo import dati: <strong><?= date('d/m/Y', strtotime($stats['last_import'])) ?></strong>
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Materiali Critici -->
<?php if (!$criticalMaterials->isEmpty()): ?>
    <div
        class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm mb-8">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-title-md font-bold text-gray-900 dark:text-white">
                    Materiali con Situazione Critica
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Materiali con quantità mancanti
                </p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Codice Materiale
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Descrizione
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Fabbisogno
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Ordinato
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Ricevuto
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Mancante
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Azioni
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($criticalMaterials as $material): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($material->material_code) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate">
                                    <?= htmlspecialchars($material->description) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">
                                <?= number_format($material->fabbisogno, 0, ',', '.') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">
                                <?= number_format($material->ordinato, 0, ',', '.') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white">
                                <?= number_format($material->ricevuto, 0, ',', '.') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <?= number_format($material->mancante, 0, ',', '.') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= $this->url('/mrp/material/' . $material->id) ?>"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/40 transition-colors"
                                    title="Visualizza dettagli">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-8">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-800 dark:text-green-200">
                    <?php if ($stats['total_materials'] > 0): ?>
                        Ottimo! Non ci sono materiali con situazione critica.
                    <?php else: ?>
                        Nessun materiale presente. Inizia caricando i dati ERP.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Menu Azioni -->
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
    <a href="<?= $this->url('/mrp/import') ?>"
        class="group relative rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm p-6 hover:shadow-xl transition-all duration-200">
        <div>
            <span
                class="rounded-lg inline-flex p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-600 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/40">
                <i class="fas fa-upload text-xl"></i>
            </span>
        </div>
        <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Import Dati ERP
            </h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Carica i file Excel per aggiornare i fabbisogni materiali
            </p>
        </div>
    </a>

    <a href="<?= $this->url('/mrp/materials') ?>"
        class="group relative rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm p-6 hover:shadow-xl transition-all duration-200">
        <div>
            <span
                class="rounded-lg inline-flex p-3 bg-green-50 dark:bg-green-900/20 text-green-600 group-hover:bg-green-100 dark:group-hover:bg-green-900/40">
                <i class="fas fa-list text-xl"></i>
            </span>
        </div>
        <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Visualizza Materiali
            </h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Consulta l'elenco completo dei materiali e la loro situazione
            </p>
        </div>
    </a>

    <div
        class="group relative rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm p-6">
        <div>
            <span class="rounded-lg inline-flex p-3 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600">
                <i class="fas fa-chart-bar text-xl"></i>
            </span>
        </div>
        <div class="mt-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Report & Analytics
            </h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Analisi avanzate e reportistica (Disponibile prossimamente)
            </p>
        </div>
    </div>
</div>

<script>
    // Gestione PJAX per navigazione fluida
    document.addEventListener('DOMContentLoaded', function () {
        console.log('MRP Dashboard loaded');
    });

    // Cleanup per PJAX
    function cleanupEventListeners() {
        // Cleanup eventuali event listener se necessario
    }
</script>