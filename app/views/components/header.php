<header
    class="sticky top-0 z-99999 flex w-full border-gray-200/50 bg-white/95 lg:border-b dark:border-gray-800/50 dark:bg-gray-900/95 backdrop-blur-sm shadow-lg">
    <meta name="robots" content="noindex, nofollow">
    <div class="flex grow flex-col items-center justify-between lg:flex-row lg:px-6">
        <div
            class="flex w-full items-center justify-between gap-2 border-b border-gray-200 px-3 py-3 sm:gap-4 lg:border-b-0 lg:px-0 lg:py-4 dark:border-gray-800">

            <!-- Hamburger Toggle BTN -->
            <button
                :class="sidebarToggle ? 'lg:bg-transparent dark:lg:bg-transparent bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : 'hover:bg-blue-50 dark:hover:bg-blue-900/20'"
                class="z-88888 flex h-11 w-11 items-center justify-center rounded-xl border border-gray-200/50 text-gray-500 dark:border-gray-800/50 dark:text-gray-400 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 backdrop-blur-sm"
                @click.stop="sidebarToggle = !sidebarToggle; ">
                <svg class="fill-current transition-transform duration-200" :class="sidebarToggle ? 'rotate-90' : ''"
                    width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M0.583252 1C0.583252 0.585788 0.919038 0.25 1.33325 0.25H14.6666C15.0808 0.25 15.4166 0.585786 15.4166 1C15.4166 1.41421 15.0808 1.75 14.6666 1.75L1.33325 1.75C0.919038 1.75 0.583252 1.41422 0.583252 1ZM0.583252 11C0.583252 10.5858 0.919038 10.25 1.33325 10.25L14.6666 10.25C15.0808 10.25 15.4166 10.5858 15.4166 11C15.4166 11.4142 15.0808 11.75 14.6666 11.75L1.33325 11.75C0.919038 11.75 0.583252 11.4142 0.583252 11ZM1.33325 5.25C0.919038 5.25 0.583252 5.58579 0.583252 6C0.583252 6.41421 0.919038 6.75 1.33325 6.75L7.99992 6.75C8.41413 6.75 8.74992 6.41421 8.74992 6C8.74992 5.58579 8.41413 5.25 7.99992 5.25L1.33325 5.25Z"
                        fill="" />
                </svg>
            </button>

            <!-- Search Form Mobile Toggle -->
            <div class="flex items-center gap-3 lg:hidden">
                <button
                    class="flex h-11 w-11 items-center justify-center rounded-xl border border-gray-200/50 bg-white/80 text-gray-500 dark:border-gray-800/50 dark:bg-gray-800/80 dark:text-gray-400 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 hover:bg-blue-50 dark:hover:bg-blue-900/20 backdrop-blur-sm"
                    @click="searchToggle = !searchToggle">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <!-- Search Bar Desktop -->
            <div class="hidden lg:block flex-1 max-w-lg mx-6">
                <div class="relative">
                    <div class="relative w-full">
                        <button class="absolute left-4 top-1/2 -translate-y-1/2 z-10" type="button">
                            <div
                                class="flex h-5 w-5 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 shadow-sm">
                                <i class="fas fa-search text-xs text-white"></i>
                            </div>
                        </button>
                        <input
                            type="text"
                            placeholder="Cerca cartellini, articoli, utenti..."
                            class="w-full bg-gray-50/80 dark:bg-gray-800/50 pl-12 pr-4 py-3 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none rounded-xl border border-gray-200/50 dark:border-gray-700/50 shadow-md hover:shadow-lg focus:shadow-lg focus:bg-white dark:focus:bg-gray-800 transition-all duration-200 backdrop-blur-sm"
                        />
                    </div>

                    <!-- Search Results Dropdown -->
                    <div
                        class="absolute top-full mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 max-h-96 overflow-y-auto"
                        style="display: none;"
                    >
                        <!-- Content will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Right Side Items -->
            <div class="flex items-center gap-3 ml-auto">
                <!-- Quick Actions -->
                <div class="relative hidden md:block" x-data="{dropdownOpen: false}">
                    <button @click="dropdownOpen = !dropdownOpen"
                        class="relative flex h-11 w-11 items-center justify-center rounded-xl border border-gray-200/50 bg-white/80 text-gray-500 hover:bg-gradient-to-r hover:from-orange-50 hover:to-orange-100 hover:text-orange-600 hover:border-orange-200 dark:border-gray-800/50 dark:bg-gray-800/80 dark:text-gray-400 dark:hover:from-orange-900/20 dark:hover:to-orange-800/20 dark:hover:text-orange-400 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 backdrop-blur-sm">
                        <i class="fas fa-plus"></i>
                    </button>

                    <!-- Quick Actions Dropdown -->
                    <div x-show="dropdownOpen" @click.outside="dropdownOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-3 w-56 rounded-2xl border border-gray-200/50 bg-white/95 shadow-xl dark:border-gray-800/50 dark:bg-gray-800/95 backdrop-blur-sm"
                        style="display: none;">
                        <div class="p-3">
                            <div class="mb-3 px-3 py-2">
                                <h6 class="text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                    Azioni Rapide</h6>
                            </div>

                            <div class="space-y-1">
                                <?php if ($this->hasPermission('riparazioni')): ?>
                                    <a href="<?= $this->url('/riparazioni/create') ?>"
                                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 hover:text-blue-600 dark:text-gray-300 dark:hover:from-blue-900/20 dark:hover:to-blue-800/20 dark:hover:text-blue-400 transition-all duration-200">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 shadow-md">
                                            <i class="fas fa-hammer text-xs text-white"></i>
                                        </div>
                                        <span>Nuova Riparazione</span>
                                    </a>
                                <?php endif; ?>

                                <?php if ($this->hasPermission('cq')): ?>
                                    <a href="<?= $this->url('/quality/new') ?>"
                                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-100 hover:text-green-600 dark:text-gray-300 dark:hover:from-green-900/20 dark:hover:to-emerald-800/20 dark:hover:text-green-400 transition-all duration-200">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 shadow-md">
                                            <i class="fas fa-check-circle text-xs text-white"></i>
                                        </div>
                                        <span>Controllo Qualità</span>
                                    </a>
                                <?php endif; ?>

                                <?php if ($this->hasPermission('produzione')): ?>
                                    <a href="<?= $this->url('/produzione/new') ?>"
                                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gradient-to-r hover:from-yellow-50 hover:to-orange-100 hover:text-yellow-600 dark:text-gray-300 dark:hover:from-yellow-900/20 dark:hover:to-orange-800/20 dark:hover:text-yellow-400 transition-all duration-200">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-yellow-500 to-orange-500 shadow-md">
                                            <i class="fas fa-industry text-xs text-white"></i>
                                        </div>
                                        <span>Nuova Produzione</span>
                                    </a>
                                <?php endif; ?>

                                <?php if ($this->hasPermission('export')): ?>
                                    <a href="<?= $this->url('/export/create') ?>"
                                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-purple-100 hover:text-purple-600 dark:text-gray-300 dark:hover:from-purple-900/20 dark:hover:to-purple-800/20 dark:hover:text-purple-400 transition-all duration-200">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 shadow-md">
                                            <i class="fas fa-file-export text-xs text-white"></i>
                                        </div>
                                        <span>Nuovo Export/DDT</span>
                                    </a>
                                <?php endif; ?>

                                <?php if ($this->hasPermission('scm')): ?>
                                    <a href="<?= $this->url('/scm-admin/launches/create') ?>"
                                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-100 hover:text-orange-600 dark:text-gray-300 dark:hover:from-orange-900/20 dark:hover:to-red-800/20 dark:hover:text-orange-400 transition-all duration-200">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-red-500 shadow-md">
                                            <i class="fas fa-rocket text-xs text-white"></i>
                                        </div>
                                        <span>Nuovo Lancio SCM</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dark Mode Toggle -->
                <div>
                    <button
                        class="relative flex h-11 w-11 items-center justify-center rounded-xl border border-gray-200/50 bg-white/80 text-gray-500 hover:bg-gradient-to-r hover:from-purple-50 hover:to-indigo-100 hover:text-purple-600 hover:border-purple-200 dark:border-gray-800/50 dark:bg-gray-800/80 dark:text-gray-400 dark:hover:from-purple-900/20 dark:hover:to-indigo-800/20 dark:hover:text-purple-400 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 backdrop-blur-sm"
                        @click="darkMode = !darkMode">
                        <i x-show="!darkMode" class="fas fa-moon transition-transform duration-200" x-transition></i>
                        <i x-show="darkMode" class="fas fa-sun transition-transform duration-200" x-transition></i>
                    </button>
                </div>

                <!-- Notification -->
                <div class="relative" x-data="{ dropdownOpen: false, unreadCount: 0, notifications: [] }"
                     x-init="loadNotifications(); setInterval(() => loadNotifications(), 30000)">
                    <button
                        @click="dropdownOpen = !dropdownOpen; if(dropdownOpen) loadNotifications()"
                        class="relative flex h-11 w-11 items-center justify-center rounded-xl border border-gray-200/50 bg-white/80 text-gray-500 hover:bg-gradient-to-r hover:from-yellow-50 hover:to-orange-100 hover:text-yellow-600 hover:border-yellow-200 dark:border-gray-800/50 dark:bg-gray-800/80 dark:text-gray-400 dark:hover:from-yellow-900/20 dark:hover:to-orange-800/20 dark:hover:text-yellow-400 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 backdrop-blur-sm"
                    >
                        <i class="fas fa-bell"></i>
                        <span x-show="unreadCount > 0" x-text="unreadCount"
                              class="absolute -top-1 -right-1 z-1 h-5 w-5 rounded-full bg-gradient-to-r from-red-500 to-red-600 text-xs font-medium text-white flex items-center justify-center shadow-lg animate-pulse"></span>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div
                        x-show="dropdownOpen"
                        @click.outside="dropdownOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-3 w-96 rounded-2xl border border-gray-200/50 bg-white/95 shadow-xl dark:border-gray-800/50 dark:bg-gray-800/95 backdrop-blur-sm z-50"
                        style="display: none;"
                    >
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <h6 class="font-semibold text-gray-900 dark:text-white">Notifiche</h6>
                            <button
                                @click="markAllAsRead()"
                                x-show="unreadCount > 0"
                                class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                Segna tutte come lette
                            </button>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            <template x-if="notifications.length === 0">
                                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-bell-slash text-3xl mb-2"></i>
                                    <p class="text-sm">Nessuna notifica</p>
                                </div>
                            </template>

                            <template x-for="notif in notifications" :key="notif.id">
                                <div
                                    :class="!notif.is_read ? 'bg-blue-50 dark:bg-blue-900/10' : ''"
                                    class="flex items-start gap-3 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700/50 last:border-b-0 transition-all duration-200 cursor-pointer"
                                    @click="markAsRead(notif.id)"
                                >
                                    <div class="flex-shrink-0">
                                        <div
                                            :class="{
                                                'bg-green-100 text-green-600 dark:bg-green-900/20': notif.type === 'success',
                                                'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/20': notif.type === 'warning',
                                                'bg-red-100 text-red-600 dark:bg-red-900/20': notif.type === 'error',
                                                'bg-blue-100 text-blue-600 dark:bg-blue-900/20': notif.type === 'info' || notif.type === 'system',
                                                'bg-purple-100 text-purple-600 dark:bg-purple-900/20': notif.type === 'quality',
                                                'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/20': notif.type === 'production',
                                                'bg-orange-100 text-orange-600 dark:bg-orange-900/20': notif.type === 'export',
                                                'bg-cyan-100 text-cyan-600 dark:bg-cyan-900/20': notif.type === 'repair'
                                            }"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl shadow-sm"
                                        >
                                            <i :class="notif.icon" class="text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notif.title"></p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2" x-text="notif.message"></p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1" x-text="notif.time_ago"></p>
                                    </div>
                                    <template x-if="!notif.is_read">
                                        <div class="flex-shrink-0">
                                            <div class="h-2 w-2 rounded-full bg-blue-600"></div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <div class="border-t border-gray-200 p-3 text-center dark:border-gray-700">
                            <a href="<?= $this->url('/notifications') ?>"
                               class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                Visualizza tutte le notifiche
                            </a>
                        </div>
                    </div>

                    <script>
                    function loadNotifications() {
                        fetch('<?= $this->url('/notifications/api/list') ?>?limit=10')
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    this.notifications = data.data.notifications;
                                    this.unreadCount = data.data.unread_count;
                                }
                            })
                            .catch(err => console.error('Errore caricamento notifiche:', err));
                    }

                    function markAsRead(notifId) {
                        fetch(`<?= $this->url('/notifications/api/mark-read/') ?>${notifId}`, {
                            method: 'POST'
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                loadNotifications();
                            }
                        });
                    }

                    function markAllAsRead() {
                        fetch('<?= $this->url('/notifications/api/mark-all-read') ?>', {
                            method: 'POST'
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                loadNotifications();
                            }
                        });
                    }
                    </script>
                </div>

                <!-- User Area -->
                <div class="relative" x-data="{dropdownOpen: false}">
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-3 text-sm font-medium">
                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-sm font-semibold text-gray-700">
                                <?= strtoupper(substr($_SESSION['nome'] ?? 'U', 0, 1)) ?>
                            </span>
                        </div>

                        <span class="hidden lg:block">
                            <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                <?= $_SESSION['nome'] ?? 'Utente' ?>
                            </span>
                            <span class="block text-sm text-gray-500 dark:text-gray-400">
                                <?= ucfirst($_SESSION['admin_type'] ?? 'user') ?>
                            </span>
                        </span>

                        <svg class="hidden fill-current sm:block" width="12" height="8" viewBox="0 0 12 8" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M0.410765 0.910734C0.736202 0.585297 1.26384 0.585297 1.58928 0.910734L6.00002 5.32148L10.4108 0.910734C10.7362 0.585297 11.2638 0.585297 11.5893 0.910734C11.9147 1.23617 11.9147 1.76381 11.5893 2.08924L6.58928 7.08924C6.26384 7.41468 5.7362 7.41468 5.41077 7.08924L0.410765 2.08924C0.0853277 1.76381 0.0853277 1.23617 0.410765 0.910734Z"
                                fill="" />
                        </svg>
                    </button>

                    <!-- User Dropdown -->
                    <div x-show="dropdownOpen" @click.outside="dropdownOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800"
                        style="display: none;">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                <?= $_SESSION['nome'] ?? 'Utente' ?>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                <?= $_SESSION['mail'] ?? '' ?>
                            </p>
                        </div>

                        <div class="p-2">
                            <a href="<?= $this->url('/profile') ?>"
                                class="flex items-center gap-2 rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                                <i class="fas fa-user w-4"></i>
                                Profilo
                            </a>

                            <a href="<?= $this->url('/settings') ?>"
                                class="flex items-center gap-2 rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                                <i class="fas fa-cog w-4"></i>
                                Impostazioni
                            </a>

                            <div class="my-1 h-px bg-gray-200 dark:bg-gray-700"></div>

                            <a href="<?= $this->url('/logout') ?>" data-no-pjax
                                class="flex items-center gap-2 rounded-md px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                                <i class="fas fa-sign-out-alt w-4"></i>
                                Esci
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar Mobile -->
        <div x-show="searchToggle" x-transition class="w-full px-3 py-2 lg:hidden" style="display: none;">
            <form>
                <div class="relative">
                    <button class="absolute left-3 top-1/2 -translate-y-1/2">
                        <i class="fas fa-search text-gray-400"></i>
                    </button>
                    <input type="text" placeholder="Cerca cartellini, articoli..."
                        class="w-full bg-gray-50 pl-10 pr-4 text-black focus:outline-none dark:bg-gray-800 dark:text-white rounded-lg border border-gray-200 py-2.5 dark:border-gray-800" />
                </div>
            </form>
        </div>
    </div>
</header>