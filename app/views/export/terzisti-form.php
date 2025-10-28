<?php
/**
 * Export - Form Terzisti (Nuovo/Modifica)
 * Form per la gestione terzisti
 */
?>


<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            <?= $isEdit ? 'Modifica' : 'Nuovo' ?> Terzista
        </h1>
        <nav class="flex mt-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="<?= $this->url('/') ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="<?= $this->url('/export/dashboard') ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                            Export
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="<?= $this->url('/export/terzisti') ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                            Gestione Terzisti
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400"><?= $isEdit ? 'Modifica' : 'Nuovo' ?></span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
    
    <a href="<?= $this->url('/export/terzisti') ?>" 
       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>
        Torna all'elenco
    </a>
</div>

<!-- Form Card -->
<div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-building mr-3 text-orange-500"></i>
            Dati Terzista
        </h3>
    </div>
    
    <form method="POST" action="<?= $isEdit ? $this->url('/export/terzisti/' . $terzista->id . '/update') : $this->url('/export/terzisti/store') ?>" class="p-6">
        
        <!-- Ragione Sociale (Obbligatorio) -->
        <div class="mb-6">
            <label for="ragione_sociale" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-building mr-1"></i>
                Ragione Sociale <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="ragione_sociale" 
                   name="ragione_sociale"
                   value="<?= htmlspecialchars($terzista->ragione_sociale ?? '') ?>"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                   placeholder="Nome dell'azienda"
                   required>
        </div>
        
        <!-- Sezione Indirizzo -->
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4 flex items-center">
                <i class="fas fa-map-marker-alt mr-2"></i>
                Indirizzo
            </h4>
            
            <div class="grid grid-cols-1 gap-4">
                <!-- Indirizzo 1 -->
                <div>
                    <label for="indirizzo_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Indirizzo Principale
                    </label>
                    <input type="text" 
                           id="indirizzo_1" 
                           name="indirizzo_1"
                           value="<?= htmlspecialchars($terzista->indirizzo_1 ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                           placeholder="Via, numero civico">
                </div>
                
                <!-- Indirizzo 2 -->
                <div>
                    <label for="indirizzo_2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Indirizzo Secondario
                    </label>
                    <input type="text" 
                           id="indirizzo_2" 
                           name="indirizzo_2"
                           value="<?= htmlspecialchars($terzista->indirizzo_2 ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                           placeholder="CAP, Città">
                </div>
                
                <!-- Indirizzo 3 -->
                <div>
                    <label for="indirizzo_3" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Indirizzo Aggiuntivo
                    </label>
                    <input type="text" 
                           id="indirizzo_3" 
                           name="indirizzo_3"
                           value="<?= htmlspecialchars($terzista->indirizzo_3 ?? '') ?>"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                           placeholder="Provincia, altre info">
                </div>
            </div>
        </div>
        
        <!-- Nazione e Consegna -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Nazione -->
            <div>
                <label for="nazione" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-globe mr-1"></i>
                    Nazione
                </label>
                <input type="text" 
                       id="nazione" 
                       name="nazione"
                       value="<?= htmlspecialchars($terzista->nazione ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                       placeholder="Italia">
            </div>
            
            <!-- Consegna -->
            <div>
                <label for="consegna" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-truck mr-1"></i>
                    Modalità Consegna
                </label>
                <input type="text" 
                       id="consegna" 
                       name="consegna"
                       value="<?= htmlspecialchars($terzista->consegna ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                       placeholder="Franco fabbrica, CIF, etc.">
            </div>
        </div>
        
        <!-- Autorizzazione -->
        <div class="mb-8">
            <label for="autorizzazione" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-certificate mr-1"></i>
                Autorizzazione
            </label>
            <textarea id="autorizzazione" 
                      name="autorizzazione"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                      placeholder="Numero autorizzazione o altre note legali"><?= htmlspecialchars($terzista->autorizzazione ?? '') ?></textarea>
        </div>
        
        <!-- Pulsanti di azione -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="<?= $this->url('/export/terzisti') ?>" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-times mr-2"></i>
                Annulla
            </a>
            
            <button type="submit" 
                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 shadow-md hover:shadow-lg transition-all duration-200">
                <i class="fas fa-<?= $isEdit ? 'save' : 'plus' ?> mr-2"></i>
                <?= $isEdit ? 'Aggiorna' : 'Crea' ?> Terzista
            </button>
        </div>
    </form>
</div>

<script>
(function() {
    'use strict';
    
    function initTerzistiForm() {
        const form = document.querySelector('form');
        const ragioneSocialeInput = document.getElementById('ragione_sociale');
        
        if (form && ragioneSocialeInput) {
            // Validazione lato client
            form.addEventListener('submit', function(e) {
                const ragioneSociale = ragioneSocialeInput.value.trim();
                
                if (!ragioneSociale) {
                    e.preventDefault();
                    if (window.showAlert) {
                        window.showAlert('La ragione sociale è obbligatoria', 'error');
                    } else {
                        alert('La ragione sociale è obbligatoria');
                    }
                    ragioneSocialeInput.focus();
                    return false;
                }
            });
            
            // Auto-focus sul primo campo
            ragioneSocialeInput.focus();
        }
    }
    
    // Registrazione PJAX
    if (window.WEBGRE && window.WEBGRE.onPageLoad) {
        window.WEBGRE.onPageLoad(initTerzistiForm);
    }
    
    // Fallback primo caricamento
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTerzistiForm);
    } else {
        initTerzistiForm();
    }
})();
</script>