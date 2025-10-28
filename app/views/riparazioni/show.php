<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <!-- Icon Box -->
                <div class="mr-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3 rounded-2xl shadow-lg">
                    <i class="fas fa-tools fa-2x"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Riparazione #<?= htmlspecialchars($riparazione->IDRIP) ?>
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Dettagli e gestione della riparazione selezionata
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <!-- Pulsante Stampa PDF -->
            <a href="<?= $this->url('/riparazioni/' . $riparazione->IDRIP . '/print') ?>"
                class="inline-flex items-center rounded-lg bg-gradient-to-r from-red-500 to-red-600 px-4 py-2 text-sm font-medium text-white hover:from-red-600 hover:to-red-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5"
                target="_blank">
                <i class="fas fa-print mr-2"></i>
                Stampa PDF
            </a>

            <?php if (!$riparazione->COMPLETA): ?>
                <a href="<?= $this->url('/riparazioni/' . $riparazione->IDRIP . '/edit') ?>"
                    class="inline-flex items-center rounded-lg bg-gradient-to-r from-green-500 to-green-600 px-4 py-2 text-sm font-medium text-white hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                    <i class="fas fa-edit mr-2"></i>
                    Modifica
                </a>

                <form method="POST" action="<?= $this->url('/riparazioni/' . $riparazione->IDRIP . '/complete') ?>"
                    class="inline">
                    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                    <button type="submit" onclick="return confirm('Sei sicuro di voler completare questa riparazione?')"
                        class="inline-flex items-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                        <i class="fas fa-check mr-2"></i>
                        Completa
                    </button>
                </form>
            <?php endif; ?>

            <a href="<?= $this->url('/riparazioni') ?>"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna all'elenco
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
                <a href="<?= $this->url('/riparazioni') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                    Riparazioni
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">#<?= htmlspecialchars($riparazione->IDRIP) ?></span>
            </div>
        </li>
    </ol>
