<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Modifica Produzione
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
                        <a href="<?= $this->url('/produzione/calendar') ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                            Produzione
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="<?= $this->url('/produzione/show?month=' . urlencode($month) . '&day=' . urlencode($day)) ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                            <?= htmlspecialchars($day . ' ' . $month) ?>
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Modifica</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
    
    <a href="<?= $this->url('/produzione/show?month=' . urlencode($month) . '&day=' . urlencode($day)) ?>" 
       class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>
        Torna al dettaglio
    </a>
</div>

<!-- Main Form -->
<form id="produzioneForm" action="<?= $this->url('/produzione/update') ?>" method="POST" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    <input type="hidden" name="id" value="<?= $produzione['ID'] ?>">
    
    <!-- Date Selection (Read Only) -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
            <i class="fas fa-calendar-alt mr-3 text-blue-500"></i>
            Data Produzione
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Month (Hidden) -->
            <input type="hidden" name="month" value="<?= htmlspecialchars($month) ?>">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mese</label>
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                        <?= htmlspecialchars($month) ?>
                    </span>
                </div>
            </div>
            
            <!-- Day (Hidden) -->
            <input type="hidden" name="day" value="<?= $day ?>">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Giorno</label>
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                        <?= sprintf('%02d', $day) ?>
                    </span>
                </div>
            </div>
            
            <!-- Year (Hidden) -->
            <input type="hidden" name="year" value="<?= date('Y') ?>">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Anno</label>
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">
                        <?= date('Y') ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Montaggio Section -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
            <i class="fas fa-cogs mr-3 text-blue-500"></i>
            Montaggio
        </h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Manovia 1 -->
            <div class="space-y-4">
                <div class="space-y-2">
                    <label for="manovia1" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        MANOVIA 1
                    </label>
                    <input type="number" id="manovia1" name="manovia1" min="0" step="1" 
                           value="<?= $produzione['MANOVIA1'] ?? '' ?>"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                           onchange="calculateTotals()">
                </div>
                <div class="space-y-2">
                    <label for="note1" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Note MANOVIA 1
                    </label>
                    <textarea id="note1" name="note1" rows="3" 
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Note aggiuntive..."><?= htmlspecialchars($produzione['MANOVIA1NOTE'] ?? '') ?></textarea>
                </div>
            </div>
            
            <!-- Manovia 2 -->
            <div class="space-y-4">
                <div class="space-y-2">
                    <label for="manovia2" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        MANOVIA 2
                    </label>
                    <input type="number" id="manovia2" name="manovia2" min="0" step="1" 
                           value="<?= $produzione['MANOVIA2'] ?? '' ?>"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                           onchange="calculateTotals()">
                </div>
                <div class="space-y-2">
                    <label for="note2" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Note MANOVIA 2
                    </label>
                    <textarea id="note2" name="note2" rows="3" 
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Note aggiuntive..."><?= htmlspecialchars($produzione['MANOVIA2NOTE'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Orlatura Section -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
            <i class="fas fa-cut mr-3 text-green-500"></i>
            Orlatura
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="space-y-4">
                <div class="space-y-2">
                    <label for="orlatura<?= $i ?>" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        ORLATURA <?= $i ?>
                    </label>
                    <input type="number" id="orlatura<?= $i ?>" name="orlatura<?= $i ?>" min="0" step="1" 
                           value="<?= $produzione["ORLATURA$i"] ?? '' ?>"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500"
                           onchange="calculateTotals()">
                </div>
                <div class="space-y-2">
                    <label for="orlaturanote<?= $i ?>" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Note ORLATURA <?= $i ?>
                    </label>
                    <textarea id="orlaturanote<?= $i ?>" name="orlaturanote<?= $i ?>" rows="2" 
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500"
                              placeholder="Note..."><?= htmlspecialchars($produzione["ORLATURA{$i}NOTE"] ?? '') ?></textarea>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
    
    <!-- Taglio Section -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
            <i class="fas fa-scissors mr-3 text-orange-500"></i>
            Taglio
        </h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Taglio 1 -->
            <div class="space-y-4">
                <div class="space-y-2">
                    <label for="taglio1" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        TAGLIO 1
                    </label>
                    <input type="number" id="taglio1" name="taglio1" min="0" step="1" 
                           value="<?= $produzione['TAGLIO1'] ?? '' ?>"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500"
                           onchange="calculateTotals()">
                </div>
                <div class="space-y-2">
                    <label for="taglionote1" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Note TAGLIO 1
                    </label>
                    <textarea id="taglionote1" name="taglionote1" rows="3" 
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500"
                              placeholder="Note aggiuntive..."><?= htmlspecialchars($produzione['TAGLIO1NOTE'] ?? '') ?></textarea>
                </div>
            </div>
            
            <!-- Taglio 2 -->
            <div class="space-y-4">
                <div class="space-y-2">
                    <label for="taglio2" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        TAGLIO 2
                    </label>
                    <input type="number" id="taglio2" name="taglio2" min="0" step="1" 
                           value="<?= $produzione['TAGLIO2'] ?? '' ?>"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500"
                           onchange="calculateTotals()">
                </div>
                <div class="space-y-2">
                    <label for="taglionote2" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Note TAGLIO 2
                    </label>
                    <textarea id="taglionote2" name="taglionote2" rows="3" 
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500"
                              placeholder="Note aggiuntive..."><?= htmlspecialchars($produzione['TAGLIO2NOTE'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Totals Display -->
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 p-6 shadow-lg dark:border-gray-800 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
            <i class="fas fa-calculator mr-3 text-purple-500"></i>
            Totali Calcolati
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Montaggio</div>
                <div id="totalMontaggio" class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    <?= ($produzione['MANOVIA1'] ?? 0) + ($produzione['MANOVIA2'] ?? 0) ?>
                </div>
            </div>
            
            <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Orlatura</div>
                <div id="totalOrlatura" class="text-2xl font-bold text-green-600 dark:text-green-400">
                    <?= ($produzione['ORLATURA1'] ?? 0) + ($produzione['ORLATURA2'] ?? 0) + ($produzione['ORLATURA3'] ?? 0) + ($produzione['ORLATURA4'] ?? 0) + ($produzione['ORLATURA5'] ?? 0) ?>
                </div>
            </div>
            
            <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Taglio</div>
                <div id="totalTaglio" class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                    <?= ($produzione['TAGLIO1'] ?? 0) + ($produzione['TAGLIO2'] ?? 0) ?>
                </div>
            </div>
            
            <div class="text-center p-4 bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-800/50 dark:to-pink-800/50 rounded-xl shadow-sm border-2 border-dashed border-purple-200 dark:border-purple-600">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Totale</div>
                <div id="totalGenerale" class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                    <?= ($produzione['TOTALIMONTAGGIO'] ?? 0) + ($produzione['TOTALIORLATURA'] ?? 0) + ($produzione['TOTALITAGLIO'] ?? 0) ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Form Actions -->
    <div class="flex items-center justify-end space-x-4 pt-6">
        <a href="<?= $this->url('/produzione/show?month=' . urlencode($month) . '&day=' . urlencode($day)) ?>" 
           class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-times mr-2"></i>
            Annulla
        </a>
        
        <button type="submit" 
                class="inline-flex items-center px-6 py-3 rounded-lg border border-transparent bg-gradient-to-r from-blue-500 to-blue-600 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
            <i class="fas fa-save mr-2"></i>
            Aggiorna Produzione
        </button>
    </div>
    
</form>

<script>
// Calcola i totali in tempo reale
function calculateTotals() {
    // Totale Montaggio
    const manovia1 = parseInt(document.getElementById('manovia1').value) || 0;
    const manovia2 = parseInt(document.getElementById('manovia2').value) || 0;
    const totalMontaggio = manovia1 + manovia2;
    document.getElementById('totalMontaggio').textContent = totalMontaggio;
    
    // Totale Orlatura
    let totalOrlatura = 0;
    for (let i = 1; i <= 5; i++) {
        const orlatura = parseInt(document.getElementById(`orlatura${i}`).value) || 0;
        totalOrlatura += orlatura;
    }
    document.getElementById('totalOrlatura').textContent = totalOrlatura;
    
    // Totale Taglio
    const taglio1 = parseInt(document.getElementById('taglio1').value) || 0;
    const taglio2 = parseInt(document.getElementById('taglio2').value) || 0;
    const totalTaglio = taglio1 + taglio2;
    document.getElementById('totalTaglio').textContent = totalTaglio;
    
    // Totale Generale
    const totalGenerale = totalMontaggio + totalOrlatura + totalTaglio;
    document.getElementById('totalGenerale').textContent = totalGenerale;
}

// Validazione form
document.getElementById('produzioneForm').addEventListener('submit', function(e) {
    // Verifica che almeno un campo numerico sia compilato
    const numericFields = ['manovia1', 'manovia2', 'orlatura1', 'orlatura2', 'orlatura3', 'orlatura4', 'orlatura5', 'taglio1', 'taglio2'];
    let hasValue = false;
    
    for (const field of numericFields) {
        if (parseInt(document.getElementById(field).value) > 0) {
            hasValue = true;
            break;
        }
    }
    
    if (!hasValue) {
        e.preventDefault();
        showAlert('Per favore inserisci almeno un valore di produzione', 'warning');
        return;
    }
    
    // Conferma aggiornamento con modal personalizzato
    e.preventDefault();
    
    if (window.WebgreModals) {
        window.WebgreModals.confirm({
            title: 'Conferma Aggiornamento',
            message: 'Sei sicuro di voler aggiornare questi dati di produzione?',
            confirmText: 'Aggiorna',
            cancelText: 'Annulla',
            type: 'warning',
            onConfirm: function() {
                // Submit il form
                e.target.submit();
            }
        });
    } else {
        // Fallback se modals non disponibili
        if (confirm('Sei sicuro di voler aggiornare questi dati di produzione?')) {
            e.target.submit();
        }
    }
});

// Inizializzazione
document.addEventListener('DOMContentLoaded', function() {
    // I totali sono già calcolati server-side, ma aggiorniamo per coerenza
    calculateTotals();
});
</script>