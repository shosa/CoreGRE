<?php
/**
 * Tracking - Associazione Cartellini Selezionati
 * Replica esatta del sistema legacy processLink.php
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="mr-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3 rounded-2xl shadow-lg">
                    <i class="fas fa-link fa-2x"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Associazione Cartellini
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Associa i cartellini selezionati ad un tipo di tracking e ai relativi lotti
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?= $this->url('/tracking/multisearch') ?>" 
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alla Ricerca
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
                <a href="<?= $this->url('/tracking/multisearch') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">Ricerca Multipla</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Associazione</span>
            </div>
        </li>
    </ol>
</nav>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
    <!-- Riepilogo Selezione -->
    <div class="xl:col-span-4">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-list-check mr-3 text-blue-500"></i>
                    Riepilogo Selezione
                </h3>
            </div>
            <div class="p-6">
                <h5 class="text-md font-medium text-gray-900 dark:text-white mb-4">Cartellini:</h5>
                <div class="max-h-96 overflow-y-auto">
                    <div class="grid grid-cols-3 gap-2">
                        <?php foreach ($selectedCartels as $cartel): ?>
                            <div class="bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg text-sm font-mono text-center text-gray-900 dark:text-white">
                                <?= htmlspecialchars($cartel) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <p class="text-blue-700 dark:text-blue-300 text-sm font-medium">
                        <i class="fas fa-info-circle mr-2"></i>
                        Totale: <?= count($selectedCartels) ?> cartellini selezionati
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Associazione -->
    <div class="xl:col-span-8">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-cogs mr-3 text-blue-500"></i>
                    Esegui Associazione
                </h3>
            </div>
            <div class="p-6">
                <form id="associazioneForm" method="post" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                    
                    <div>
                        <label for="type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Seleziona Tipo *
                        </label>
                        <select class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400" 
                                id="type_id" name="type_id" onchange="toggleLotInput()" required>
                            <option value="">Seleziona Tipo</option>
                            <?php foreach ($trackTypes as $type): ?>
                                <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="lotNumbers" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Numeri di Lotto *
                        </label>
                        <textarea class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400" 
                                  id="lotNumbers" name="lotNumbers" rows="4" 
                                  placeholder="Attenzione: inserire un valore per riga"
                                  disabled required></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Inserisci un numero di lotto per riga
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-8 py-3 text-sm font-medium text-white shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed">
                            <i class="fas fa-save mr-2"></i>
                            Salva Associazione
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
<?= $pageScripts ?>
</script>