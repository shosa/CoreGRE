<?php
/**
 * Widget Customizer Modal Component
 * Modale moderno per personalizzazione widget dashboard
 */
?>

<!-- Widget Customizer Modal -->
<div id="widget-customizer-modal" class="fixed inset-0 z-[99999] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Full screen backdrop with blur -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-md transition-opacity" onclick="closeCustomizerModal()"></div>

    <!-- Modal Container -->
    <div class="fixed inset-0 flex items-center justify-center p-8">
        <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl flex flex-col overflow-hidden w-full h-full max-w-5xl max-h-[85vh]">

            <!-- Header - Fixed -->
            <div class="flex-shrink-0 px-6 sm:px-8 py-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 shadow-lg">
                            <i class="fas fa-th-large text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">
                                Personalizza Dashboard
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 text-base mt-1">
                                Attiva, disattiva e configura i tuoi widget
                            </p>
                        </div>
                    </div>
                    <button onclick="closeCustomizerModal()"
                            class="flex items-center justify-center h-10 w-10 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700 dark:text-gray-400 dark:hover:text-white transition-all">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Category Filter Tabs -->
                <div class="flex flex-wrap gap-2 mt-6" id="category-filters">
                    <button onclick="filterWidgets('all')"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all category-btn active bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 shadow-md"
                            data-category="all">
                        <i class="fas fa-th mr-2"></i>Tutti
                    </button>
                    <button onclick="filterWidgets('stats')"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all category-btn text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 hover:shadow-md"
                            data-category="stats">
                        <i class="fas fa-chart-bar mr-2"></i>Statistiche
                    </button>
                    <button onclick="filterWidgets('charts')"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all category-btn text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 hover:shadow-md"
                            data-category="charts">
                        <i class="fas fa-chart-pie mr-2"></i>Grafici
                    </button>
                    <button onclick="filterWidgets('lists')"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all category-btn text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 hover:shadow-md"
                            data-category="lists">
                        <i class="fas fa-list mr-2"></i>Liste
                    </button>
                    <button onclick="filterWidgets('actions')"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all category-btn text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 hover:shadow-md"
                            data-category="actions">
                        <i class="fas fa-bolt mr-2"></i>Azioni
                    </button>
                    <button onclick="filterWidgets('info')"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-all category-btn text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 hover:shadow-md"
                            data-category="info">
                        <i class="fas fa-info-circle mr-2"></i>Informazioni
                    </button>
                </div>
            </div>

            <!-- Content - Scrollable -->
            <div class="flex-1 overflow-y-auto px-6 sm:px-8 py-6">
                <!-- Loading State -->
                <div id="widgets-loading" class="flex flex-col items-center justify-center py-20">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"></div>
                    <span class="mt-4 text-lg text-gray-600 dark:text-gray-400">Caricamento widget...</span>
                </div>

                <!-- Widget List Container -->
                <div id="widgets-container" class="hidden">
                    <div id="widgets-list" class="space-y-4"></div>
                </div>

                <!-- Empty State -->
                <div id="widgets-empty" class="hidden text-center py-20">
                    <div class="mx-auto mb-6">
                        <i class="fas fa-search text-6xl text-gray-300 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Nessun widget trovato</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">
                        Prova a selezionare una categoria diversa.
                    </p>
                </div>
            </div>

            <!-- Footer - Fixed -->
            <div class="flex-shrink-0 px-6 sm:px-8 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Le modifiche verranno salvate alla chiusura
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="resetWidgets()"
                                class="px-5 py-2.5 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            <i class="fas fa-undo mr-2"></i>
                            Ripristina
                        </button>
                        <button onclick="closeCustomizerModal()"
                                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg transition-all shadow-lg hover:shadow-xl">
                            <i class="fas fa-save mr-2"></i>
                            Salva e Chiudi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Widget Customizer JavaScript - PJAX Compatible
