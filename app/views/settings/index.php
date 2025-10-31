<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Include componenti -->
<?php include APP_ROOT . '/app/views/components/alerts.php'; ?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div
                    class="mr-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-r from-slate-600 to-slate-700 shadow-lg">
                    <i class="fas fa-cog text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Impostazioni Sistema
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Gestione avanzata parametri e configurazioni COREGRE
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0">
            <div class="flex items-center space-x-3">
                <div class="rounded-lg bg-green-50 dark:bg-green-900/20 px-3 py-1">
                    <span class="text-sm font-medium text-green-700 dark:text-green-300">
                        <i class="fas fa-circle text-xs text-green-500 mr-2"></i>
                        Sistema Attivo
                    </span>
                </div>
            </div>
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
                <span class="text-gray-700 dark:text-gray-300">Impostazioni</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Tabs Navigation -->
<div class="mb-8">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
            <button onclick="switchTab('import')" id="tab-import"
                class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap transition-colors">
                <i class="fas fa-file-excel mr-2"></i>
                Import Excel
            </button>
            <button onclick="switchTab('sistema')" id="tab-sistema"
                class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap transition-colors">
                <i class="fas fa-cogs mr-2"></i>
                Sistema
            </button>
            <button onclick="switchTab('notifiche')" id="tab-notifiche"
                class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap transition-colors">
                <i class="fas fa-bell mr-2"></i>
                Notifiche
            </button>
            <button onclick="switchTab('email')" id="tab-email"
                class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap transition-colors">
                <i class="fas fa-envelope mr-2"></i>
                Email & SMTP
            </button>
            <button onclick="switchTab('tabelle')" id="tab-tabelle"
                class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 whitespace-nowrap transition-colors">
                <i class="fas fa-table mr-2"></i>
                Tabelle Sistema
            </button>
        </nav>
    </div>
</div>

