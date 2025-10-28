<!-- Mobile Overlay -->
<div x-show="isMobile && sidebarToggle" x-transition.opacity.duration.300ms @click="sidebarToggle = false"
    class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"></div>

<!-- Sidebar -->
<div class="bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col h-full transition-all duration-300 ease-in-out
            fixed inset-y-0 left-0 z-50 lg:relative lg:z-auto overflow-hidden
            transform lg:transform-none" :class="{
        // Mobile behavior
        'translate-x-0': (isMobile && sidebarToggle) || !isMobile,
        '-translate-x-full': isMobile && !sidebarToggle,
        
        // Desktop - forza collapsed con CSS
        'sidebar-collapsed': !isMobile && sidebarToggle,
        'w-64': !isMobile && !sidebarToggle
     }"> <!-- SIDEBAR HEADER -->
    <div class="flex items-center border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-blue-50  dark:from-gray-800 dark:to-gray-900 py-6"
        :class="(!isMobile && sidebarToggle) ? 'px-3 justify-center' : 'px-6 justify-between'">
        <a href="<?= $this->url('/') ?>" class="flex items-center group">
            <div
                class="p-2 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 shadow-lg group-hover:shadow-xl transition-all duration-200 group-hover:-translate-y-0.5">
                <img class="h-6 w-6" src="<?= $this->url('/public/assets/logo-white.png') ?>" alt="COREGRE" />
            </div>
            <span
                class="sidebar-text ml-3 text-xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                COREGRE
            </span>
        </a>
        <!-- Close button solo su mobile -->
        <button @click="sidebarToggle = false"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-white hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 lg:hidden">
            <i class="fas fa-times"></i>
        </button>
    </div> <!-- SIDEBAR MENU -->
    <div class="flex flex-1 flex-col overflow-y-auto scrollbar-hidden py-4"
        :class="(!isMobile && sidebarToggle) ? 'px-2' : 'px-4'">
        <nav x-data="{ activeMenu: null }"> <!-- MENU Section -->
            <div class="mb-8">
                <ul class="space-y-2">
                    <!-- Dashboard -->
                    <li>
                        <a href="<?= $this->url('/') ?>"
                            class="sidebar-item flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 hover:text-blue-600 dark:text-gray-300 dark:hover:from-blue-900/20 dark:hover:to-blue-800/20 dark:hover:text-blue-400 shadow-sm hover:shadow-lg hover:-translate-y-0.5 group">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 shadow-md group-hover:shadow-lg transition-all duration-200">
                                <i class="fas fa-home text-sm text-white"></i>
                            </div>
                            <span class="sidebar-text">Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div> <!-- FUNZIONI Section -->
            <div class="sidebar-text mb-4 px-3 py-2 rounded-lg bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-800
                    dark:to-gray-700 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">Funzioni
                </h3>
            </div>
            <div class="mb-8">
                <ul class="space-y-2"> <!-- Riparazioni --> <?php if ($this->hasPermission('riparazioni')): ?>
                        <li>
                            <button @click="activeMenu=activeMenu==='riparazioni' ? null : 'riparazioni' "
                                class="sidebar-item flex w-full items-center justify-between rounded-xl px-4 py-3 font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 hover:text-blue-600 dark:text-gray-300 dark:hover:from-blue-900/20 dark:hover:to-blue-800/20 dark:hover:text-blue-400 shadow-sm hover:shadow-lg hover:-translate-y-0.5 group"
                                :class="activeMenu==='riparazioni' ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-600 dark:from-blue-900/20 dark:to-blue-800/20 dark:text-blue-400 shadow-lg' : '' ">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r
                                        from-blue-500 to-blue-600 shadow-md group-hover:shadow-lg transition-all
                                        duration-200"> <i class="fas fa-hammer text-sm text-white"></i> </div>
                                    <span class="sidebar-text">Riparazioni</span>
                                </div> <i class="sidebar-text fas fa-chevron-down text-xs transition-transform duration-300"
                                    :class="activeMenu==='riparazioni' ? 'rotate-180' : '' "></i>
                            </button> <!-- Submenu -->
                            <div x-show="activeMenu==='riparazioni' " x-collapse
                                class="dropdown-menu mt-3 space-y-1 pl-6 pr-2" data-title="Riparazioni">
                                <div class="rounded-lg bg-gray-50/80 dark:bg-gray-800/40 p-2 shadow-inner
                                    backdrop-blur-sm"> <a href="<?= $this->url('/riparazioni/create') ?>" class="flex
                                        items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600
                                        transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400
                                        dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md
                                        hover:-translate-y-0.5"> <i class="fas fa-plus text-blue-500
                                            dark:text-blue-400"></i> <span>Nuova</span> </a> <a
                                        href="<?= $this->url('/riparazioni') ?>" class="flex items-center gap-3 rounded-lg
                                        px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200
                                        hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700
                                        dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5"> <i
                                            class="fas fa-list text-blue-500 dark:text-blue-400"></i> <span>Elenco</span>
                                    </a></div>
                            </div>
                        </li> <?php endif; ?> <!-- Controllo Qualità --> <?php if ($this->hasPermission('quality')): ?>

                        <li> <button @click="activeMenu=activeMenu==='quality' ? null : 'quality' " class="sidebar-item flex w-full
                                items-center justify-between rounded-xl px-4 py-3 font-medium text-gray-700 transition-all
                                duration-200 hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-100
                                hover:text-green-600 dark:text-gray-300 dark:hover:from-green-900/20
                                dark:hover:to-emerald-800/20 dark:hover:text-green-400 shadow-sm hover:shadow-lg
                                hover:-translate-y-0.5 group" :class="activeMenu==='quality'
                                ? 'bg-gradient-to-r from-green-50 to-emerald-100 text-green-600 dark:from-green-900/20 dark:to-emerald-800/20 dark:text-green-400 shadow-lg'
                                : '' ">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r
                                        from-green-500 to-emerald-600 shadow-md group-hover:shadow-lg transition-all
                                        duration-200"> <i class="fas fa-check-circle text-sm text-white"></i> </div>
                                    <span class="sidebar-text">Controllo Qualità</span>
                                </div> <i class="sidebar-text fas fa-chevron-down text-xs transition-transform duration-300"
                                    :class="activeMenu==='quality' ? 'rotate-180' : '' "></i>
                            </button> <!-- Submenu -->
                            <div x-show="activeMenu==='quality' " x-collapse class="dropdown-menu mt-3 space-y-1 pl-6 pr-2"
                                data-title="Controllo Qualità">
                                <div class="rounded-lg bg-gray-50/80 dark:bg-gray-800/40 p-2 shadow-inner
                                    backdrop-blur-sm"> <a href="<?= $this->url('/quality/') ?>" class="flex
                                        items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600
                                        transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400
                                        dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md
                                        hover:-translate-y-0.5"> <i class="fas fa-home text-green-500
                                            dark:text-green-400"></i> <span>Dashboard</span> </a> <a
                                        href="<?= $this->url('/quality/hermes') ?>" class="flex items-center gap-3 rounded-lg
                                        px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200
                                        hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700
                                        dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5"> <i
                                            class="fas fa-h text-green-500 dark:text-green-400"></i> <span>Hermes</span>
                                    </a>
                                    <a href="<?= $this->url('/quality/operators') ?>" class="flex items-center gap-3 rounded-lg
                                        px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200
                                        hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700
                                        dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5"> <i
                                            class="fas fa-users text-green-500 dark:text-green-400"></i>
                                        <span>Operatori</span>
                                    </a>
                                </div>
                            </div>
                        </li> <?php endif; ?> <!-- Produzione --> <?php if ($this->hasPermission('produzione')): ?>
                        <li> <button @click="activeMenu=activeMenu==='production' ? null : 'production' " class="sidebar-item flex
                                w-full items-center justify-between rounded-xl px-4 py-3 font-medium text-gray-700
                                transition-all duration-200 hover:bg-gradient-to-r hover:from-yellow-50 hover:to-orange-100
                                hover:text-yellow-600 dark:text-gray-300 dark:hover:from-yellow-900/20
                                dark:hover:to-orange-800/20 dark:hover:text-yellow-400 shadow-sm hover:shadow-lg
                                hover:-translate-y-0.5 group" :class="activeMenu==='production'
                                ? 'bg-gradient-to-r from-yellow-50 to-orange-100 text-yellow-600 dark:from-yellow-900/20 dark:to-orange-800/20 dark:text-yellow-400 shadow-lg'
                                : '' ">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r
                                        from-yellow-500 to-orange-500 shadow-md group-hover:shadow-lg transition-all
                                        duration-200"> <i class="fas fa-calendar text-sm text-white"></i> </div>
                                    <span class="sidebar-text">Produzione</span>
                                </div> <i class="sidebar-text fas fa-chevron-down text-xs transition-transform duration-300"
                                    :class="activeMenu==='production' ? 'rotate-180' : '' "></i>
                            </button> <!-- Submenu -->
                            <div x-show="activeMenu==='production' " x-collapse
                                class="dropdown-menu mt-3 space-y-1 pl-6 pr-2" data-title="Produzione">
                                <div class="rounded-lg bg-gray-50/80 dark:bg-gray-800/40 p-2 shadow-inner
                                    backdrop-blur-sm"> <a href="<?= $this->url('/produzione/new') ?>" class="flex
                                        items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600
                                        transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400
                                        dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md
                                        hover:-translate-y-0.5"> <i class="fas fa-plus text-yellow-500
                                            dark:text-yellow-400"></i> <span>Nuova</span> </a> <a
                                        href="<?= $this->url('/produzione/calendar') ?>" class="flex items-center gap-3
                                        rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200
                                        hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700
                                        dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5"> <i
                                            class="fas fa-calendar text-yellow-500 dark:text-yellow-400"></i>
                                        <span>Calendario</span> </a>
                                    <a href="<?= $this->url('/produzione/csv') ?>" class="flex items-center gap-3
                                        rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200
                                        hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700
                                        dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5"> <i
                                            class="fas fa-file-csv text-yellow-500 dark:text-yellow-400"></i>
                                        <span>Report CSV</span> </a>
                                </div>
                            </div>

                        </li> <?php endif; ?>

                    <!-- Export -->
                    <?php if ($this->hasPermission('export')): ?>
                        <li>
                            <button @click="activeMenu=activeMenu==='export' ? null : 'export'"
                                class="sidebar-item flex w-full items-center justify-between rounded-xl px-4 py-3 font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-100 hover:text-indigo-600 dark:text-gray-300 dark:hover:from-indigo-900/20 dark:hover:to-purple-800/20 dark:hover:text-indigo-400 shadow-sm hover:shadow-lg hover:-translate-y-0.5 group"
                                :class="activeMenu==='export' ? 'bg-gradient-to-r from-indigo-50 to-purple-100 text-indigo-600 dark:from-indigo-900/20 dark:to-purple-800/20 dark:text-indigo-400 shadow-lg' : ''">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 shadow-md group-hover:shadow-lg transition-all duration-200">
                                        <i class="fas fa-globe-europe text-sm text-white"></i>
                                    </div>
                                    <span class="sidebar-text">Export</span>
                                </div>
                                <i class="sidebar-text fas fa-chevron-down text-xs transition-transform duration-300"
                                    :class="activeMenu==='export' ? 'rotate-180' : ''"></i>
                            </button>
                            <!-- Submenu -->
                            <div x-show="activeMenu==='export'" x-collapse class="dropdown-menu mt-3 space-y-1 pl-6 pr-2"
                                data-title="Export">
                                <div class="rounded-lg bg-gray-50/80 dark:bg-gray-800/40 p-2 shadow-inner backdrop-blur-sm">
                                    <a href="<?= $this->url('/export/dashboard') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fas fa-home text-indigo-500 dark:text-indigo-400"></i>
                                        <span>Dashboard</span>
                                    </a>
                                    <a href="<?= $this->url('/export/create') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fas fa-plus text-indigo-500 dark:text-indigo-400"></i>
                                        <span>Nuovo DDT</span>
                                    </a>
                                    <a href="<?= $this->url('/export') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fas fa-list text-indigo-500 dark:text-indigo-400"></i>
                                        <span>Lista Documenti</span>
                                    </a>
                                    <a href="<?= $this->url('/export/terzisti') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fas fa-building text-indigo-500 dark:text-indigo-400"></i>
                                        <span>Terzisti</span>
                                    </a>
                                </div>
                            </div>
                        </li>
                    <?php endif; ?>


                    <!-- SCM -->
                    <?php if ($this->hasPermission('scm')): ?>
                        <li>
                            <button @click="activeMenu=activeMenu==='scm' ? null : 'scm'"
                                class="sidebar-item flex w-full items-center justify-between rounded-xl px-4 py-3 font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-100 hover:text-orange-600 dark:text-gray-300 dark:hover:from-orange-900/20 dark:hover:to-red-800/20 dark:hover:text-orange-400 shadow-sm hover:shadow-lg hover:-translate-y-0.5 group"
                                :class="activeMenu==='scm' ? 'bg-gradient-to-r from-orange-50 to-red-100 text-orange-600 dark:from-orange-900/20 dark:to-red-800/20 dark:text-orange-400 shadow-lg' : ''">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-red-500 shadow-md group-hover:shadow-lg transition-all duration-200">
                                        <i class="fas fa-industry text-sm text-white"></i>
                                    </div>
                                    <span class="sidebar-text">SCM</span>
                                </div>
                                <i class="sidebar-text fas fa-chevron-down text-xs transition-transform duration-300"
                                    :class="activeMenu==='scm' ? 'rotate-180' : ''"></i>
                            </button>
                            <!-- Submenu -->
                            <div x-show="activeMenu==='scm'" x-collapse class="dropdown-menu mt-3 space-y-1 pl-6 pr-2"
                                data-title="SCM">
                                <div class="rounded-lg bg-gray-50/80 dark:bg-gray-800/40 p-2 shadow-inner backdrop-blur-sm">
                                    <a href="<?= $this->url('/scm-admin') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fas fa-home text-orange-500 dark:text-orange-400"></i>
                                        <span>Dashboard</span>
                                    </a>
                                    <a href="<?= $this->url('/scm-admin/launches') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fas fa-rocket text-orange-500 dark:text-orange-400"></i>
                                        <span>Lanci</span>
                                    </a>
                                    <a href="<?= $this->url('/scm-admin/laboratories') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fas fa-building text-orange-500 dark:text-orange-400"></i>
                                        <span>Laboratori</span>
                                    </a>
                                    <a href="<?= $this->url('/scm-admin/monitoring') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fas fa-chart-line text-orange-500 dark:text-orange-400"></i>
                                        <span>Monitoring</span>
                                    </a>
                                </div>
                            </div>
                        </li>
                    <?php endif; ?> <!-- Tracking -->
                    <!-- MRP -->
                    <?php if ($this->hasPermission('mrp')): ?>
                        <li>
                            <button @click="activeMenu=activeMenu==='mrp' ? null : 'mrp'"
                                class="sidebar-item flex w-full items-center justify-between rounded-xl px-4 py-3 font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-100 hover:text-blue-600 dark:text-gray-300 dark:hover:from-blue-900/20 dark:hover:to-indigo-800/20 dark:hover:text-blue-400 shadow-sm hover:shadow-lg hover:-translate-y-0.5 group"
                                :class="activeMenu==='mrp' ? 'bg-gradient-to-r from-blue-50 to-indigo-100 text-blue-600 dark:from-blue-900/20 dark:to-indigo-800/20 dark:text-blue-400 shadow-lg' : ''">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 shadow-md group-hover:shadow-lg transition-all duration-200">
                                        <i class="fas fa-box text-sm text-white"></i>
                                    </div>
                                    <span class="sidebar-text">MRP</span>
                                </div>
                                <i class="sidebar-text fas fa-chevron-down text-xs transition-transform duration-300"
                                    :class="activeMenu==='mrp' ? 'rotate-180' : ''"></i>
                            </button>
                            <!-- Submenu -->
                            <div x-show="activeMenu==='mrp'" x-collapse class="dropdown-menu mt-3 space-y-1 pl-6 pr-2"
                                data-title="MRP">
                                <div class="rounded-lg bg-gray-50/80 dark:bg-gray-800/40 p-2 shadow-inner backdrop-blur-sm">
                                    <a href="<?= $this->url('/mrp') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fas fa-home text-blue-500 dark:text-blue-400"></i>
                                        <span>Dashboard</span>
                                    </a>
                                    <a href="<?= $this->url('/mrp/materials') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fas fa-list text-blue-500 dark:text-blue-400"></i>
                                        <span>Materiali</span>
                                    </a>
                                    <a href="<?= $this->url('/mrp/categories') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fas fa-tags text-blue-500 dark:text-blue-400"></i>
                                        <span>Categorie</span>
                                    </a>
                                    <a href="<?= $this->url('/mrp/import') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fas fa-upload text-blue-500 dark:text-blue-400"></i>
                                        <span>Import DMODA</span>
                                    </a>
                                </div>
                            </div>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->hasPermission('tracking')): ?>
                        <li>
                            <button @click="activeMenu=activeMenu==='tracking' ? null : 'tracking'"
                                class="sidebar-item flex w-full items-center justify-between rounded-xl px-4 py-3 font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-100 hover:text-purple-600 dark:text-gray-300 dark:hover:from-purple-900/20 dark:hover:to-pink-800/20 dark:hover:text-purple-400 shadow-sm hover:shadow-lg hover:-translate-y-0.5 group"
                                :class="activeMenu==='tracking' ? 'bg-gradient-to-r from-purple-50 to-pink-100 text-purple-600 dark:from-purple-900/20 dark:to-pink-800/20 dark:text-purple-400 shadow-lg' : '' ">

                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r
                    from-purple-500 to-pink-600 shadow-md group-hover:shadow-lg transition-all duration-200">
                                        <i class="fas fa-map-marker-alt text-sm text-white"></i>
                                    </div>
                                    <span class="sidebar-text">Tracking</span>
                                </div>

                                <i class="sidebar-text fas fa-chevron-down text-xs transition-transform duration-300"
                                    :class="activeMenu==='tracking' ? 'rotate-180' : '' "></i>
                            </button>

                            <!-- Submenu -->
                            <div x-show="activeMenu==='tracking'" x-collapse class="dropdown-menu mt-3 space-y-1 pl-6 pr-2"
                                data-title="Tracking">
                                <div class="rounded-lg bg-gray-50/80 dark:bg-gray-800/40 p-2 shadow-inner backdrop-blur-sm">

                                    <a href="<?= $this->url('/tracking/') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fa fa-home text-purple-500 dark:text-purple-400"></i> <span>Menu</span>
                                    </a>

                                    <a href="<?= $this->url('/tracking/multiSearch') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fa fa-magnifying-glass-plus text-purple-500 dark:text-purple-400"></i>
                                        <span>Associa per Ricerca</span>
                                    </a>

                                    <a href="<?= $this->url('/tracking/orderSearch') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fa fa-link text-purple-500 dark:text-purple-400"></i> <span>Associa per
                                            Cartellini</span>
                                    </a>

                                    <a href="<?= $this->url('/tracking/packinglist') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fa fa-list text-purple-500 dark:text-purple-400"></i> <span>Crea Packing
                                            List</span>
                                    </a>

                                    <a href="<?= $this->url('/tracking/treeView') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fa fa-folder-tree text-purple-500 dark:text-purple-400"></i> <span>Albero
                                            Dettagli</span>
                                    </a>

                                    <a href="<?= $this->url('/tracking/lotdetail') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fa fa-file-invoice text-purple-500 dark:text-purple-400"></i>
                                        <span>Dettagli Lotti</span>
                                    </a>

                                    <a href="<?= $this->url('/tracking/makeFiches') ?>"
                                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 transition-all duration-200 hover:bg-white hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                                        <i class="fa fa-tag text-purple-500 dark:text-purple-400"></i> <span>Stampa
                                            Fiches</span>
                                    </a>

                                </div>
                            </div>
                        </li>
                    <?php endif; ?>

                </ul>
            </div> <!-- STRUMENTI Section -->
            <?php if ($this->hasPermission('artisan') || $this->hasPermission('cron') || $this->hasPermission('dbsql') || $this->isAdmin()): ?>
                <div>

                    <div class="sidebar-text mb-4 px-3 py-2 rounded-lg bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-800
                    dark:to-gray-700 shadow-sm">
                        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">Admin
                        </h3>
                    </div>
                    <ul class="space-y-2">


                        <!-- Database -->
                        <?php if ($this->hasPermission('dbsql')): ?>
                            <li> <a href="<?= $this->url('/database') ?>" class="sidebar-item flex items-center gap-3 rounded-xl px-4
                                py-3 font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r
                                hover:from-cyan-50 hover:to-cyan-100 hover:text-gray-800 dark:text-gray-300
                                dark:hover:from-gray-800/50 dark:hover:to-gray-700/50 dark:hover:text-gray-200 shadow-sm
                                hover:shadow-lg hover:-translate-y-0.5 group">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r
                                    from-cyan-500 to-cyan-600 shadow-md group-hover:shadow-lg transition-all duration-200">
                                        <i class="fas fa-database text-sm text-white"></i>
                                    </div> <span class="sidebar-text">Database</span>

                                </a> </li> <?php endif; ?>

                        <!-- Artisan Console -->
                        <?php if ($this->hasPermission('artisan')): ?>
                            <li>
                                <a href="<?= $this->url('/artisan-web') ?>" class="sidebar-item flex items-center gap-3 rounded-xl px-4
                                py-3 font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r
                                hover:from-cyan-50 hover:to-cyan-100 hover:text-gray-800 dark:text-gray-300
                                dark:hover:from-gray-800/50 dark:hover:to-gray-700/50 dark:hover:text-gray-200 shadow-sm
                                hover:shadow-lg hover:-translate-y-0.5 group">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r
                                    from-cyan-500 to-cyan-600 shadow-md group-hover:shadow-lg transition-all duration-200">
                                        <i class="fas fa-terminal text-sm text-white"></i>
                                    </div>
                                    <span class="sidebar-text">Artisan Console</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Cron Jobs -->
                        <?php if ($this->hasPermission('cron')): ?>
                            <li>
                                <a href="<?= $this->url('/cron') ?>"
                                    class="sidebar-item flex items-center gap-3 rounded-xl px-4 py-3 font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r hover:from-cyan-50 hover:to-cyan-100 hover:text-gray-800 dark:text-gray-300 dark:hover:from-gray-800/50 dark:hover:to-gray-700/50 dark:hover:text-gray-200 shadow-sm hover:shadow-lg hover:-translate-y-0.5 group">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-cyan-500 to-cyan-600 shadow-md group-hover:shadow-lg transition-all duration-200">
                                        <i class="fas fa-clock text-sm text-white"></i>
                                    </div>
                                    <span class="sidebar-text">Cron Jobs</span>
                                </a>
                            </li>
                        <?php endif; ?>


                    </ul>
                </div>
            <?php endif; ?>
            <!-- STRUMENTI Section -->
            <?php if ($this->hasPermission('utenti') || $this->hasPermission('log') || $this->hasPermission('etichette') || $this->hasPermission('settings') || $this->isAdmin()): ?>
                <div>

                    <div class="sidebar-text mb-4 px-3 py-2 rounded-lg bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-800
                    dark:to-gray-700 shadow-sm">
                        <h3 class="text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-300">Strumenti
                        </h3>
                    </div>
                    <ul class="space-y-2"> <!-- Utenti --> <?php if ($this->hasPermission('utenti')): ?>
                            <li> <a href="<?= $this->url('/users') ?>" class="sidebar-item flex items-center gap-3 rounded-xl px-4 py-3
                                font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r
                                hover:from-gray-50 hover:to-gray-100 hover:text-gray-800 dark:text-gray-300
                                dark:hover:from-gray-800/50 dark:hover:to-gray-700/50 dark:hover:text-gray-200 shadow-sm
                                hover:shadow-lg hover:-translate-y-0.5 group">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r
                                    from-gray-500 to-gray-600 shadow-md group-hover:shadow-lg transition-all duration-200">
                                        <i class="fas fa-users text-sm text-white"></i>
                                    </div> <span class="sidebar-text">Utenti</span>
                                </a> </li> <?php endif; ?> <!-- Attività --> <?php if ($this->hasPermission('log')): ?>
                            <li> <a href="<?= $this->url('/logs') ?>" class="sidebar-item flex items-center gap-3 rounded-xl px-4
                                py-3 font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r
                                hover:from-gray-50 hover:to-gray-100 hover:text-gray-800 dark:text-gray-300
                                dark:hover:from-gray-800/50 dark:hover:to-gray-700/50 dark:hover:text-gray-200 shadow-sm
                                hover:shadow-lg hover:-translate-y-0.5 group">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r
                                    from-gray-500 to-gray-600 shadow-md group-hover:shadow-lg transition-all duration-200">
                                        <i class="fas fa-chart-line text-sm text-white"></i>
                                    </div> <span class="sidebar-text">Attività</span>

                                </a> </li>
                        <?php endif; ?>

                        <!-- Etichette DYMO -->
                        <?php if ($this->hasPermission('etichette')): ?>
                            <li>
                                <a href="<?= $this->url('/etichette') ?>" class="sidebar-item flex items-center gap-3 rounded-xl px-4
                                py-3 font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r
                                hover:from-gray-50 hover:to-gray-100 hover:text-gray-800 dark:text-gray-300
                                dark:hover:from-gray-800/50 dark:hover:to-gray-700/50 dark:hover:text-gray-200 shadow-sm
                                hover:shadow-lg hover:-translate-y-0.5 group">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r
                                    from-gray-500 to-gray-600 shadow-md group-hover:shadow-lg transition-all duration-200">
                                        <i class="fas fa-barcode text-sm text-white"></i>
                                    </div>
                                    <span class="sidebar-text">Etichette </span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Impostazioni --> <?php if ($this->hasPermission('settings')): ?>
                            <li> <a href="<?= $this->url('/settings') ?>" class="sidebar-item flex items-center gap-3 rounded-xl px-4 py-3
                                font-medium text-gray-700 transition-all duration-200 hover:bg-gradient-to-r
                                hover:from-gray-50 hover:to-gray-100 hover:text-gray-800 dark:text-gray-300
                                dark:hover:from-gray-800/50 dark:hover:to-gray-700/50 dark:hover:text-gray-200 shadow-sm
                                hover:shadow-lg hover:-translate-y-0.5 group">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r
                                    from-gray-500 to-gray-600 shadow-md group-hover:shadow-lg transition-all duration-200">
                                        <i class="fas fa-cog text-sm text-white"></i>
                                    </div> <span class="sidebar-text">Impostazioni</span>
                                </a> </li> <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </nav>
    </div>
</div>