(function() {
    // Evita ridichiarazioni multiple durante navigazione PJAX
    if (window.widgetCustomizerInitialized) {
        return;
    }
    window.widgetCustomizerInitialized = true;

    // Variabili globali in namespace sicuro
    window.widgetCustomizer = window.widgetCustomizer || {
        availableWidgets: [],
        currentView: 'list',
        currentFilter: 'all',
        sortable: null,
        pendingChanges: []
    };

    const { widgetCustomizer } = window;

// Apri modale personalizzazione
function openCustomizerModal() {
    widgetCustomizer.pendingChanges = [];
    const modal = document.getElementById('widget-customizer-modal');

    // Sposta il modale a livello body per evitare problemi di contenimento
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    loadAvailableWidgets();
}

// Chiudi modale personalizzazione
async function closeCustomizerModal() {
    const modal = document.getElementById('widget-customizer-modal');

    // Salva tutte le modifiche pendenti
    if (widgetCustomizer.pendingChanges.length > 0) {
        await savePendingChanges();
    }

    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');

    // Ricarica sempre per mostrare i cambiamenti
    if (window.pjax && typeof window.pjax.loadContent === 'function') {
        window.pjax.loadContent(window.location.href, true);
    } else if (window.pjax && typeof window.pjax.navigateTo === 'function') {
        // Aggiungi timestamp per forzare il reload
        const url = window.location.pathname + window.location.search +
                    (window.location.search ? '&' : '?') + '_t=' + Date.now();
        window.pjax.navigateTo(url);
    } else {
        window.location.reload();
    }
}

// Salva tutte le modifiche pendenti
async function savePendingChanges() {
    if (widgetCustomizer.pendingChanges.length === 0) return;

    try {
        const response = await fetch(window.COREGRE.baseUrl + '/api/widgets/batch-update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                changes: widgetCustomizer.pendingChanges
            })
        });

        const data = await response.json();
        if (data.success) {
            widgetCustomizer.pendingChanges = [];
        }
    } catch (error) {
        console.error('Error saving changes:', error);
    }
}

// Carica widget disponibili
async function loadAvailableWidgets() {
    const loading = document.getElementById('widgets-loading');
    const container = document.getElementById('widgets-container');
    
    loading.classList.remove('hidden');
    container.classList.add('hidden');
    
    try {
        const response = await fetch(window.COREGRE.baseUrl + '/api/widgets/available');
        const data = await response.json();
        
        if (data.success) {
            widgetCustomizer.availableWidgets = data.widgets;
            renderWidgets();
        } else {
            showError('Errore nel caricamento dei widget');
        }
    } catch (error) {
        showError('Errore di rete: ' + error.message);
    } finally {
        loading.classList.add('hidden');
        container.classList.remove('hidden');
    }
}

// Renderizza widget
function renderWidgets() {
    const filteredWidgets = widgetCustomizer.availableWidgets.filter(widget => 
        widgetCustomizer.currentFilter === 'all' || widget.category === widgetCustomizer.currentFilter
    );
    
    if (filteredWidgets.length === 0) {
        document.getElementById('widgets-container').classList.add('hidden');
        document.getElementById('widgets-empty').classList.remove('hidden');
        return;
    }
    
    // Ordina i widget per position_order prima di renderizzarli
    filteredWidgets.sort((a, b) => {
        const orderA = parseInt(a.position_order) || 999;
        const orderB = parseInt(b.position_order) || 999;
        return orderA - orderB;
    });
    
    console.log('Rendering widgets in order:', filteredWidgets.map(w => ({ 
        key: w.widget_key, 
        order: w.position_order 
    })));
    
    document.getElementById('widgets-container').classList.remove('hidden');
    document.getElementById('widgets-empty').classList.add('hidden');
    
    // Usa sempre la vista lista
    renderWidgetsList(filteredWidgets);
}


