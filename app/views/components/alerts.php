<div id="alert-container" class="space-y-3">
    <?php
    $alertTypes = ['success', 'error', 'warning', 'info'];
    
    foreach ($alertTypes as $type) {
        $sessionKey = "alert_{$type}";
        if (isset($_SESSION[$sessionKey])) {
            $messages = is_array($_SESSION[$sessionKey]) ? $_SESSION[$sessionKey] : [$_SESSION[$sessionKey]];
            
            foreach ($messages as $message) {
                $iconMap = [
                    'success' => 'fa-check-circle',
                    'error' => 'fa-exclamation-circle',
                    'warning' => 'fa-exclamation-triangle',
                    'info' => 'fa-info-circle'
                ];
                $icon = $iconMap[$type] ?? 'fa-info-circle';
                ?>
                <div class="alert alert-<?= $type ?> animate-fade-in" role="alert">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas <?= $icon ?> w-5 h-5"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">
                                <?= htmlspecialchars($message) ?>
                            </p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button type="button" class="inline-flex rounded-md p-1.5 hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-current" 
                                    onclick="this.closest('.alert').remove()">
                                <span class="sr-only">Chiudi</span>
                                <i class="fas fa-times w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php
            }
            
            // Pulisci i messaggi dalla sessione dopo averli mostrati
            unset($_SESSION[$sessionKey]);
        }
    }
    ?>
</div>

<script>
// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentNode) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            }
        }, 5000);
    });
});

// Funzione helper per aggiungere alert dinamicamente
function addAlert(message, type = 'info') {
    const container = document.getElementById('alert-container');
    const alertId = 'alert-' + Date.now();
    
    const iconMap = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    };
    
    const alertHTML = `
        <div id="${alertId}" class="alert alert-${type} animate-fade-in" role="alert">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas ${iconMap[type] || iconMap.info} w-5 h-5"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button type="button" class="inline-flex rounded-md p-1.5 hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-current" 
                            onclick="this.closest('.alert').remove()">
                        <span class="sr-only">Chiudi</span>
                        <i class="fas fa-times w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('afterbegin', alertHTML);
    
    // Auto-hide dopo 5 secondi
    setTimeout(function() {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            alertElement.style.opacity = '0';
            alertElement.style.transform = 'translateY(-10px)';
            setTimeout(() => alertElement.remove(), 300);
        }
    }, 5000);
}
</script>