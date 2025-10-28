<?php
/**
 * Tracking - Gestione Dettagli Lotti
 * Replica esatta del sistema legacy lotDetailManager.php
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="mr-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white p-3 rounded-2xl shadow-lg">
                    <i class="fas fa-cogs fa-2x"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Gestione Dettagli Tracking
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Gestisci dettagli lotti, date ordini e codici SKU per il tracking
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?= $this->url('/tracking') ?>"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alla Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500 dark:text-gray-400">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/tracking') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-home mr-2"></i>
                Tracking
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Dettagli Tracking</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Tab Navigation -->
<div class="mb-8">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
            <button onclick="switchTab('lotti')" id="tab-lotti"
                class="tab-button active border-b-2 border-purple-500 py-2 px-1 text-sm font-medium text-purple-600 dark:text-purple-400">
                <i class="fas fa-boxes mr-2"></i>
                Dettagli Lotti
            </button>
            <button onclick="switchTab('date')" id="tab-date"
                class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                <i class="fas fa-calendar mr-2"></i>
                Date Ordini
            </button>
            <button onclick="switchTab('sku')" id="tab-sku"
                class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                <i class="fas fa-barcode mr-2"></i>
                Codici SKU
            </button>
        </nav>
    </div>
</div>

<!-- Tab Content Lotti -->
<div id="content-lotti" class="tab-content">
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Lotti senza riferimenti -->
        <div class="xl:col-span-5">
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-exclamation-triangle mr-3 text-yellow-500"></i>
                        Lotti senza riferimenti
                    </h3>
                </div>
                <div class="p-6">
                    <form id="saveReferencesForm" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">

                        <?php if (empty($lotsWithoutReferences)): ?>
                            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg text-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Tutti i Lotti per i quali è presente almeno 1 associazione hanno dei riferimenti, per
                                modificarli usa il campo ricerca.
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Tipo</th>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Lotto</th>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                DDT</th>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Data</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <?php foreach ($lotsWithoutReferences as $index => $lot): ?>
                                            <tr>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    <?= htmlspecialchars($lot['type_name'] ?? '') ?>
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    <em><?= htmlspecialchars($lot['lot'] ?? '') ?></em>
                                                    <input type="hidden" name="lots[<?= $index ?>][number]"
                                                        value="<?= htmlspecialchars($lot['lot'] ?? '') ?>">
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap">
                                                    <input type="text" name="lots[<?= $index ?>][doc]"
                                                        value="<?= $lot['doc'] ? htmlspecialchars($lot['doc']) : '' ?>"
                                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white <?= empty($lot['doc']) ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' ?>"
                                                        placeholder="DDT">
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap">
                                                    <input type="date" name="lots[<?= $index ?>][date]"
                                                        value="<?= $lot['date'] ? htmlspecialchars(date('Y-m-d', strtotime($lot['date']))) : '' ?>"
                                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white <?= (empty($lot['date']) || $lot['date'] == '0000-00-00') ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' ?>">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6">
                                <button type="submit" name="save_references"
                                    class="w-full inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200">
                                    <i class="fas fa-save mr-2"></i>
                                    Salva Riferimenti
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Ricerca e modifica lotto -->
        <div class="xl:col-span-7">
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-search mr-3 text-purple-500"></i>
                        Ricerca e Modifica Lotto
                    </h3>
                    <button onclick="showAllLotsModal()"
                        class="inline-flex items-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                        <i class="fas fa-expand-arrows mr-2"></i>
                        Vedi tutto
                    </button>
                </div>
                <div class="p-6">
                    <!-- Form ricerca -->
                    <form id="searchLotForm" class="mb-6">
                        <div class="mb-4">
                            <label for="search_lot"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Numero Lotto
                            </label>
                            <input type="text" id="search_lot" name="search_lot" placeholder="Inserisci numero lotto"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                        </div>
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200">
                            <i class="fas fa-search mr-2"></i>
                            Cerca
                        </button>
                    </form>

                    <!-- Risultato ricerca -->
                    <div id="lotDetailsContainer" style="display: none;">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-4">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Dettagli Lotto</h4>
                            <form id="updateLotForm" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                                <input type="hidden" id="lot_number" name="lot">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="lot_doc"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">DDT</label>
                                        <input type="text" id="lot_doc" name="doc"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                                    </div>
                                    <div>
                                        <label for="lot_date"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data</label>
                                        <input type="date" id="lot_date" name="date"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="lot_note"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note</label>
                                    <textarea id="lot_note" name="note" rows="3"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                        placeholder="Inserisci note aggiuntive"></textarea>
                                </div>

                                <button type="submit" name="update_lot_details"
                                    class="w-full inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200">
                                    <i class="fas fa-check mr-2"></i>
                                    Aggiorna Lotto
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Messaggio nessun risultato -->
                    <div id="noResultsMessage"
                        class="hidden bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        <i class="fas fa-times-circle mr-2"></i>
                        <span id="noResultsText"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<!-- Tab Content Date Ordini -->
<div id="content-date" class="tab-content hidden">
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Ordini senza date -->
        <div class="xl:col-span-5">
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-exclamation-triangle mr-3 text-yellow-500"></i>
                        Ordini senza date
                    </h3>
                </div>
                <div class="p-6">
                    <form id="saveOrderDatesForm" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                        <input type="hidden" name="save_order_dates" value="1">

                        <?php if (empty($ordersWithoutDate)): ?>
                            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg text-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Tutti gli ordini hanno date assegnate. Usa la ricerca per modificarle.
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Ordine</th>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Data</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <?php foreach ($ordersWithoutDate as $index => $order): ?>
                                            <tr>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    <em><?= htmlspecialchars($order['Ordine'] ?? '') ?></em>
                                                    <input type="hidden" name="orders[<?= $index ?>][ordine]"
                                                        value="<?= htmlspecialchars($order['Ordine'] ?? '') ?>">
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap">
                                                    <input type="date" name="orders[<?= $index ?>][date]"
                                                        value="<?= htmlspecialchars($order['date'] ?? '') ?>"
                                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white <?= (empty($order['date']) || $order['date'] == '0000-00-00') ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' ?>">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6">
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200">
                                    <i class="fas fa-save mr-2"></i>
                                    Salva Date Ordini
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Ricerca e modifica ordine -->
        <div class="xl:col-span-7">
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-search mr-3 text-purple-500"></i>
                        Ricerca e Modifica Ordine
                    </h3>
                    <button onclick="showAllOrdersModal()"
                        class="inline-flex items-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                        <i class="fas fa-expand-arrows mr-2"></i>
                        Vedi tutto
                    </button>
                </div>
                <div class="p-6">
                    <!-- Form ricerca ordine -->
                    <form id="searchOrderForm" class="mb-6">
                        <div class="mb-4">
                            <label for="search_order"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Numero Ordine
                            </label>
                            <input type="text" id="search_order" name="search_order"
                                placeholder="Inserisci numero ordine"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                        </div>
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200">
                            <i class="fas fa-search mr-2"></i>
                            Cerca Ordine
                        </button>
                    </form>

                    <!-- Risultato ricerca ordine -->
                    <div id="orderDetailsContainer" style="display: none;">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-4">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Dettagli Ordine</h4>
                            <form id="updateOrderForm" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                                <input type="hidden" name="update_order_details" value="1">
                                <input type="hidden" id="order_number" name="ordine">

                                <div class="mb-4">
                                    <label for="order_date"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data
                                        Ordine</label>
                                    <input type="date" id="order_date" name="date"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>

                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200">
                                    <i class="fas fa-check mr-2"></i>
                                    Aggiorna Ordine
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Messaggio nessun risultato ordine -->
                    <div id="noOrderResultsMessage"
                        class="hidden bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        <i class="fas fa-times-circle mr-2"></i>
                        <span id="noOrderResultsText"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab Content SKU -->
<div id="content-sku" class="tab-content hidden">
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- Articoli senza SKU -->
        <div class="xl:col-span-5">
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-exclamation-triangle mr-3 text-yellow-500"></i>
                        Articoli senza codice SKU
                    </h3>
                </div>
                <div class="p-6">
                    <form id="saveSkuForm" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                        <input type="hidden" name="save_sku" value="1">

                        <?php if (empty($articoliWithoutSku)): ?>
                            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg text-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Tutti gli articoli hanno codici SKU assegnati. Usa la ricerca per modificarli.
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Articolo</th>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Codice SKU</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <?php foreach ($articoliWithoutSku as $index => $articolo): ?>
                                            <tr>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    <em><?= htmlspecialchars($articolo['Articolo'] ?? '') ?></em>
                                                    <input type="hidden" name="arts[<?= $index ?>][articolo]"
                                                        value="<?= htmlspecialchars($articolo['Articolo'] ?? '') ?>">
                                                </td>
                                                <td class="px-3 py-4 whitespace-nowrap">
                                                    <input type="text" name="arts[<?= $index ?>][sku]"
                                                        value="<?= htmlspecialchars($articolo['sku'] ?? '') ?>"
                                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 <?= empty($articolo['sku']) ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' ?>"
                                                        placeholder="Inserisci SKU">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6">
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200">
                                    <i class="fas fa-save mr-2"></i>
                                    Salva Codici SKU
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Ricerca e modifica articolo -->
        <div class="xl:col-span-7">
            <div
                class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-search mr-3 text-purple-500"></i>
                        Ricerca e Modifica Articolo
                    </h3>
                    <button onclick="showAllArticoliModal()"
                        class="inline-flex items-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                        <i class="fas fa-expand-arrows mr-2"></i>
                        Vedi tutto
                    </button>
                </div>
                <div class="p-6">
                    <!-- Form ricerca articolo -->
                    <form id="searchArticoloForm" class="mb-6">
                        <div class="mb-4">
                            <label for="search_articolo"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Codice Articolo
                            </label>
                            <input type="text" id="search_articolo" name="search_articolo"
                                placeholder="Inserisci codice articolo"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                        </div>
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200">
                            <i class="fas fa-search mr-2"></i>
                            Cerca Articolo
                        </button>
                    </form>

                    <!-- Risultato ricerca articolo -->
                    <div id="articoloDetailsContainer" style="display: none;">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-4">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Dettagli Articolo</h4>
                            <form id="updateArticoloForm" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                                <input type="hidden" name="update_articolo_details" value="1">
                                <input type="hidden" id="articolo_code" name="articolo">

                                <div class="mb-4">
                                    <label for="articolo_sku"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Codice
                                        SKU</label>
                                    <input type="text" id="articolo_sku" name="sku" placeholder="Inserisci codice SKU"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                                </div>

                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200">
                                    <i class="fas fa-check mr-2"></i>
                                    Aggiorna Articolo
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Messaggio nessun risultato articolo -->
                    <div id="noArticoloResultsMessage"
                        class="hidden bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        <i class="fas fa-times-circle mr-2"></i>
                        <span id="noArticoloResultsText"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tutti i Lotti -->
<div id="allLotsModal-new" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 99999;">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tutti i Lotti</h3>
            <button onclick="hideAllLotsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Lotto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                        </tr>
                    </thead>
                    <tbody id="allLotsTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($allLots as $lot): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($lot['lot'] ?? '') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($lot['type_name'] ?? '') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($lot['doc'] ?? '') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($lot['date'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex items-center justify-end p-4 border-t border-gray-200 dark:border-gray-700">
            <button onclick="hideAllLotsModal()"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                Chiudi
            </button>
        </div>
    </div>
</div>

<!-- Modal Tutti gli Ordini -->
<div id="allOrdersModal-new" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 99999;">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tutti gli Ordini</h3>
            <button onclick="hideAllOrdersModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ordine</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                        </tr>
                    </thead>
                    <tbody id="allOrdersTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($allOrders as $order): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($order['Ordine']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($order['date'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex items-center justify-end p-4 border-t border-gray-200 dark:border-gray-700">
            <button onclick="hideAllOrdersModal()"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                Chiudi
            </button>
        </div>
    </div>
</div>

<!-- Modal Tutti gli Articoli -->
<div id="allArticoliModal-new" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 99999;">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 shadow-lg rounded-lg bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tutti gli Articoli</h3>
            <button onclick="hideAllArticoliModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Articolo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                        </tr>
                    </thead>
                    <tbody id="allArticoliTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($allArticoli as $articolo): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($articolo['Articolo'] ?? '') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($articolo['sku'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex items-center justify-end p-4 border-t border-gray-200 dark:border-gray-700">
            <button onclick="hideAllArticoliModal()"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                Chiudi
            </button>
        </div>
    </div>
</div>

<script>
    // Tab switching functionality
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
            button.classList.remove('text-purple-600', 'border-purple-500', 'dark:text-purple-500');
            button.classList.add('text-gray-500', 'border-transparent', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
        });
        
        // Show selected tab content
        document.getElementById('content-' + tabName).classList.remove('hidden');
        
        // Add active class to selected tab button
        const activeButton = document.getElementById('tab-' + tabName);
        activeButton.classList.add('active');
        activeButton.classList.remove('text-gray-500', 'border-transparent', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
        activeButton.classList.add('text-purple-600', 'border-purple-500', 'dark:text-purple-500');
    }
    
    // Ricerca ordine
    document.getElementById('searchOrderForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const ordineInput = document.getElementById('search_order');
        const ordine = ordineInput.value.trim();
        
        if (!ordine) {
            if (window.COREGRE && window.COREGRE.showModal) {
                window.COREGRE.showModal('Errore', 'Inserisci un numero ordine', 'error');
            } else {
                alert('Inserisci un numero ordine');
            }
            return;
        }
        
        try {
            const response = await fetch(window.COREGRE.baseUrl + '/tracking/search-order-details?ordine=' + encodeURIComponent(ordine));
            const data = await response.json();
            
            if (data.success) {
                // Mostra i dettagli dell'ordine
                document.getElementById('order_number').value = data.data.ordine;
                document.getElementById('order_date').value = data.data.date || '';
                document.getElementById('orderDetailsContainer').style.display = 'block';
                document.getElementById('noOrderResultsMessage').classList.add('hidden');
            } else {
                // Mostra messaggio di errore
                document.getElementById('orderDetailsContainer').style.display = 'none';
                document.getElementById('noOrderResultsMessage').classList.remove('hidden');
                document.getElementById('noOrderResultsText').textContent = data.error || 'Nessun dettaglio trovato per l\'ordine ' + ordine;
            }
        } catch (error) {
            console.error('Errore nella ricerca ordine:', error);
            showAlert('Errore durante la ricerca', 'error');
        }
    });
    
    // Ricerca articolo
    document.getElementById('searchArticoloForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const articoloInput = document.getElementById('search_articolo');
        const articolo = articoloInput.value.trim();
        
        if (!articolo) {
            if (window.COREGRE && window.COREGRE.showModal) {
                window.COREGRE.showModal('Errore', 'Inserisci un codice articolo', 'error');
            } else {
                alert('Inserisci un codice articolo');
            }
            return;
        }
        
        try {
            const response = await fetch(window.COREGRE.baseUrl + '/tracking/search-articolo-details?articolo=' + encodeURIComponent(articolo));
            const data = await response.json();
            
            if (data.success) {
                // Mostra i dettagli dell'articolo
                document.getElementById('articolo_code').value = data.data.art;
                document.getElementById('articolo_sku').value = data.data.sku || '';
                document.getElementById('articoloDetailsContainer').style.display = 'block';
                document.getElementById('noArticoloResultsMessage').classList.add('hidden');
            } else {
                // Mostra messaggio di errore
                document.getElementById('articoloDetailsContainer').style.display = 'none';
                document.getElementById('noArticoloResultsMessage').classList.remove('hidden');
                document.getElementById('noArticoloResultsText').textContent = data.error || 'Nessun dettaglio trovato per l\'articolo ' + articolo;
            }
        } catch (error) {
            console.error('Errore nella ricerca articolo:', error);
            showAlert('Errore durante la ricerca', 'error');
        }
    });
    
    // Initialize with first tab active - PJAX compatible
    function initLotDetail() {
        switchTab('lotti');
    }
    
    // Registrazione PJAX
    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initLotDetail);
    }
    
    // Fallback primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLotDetail);
    } else {
        initLotDetail();
    }
    
    <?= $pageScripts ?>
</script>