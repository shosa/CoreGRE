<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-6 py-4">
        <!-- Mobile menu button -->
        <button id="mobile-menu-toggle" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
            <span class="sr-only">Apri menu</span>
            <i class="fas fa-bars w-6 h-6"></i>
        </button>
        
        <!-- Search bar -->
        <div class="flex-1 max-w-lg mx-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search w-5 h-5 text-gray-400"></i>
                </div>
                <input type="search" 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                       placeholder="Cerca cartellini, articoli..."
                       id="global-search">
            </div>
        </div>
        
        <!-- Right side items -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative">
                <button class="p-2 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        id="notifications-button"
                        onclick="toggleNotifications()">
                    <span class="sr-only">Visualizza notifiche</span>
                    <i class="fas fa-bell w-6 h-6"></i>
                    <span id="notification-badge" class="hidden absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                        3
                    </span>
                </button>
                
                <!-- Notifications dropdown -->
                <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-sm font-medium text-gray-900">Notifiche</h3>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle w-5 h-5 text-yellow-500"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">Riparazione scaduta</p>
                                    <p class="text-sm text-gray-500">Cartellino #12345 - Cliente XYZ</p>
                                    <p class="text-xs text-gray-400 mt-1">2 ore fa</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 text-center text-sm text-gray-500">
                            <a href="<?= $this->url('/notifications') ?>" class="text-blue-600 hover:text-blue-500">
                                Visualizza tutte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick actions -->
            <div class="relative">
                <button class="p-2 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        onclick="toggleQuickActions()">
                    <i class="fas fa-plus w-6 h-6"></i>
                </button>
                
                <!-- Quick actions dropdown -->
                <div id="quick-actions-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1">
                        <?php if ($this->hasPermission('riparazioni')): ?>
                        <a href="<?= $this->url('/riparazioni/create') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-hammer w-4 h-4 mr-2"></i>
                            Nuova Riparazione
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($this->hasPermission('cq')): ?>
                        <a href="<?= $this->url('/quality/new') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-check-circle w-4 h-4 mr-2"></i>
                            Controllo Qualità
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($this->hasPermission('produzione')): ?>
                        <a href="<?= $this->url('/produzione/new') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-industry w-4 h-4 mr-2"></i>
                            Nuova Produzione
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- User menu -->
            <div class="relative">
                <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        id="user-menu-button"
                        onclick="toggleUserMenu()">
                    <span class="sr-only">Apri menu utente</span>
                    <div class="h-8 w-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-white">
                            <?= strtoupper(substr($_SESSION['nome'] ?? 'U', 0, 1)) ?>
                        </span>
                    </div>
                    <span class="hidden md:ml-3 md:block text-sm font-medium text-gray-700">
                        <?= $_SESSION['nome'] ?? 'Utente' ?>
                    </span>
                    <i class="hidden md:ml-1 md:block fas fa-chevron-down w-4 h-4 text-gray-400"></i>
                </button>
                
                <!-- User dropdown -->
                <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <p class="text-sm font-medium text-gray-900"><?= $_SESSION['nome'] ?? 'Utente' ?></p>
                        <p class="text-sm text-gray-500"><?= $_SESSION['mail'] ?? '' ?></p>
                    </div>
                    <div class="py-1">
                        <a href="<?= $this->url('/profile') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user w-4 h-4 mr-2"></i>
                            Profilo
                        </a>
                        <a href="<?= $this->url('/settings') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog w-4 h-4 mr-2"></i>
                            Impostazioni
                        </a>
                        <div class="border-t border-gray-200"></div>
                        <a href="<?= $this->url('/auth/logout') ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt w-4 h-4 mr-2"></i>
                            Esci
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
// Toggle functions per i dropdown
function toggleNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    dropdown.classList.toggle('hidden');
    
    // Chiudi altri dropdown
    document.getElementById('quick-actions-dropdown').classList.add('hidden');
    document.getElementById('user-menu-dropdown').classList.add('hidden');
}

function toggleQuickActions() {
    const dropdown = document.getElementById('quick-actions-dropdown');
    dropdown.classList.toggle('hidden');
    
    // Chiudi altri dropdown
    document.getElementById('notifications-dropdown').classList.add('hidden');
    document.getElementById('user-menu-dropdown').classList.add('hidden');
}

function toggleUserMenu() {
    const dropdown = document.getElementById('user-menu-dropdown');
    dropdown.classList.toggle('hidden');
    
    // Chiudi altri dropdown
    document.getElementById('notifications-dropdown').classList.add('hidden');
    document.getElementById('quick-actions-dropdown').classList.add('hidden');
}

// Chiudi dropdown quando si clicca fuori
document.addEventListener('click', function(event) {
    const isNotificationButton = event.target.closest('#notifications-button');
    const isQuickActionButton = event.target.closest('[onclick="toggleQuickActions()"]');
    const isUserMenuButton = event.target.closest('#user-menu-button');
    
    if (!isNotificationButton) {
        document.getElementById('notifications-dropdown').classList.add('hidden');
    }
    if (!isQuickActionButton) {
        document.getElementById('quick-actions-dropdown').classList.add('hidden');
    }
    if (!isUserMenuButton) {
        document.getElementById('user-menu-dropdown').classList.add('hidden');
    }
});

// Global search functionality
document.getElementById('global-search').addEventListener('input', function(e) {
    const query = e.target.value;
    if (query.length > 2) {
        // Implementa la ricerca globale
        console.log('Searching for:', query);
    }
});

// Mobile menu toggle
document.getElementById('mobile-menu-toggle')?.addEventListener('click', function() {
    toggleSidebar();
});
</script>