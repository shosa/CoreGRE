<?php
/**
 * Tracking - Associazione per Cartellini
 * Replica esatta del sistema legacy orderSearch.php
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="mr-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white p-3 rounded-2xl shadow-lg">
                    <i class="fas fa-clipboard-list fa-2x"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Associazione per Cartellini
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Inserisci manualmente i cartellini per creare associazioni di tracking
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?= $this->url('/tracking') ?>" 
               class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alla Home
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
                <span class="text-gray-700 dark:text-gray-300">Associazione per Cartellini</span>
            </div>
        </li>
    </ol>
</nav>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <!-- Inserimento Cartellini -->
    <div class="xl:col-span-1">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-keyboard mr-3 text-orange-500"></i>
                    Inserimento Cartellini
                </h3>
            </div>
            <div class="p-6">
                <!-- Griglia cartellini -->
                <div id="commessa-fields" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-4">
                    <?php for ($i = 0; $i < 30; $i++): ?>
                        <div class="input-item">
                            <input type="text" 
                                   class="commessa-input w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 text-sm" 
                                   placeholder="" 
                                   onchange="verifyCommessa(this)">
                        </div>
                    <?php endfor; ?>
                </div>
                
                <!-- Pulsante aggiungi campo -->
                <div class="mb-6">
                    <button onclick="addField()" 
                            class="inline-flex items-center rounded-full bg-orange-600 p-2 text-white shadow-lg hover:bg-orange-700 transition-all duration-200">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                <!-- Form invio dati -->
                <form id="invioForm" method="post" action="<?= $this->url('/tracking/process-links') ?>">
                    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                    <input type="hidden" id="selectedCartelsInput" name="selectedCartels">
                    
                    <button type="button" id="avantiBtn" 
                            onclick="inviaDati()" 
                            disabled
                            class="w-full inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed">
                        <i class="fas fa-paper-plane mr-2"></i>
                        AVANTI
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Riepilogo Selezione -->
    <div class="xl:col-span-1">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-list-check mr-3 text-orange-500"></i>
                    Riepilogo Selezione
                </h3>
            </div>
            <div class="p-6">
                <!-- Pulsante carica riepilogo -->
                <button id="loadSummaryBtn" 
                        onclick="loadSummary()" 
                        disabled
                        class="w-full inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed mb-4">
                    <i class="fas fa-refresh mr-2"></i>
                    CARICA RIEPILOGO
                </button>

                <!-- Lista riepilogo -->
                <div id="summary-list" class="grid grid-cols-1 gap-4 mb-4"></div>
                
                <!-- Totale -->
                <div id="summary-total" class="text-right font-bold text-lg text-gray-900 dark:text-white"></div>

                <!-- Messaggi -->
                <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mt-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Impossibile generare un riepilogo, ci sono cartellini inesistenti nella griglia.
                </div>

                <div id="update-message" class="hidden bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mt-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    Rilevate modifiche, aggiorna il riepilogo.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
<?= $pageScripts ?>
</script>