<!-- Tab Content -->
<div id="tab-content">

    <!-- Import Excel Tab -->
    <div id="content-import" class="tab-content hidden">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 p-8">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    <i class="fas fa-cloud-upload-alt text-blue-600 mr-2"></i>
                    Aggiornamento Cartellini da Excel
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Il cuore dell'applicazione: carica file Excel per aggiornare automaticamente i dati dei cartellini
                </p>
            </div>

            <!-- Current Data Info -->
            <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                <p class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-3">
                    <i class="fas fa-database mr-2"></i>
                    Stato Attuale Database:
                </p>
                <div class="grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                            <?= number_format($datiInfo['totalRows']) ?>
                        </div>
                        <div class="text-xs text-blue-700 dark:text-blue-300 mt-1">Record Totali</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                            <?= $datiInfo['minCartel'] ?>
                        </div>
                        <div class="text-xs text-green-700 dark:text-green-300 mt-1">Cartellino Min</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                            <?= $datiInfo['maxCartel'] ?>
                        </div>
                        <div class="text-xs text-purple-700 dark:text-purple-300 mt-1">Cartellino Max</div>
                    </div>
                </div>
            </div>

            <form id="xlsx-upload-form-main" enctype="multipart/form-data">
                <input type="file" id="xlsx-file-input-main" name="xlsx_file" accept=".xlsx,.xls" class="hidden">

                <!-- Drop Zone -->
                <div id="drop-zone-main"
                    class="relative border-2 border-dashed border-blue-300 dark:border-blue-600 rounded-2xl p-12 text-center bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 hover:from-blue-100 hover:to-indigo-100 dark:hover:from-blue-900/30 dark:hover:to-indigo-900/30 transition-all duration-300 cursor-pointer group hover:border-blue-400 dark:hover:border-blue-500">

                    <!-- Upload State -->
                    <div id="upload-state-main" class="space-y-4">
                        <div class="flex justify-center">
                            <div
                                class="w-20 h-20 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-300">
                                <i class="fas fa-cloud-upload-alt text-white text-3xl"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                Carica file Excel
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                <span class="font-medium text-blue-600 dark:text-blue-400">Clicca per selezionare</span>
                                o trascina qui il file
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                Supportati: .xlsx, .xls • Max 10MB
                            </p>
                        </div>
                    </div>

                    <!-- File Selected State -->
                    <div id="file-selected-state-main" class="hidden space-y-4">
                        <div class="flex justify-center">
                            <div
                                class="w-20 h-20 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-file-excel text-white text-3xl"></i>
                            </div>
                        </div>
                        <div>
                            <h4 id="selected-filename-main"
                                class="text-xl font-semibold text-green-700 dark:text-green-400 mb-1">
                                File selezionato
                            </h4>
                            <p id="selected-filesize-main"
                                class="text-sm text-green-600 dark:text-green-500 mb-3">
                                Dimensione file
                            </p>
                            <button type="button" onclick="clearSelectedFileMain()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-red-100 border border-red-300 rounded-lg hover:bg-red-200 hover:border-red-400 transition-colors dark:text-red-400 dark:bg-red-900/30 dark:border-red-800 dark:hover:bg-red-900/40">
                                <i class="fas fa-times mr-2"></i>
                                Rimuovi file
                            </button>
                        </div>
                    </div>

                    <!-- Drag Over State -->
                    <div id="drag-over-state-main"
                        class="hidden absolute inset-0 bg-blue-500/10 backdrop-blur-sm rounded-2xl border-2 border-blue-500 flex items-center justify-center">
                        <div class="text-center">
                            <div
                                class="w-20 h-20 bg-blue-500 rounded-2xl flex items-center justify-center shadow-xl mx-auto mb-4">
                                <i class="fas fa-download text-white text-3xl"></i>
                            </div>
                            <p class="text-xl font-semibold text-blue-700 dark:text-blue-300">
                                Rilascia il file qui
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Progress Indicator -->
                <div id="upload-progress-main" class="hidden mt-6">
                    <div class="bg-blue-100 dark:bg-blue-900/30 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Elaborazione in corso...</span>
                            <span id="progress-percentage-main" class="text-sm text-blue-600 dark:text-blue-400">0%</span>
                        </div>
                        <div class="w-full bg-blue-200 dark:bg-blue-800 rounded-full h-2">
                            <div id="progress-bar-main"
                                class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                style="width: 0%"></div>
                        </div>
                        <p id="progress-text-main" class="text-xs text-blue-600 dark:text-blue-400 mt-2">Preparazione file...</p>
                    </div>
                </div>

                <button id="upload-btn-main" type="submit"
                    class="mt-6 w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium py-4 px-6 rounded-xl transition-all shadow-lg hover:shadow-xl text-lg">
                    <i id="upload-icon-main" class="fas fa-upload mr-2"></i>
                    <span id="upload-text-main">Carica e Processa</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Sistema Tab -->
    <div id="content-sistema" class="tab-content hidden">
        <form id="sistema-settings-form">
            <input type="hidden" name="section" value="sistema">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Paginazione -->
                <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-list text-blue-600 mr-2"></i>
                        Paginazione
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Paginazione Default
                            </label>
                            <input type="number" name="pagination_default" value="<?= $settings['pagination']['default'] ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Paginazione Log
                            </label>
                            <input type="number" name="pagination_logs" value="<?= $settings['pagination']['logs'] ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Paginazione Database
                            </label>
                            <input type="number" name="pagination_database" value="<?= $settings['pagination']['database'] ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Paginazione Export
                            </label>
                            <input type="number" name="pagination_export" value="<?= $settings['pagination']['export'] ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Paginazione Treeview
                            </label>
                            <input type="number" name="pagination_treeview" value="<?= $settings['pagination']['treeview'] ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Limite Massimo Risultati
                            </label>
                            <input type="number" name="pagination_max_limit" value="<?= $settings['pagination']['max_limit'] ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Performance -->
                <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-tachometer-alt text-green-600 mr-2"></i>
                        Performance & Sistema
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Cache TTL (secondi)
                            </label>
                            <input type="number" name="cache_ttl" value="<?= $settings['system']['cache_ttl'] ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Limite Elementi Recenti
                            </label>
                            <input type="number" name="recent_items_limit" value="<?= $settings['system']['recent_items_limit'] ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Max Upload Size (MB)
                            </label>
                            <input type="number" name="max_upload_size_mb" value="<?= $settings['system']['max_upload_size_mb'] ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Session Timeout Warning (secondi)
                            </label>
                            <input type="number" name="session_timeout_warning" value="<?= $settings['system']['session_timeout_warning'] ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Percorso PHP CLI
                                <span class="text-xs text-gray-500">(lascia vuoto per auto-detect)</span>
                            </label>
                            <input type="text" name="php_cli_path" value="<?= $settings['system']['php_cli_path'] ?? '' ?>"
                                placeholder="Auto-rilevato"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-medium py-3 px-4 rounded-xl transition-all shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>
                    Salva Impostazioni Sistema
                </button>
            </div>
        </form>
    </div>

    <!-- Notifiche Tab -->
    <div id="content-notifiche" class="tab-content hidden">
        <form id="notifiche-settings-form">
            <input type="hidden" name="section" value="notifiche">

            <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-bell text-yellow-600 mr-2"></i>
                    Configurazione Notifiche & Alert
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Alert Timeout (millisecondi)
                        </label>
                        <input type="number" name="alert_timeout" value="<?= $settings['notifications']['alert_timeout'] ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Posizione Alert
                        </label>
                        <select name="alert_position"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="top-right" <?= $settings['notifications']['alert_position'] === 'top-right' ? 'selected' : '' ?>>In Alto a Destra</option>
                            <option value="top-left" <?= $settings['notifications']['alert_position'] === 'top-left' ? 'selected' : '' ?>>In Alto a Sinistra</option>
                            <option value="bottom-right" <?= $settings['notifications']['alert_position'] === 'bottom-right' ? 'selected' : '' ?>>In Basso a Destra</option>
                            <option value="bottom-left" <?= $settings['notifications']['alert_position'] === 'bottom-left' ? 'selected' : '' ?>>In Basso a Sinistra</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="enable_browser_notifications" name="enable_browser_notifications" value="true"
                            <?= $settings['notifications']['enable_browser_notifications'] ? 'checked' : '' ?>
                            class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 rounded focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="enable_browser_notifications" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                            Abilita Notifiche Browser
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="enable_sound_notifications" name="enable_sound_notifications" value="true"
                            <?= $settings['notifications']['enable_sound_notifications'] ? 'checked' : '' ?>
                            class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 rounded focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="enable_sound_notifications" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                            Abilita Suoni Notifiche
                        </label>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-yellow-600 to-yellow-700 hover:from-yellow-700 hover:to-yellow-800 text-white font-medium py-3 px-4 rounded-xl transition-all shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>
                        Salva Impostazioni Notifiche
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Email Tab -->
    <div id="content-email" class="tab-content hidden">
        <form id="email-settings-form">
            <input type="hidden" name="section" value="email">

            <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-envelope text-red-600 mr-2"></i>
                    Configurazione Email & SMTP
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Server SMTP</label>
                        <input type="text" name="production_senderSMTP" id="smtp-server"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Porta</label>
                            <input type="number" name="production_senderPORT" id="smtp-port" value="587"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sicurezza</label>
                            <select name="smtp_security"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="tls" selected>TLS</option>
                                <option value="ssl">SSL</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Mittente</label>
                        <input type="email" name="production_senderEmail" id="smtp-email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password App</label>
                        <input type="password" name="production_senderPassword" id="smtp-password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Destinatari Alert</label>
                        <textarea name="production_recipients" id="smtp-recipients" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Separa gli indirizzi con punto e virgola</p>
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium py-3 px-4 rounded-xl transition-all shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>
                        Salva Configurazione SMTP
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabelle Tab -->
    <div id="content-tabelle" class="tab-content hidden">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-table text-purple-600 mr-2"></i>
                Gestione Tabelle di Sistema
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Gestisci reparti, laboratori e linee di produzione
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="tables-content">
                <!-- Content will be loaded dynamically -->
                <div class="text-center py-8 col-span-3">
                    <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-2"></i>
                    <p class="text-gray-500 dark:text-gray-400">Caricamento...</p>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// Include inline JavaScript from settings-tabs.js
<?php include APP_ROOT . '/public/js/settings-tabs.js'; ?>
</script>
