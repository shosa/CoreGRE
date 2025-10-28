<?php
/**
 * Riparazioni - Modifica Riparazione
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <!-- Icon Box -->
                <div class="mr-4 bg-gradient-to-r from-green-500 to-green-600 text-white p-3 rounded-2xl shadow-lg">
                    <i class="fas fa-edit fa-2x"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Modifica Riparazione #<?= htmlspecialchars($riparazione->IDRIP) ?>
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Aggiorna i dati della riparazione selezionata
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <?php
            $urgencyColor = 'gray';
            $urgencyBg = 'bg-gray-100 text-gray-800';
            switch ($riparazione->URGENZA ?? 'BASSA') {
                case 'ALTA':
                    $urgencyBg = 'bg-red-100 text-red-800';
                    break;
                case 'MEDIA':
                    $urgencyBg = 'bg-yellow-100 text-yellow-800';
                    break;
                case 'BASSA':
                    $urgencyBg = 'bg-green-100 text-green-800';
                    break;
            }
            ?>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $urgencyBg ?>">
                Urgenza: <?= htmlspecialchars($riparazione->URGENZA ?? 'BASSA') ?>
            </span>

            <a href="<?= $this->url('/riparazioni/' . $riparazione->IDRIP) ?>"
               class="inline-flex items-center rounded-lg bg-gradient-to-r from-gray-500 to-gray-600 px-4 py-2 text-sm font-medium text-white hover:from-gray-600 hover:to-gray-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-arrow-left mr-2"></i>
                Annulla
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
                <a href="<?= $this->url('/riparazioni/' . $riparazione->IDRIP) ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                    #<?= htmlspecialchars($riparazione->IDRIP) ?>
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Modifica</span>
            </div>
        </li>
    </ol>
</nav>

<form action="<?= $this->url('/riparazioni/' . $riparazione->IDRIP . '/update') ?>" method="POST"
    id="editRiparazioneForm">
    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
    <input type="hidden" name="numerata" value="<?= htmlspecialchars($riparazione->NU ?? '') ?>">
    <input type="hidden" name="utente" value="<?= htmlspecialchars($riparazione->UTENTE ?? $_SESSION['username']) ?>">
    <input type="hidden" name="cliente" value="<?= htmlspecialchars($riparazione->CLIENTE ?? '') ?>">
    <input type="hidden" name="commessa" value="<?= htmlspecialchars($riparazione->COMMESSA ?? '') ?>">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Contenuto Principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Dati Principali -->
            <div
                class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg mr-3">
                            <i class="fas fa-edit text-white"></i>
                        </div>
                        Dati Principali
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        Modifica i dati della riparazione. I campi contrassegnati con <span
                            class="text-red-500">*</span> sono obbligatori.
                    </p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Codice -->
                        <div>
                            <label for="CODICE" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-barcode text-blue-500 mr-2"></i>
                                Codice Articolo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="CODICE" name="CODICE"
                                value="<?= htmlspecialchars($riparazione->CODICE ?? '') ?>" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                placeholder="Inserisci codice articolo">
                        </div>

                        <!-- Cartellino -->
                        <div>
                            <label for="cartellino"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-ticket-alt text-purple-500 mr-2"></i>
                                Cartellino <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="cartellino" name="cartellino"
                                value="<?= htmlspecialchars($riparazione->CARTELLINO ?? '') ?>" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                placeholder="Numero cartellino">
                        </div>

                        <!-- Articolo -->
                        <div class="md:col-span-2">
                            <label for="ARTICOLO"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-tag text-green-500 mr-2"></i>
                                Descrizione Articolo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="ARTICOLO" name="ARTICOLO"
                                value="<?= htmlspecialchars($riparazione->ARTICOLO ?? '') ?>" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                placeholder="Descrizione dell'articolo">
                        </div>

                        <!-- Quantità (calcolata automaticamente dalla numerata) -->
                        <div>
                            <label for="qta_display"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-sort-numeric-up text-orange-500 mr-2"></i>
                                Quantità Totale
                            </label>
                            <div
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                                <?= htmlspecialchars($riparazione->QTA ?? '0') ?> paia
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                La quantità totale verrà calcolata automaticamente dalla numerata.
                            </p>
                        </div>

                        <!-- Linea -->
                        <div>
                            <label for="linea" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-route text-indigo-500 mr-2"></i>
                                Linea
                            </label>
                            <input type="text" id="linea" name="linea"
                                value="<?= htmlspecialchars($riparazione->LINEA ?? '') ?>"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                placeholder="Linea produttiva">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Numerata Section -->
            <?php
            // Carica la numerata corretta se abbiamo NU
            $numerataTaglie = null;
            if (!empty($riparazione->NU)) {
                $numerataTaglie = \App\Models\IdSize::where('ID', $riparazione->NU)->first();
            }

            // Mostra taglie se esiste la numerata
            $hasTaglie = $numerataTaglie ? true : false;
            ?>
            <?php if ($hasTaglie): ?>
                <div
                    class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 mb-8 backdrop-blur-sm">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mr-3">
                                    <i class="fas fa-ruler-combined text-white"></i>
                                </div>
                                Scalarino Taglie
                            </h2>
                            <div class="flex items-center space-x-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    Totale: <span id="totalQuantity" class="font-bold ml-1">0</span> paia
                                </span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            Modifica le quantità da riparare per ogni taglia. I valori attuali sono precompilati.
                        </p>
                    </div>
                    <div class="p-6">
                        <!-- Header con conteggio taglie attive -->
                        <?php
                        $activeSizes = 0;
                        for ($i = 1; $i <= 20; $i++) {
                            $nField = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                            // Conta tutte le taglie disponibili nella numerata
                            if ($numerataTaglie && !empty($numerataTaglie->$nField)) {
                                $activeSizes++;
                            }
                        }
                        ?>
                        <div
                            class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 shadow-md">
                                        <i class="fas fa-ruler text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">
                                            <?= $activeSizes ?> Taglie Disponibili
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Articolo: <?= htmlspecialchars($riparazione->ARTICOLO ?? '') ?>
                                        </p>
                                    </div>
                                </div>
                                <button type="button" onclick="fillAllSizes()"
                                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm rounded-lg hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                                    <i class="fas fa-magic mr-2"></i>
                                    Compila Tutto
                                </button>
                            </div>
                        </div>

                        <!-- Tabella creativa per le taglie -->
                        <div
                            class="overflow-x-auto bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-inner">
                            <table class="w-full">
                                <!-- Header con le taglie -->
                                <thead>
                                    <tr class="border-b-2 border-gray-200 dark:border-gray-600">
                                        <?php
                                        // Header con le etichette della numerata o taglie semplici
                                        for ($i = 1; $i <= 20; $i++) {
                                            $nField = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                            $pField = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);

                                            // In modifica: mostra tutte le taglie disponibili nella numerata
                                            if ($numerataTaglie && !empty($numerataTaglie->$nField)) {
                                                $taglia = htmlspecialchars($numerataTaglie->$nField);

                                                echo '<th class="p-6 text-center">';
                                                echo '<div class="inline-flex items-center justify-center w-16 h-12 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl shadow-lg font-bold text-lg transform hover:scale-105 transition-all duration-200">';
                                                echo $taglia;
                                                echo '</div>';
                                                echo '</th>';
                                            }
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Quantità attuali da riparare -->
                                    <tr
                                        class="bg-blue-50/30 dark:bg-blue-900/20 border-b border-gray-200 dark:border-gray-600">
                                        <?php
                                        for ($i = 1; $i <= 20; $i++) {
                                            $nField = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                            $pField = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);

                                            // In modifica: mostra tutte le taglie disponibili nella numerata
                                            if ($numerataTaglie && !empty($numerataTaglie->$nField)) {
                                                $currentValue = $riparazione->$pField ?? 0;
                                                echo '<td class="p-6 text-center">';
                                                echo '<div class="inline-flex items-center justify-center w-12 h-8 bg-blue-100 dark:bg-blue-800/50 border border-blue-200 dark:border-blue-600 rounded-lg text-blue-800 dark:text-blue-200 font-bold text-sm">';
                                                echo $currentValue;
                                                echo '</div>';
                                                echo '</td>';
                                            }
                                        }
                                        ?>
                                    </tr>
                                    <!-- Input per quantità da riparare -->
                                    <tr>
                                        <?php
                                        for ($i = 1; $i <= 20; $i++) {
                                            $nField = 'N' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                            $pField = 'P' . str_pad($i, 2, '0', STR_PAD_LEFT);

                                            // In modifica: mostra tutte le taglie disponibili nella numerata
                                            if ($numerataTaglie && !empty($numerataTaglie->$nField)) {
                                                $currentRepairValue = $riparazione->$pField ?? 0;

                                                echo '<td class="p-6 text-center">';
                                                echo '<input type="number" name="' . $pField . '" value="' . $currentRepairValue . '" min="0" max="9999" ';
                                                echo 'class="w-16 h-12 text-center text-lg font-bold border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-400 dark:bg-gray-700 dark:text-white transition-all duration-200 hover:border-blue-400 dark:hover:border-blue-500" ';
                                                echo 'oninput="updateTableProgress(this)" />';
                                                echo '</td>';
                                            }
                                        }
                                        ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Suggerimenti per quantità -->
                        <div
                            class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <p class="text-sm text-blue-800 dark:text-blue-200 flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i>
                                <strong>Suggerimento:</strong> Usa <kbd
                                    class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">Ctrl+Shift+F</kbd> per
                                compilare velocemente tutte le taglie con 1.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Assegnazione e Priorità -->
            <div
                class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mr-3">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        Assegnazione e Priorità
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Reparto -->
                        <div>
                            <label for="reparto"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-building text-gray-500 mr-2"></i>
                                Reparto <span class="text-red-500">*</span>
                            </label>
                            <select id="reparto" name="reparto" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="">Seleziona reparto</option>
                                <?php foreach ($reparti as $rep): ?>
                                    <option value="<?= htmlspecialchars($rep->Nome) ?>"
                                        <?= ($riparazione->REPARTO === $rep->Nome) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($rep->Nome) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Laboratorio -->
                        <div>
                            <label for="laboratorio"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-flask text-purple-500 mr-2"></i>
                                Laboratorio <span class="text-red-500">*</span>
                            </label>
                            <select id="laboratorio" name="laboratorio" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="">Seleziona laboratorio</option>
                                <?php foreach ($laboratori as $lab): ?>
                                    <option value="<?= htmlspecialchars($lab->Nome) ?>"
                                        <?= ($riparazione->LABORATORIO === $lab->Nome) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($lab->Nome) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Urgenza -->
                        <div>
                            <label for="urgenza"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                Urgenza <span class="text-red-500">*</span>
                            </label>
                            <select id="urgenza" name="urgenza" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="BASSA" <?= ($riparazione->URGENZA === 'BASSA') ? 'selected' : '' ?>>🟢
                                    Bassa</option>
                                <option value="MEDIA" <?= ($riparazione->URGENZA === 'MEDIA') ? 'selected' : '' ?>>🟡
                                    Media</option>
                                <option value="ALTA" <?= ($riparazione->URGENZA === 'ALTA') ? 'selected' : '' ?>>🔴 Alta
                                </option>
                            </select>
                        </div>

                        <!-- Data -->
                        <div>
                            <label for="data" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-calendar text-blue-500 mr-2"></i>
                                Data <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="data" name="data"
                                value="<?= $riparazione->DATA ? date('Y-m-d', strtotime($riparazione->DATA)) : date('Y-m-d') ?>"
                                required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Causale -->
            <div
                class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-600 shadow-lg mr-3">
                            <i class="fas fa-clipboard-list text-white"></i>
                        </div>
                        Motivo Riparazione
                    </h2>
                </div>

                <div class="p-6">
                    <label for="causale" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-comment-alt text-red-500 mr-2"></i>
                        Descrizione del problema <span class="text-red-500">*</span>
                    </label>
                    <textarea id="causale" name="causale" required rows="4"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                        placeholder="Descrivi dettagliatamente il motivo della riparazione..."><?= htmlspecialchars($riparazione->CAUSALE ?? '') ?></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Spiega chiaramente qual è il problema riscontrato sull'articolo
                    </p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Azioni -->
            <div
                class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg mr-3">
                            <i class="fas fa-cogs text-white"></i>
                        </div>
                        Azioni
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-[1.02] hover:shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        Salva Modifiche
                    </button>

                    <a href="<?= $this->url('/riparazioni/' . $riparazione->IDRIP) ?>"
                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Annulla
                    </a>
                </div>
            </div>

            <!-- Informazioni Riparazione -->
            <div
                class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg mr-3">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                        Informazioni
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">ID Riparazione:</span>
                            <span
                                class="font-semibold text-gray-900 dark:text-white">#<?= htmlspecialchars($riparazione->IDRIP) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Stato:</span>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $riparazione->COMPLETA ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' ?>">
                                <?= $riparazione->COMPLETA ? 'Completata' : 'In Corso' ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Creata da:</span>
                            <span
                                class="font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($riparazione->UTENTE ?? 'N/A') ?></span>
                        </div>
                        <?php if ($riparazione->COMMESSA): ?>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Commessa:</span>
                                <span
                                    class="font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($riparazione->COMMESSA) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Suggerimenti -->
            <div
                class="bg-white dark:bg-gray-800/40 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 backdrop-blur-sm">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-yellow-500 to-yellow-600 shadow-lg mr-3">
                            <i class="fas fa-lightbulb text-white"></i>
                        </div>
                        Suggerimenti
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div
                            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                <strong>Modifica attenta:</strong><br>
                                Verifica che tutte le informazioni siano corrette prima di salvare.
                            </p>
                        </div>
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <strong>Causale dettagliata:</strong><br>
                                Una descrizione precisa aiuta il laboratorio a comprendere il problema.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Riparazioni Edit - JavaScript compatibile con PJAX
(function() {
    // Variabili globali per cleanup
    let eventListeners = [];
    
    // Funzione di inizializzazione
    function initRiparazioniEdit() {
        // Cleanup precedenti event listeners
        cleanupEventListeners();
        
        const form = document.getElementById('editRiparazioneForm');
        
        if (form) {
            function formSubmitHandler(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('border-red-500', 'focus:ring-red-500');
                    } else {
                        field.classList.remove('border-red-500', 'focus:ring-red-500');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    if (window.WebgreNotifications && window.WebgreNotifications.error) {
                        window.WebgreNotifications.error('Per favore, compila tutti i campi obbligatori.');
                    } else if (window.showAlert) {
                        window.showAlert('Per favore, compila tutti i campi obbligatori.', 'error');
                    } else {
                        alert('Per favore, compila tutti i campi obbligatori.');
                    }
                    return false;
                }

                // Conferma modifiche
                if (!confirm('Sei sicuro di voler salvare le modifiche a questa riparazione?')) {
                    e.preventDefault();
                    return false;
                }
            }
            
            form.addEventListener('submit', formSubmitHandler);
            eventListeners.push({ element: form, event: 'submit', handler: formSubmitHandler });
        }

        // Initialize numerata progress tracking
        updateTotalQuantity();
    }
    
    function cleanupEventListeners() {
        eventListeners.forEach(({ element, event, handler }) => {
            element.removeEventListener(event, handler);
        });
        eventListeners = [];
    }

    // Funzioni globali (usate da onclick inline)
    window.updateTableProgress = function(input) {
        const value = parseInt(input.value) || 0;

        // Feedback visivo
        if (value > 0) {
            input.classList.remove('border-gray-300', 'dark:border-gray-600');
            input.classList.add('border-purple-400', 'dark:border-purple-500');
        } else {
            input.classList.remove('border-purple-400', 'dark:border-purple-500');
            input.classList.add('border-gray-300', 'dark:border-gray-600');
        }

        // Add scaling effect
        input.classList.add('scale-105');
        setTimeout(() => {
            input.classList.remove('scale-105');
        }, 150);

        // Update total
        updateTotalQuantity();
    };

    // Calculate and update total quantity
    window.updateTotalQuantity = function() {
        const quantityInputs = document.querySelectorAll('input[type="number"][name^="P"]');
        let total = 0;

        quantityInputs.forEach(input => {
            const value = parseInt(input.value) || 0;
            total += value;
        });

        const totalElement = document.getElementById('totalQuantity');
        if (totalElement) {
            totalElement.textContent = total;

            // Animate the number change
            totalElement.classList.add('animate-pulse');
            setTimeout(() => {
                totalElement.classList.remove('animate-pulse');
            }, 300);
        }
    };

    // Fill all sizes with 1 (helper function)
    window.fillAllSizes = function() {
        const quantityInputs = document.querySelectorAll('input[type="number"][name^="P"]');

        quantityInputs.forEach(input => {
            input.value = 1;
            updateTableProgress(input);
        });

        if (window.WebgreNotifications && window.WebgreNotifications.success) {
            window.WebgreNotifications.success('Tutte le quantità sono state impostate a 1.');
        } else if (window.showAlert) {
            window.showAlert('Tutte le quantità sono state impostate a 1.', 'success');
        } else {
            alert('Tutte le quantità sono state impostate a 1.');
        }
    };

    // Keyboard shortcuts handler
    let keyboardListener = null;
    
    function initKeyboardShortcuts() {
        // Rimuovi listener esistente per evitare duplicati
        if (keyboardListener) {
            document.removeEventListener('keydown', keyboardListener);
        }
        
        keyboardListener = function(e) {
            // Ctrl+Shift+F = Fill all sizes
            if (e.ctrlKey && e.shiftKey && e.key === 'F') {
                e.preventDefault();
                fillAllSizes();
            }
        };
        
        document.addEventListener('keydown', keyboardListener);
    }

    // Registra l'inizializzatore per PJAX
    if (window.WEBGRE && window.WEBGRE.onPageLoad) {
        window.WEBGRE.onPageLoad(() => {
            initRiparazioniEdit();
            initKeyboardShortcuts();
        });
    }

    // Inizializza anche al primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initRiparazioniEdit();
            initKeyboardShortcuts();
        });
    } else {
        initRiparazioniEdit();
        initKeyboardShortcuts();
    }
})();
</script>