</nav>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informazioni Principali -->
    <div class="lg:col-span-2">
        <div
            class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mr-3">
                            <i class="fas fa-clipboard-check text-white"></i>
                        </div>
                        Dettagli Riparazione
                    </h2>
                    <?php
                    $urgencyColor = 'gray';
                    $urgencyBg = 'bg-gray-100 text-gray-800';
                    switch ($riparazione->URGENZA ?? 'BASSA') {
                        case 'ALTA':
                            $urgencyColor = 'red';
                            $urgencyBg = 'bg-red-100 text-red-800';
                            break;
                        case 'MEDIA':
                            $urgencyColor = 'yellow';
                            $urgencyBg = 'bg-yellow-100 text-yellow-800';
                            break;
                        case 'BASSA':
                            $urgencyColor = 'green';
                            $urgencyBg = 'bg-green-100 text-green-800';
                            break;
                    }

                    $isComplete = $riparazione->COMPLETA == 1;
                    $statusColor = $isComplete ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
                    $statusText = $isComplete ? 'Completa' : 'Aperta';
                    ?>
                    <div class="flex items-center space-x-2">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $urgencyBg ?>">
                            Urgenza: <?= htmlspecialchars($riparazione->URGENZA ?? 'BASSA') ?>
                        </span>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColor ?>">
                            <?= $statusText ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                            Articolo
                        </h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Codice</label>
                                <p
                                    class="mt-1 text-sm text-gray-900 dark:text-white font-mono bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded">
                                    <?= htmlspecialchars($riparazione->CODICE ?? '') ?>
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrizione</label>
                                <p
                                    class="mt-1 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded">
                                    <?= htmlspecialchars($riparazione->ARTICOLO ?? '') ?>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantità
                                    Totale</label>
                                <p class="mt-1 text-lg font-bold text-blue-600 dark:text-blue-400">
                                    <?= htmlspecialchars($riparazione->QTA ?? '0') ?> pz
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                            Produzione
                        </h4>
                        <div class="space-y-3">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cartellino</label>
                                <p
                                    class="mt-1 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded">
                                    <?= htmlspecialchars($riparazione->CARTELLINO ?? '') ?>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Linea</label>
                                <p
                                    class="mt-1 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded">
                                    <?= htmlspecialchars($riparazione->LINEA ?? '') ?>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data</label>
                                <p
                                    class="mt-1 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded">
                                    <?= htmlspecialchars($riparazione->DATA ?? '') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dettaglio Taglie -->
                <?php
                // Verifica se ci sono quantità per le taglie
                $hasTaglie = false;
                if (isset($sizesWithQuantities) && !empty($sizesWithQuantities)) {
                    $hasTaglie = true;
                } else {
                    // Fallback: controlla direttamente nelle taglie P01-P20
                    for ($i = 1; $i <= 20; $i++) {
                        $pField = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                        if (!empty($riparazione->$pField) && $riparazione->$pField > 0) {
                            $hasTaglie = true;
                            break;
                        }
                    }
                }
                ?>
                <?php if ($hasTaglie): ?>
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">
                            Dettaglio per Taglia
                        </h4>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200 dark:border-gray-600">
                                            <?php
                                            // Prima ottengo la numerata se disponibile
                                            $numerataData = null;
                                            if ($riparazione->NU) {
                                                $numerataData = \App\Models\IdSize::where('ID', $riparazione->NU)->first();
                                            }

                                            for ($i = 1; $i <= 20; $i++): ?>
                                                <?php
                                                $nField = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                                $pField = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                                if (!empty($riparazione->$pField) && $riparazione->$pField > 0):
                                                    // Usa il nome dalla numerata se disponibile, altrimenti il numero
                                                    $tagliaName = ($numerataData && !empty($numerataData->$nField)) ? $numerataData->$nField : $i;
                                                    ?>
                                                    <th class="px-2 py-2 text-center font-medium text-gray-700 dark:text-gray-300">
                                                        <?= htmlspecialchars($tagliaName) ?>
                                                    </th>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                            <th class="px-2 py-2 text-center font-medium text-blue-600 dark:text-blue-400">
                                                TOTALE
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <?php
                                            $totale = 0;
                                            for ($i = 1; $i <= 20; $i++):
                                                $pField = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                                if (!empty($riparazione->$pField) && $riparazione->$pField > 0):
                                                    $totale += $riparazione->$pField;
                                                    ?>
                                                    <td class="px-2 py-2 text-center font-semibold text-gray-900 dark:text-white">
                                                        <?= htmlspecialchars($riparazione->$pField) ?>
                                                    </td>
                                                <?php endif; endfor; ?>
                                            <td class="px-2 py-2 text-center font-bold text-blue-600 dark:text-blue-400">
                                                <?= $totale ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Informazioni Riparazione -->
        <div
            class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg mr-3">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    Informazioni Riparazione
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reparto</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            <?= htmlspecialchars($riparazione->REPARTO ?? 'Non specificato') ?>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Laboratorio</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            <?= htmlspecialchars($riparazione->LABORATORIO ?? 'Non specificato') ?>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Utente</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            <?= htmlspecialchars($riparazione->UTENTE ?? 'Non specificato') ?>
                        </p>
                    </div>
                    <?php if (!empty($riparazione->CLIENTE)): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cliente</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <?= htmlspecialchars($riparazione->CLIENTE) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($riparazione->COMMESSA)): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Commessa</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <?= htmlspecialchars($riparazione->COMMESSA) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Causale -->
        <?php if (!empty($riparazione->CAUSALE)): ?>
            <div
                class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-yellow-500 to-yellow-600 shadow-lg mr-3">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                        Causale Riparazione
                    </h3>
                    <div
                        class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200 whitespace-pre-wrap">
                            <?= htmlspecialchars($riparazione->CAUSALE) ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>