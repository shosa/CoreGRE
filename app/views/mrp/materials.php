<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Dashboard Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Materiali MRP
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Situazione completa dei materiali con calcoli MRP
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="<?= $this->url('/mrp/import') ?>"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <i class="fas fa-upload mr-2"></i>
                Aggiorna Dati
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
                <span class="text-gray-700 dark:text-gray-300">Materiali</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Filtri -->
<div class="mb-0">
    <div
        class="rounded-t-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-800/40 md:p-6 shadow-lg backdrop-blur-sm border-b-0">
        <form method="GET" action="<?= $this->url('/mrp/materials') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="category" value="<?= htmlspecialchars($categoryFilter) ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Cerca
                </label>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                    placeholder="Codice o descrizione..."
                    class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Con Taglie
                </label>
                <select name="has_size"
                    class="w-full rounded-lg border-gray-300 px-4 py-2.5 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary">
                    <option value="">Tutti</option>
                    <option value="1" <?= $hasSize === '1' ? 'selected' : '' ?>>Sì</option>
                    <option value="0" <?= $hasSize === '0' ? 'selected' : '' ?>>No</option>
                </select>
            </div>

            <div></div>

            <div class="flex items-end">
                <button type="submit"
                    class="w-full rounded-lg border border-gray-300 bg-gradient-to-r from-gray-500 to-gray-600 px-4 py-2.5 text-sm font-medium text-white hover:from-gray-600 hover:to-gray-700 shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-search mr-2"></i>
                    Filtra
                </button>
            </div>
        </form>
    </div>

    <!-- Tab Categorie -->
    <div
        class="border-x border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800/40 px-5 md:px-6 py-3 shadow-lg backdrop-blur-sm">
        <div class="flex flex-wrap gap-2">
            <!-- Tab Tutte -->
            <button onclick="filterByCategory('')"
                class="category-tab flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 <?= empty($categoryFilter) ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-md' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-gray-200' ?>"
                data-category="">
                <i class="fas fa-th-large mr-2 text-xs"></i>
                <span>TUTTO</span>
                <span
                    class="ml-2 <?= empty($categoryFilter) ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300' ?> py-0.5 px-2 rounded-full text-xs font-semibold">
                    <?= $materials->count() ?>
                </span>
            </button>

            <!-- Tab per ogni categoria -->
            <?php foreach ($categories as $cat): ?>
                <button onclick="filterByCategory('<?= htmlspecialchars($cat->category) ?>')"
                    class="category-tab flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 <?= $categoryFilter === $cat->category ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-md' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-gray-200' ?>"
                    data-category="<?= htmlspecialchars($cat->category) ?>">
                    <i class="fas fa-tag mr-2 text-xs"></i>
                    <span><?= htmlspecialchars($cat->category_name ?: $cat->category) ?></span>
                    <span
                        class="ml-2 <?= $categoryFilter === $cat->category ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300' ?> py-0.5 px-2 rounded-full text-xs font-semibold">
                        <?= $cat->material_count ?>
                    </span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Tabella Materiali -->
<div
    class="rounded-b-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800/40 shadow-lg backdrop-blur-sm border-t-0">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h3 class="text-title-md font-bold text-gray-900 dark:text-white">
                Elenco Materiali
                <span class="materials-count ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                    (<?= $materials->count() ?> elementi)
                </span>

            </h3>
        </div>
    </div>

    <?php if ($materials->isEmpty()): ?>
        <div class="px-4 py-8 text-center">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-inbox text-4xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                Nessun materiale trovato
            </h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
                <?php if ($search || $hasSize !== ''): ?>
                    Prova a modificare i filtri di ricerca.
                <?php else: ?>
                    Inizia caricando i dati dal sistema ERP.
                <?php endif; ?>
            </p>
            <a href="<?= $this->url('/mrp/import') ?>"
                class="pjax-link inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-upload mr-2"></i>
                Import Dati ERP
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Materiale
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
                            Situazione
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Azioni
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($materials as $material):
                        $mancante = $material->mancante;
                        $fabbisogno = $material->fabbisogno;
                        $ordinato = $material->ordinato;

                        if ($mancante > 0) {
                            $situationText = 'DA ORDINARE';
                            $situationClass = 'bg-red-100 text-red-800';
                        } elseif ($mancante === 0) {
                            $situationText = 'OK';
                            $situationClass = 'bg-green-100 text-green-800';
                        } elseif ($mancante < 0) {
                            $situationText = 'ECCESSO';
                            $situationClass = 'bg-yellow-100 text-yellow-800';
                        } elseif ($ordinato < $fabbisogno) {
                            $situationText = 'DA RICEVERE';
                            $situationClass = 'bg-purple-100 text-purple-800';
                        }
                        ?>
                        <tr class="material-row hover:bg-gray-50 dark:hover:bg-gray-700"
                            data-category="<?= htmlspecialchars($material->category ?: '') ?>">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($material->material_code) ?>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 max-w-xs ">
                                        <?= htmlspecialchars($material->description) ?>
                                    </div>
                                    <?php if ($material->unit_measure): ?>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mt-1">
                                            <?= htmlspecialchars($material->unit_measure) ?>
                                        </span>
                                    <?php endif; ?>
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
                                <div class="flex flex-col items-end space-y-1">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $situationClass ?>">
                                        <?= $situationText ?>
                                    </span>
                                    <?php if ($mancante != 0): ?>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            <?= $mancante > 0 ? 'Manca: ' . number_format($mancante, 0, ',', '.') : 'Eccesso: ' . number_format(abs($mancante), 0, ',', '.') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= $this->url('/mrp/material/' . $material->id) ?>"
                                    class="pjax-link inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-800/40 transition-colors"
                                    title="Visualizza dettagli">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Legenda -->
