<?php
/**
 * Notification System Component
 * Sistema di notifiche universale per tutta l'applicazione
 */
?>

<!-- Notification Container -->
<div id="notification-container" class="fixed top-24 right-6 z-[9999] space-y-4 pointer-events-none"></div>

<script>
// Sistema di notifiche universale
window.CoregreNotifications = {
    /**
     * Mostra una notifica
     */
    show: function(message, type = 'info', duration = 5000) {
        const container = document.getElementById('notification-container');
        if (!container) return;
        
        const id = 'notification-' + Date.now();
        const typeConfig = {
            success: {
                bg: 'bg-gradient-to-r from-green-500 to-green-600',
                icon: 'fa-check-circle',
                border: 'border-green-200'
            },
            error: {
                bg: 'bg-gradient-to-r from-red-500 to-red-600',
                icon: 'fa-times-circle',
                border: 'border-red-200'
            },
            warning: {
                bg: 'bg-gradient-to-r from-yellow-500 to-yellow-600',
                icon: 'fa-exclamation-triangle',
                border: 'border-yellow-200'
            },
            info: {
                bg: 'bg-gradient-to-r from-blue-500 to-blue-600',
                icon: 'fa-info-circle',
                border: 'border-blue-200'
            }
        };
        
        const config = typeConfig[type] || typeConfig.info;
        
        const notification = document.createElement('div');
        notification.id = id;
        notification.className = `min-w-80 max-w-md w-full ${config.bg} shadow-2xl rounded-2xl pointer-events-auto transform transition-all duration-300 translate-x-full opacity-0 border border-white/20`;
        
        notification.innerHTML = `
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 shadow-lg">
                            <i class="fas ${config.icon} text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-base font-semibold text-white leading-relaxed break-words">${message}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <button onclick="CoregreNotifications.remove('${id}')" class="bg-white/20 rounded-xl p-2 hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 transition-colors shadow-lg hover:shadow-xl">
                            <i class="fas fa-times text-white text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
        }, 100);
        
        // Auto remove
        if (duration > 0) {
            setTimeout(() => {
                this.remove(id);
            }, duration);
        }
        
        return id;
    },

    /**
     * Rimuove una notifica
     */
    remove: function(id) {
        const notification = document.getElementById(id);
        if (notification) {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }
    },

    /**
     * Rimuove tutte le notifiche che contengono un certo testo
     */
    removeByText: function(text) {
        const notifications = document.querySelectorAll('#notification-container > div');
        notifications.forEach(n => {
            if (n.textContent.includes(text)) {
                this.remove(n.id);
            }
        });
    },

    /**
     * Shortcuts per i vari tipi
     */
    success: function(message, duration = 5000) {
        return this.show(message, 'success', duration);
    },

    error: function(message, duration = 5000) {
        return this.show(message, 'error', duration);
    },

    warning: function(message, duration = 5000) {
        return this.show(message, 'warning', duration);
    },

    info: function(message, duration = 5000) {
        return this.show(message, 'info', duration);
    },

    loading: function(message) {
        return this.show(message, 'info', 0); // No auto-hide
    }
};

// Alias per retrocompatibilità
window.showNotification = CoregreNotifications.show.bind(CoregreNotifications);
window.removeNotification = CoregreNotifications.remove.bind(CoregreNotifications);
</script>