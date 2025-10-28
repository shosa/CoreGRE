<?php
/**
 * Modal System Component
 * Sistema di modali universale per tutta l'applicazione
 */
?>

<!-- Animazioni Modali -->
<style>
    /* Animazioni modali globali */
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
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes modalFadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }

    @keyframes modalSlideOut {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(-50px);
            opacity: 0;
        }
    }

    /* Classi per animazioni modali */
    .modal-backdrop {
        animation: modalFadeIn 0.2s ease-out;
    }

    .modal-backdrop.modal-closing {
        animation: modalFadeOut 0.2s ease-out;
    }

    .modal-content {
        animation: modalSlideIn 0.3s ease-out;
    }

    .modal-content.modal-closing {
        animation: modalSlideOut 0.2s ease-out;
    }

    /* Smooth transitions */
    .modal-backdrop,
    .modal-content {
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

<script>
// Sistema di modali universale
window.CoregreModals = {
    /**
     * Apre un modale HTML statico con animazioni
     */
    openModal: function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        // Sposta il modale a livello body se non è già lì
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        // Aggiungi classi per animazioni
        modal.classList.add('modal-backdrop');
        const content = modal.children[0];
        if (content) {
            content.classList.add('modal-content');
        }

        // Mostra il modale
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        // Focus sul primo campo input
        setTimeout(() => {
            const firstInput = modal.querySelector('input:not([type="hidden"]):not([disabled]), textarea:not([disabled])');
            if (firstInput) firstInput.focus();
        }, 100);
    },

    /**
     * Chiude un modale HTML statico con animazioni
     */
    closeModal: function(modalId, callback) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        // Aggiungi classe di chiusura per animazione
        modal.classList.add('modal-closing');
        const content = modal.children[0];
        if (content) {
            content.classList.add('modal-closing');
        }

        // Attendi la fine dell'animazione prima di nascondere
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('modal-closing', 'modal-backdrop');
            if (content) {
                content.classList.remove('modal-closing', 'modal-content');
            }

            // Callback opzionale
            if (typeof callback === 'function') {
                callback();
            }
        }, 200);
    },

    /**
     * Mostra un modale di conferma dinamico
     */
    confirm: function(options) {
        const defaults = {
            title: 'Conferma',
            message: 'Sei sicuro?',
            confirmText: 'Conferma',
            cancelText: 'Annulla',
            confirmClass: 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800',
            type: 'info', // info, warning, danger
            onConfirm: null,
            onCancel: null
        };
        
        const config = Object.assign({}, defaults, options);
        
        // Configura icone e colori in base al tipo
        let iconConfig = {
            info: {
                icon: 'fa-info-circle',
                bgClass: 'bg-blue-100 dark:bg-blue-900/30',
                iconClass: 'text-blue-600 dark:text-blue-400'
            },
            warning: {
                icon: 'fa-exclamation-triangle',
                bgClass: 'bg-yellow-100 dark:bg-yellow-900/30',
                iconClass: 'text-yellow-600 dark:text-yellow-400'
            },
            danger: {
                icon: 'fa-exclamation-triangle',
                bgClass: 'bg-red-100 dark:bg-red-900/30',
                iconClass: 'text-red-600 dark:text-red-400'
            }
        };
        
        if (config.type === 'danger') {
            config.confirmClass = 'bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800';
        }
        
        const modalConfig = iconConfig[config.type] || iconConfig.info;
        const modalId = 'modal-' + Date.now();
        
        const modalHtml = `
            <div id="${modalId}" class="fixed inset-0 z-[99999] overflow-y-auto modal-backdrop" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-4 text-center">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="CoregreModals.close('${modalId}')"></div>

                    <!-- Modal panel -->
                    <div class="modal-content relative inline-block align-middle bg-white dark:bg-gray-800 rounded-2xl px-6 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all max-w-lg w-full mx-4 border border-gray-200 dark:border-gray-700">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-xl ${modalConfig.bgClass} sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas ${modalConfig.icon} ${modalConfig.iconClass} text-lg"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">
                                    ${config.title}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        ${config.message}
                                    </p>
                                    ${config.type === 'danger' ? '<p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Questa azione non può essere annullata.</p>' : ''}
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                            <button type="button" onclick="CoregreModals.handleConfirm('${modalId}')" 
                                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 ${config.confirmClass} text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200 hover:shadow-lg">
                                <i class="fas fa-check mr-2"></i>
                                ${config.confirmText}
                            </button>
                            <button type="button" onclick="CoregreModals.handleCancel('${modalId}')" 
                                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                                ${config.cancelText}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Aggiungi modal al DOM
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Salva i callback
        this._modals = this._modals || {};
        this._modals[modalId] = {
            onConfirm: config.onConfirm,
            onCancel: config.onCancel
        };

        // Le animazioni sono gestite dalle classi CSS globali modal-backdrop e modal-content

        return modalId;
    },

    /**
     * Gestisce il click di conferma
     */
    handleConfirm: function(modalId) {
        const modal = this._modals && this._modals[modalId];
        if (modal && typeof modal.onConfirm === 'function') {
            modal.onConfirm();
        }
        this.close(modalId);
    },

    /**
     * Gestisce il click di annullamento
     */
    handleCancel: function(modalId) {
        const modal = this._modals && this._modals[modalId];
        if (modal && typeof modal.onCancel === 'function') {
            modal.onCancel();
        }
        this.close(modalId);
    },

    /**
     * Chiude un modale
     */
    close: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Aggiungi classe di chiusura per animazione
            modal.classList.add('modal-closing');
            const content = modal.querySelector('.modal-content');
            if (content) {
                content.classList.add('modal-closing');
            }

            setTimeout(() => {
                if (modal.parentNode) {
                    modal.remove();
                }
                // Pulisci i callback
                if (this._modals && this._modals[modalId]) {
                    delete this._modals[modalId];
                }
            }, 200);
        }
    },

    /**
     * Shortcut per modale di eliminazione
     */
    confirmDelete: function(message, onConfirm, count = 1) {
        const title = 'Conferma Eliminazione';
        const finalMessage = message || (count === 1 
            ? 'Sei sicuro di voler eliminare questo elemento?' 
            : `Sei sicuro di voler eliminare ${count} elementi?`);
            
        return this.confirm({
            title: title,
            message: finalMessage,
            confirmText: 'Elimina',
            cancelText: 'Annulla',
            type: 'danger',
            onConfirm: onConfirm
        });
    },

    /**
     * Modale informativo
     */
    alert: function(title, message, onClose) {
        return this.confirm({
            title: title,
            message: message,
            confirmText: 'OK',
            cancelText: null,
            type: 'info',
            onConfirm: onClose
        });
    }
};
</script>