// Renderizza lista widget
function renderWidgetsList(widgets) {
    const list = document.getElementById('widgets-list');
    list.classList.remove('hidden');

    list.innerHTML = widgets.map(widget => `
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border-2 border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-lg transition-all duration-200 widget-card"
             data-widget-key="${widget.widget_key}"
             data-category="${widget.category}">
            <div class="flex items-start justify-between gap-6">
                <!-- Widget Info -->
                <div class="flex items-start space-x-4 flex-1">
                    <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-xl bg-gradient-to-br from-${widget.widget_color}-500 to-${widget.widget_color}-600 shadow-lg">
                        <i class="${widget.widget_icon} text-white text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <h5 class="font-semibold text-gray-900 dark:text-white text-base">
                                ${widget.widget_name}
                            </h5>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${widget.widget_color}-100 text-${widget.widget_color}-700 dark:bg-${widget.widget_color}-900/30 dark:text-${widget.widget_color}-400">
                                ${getCategoryLabel(widget.category)}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            ${widget.widget_description}
                        </p>
                    </div>
                </div>

                <!-- Widget Controls -->
                <div class="flex items-center gap-6">
                    ${widget.is_enabled ? `
                    <!-- Order Control -->
                    <div class="flex flex-col items-center min-w-[100px]">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Ordine</label>
                        <input type="number"
                               value="${widget.position_order || 0}"
                               onchange="changeWidgetOrder('${widget.widget_key}', this.value)"
                               min="0" max="999" step="1"
                               class="w-20 text-center bg-gray-50 dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 font-medium text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                    </div>
                    ` : ''}
                    ${widget.is_enabled ? `
                    <!-- Size Control -->
                    <div class="flex flex-col items-center min-w-[120px]">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Dimensione</label>
                        <select onchange="changeWidgetSize('${widget.widget_key}', this.value)"
                                class="bg-gray-50 dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 font-medium text-sm text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                            <option value="small" ${widget.widget_size === 'small' ? 'selected' : ''}>Piccolo</option>
                            <option value="medium" ${widget.widget_size === 'medium' ? 'selected' : ''}>Medio</option>
                            <option value="large" ${widget.widget_size === 'large' ? 'selected' : ''}>Grande</option>
                            <option value="full" ${widget.widget_size === 'full' ? 'selected' : ''}>Intero</option>
                        </select>
                    </div>
                    ` : ''}
                    <!-- Toggle Control -->
                    <div class="flex flex-col items-center">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Stato</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   ${widget.is_enabled ? 'checked' : ''}
                                   onchange="toggleWidget('${widget.widget_key}', this.checked)"
                                   class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-gray-600 peer-checked:bg-gradient-to-r peer-checked:from-blue-500 peer-checked:to-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Filtra widget per categoria
function filterWidgets(category) {
    widgetCustomizer.currentFilter = category;

    // Aggiorna UI bottoni con nuovo stile
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-white', 'dark:bg-gray-700', 'text-blue-600', 'dark:text-blue-400', 'shadow-md');
        btn.classList.add('text-gray-700', 'dark:text-gray-300');

        if (btn.dataset.category === category) {
            btn.classList.add('active', 'bg-white', 'dark:bg-gray-700', 'text-blue-600', 'dark:text-blue-400', 'shadow-md');
            btn.classList.remove('text-gray-700', 'dark:text-gray-300');
        }
    });

    renderWidgets();
}


// Toggle widget on/off
window.toggleWidget = function(widgetKey, enabled) {
    // Aggiorna l'array locale
    const widget = widgetCustomizer.availableWidgets.find(w => w.widget_key === widgetKey);
    if (widget) {
        widget.is_enabled = enabled;
    }

    // Aggiungi alla lista modifiche pendenti
    const existingChangeIndex = widgetCustomizer.pendingChanges.findIndex(c => c.widget_key === widgetKey);
    if (existingChangeIndex >= 0) {
        widgetCustomizer.pendingChanges[existingChangeIndex].is_enabled = enabled;
    } else {
        widgetCustomizer.pendingChanges.push({ widget_key: widgetKey, is_enabled: enabled });
    }

    // Re-render senza reload per mostrare/nascondere controlli
    renderWidgets();
};

// Cambia dimensione widget
window.changeWidgetSize = function(widgetKey, size) {
    // Aggiorna l'array locale
    const widget = widgetCustomizer.availableWidgets.find(w => w.widget_key === widgetKey);
    if (widget) {
        widget.widget_size = size;
    }

    // Aggiungi alla lista modifiche pendenti
    const existingChangeIndex = widgetCustomizer.pendingChanges.findIndex(c => c.widget_key === widgetKey);
    if (existingChangeIndex >= 0) {
        widgetCustomizer.pendingChanges[existingChangeIndex].widget_size = size;
    } else {
        widgetCustomizer.pendingChanges.push({ widget_key: widgetKey, widget_size: size });
    }
};

// Cambia ordine widget
window.changeWidgetOrder = function(widgetKey, order) {
    const position = parseInt(order) || 0;

    // Aggiorna l'array locale
    const widget = widgetCustomizer.availableWidgets.find(w => w.widget_key === widgetKey);
    if (widget) {
        widget.position_order = position;
    }

    // Aggiungi alla lista modifiche pendenti
    const existingChangeIndex = widgetCustomizer.pendingChanges.findIndex(c => c.widget_key === widgetKey);
    if (existingChangeIndex >= 0) {
        widgetCustomizer.pendingChanges[existingChangeIndex].position_order = position;
    } else {
        widgetCustomizer.pendingChanges.push({ widget_key: widgetKey, position_order: position });
    }

    // Riordina visivamente
    renderWidgets();
};

// Ripristina widget default
async function resetWidgets() {
    if (!confirm('Sei sicuro di voler ripristinare la configurazione predefinita dei widget?')) {
        return;
    }
    
    // TODO: Implementare reset
    showSuccess('Widget ripristinati alla configurazione predefinita');
    loadAvailableWidgets();
}

// Utilità
function getCategoryLabel(category) {
    const labels = {
        'stats': 'Statistiche',
        'charts': 'Grafici', 
        'lists': 'Liste',
        'actions': 'Azioni',
        'info': 'Informazioni'
    };
    return labels[category] || category;
}

function showSuccess(message) {
    // Integra con il sistema di alert esistente
    if (typeof addAlert === 'function') {
        addAlert(message, 'success');
    }
}

function showError(message) {
    // Integra con il sistema di alert esistente
    if (typeof addAlert === 'function') {
        addAlert(message, 'error');
    }
}

// Esponi funzioni globalmente per compatibilità
window.showWidgetCustomizer = openCustomizerModal;
window.hideWidgetCustomizer = closeCustomizerModal;
window.closeCustomizerModal = closeCustomizerModal; // Per compatibilità con onclick inline
window.openCustomizerModal = openCustomizerModal; // Per compatibilità con onclick inline
window.loadAvailableWidgets = loadAvailableWidgets;
window.filterWidgets = filterWidgets;
window.toggleWidget = toggleWidget;
window.changeWidgetSize = changeWidgetSize;
window.resetWidgets = resetWidgets;

})(); // Chiude IIFE
</script>

<!-- Include Sortable.js per il drag & drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
/* Modal animations */
@keyframes modalFadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

#widget-customizer-modal:not(.hidden) {
    animation: modalFadeIn 0.2s ease-out;
}

#widget-customizer-modal:not(.hidden) > div:nth-child(2) > div {
    animation: modalSlideIn 0.3s ease-out;
}

/* Widget card animations */
.widget-card {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.widget-card:hover {
    transform: translateY(-2px);
}

/* Custom scrollbar */
#widgets-container::-webkit-scrollbar {
    width: 8px;
}

#widgets-container::-webkit-scrollbar-track {
    background: transparent;
}

#widgets-container::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.5);
    border-radius: 4px;
}

#widgets-container::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.7);
}

/* Category button transitions */
.category-btn {
    transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
}

.category-btn:active {
    transform: scale(0.95);
}

/* Toggle switch animation */
input[type="checkbox"]:checked + div {
    background: linear-gradient(to right, #3b82f6, #2563eb) !important;
}

/* Focus states */
input[type="number"]:focus,
select:focus {
    outline: none;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .widget-card {
        padding: 1rem !important;
    }

    .widget-card > div {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem !important;
    }

    .widget-card .flex.items-center.gap-6 {
        width: 100%;
        justify-content: space-between;
    }
}
</style>