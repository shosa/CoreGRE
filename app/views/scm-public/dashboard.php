<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Header - Stile COREGRE Moderno -->
    <nav class="bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 shadow-2xl">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center">
                    <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm mr-4">
                        <i class="fas fa-industry text-white text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-white text-xl font-bold">SCM Terzisti</div>
                        <div class="text-blue-200 text-sm">Sistema Controllo Produzione</div>
                    </div>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-right">
                        <div class="text-white text-sm font-medium">
                            <i class="fas fa-building mr-2 text-blue-300"></i>
                            <?= htmlspecialchars($laboratorio_nome) ?>
                        </div>
                        <div class="text-blue-200 text-xs">Laboratorio Terzista</div>
                    </div>
                    <a href="<?= $this->url('/scm/logout') ?>"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-sm font-medium rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Esci
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 py-4">
        <?php include VIEW_PATH . '/components/alerts.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 pb-8">
        <!-- Dashboard Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                <i class="fas fa-tachometer-alt mr-2 text-blue-600"></i>
                Dashboard Produzione
            </h1>
            <p class="text-gray-600">
                Gestione lanci di produzione e monitoraggio avanzamenti
            </p>
        </div>

        <!-- Statistics Cards - Stile COREGRE Moderno -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Paia Totali -->
            <div
                class="relative overflow-hidden bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-2xl shadow-xl">
                <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                <div class="relative p-6 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                            <i class="fas fa-shoe-prints text-2xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold"><?= number_format($stats['total_paia'] ?? 0) ?></div>
                            <div class="text-blue-100 text-sm font-medium">paia totali</div>
                        </div>
                    </div>
                    <div class="text-blue-100 text-sm">Volume di produzione complessivo</div>
                </div>
            </div>

            <!-- Paia in Preparazione -->
            <div
                class="relative overflow-hidden bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 rounded-2xl shadow-xl">
                <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                <div class="relative p-6 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold"><?= number_format($stats['paia_preparazione'] ?? 0) ?></div>
                            <div class="text-amber-100 text-sm font-medium">in preparazione</div>
                        </div>
                    </div>
                    <div class="text-amber-100 text-sm">In attesa di avvio lavorazione</div>
                </div>
            </div>

            <!-- Paia in Lavorazione -->
            <div
                class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-600 to-red-600 rounded-2xl shadow-xl">
                <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                <div class="relative p-6 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                            <i class="fas fa-cogs text-2xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold"><?= number_format($stats['paia_lavorazione'] ?? 0) ?></div>
                            <div class="text-orange-100 text-sm font-medium">in lavorazione</div>
                        </div>
                    </div>
                    <div class="text-orange-100 text-sm">Attualmente in produzione</div>
                </div>
            </div>

            <!-- Paia Completati -->
            <div
                class="relative overflow-hidden bg-gradient-to-br from-emerald-500 via-green-600 to-teal-700 rounded-2xl shadow-xl">
                <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                <div class="relative p-6 text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold"><?= number_format($stats['paia_completi'] ?? 0) ?></div>
                            <div class="text-green-100 text-sm font-medium">completati</div>
                        </div>
                    </div>
                    <div class="text-green-100 text-sm">Lavorazione terminata</div>
                </div>
            </div>
        </div>

        <!-- Progress Overview -->
        <div class="bg-gradient-to-r from-slate-50 to-gray-100 rounded-2xl shadow-lg border border-gray-200 p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Avanzamento Globale</h3>
                    <p class="text-gray-600">Percentuale di completamento basata su fasi × paia</p>
                </div>
                <div class="text-right">
                    <div
                        class="text-5xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        <?= $stats['percentuale_completamento'] ?? 0 ?>%
                    </div>
                    <div class="text-gray-500 text-sm font-medium">completamento</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-4 rounded-full transition-all duration-500 shadow-lg"
                    style="width: <?= $stats['percentuale_completamento'] ?? 0 ?>%"></div>
            </div>
        </div>

        <!-- Tabs per Lanci -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <!-- Tab Headers -->
            <div class="flex border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <button id="tab-preparazione" onclick="switchTab('preparazione')"
                    class="flex-1 px-6 py-4 text-sm font-semibold text-center border-b-2 transition-all duration-200 hover:bg-gray-50
                               <?= empty($lanci_lavorazione) && !empty($lanci_preparazione) ? 'border-amber-500 text-amber-700 bg-amber-50' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
                    <i class="fas fa-clock mr-2"></i>
                    In Preparazione
                    <span
                        class="ml-2 inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                        <?= count($lanci_preparazione) ?>
                    </span>
                </button>
                <button id="tab-lavorazione" onclick="switchTab('lavorazione')"
                    class="flex-1 px-6 py-4 text-sm font-semibold text-center border-b-2 transition-all duration-200 hover:bg-gray-50
                               <?= !empty($lanci_lavorazione) ? 'border-orange-500 text-orange-700 bg-orange-50' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
                    <i class="fas fa-cogs mr-2"></i>
                    In Lavorazione
                    <span
                        class="ml-2 inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        <?= count($lanci_lavorazione) ?>
                    </span>
                </button>
                <button id="tab-completi" onclick="switchTab('completi')" class="flex-1 px-6 py-4 text-sm font-semibold text-center border-b-2 transition-all duration-200 hover:bg-gray-50
                               border-transparent text-gray-500 hover:text-gray-700">
                    <i class="fas fa-check-circle mr-2"></i>
                    Completati
                    <span
                        class="ml-2 inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <?= count($lanci_completi) ?>
                    </span>
                </button>
            </div>

            <!-- Tab Contents -->
            <!-- In Preparazione -->
            <div id="content-preparazione"
                class="tab-content <?= empty($lanci_lavorazione) && !empty($lanci_preparazione) ? '' : 'hidden' ?>">
                <?php if (!empty($lanci_preparazione)): ?>
                    <div class="p-6">
                        <div class="grid gap-4">
                            <?php foreach ($lanci_preparazione as $lancio): ?>
                                <div
                                    class="bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-xl p-6 hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-amber-400 rounded-full mr-3 animate-pulse"></div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <?= htmlspecialchars($lancio->launch_number) ?></h3>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            In Preparazione
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between mb-4">
                                        <!-- Info Compatte -->
                                        <div class="flex-1 grid grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">Data Lancio:</span>
                                                <div class="font-medium text-gray-900">
                                                    <?= date('d/m/Y', strtotime($lancio->launch_date)) ?></div>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Paia Totali:</span>
                                                <div class="font-medium text-gray-900">
                                                    <?= number_format($lancio->totale_paia) ?></div>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Articoli:</span>
                                                <div class="font-medium text-gray-900"><?= $lancio->totale_articoli ?? 0 ?>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Azione -->

                                    </div>

                                    <!-- Dettaglio Articoli -->
                                    <?php if (!empty($lancio->articoli)): ?>
                                        <div class="border-t border-amber-200 pt-4">
                                            <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                                <i class="fas fa-boxes mr-2 text-amber-600"></i>
                                                Articoli del Lancio
                                            </h4>
                                            <div class="grid gap-2">
                                                <?php foreach ($lancio->articoli as $index => $articolo): ?>
                                                    <div class="flex items-center bg-white/60 rounded-lg p-3 border border-amber-100">
                                                        <span
                                                            class="inline-flex items-center justify-center w-6 h-6 bg-amber-500 text-white text-xs font-bold rounded-full mr-3">
                                                            <?= $index + 1 ?>
                                                        </span>
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-900">
                                                                <?= htmlspecialchars($articolo['article_name']) ?></div>
                                                            <div class="text-xs text-gray-600">
                                                                <?= number_format($articolo['total_pairs']) ?> paia
                                                                <?php if ($articolo['notes']): ?>
                                                                    | <i class="fas fa-sticky-note text-amber-500"></i> Note
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fas fa-clock text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun lancio in preparazione</h3>
                        <p class="text-gray-500">Tutti i lanci sono stati avviati</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- In Lavorazione -->
            <div id="content-lavorazione" class="tab-content <?= !empty($lanci_lavorazione) ? '' : 'hidden' ?>">
                <?php if (!empty($lanci_lavorazione)): ?>
                    <div class="p-6">
                        <div class="grid gap-4">
                            <?php foreach ($lanci_lavorazione as $lancio): ?>
                                <div
                                    class="bg-gradient-to-r from-orange-50 to-red-50 border border-orange-200 rounded-xl p-6 hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-orange-500 rounded-full mr-3 animate-pulse"></div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <?= htmlspecialchars($lancio->launch_number) ?></h3>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <i class="fas fa-cogs mr-1"></i>
                                            In Lavorazione
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-6 mb-4">
                                        <!-- Info Compatte -->
                                        <div class="flex-1 grid grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">Data Lancio:</span>
                                                <div class="font-medium text-gray-900">
                                                    <?= date('d/m/Y', strtotime($lancio->launch_date)) ?></div>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Paia Totali:</span>
                                                <div class="font-medium text-gray-900">
                                                    <?= number_format($lancio->totale_paia) ?></div>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Articoli:</span>
                                                <div class="font-medium text-gray-900"><?= $lancio->totale_articoli ?? 0 ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Progress e Azione affiancati -->
                                        <div class="flex items-center gap-4">
                                            <div class="min-w-[120px]">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-xs text-gray-600">Avanzamento</span>
                                                    <span
                                                        class="text-xs font-medium text-gray-900"><?= $lancio->percentuale ?? 0 ?>%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-gradient-to-r from-orange-500 to-red-500 h-2 rounded-full transition-all duration-500"
                                                        style="width: <?= $lancio->percentuale ?? 0 ?>%"></div>
                                                </div>
                                            </div>
                                            <a href="<?= $this->url('/scm/lavora/' . $lancio->id) ?>"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-white bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 transition-all duration-200 whitespace-nowrap">
                                                <i class="fas fa-play mr-1"></i>
                                                AVANZAMENTO
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Dettaglio Articoli -->
                                    <?php if (!empty($lancio->articoli)): ?>
                                        <div class="border-t border-orange-200 pt-4">
                                            <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                                <i class="fas fa-boxes mr-2 text-orange-600"></i>
                                                Articoli del Lancio
                                            </h4>
                                            <div class="grid gap-2">
                                                <?php foreach ($lancio->articoli as $index => $articolo): ?>
                                                    <div class="flex items-center bg-white/60 rounded-lg p-3 border border-orange-100">
                                                        <span
                                                            class="inline-flex items-center justify-center w-6 h-6 bg-orange-500 text-white text-xs font-bold rounded-full mr-3">
                                                            <?= $index + 1 ?>
                                                        </span>
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-900">
                                                                <?= htmlspecialchars($articolo['article_name']) ?></div>
                                                            <div class="text-xs text-gray-600">
                                                                <?= number_format($articolo['total_pairs']) ?> paia
                                                                <?php if ($articolo['notes']): ?>
                                                                    | <i class="fas fa-sticky-note text-orange-500"></i> Note
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fas fa-cogs text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun lancio in lavorazione</h3>
                        <p class="text-gray-500">Avvia la lavorazione dei lanci in preparazione</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Completati -->
            <div id="content-completi" class="tab-content hidden">
                <?php if (!empty($lanci_completi)): ?>
                    <div class="p-6">
                        <div class="grid gap-4">
                            <?php foreach ($lanci_completi as $lancio): ?>
                                <div
                                    class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6 hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <?= htmlspecialchars($lancio->launch_number) ?></h3>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Completato
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between mb-4">
                                        <!-- Info Compatte -->
                                        <div class="flex-1 grid grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-500">Data Lancio:</span>
                                                <div class="font-medium text-gray-900">
                                                    <?= date('d/m/Y', strtotime($lancio->launch_date)) ?></div>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Paia Totali:</span>
                                                <div class="font-medium text-gray-900">
                                                    <?= number_format($lancio->totale_paia) ?></div>
                                            </div>
                                            <div>
                                                <span class="text-gray-500">Articoli:</span>
                                                <div class="font-medium text-gray-900"><?= $lancio->totale_articoli ?? 0 ?>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Azione con indicatore -->
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center text-green-600">
                                                <i class="fas fa-check-circle text-lg mr-1"></i>
                                                <span class="text-sm font-medium">100% Completato</span>
                                            </div>
                                            <a href="<?= $this->url('/scm/lavora/' . $lancio->id) ?>"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-white bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 transition-all duration-200">
                                                <i class="fas fa-eye mr-1"></i>
                                                Dettagli
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Dettaglio Articoli -->
                                    <?php if (!empty($lancio->articoli)): ?>
                                        <div class="border-t border-green-200 pt-4">
                                            <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                                <i class="fas fa-boxes mr-2 text-green-600"></i>
                                                Articoli del Lancio
                                            </h4>
                                            <div class="grid gap-2">
                                                <?php foreach ($lancio->articoli as $index => $articolo): ?>
                                                    <div class="flex items-center bg-white/60 rounded-lg p-3 border border-green-100">
                                                        <span
                                                            class="inline-flex items-center justify-center w-6 h-6 bg-green-500 text-white text-xs font-bold rounded-full mr-3">
                                                            <?= $index + 1 ?>
                                                        </span>
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-900">
                                                                <?= htmlspecialchars($articolo['article_name']) ?></div>
                                                            <div class="text-xs text-gray-600">
                                                                <span
                                                                    class="font-medium text-green-600"><?= number_format($articolo['total_pairs']) ?>
                                                                    paia completate</span>
                                                                <?php if ($articolo['notes']): ?>
                                                                    | <i class="fas fa-sticky-note text-green-500"></i> Note
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <i class="fas fa-check-circle text-green-500 text-lg"></i>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fas fa-check-circle text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nessun lancio completato</h3>
                        <p class="text-gray-500">I lanci completati appariranno qui</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Nasconde tutti i contenuti
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Rimuove gli stili attivi da tutti i tab
            document.querySelectorAll('[id^="tab-"]').forEach(tab => {
                tab.className = tab.className.replace(/border-amber-500|text-amber-700|bg-amber-50/, 'border-transparent text-gray-500 hover:text-gray-700');
                tab.className = tab.className.replace(/border-orange-500|text-orange-700|bg-orange-50/, 'border-transparent text-gray-500 hover:text-gray-700');
                tab.className = tab.className.replace(/border-green-500|text-green-700|bg-green-50/, 'border-transparent text-gray-500 hover:text-gray-700');
            });

            // Attiva il tab selezionato
            const activeTab = document.getElementById('tab-' + tabName);
            const activeContent = document.getElementById('content-' + tabName);

            if (activeTab && activeContent) {
                activeContent.classList.remove('hidden');

                // Applica gli stili corretti in base al tab
                let activeClasses = '';
                if (tabName === 'preparazione') {
                    activeClasses = 'border-amber-500 text-amber-700 bg-amber-50';
                } else if (tabName === 'lavorazione') {
                    activeClasses = 'border-orange-500 text-orange-700 bg-orange-50';
                } else if (tabName === 'completi') {
                    activeClasses = 'border-green-500 text-green-700 bg-green-50';
                }

                // Rimuove le classi inattive e aggiunge quelle attive
                activeTab.className = activeTab.className.replace('border-transparent text-gray-500 hover:text-gray-700', activeClasses);
            }
        }

        // Inizializza il tab di default
        document.addEventListener('DOMContentLoaded', function () {
            // Se ci sono lanci in lavorazione, mostra quel tab, altrimenti mostra preparazione
            <?php if (!empty($lanci_lavorazione)): ?>
                switchTab('lavorazione');
            <?php elseif (!empty($lanci_preparazione)): ?>
                switchTab('preparazione');
            <?php else: ?>
                switchTab('completi');
            <?php endif; ?>
        });
    </script>

</body>

</html>