<div class="mt-4 bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Legenda Situazione:</h4>
    <div class="flex flex-wrap gap-4 text-xs">
        <div class="flex items-center">
            <span class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 text-green-800 mr-2">OK</span>
            <span class="text-gray-600 dark:text-gray-400">Fabbisogno completamente soddisfatto</span>
        </div>
        <div class="flex items-center">
            <span class="inline-flex items-center px-2 py-1 rounded-full bg-red-100 text-red-800 mr-2">MANCANO</span>
            <span class="text-gray-600 dark:text-gray-400">Quantità mancanti rispetto al fabbisogno</span>
        </div>
        <div class="flex items-center">
            <span
                class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 mr-2">ECCESSO</span>
            <span class="text-gray-600 dark:text-gray-400">Ricevuto più del fabbisogno</span>
        </div>
    </div>
</div>

<script>
    // Filtraggio lato client per categorie
    function filterByCategory(category) {
        // Aggiorna aspetto tab
        document.querySelectorAll('.category-tab').forEach(tab => {
            const tabCategory = tab.getAttribute('data-category');
            const badge = tab.querySelector('span:last-child');

            if (tabCategory === category) {
                // Tab attivo - blu primary
                tab.className = 'category-tab flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 bg-blue-600 hover:bg-blue-700 text-white shadow-md';
                if (badge) {
                    badge.className = 'ml-2 bg-blue-500 text-white py-0.5 px-2 rounded-full text-xs font-semibold';
                }
            } else {
                // Tab inattivo - bianco
                tab.className = 'category-tab flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-gray-200';
                if (badge) {
                    badge.className = 'ml-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 py-0.5 px-2 rounded-full text-xs font-semibold';
                }
            }
        });

        // Filtra righe tabella
        const rows = document.querySelectorAll('.material-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowCategory = row.getAttribute('data-category');

            if (category === '' || rowCategory === category) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Aggiorna conteggio nella tabella
        const countElement = document.querySelector('.materials-count');
        if (countElement) {
            countElement.textContent = `(${visibleCount} elementi)`;
        }

        // Aggiorna campo hidden per i form
        const categoryInput = document.querySelector('input[name="category"]');
        if (categoryInput) {
            categoryInput.value = category;
        }

        // Aggiorna URL senza ricaricare
        const url = new URL(window.location);
        const searchParams = new URLSearchParams(url.search);

        if (category) {
            searchParams.set('category', category);
        } else {
            searchParams.delete('category');
        }

        url.search = searchParams.toString();
        window.history.replaceState({}, '', url.toString());
    }

    // Cleanup per PJAX
    function cleanupEventListeners() {
        // Cleanup eventuali event listener se necessario
    }

    // Inizializzazione
    document.addEventListener('DOMContentLoaded', function () {
        console.log('MRP Materials loaded with categories');

        // Aggiorna anche il campo hidden quando cambiano i filtri
        const categoryInput = document.querySelector('input[name="category"]');
        if (categoryInput) {
            const urlParams = new URLSearchParams(window.location.search);
            const categoryFromUrl = urlParams.get('category') || '';
            categoryInput.value = categoryFromUrl;
        }
    });
</script>