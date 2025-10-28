<?php
/**
 * SCM Admin - Form Lancio (Crea/Modifica)
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                <?= $isEdit ? 'Modifica' : 'Nuovo' ?> Lancio
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                <?= $isEdit ? 'Aggiorna i dati del lancio di produzione' : 'Crea un nuovo lancio di produzione per laboratorio terzista' ?>
            </p>
        </div>
        <a href="<?= $this->url('/scm-admin/launches') ?>"
            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Torna alla Lista
        </a>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
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
                <a href="<?= $this->url('/scm-admin') ?>"
                    class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    SCM Admin
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/scm-admin/launches') ?>"
                    class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    Lanci
                </a>
            </div>
        </li>
        <?php if ($isEdit): ?>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="<?= $this->url('/scm-admin/launches/' . $launch['id']) ?>"
                        class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        Lancio <?= htmlspecialchars($launch['launch_number']) ?>
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Modifica</span>
                </div>
            </li>
        <?php else: ?>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nuovo Lancio</span>
                </div>
            </li>
        <?php endif; ?>
    </ol>
</nav>

<form method="POST"
    action="<?= $isEdit ? $this->url('/scm-admin/launches/' . $launch['id'] . '/update') : $this->url('/scm-admin/launches/store') ?>">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Informazioni Base -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="flex items-center mb-6">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                        <i class="fas fa-rocket text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informazioni Lancio</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Dati base del lancio di produzione</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="launch_number"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Numero Lancio *
                        </label>
                        <input type="text" id="launch_number" name="launch_number"
                            value="<?= htmlspecialchars($launch['launch_number'] ?? '') ?>" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                            placeholder="LAN-2024-001">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Numero identificativo univoco del lancio
                        </p>
                    </div>

                    <div>
                        <label for="launch_date"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Data Lancio *
                        </label>
                        <input type="date" id="launch_date" name="launch_date"
                            value="<?= $launch['launch_date'] ?? date('Y-m-d') ?>" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="laboratory_id"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Laboratorio Terzista *
                        </label>
                        <select id="laboratory_id" name="laboratory_id" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleziona laboratorio</option>
                            <?php foreach ($laboratories as $lab): ?>
                                <option value="<?= $lab['id'] ?>" <?= ($launch['laboratory_id'] ?? '') == $lab['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($lab['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Stato
                        </label>
                        <select id="status" name="status"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="IN_PREPARAZIONE" <?= ($launch['status'] ?? 'IN_PREPARAZIONE') === 'IN_PREPARAZIONE' ? 'selected' : '' ?>>In Preparazione</option>
                            <option value="IN_LAVORAZIONE" <?= ($launch['status'] ?? '') === 'IN_LAVORAZIONE' ? 'selected' : '' ?>>In Lavorazione</option>
                            <option value="COMPLETATO" <?= ($launch['status'] ?? '') === 'COMPLETATO' ? 'selected' : '' ?>>
                                Completato</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Note Generali
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Note aggiuntive sul lancio..."><?= htmlspecialchars($launch['notes'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Fasi del Ciclo -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg">
                            <i class="fas fa-list-ol text-white text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Fasi del Ciclo Produttivo
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Definisci le fasi di lavorazione in
                                sequenza</p>
                        </div>
                    </div>
                    <button type="button" onclick="loadStandardPhases()"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300">
                        <i class="fas fa-magic mr-2"></i>
                        Carica Fasi Standard
                    </button>
                </div>

                <div id="phases-container">
                    <div class="space-y-3" id="phases-list">
                        <?php
                        $phases = [];
                        if (!empty($launch['phases_cycle'])) {
                            $phases = explode(';', $launch['phases_cycle']);
                        }
                        if (empty($phases)) {
                            $phases = [''];
                        }
                        ?>
                        <?php foreach ($phases as $index => $phase): ?>
                            <div class="phase-item flex items-center space-x-3">
                                <span
                                    class="flex-shrink-0 w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                    <?= $index + 1 ?>
                                </span>
                                <input type="text" name="phases[]" value="<?= htmlspecialchars(trim($phase)) ?>"
                                    placeholder="Nome fase (es. Taglio, Montaggio, Controllo...)"
                                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                                <button type="button" onclick="removePhase(this)"
                                    class="flex-shrink-0 p-2 text-red-400 hover:text-red-600 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" onclick="addPhase()"
                        class="mt-4 inline-flex items-center px-4 py-2 border border-purple-300 text-sm font-medium rounded-lg text-purple-700 bg-purple-50 hover:bg-purple-100 dark:border-purple-600 dark:text-purple-300 dark:bg-purple-900/20 dark:hover:bg-purple-900/40 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Aggiungi Fase
                    </button>
                </div>
            </div>

            <!-- Articoli del Lancio -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="flex items-center mb-6">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                        <i class="fas fa-boxes text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Articoli del Lancio</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Articoli da produrre con relative quantità
                        </p>
                    </div>
                </div>

                <div id="articles-container">
                    <div class="space-y-4" id="articles-list">
                        <?php
                        $articles = $launch['articles'] ?? [['article_name' => '', 'total_pairs' => '', 'notes' => '']];
                        if (empty($articles)) {
                            $articles = [['article_name' => '', 'total_pairs' => '', 'notes' => '']];
                        }
                        ?>
                        <?php foreach ($articles as $index => $article): ?>
                            <div
                                class="article-item bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Articolo #<?= $index + 1 ?>
                                    </span>
                                    <button type="button" onclick="removeArticle(this)"
                                        class="text-red-400 hover:text-red-600 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2">
                                        <input type="text" name="articles[<?= $index ?>][article_name]"
                                            value="<?= htmlspecialchars($article['article_name'] ?? '') ?>"
                                            placeholder="Nome/Codice articolo"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white focus:border-green-500 focus:ring-green-500">
                                    </div>
                                    <div>
                                        <input type="number" name="articles[<?= $index ?>][total_pairs]"
                                            value="<?= $article['total_pairs'] ?? '' ?>" placeholder="Paia" min="1"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white focus:border-green-500 focus:ring-green-500">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <textarea name="articles[<?= $index ?>][notes]" rows="2"
                                        placeholder="Note articolo (opzionale)"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white focus:border-green-500 focus:ring-green-500"><?= htmlspecialchars($article['notes'] ?? '') ?></textarea>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" onclick="addArticle()"
                        class="mt-4 inline-flex items-center px-4 py-2 border border-green-300 text-sm font-medium rounded-lg text-green-700 bg-green-50 hover:bg-green-100 dark:border-green-600 dark:text-green-300 dark:bg-green-900/20 dark:hover:bg-green-900/40 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Aggiungi Articolo
                    </button>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="<?= $this->url('/scm-admin/launches') ?>"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Annulla
                </a>
                <button type="submit"
                    class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i>
                    <?= $isEdit ? 'Aggiorna Lancio' : 'Crea Lancio' ?>
                </button>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class="fas fa-chart-pie mr-2 text-blue-500"></i>
                    Riepilogo
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Fasi totali</span>
                        <span id="phases-count" class="text-sm font-medium text-gray-900 dark:text-white">0</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Articoli totali</span>
                        <span id="articles-count" class="text-sm font-medium text-gray-900 dark:text-white">0</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Paia totali</span>
                        <span id="total-pairs" class="text-sm font-medium text-gray-900 dark:text-white">0</span>
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Informazioni
                </h3>
                <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <p>• Le fasi saranno eseguite in sequenza dall'alto verso il basso</p>
                    <p>• Ogni articolo dovrà completare tutte le fasi definite</p>
                    <p>• Il progresso sarà tracciato automaticamente per fase/articolo</p>
                    <p>• È possibile modificare fasi e articoli solo se il lancio è in preparazione</p>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Gestione dinamica fasi
    function addPhase() {
        const phasesList = document.getElementById('phases-list');
        const phaseCount = phasesList.children.length + 1;

        const phaseDiv = document.createElement('div');
        phaseDiv.className = 'phase-item flex items-center space-x-3';
        phaseDiv.innerHTML = `
        <span class="flex-shrink-0 w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
            ${phaseCount}
        </span>
        <input type="text" 
               name="phases[]" 
               placeholder="Nome fase (es. Taglio, Montaggio, Controllo...)"
               class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
        <button type="button" onclick="removePhase(this)" 
                class="flex-shrink-0 p-2 text-red-400 hover:text-red-600 transition-colors">
            <i class="fas fa-trash"></i>
        </button>
    `;

        phasesList.appendChild(phaseDiv);
        updatePhaseNumbers();
        updateCounts();
    }

    function removePhase(button) {
        if (document.querySelectorAll('.phase-item').length > 1) {
            button.closest('.phase-item').remove();
            updatePhaseNumbers();
            updateCounts();
        }
    }

    function updatePhaseNumbers() {
        const phases = document.querySelectorAll('.phase-item');
        phases.forEach((phase, index) => {
            const numberSpan = phase.querySelector('span');
            numberSpan.textContent = index + 1;
        });
    }

    // Gestione dinamica articoli
    let articleIndex = <?= count($articles ?? []) ?>;

    function addArticle() {
        const articlesList = document.getElementById('articles-list');

        const articleDiv = document.createElement('div');
        articleDiv.className = 'article-item bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600';
        articleDiv.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Articolo #${articleIndex + 1}
            </span>
            <button type="button" onclick="removeArticle(this)" 
                    class="text-red-400 hover:text-red-600 transition-colors">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <input type="text" 
                       name="articles[${articleIndex}][article_name]" 
                       placeholder="Nome/Codice articolo"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white focus:border-green-500 focus:ring-green-500">
            </div>
            <div>
                <input type="number" 
                       name="articles[${articleIndex}][total_pairs]" 
                       placeholder="Paia"
                       min="1"
                       onchange="updateCounts()"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white focus:border-green-500 focus:ring-green-500">
            </div>
        </div>
        <div class="mt-3">
            <textarea name="articles[${articleIndex}][notes]" 
                      rows="2"
                      placeholder="Note articolo (opzionale)"
                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white focus:border-green-500 focus:ring-green-500"></textarea>
        </div>
    `;

        articlesList.appendChild(articleDiv);
        articleIndex++;
        updateCounts();
    }

    function removeArticle(button) {
        button.closest('.article-item').remove();
        updateCounts();
    }

    // Aggiorna contatori
    function updateCounts() {
        const phasesCount = document.querySelectorAll('.phase-item').length;
        const articlesCount = document.querySelectorAll('.article-item').length;

        let totalPairs = 0;
        document.querySelectorAll('input[name*="total_pairs"]').forEach(input => {
            totalPairs += parseInt(input.value) || 0;
        });

        document.getElementById('phases-count').textContent = phasesCount;
        document.getElementById('articles-count').textContent = articlesCount;
        document.getElementById('total-pairs').textContent = totalPairs.toLocaleString();
    }

    // Carica fasi standard
    function loadStandardPhases() {
        if (window.CoregreModals && window.CoregreModals.confirm) {
            window.CoregreModals.confirm({
                title: 'Carica Fasi Standard',
                message: 'Vuoi caricare le fasi standard? Questo sostituirà le fasi attuali.',
                type: 'warning',
                confirmText: 'Carica',
                onConfirm: () => loadStandardPhasesConfirmed()
            });
        } else {
            // Fallback usando CoregreModals
            CoregreModals.confirm({
                message: 'Vuoi caricare le fasi standard? Questo sostituirà le fasi attuali.',
                onConfirm: () => loadStandardPhasesConfirmed()
            });
        }
    }
    function loadStandardPhasesConfirmed() {
        fetch('<?= $this->url('/scm-admin/standard-phases/load') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.phases && data.phases.length > 0) {
                    // Pulisci fasi attuali
                    document.getElementById('phases-list').innerHTML = '';

                    // Aggiungi fasi standard
                    data.phases.forEach((phase, index) => {
                        const phaseDiv = document.createElement('div');
                        phaseDiv.className = 'phase-item flex items-center space-x-3';
                        phaseDiv.innerHTML = `
                        <span class="flex-shrink-0 w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            ${index + 1}
                        </span>
                        <input type="text"
                               name="phases[]"
                               value="${phase.phase_name}"
                               class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                        <button type="button" onclick="removePhase(this)"
                                class="flex-shrink-0 p-2 text-red-400 hover:text-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                        document.getElementById('phases-list').appendChild(phaseDiv);
                    });

                    updateCounts();
                } else {
                    CoregreNotifications.error('Errore nel caricamento delle fasi standard: ' + (data.error || 'Nessuna fase trovata'));
                }
            })
            .catch(error => {
                console.error('Errore nel caricamento fasi standard:', error);
                CoregreNotifications.error('Errore nel caricamento delle fasi standard');
            });
    }


    // Inizializza contatori al caricamento
    document.addEventListener('DOMContentLoaded', function () {
        updateCounts();

        // Aggiorna contatori quando cambiano le quantità
        document.addEventListener('change', function (e) {
            if (e.target.name && e.target.name.includes('total_pairs')) {
                updateCounts();
            }
        });
    });
</script>