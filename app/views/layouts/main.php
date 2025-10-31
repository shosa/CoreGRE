<!DOCTYPE html>
<html lang="it" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $pageTitle ?? 'COREGRE' ?></title>

    <!-- Google Fonts - Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <link href="<?= $this->url('css/app.css') ?>" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js with Collapse plugin -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= $this->url('assets/favicon.ico') ?>">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= $this->generateCsrfToken() ?>">

    <script>
        // Tailwind config esteso
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        success: '#10b981',
                        warning: '#f59e0b',
                        error: '#ef4444',
                        info: '#06b6d4'
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui']
                    },
                    fontSize: {
                        'title-sm': ['1.125rem', '1.75rem'],
                        'title-md': ['1.25rem', '1.75rem'],
                        'title-md2': ['1.5rem', '2rem'],
                        'title-lg': ['1.75rem', '2.25rem'],
                        'title-xl': ['2.25rem', '2.5rem'],
                        'title-xl2': ['3rem', '3.5rem']
                    },
                    zIndex: {
                        '99999': '99999',
                        '9999': '9999'
                    }
                }
            }
        }
    </script>
</head>

<body class="h-full" x-data="{ 
        page: 'dashboard', 
        loaded: true, 
        darkMode: false, 
        stickyMenu: false, 
        sidebarToggle: false, 
        scrollTop: false,
        searchToggle: false,
        isMobile: window.innerWidth < 1024
    }" x-init="
        darkMode = JSON.parse(localStorage.getItem('darkMode') || 'false');
        $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
        sidebarToggle = JSON.parse(localStorage.getItem('sidebarToggle') || 'false');
        $watch('sidebarToggle', value => localStorage.setItem('sidebarToggle', JSON.stringify(value)));
        
        // Aggiorna isMobile quando la finestra si ridimensiona
        window.addEventListener('resize', () => {
            isMobile = window.innerWidth < 1024;
        });
        
        // Per desktop: sidebarToggle=true significa collassata (solo icone)
        // Per mobile: sidebarToggle=true significa visibile
    " :class="{'dark bg-gray-900': darkMode === true}">

    <?php if ($this->isAuthenticated()): ?>
        <!-- Layout Autenticato con TailAdmin Style -->
        <!-- ===== Preloader Start ===== -->
        <div x-show="!loaded" x-transition
            class="fixed inset-0 z-99999 flex items-center justify-center bg-white dark:bg-gray-900">
            <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-primary border-t-transparent">
            </div>
        </div>
        <!-- ===== Preloader End ===== -->

        <!-- ===== Page Wrapper Start ===== -->
        <div class="flex h-screen overflow-hidden">
            <!-- ===== Sidebar Start ===== -->
            <?php include VIEW_PATH . '/components/sidebar.php'; ?>
            <!-- ===== Sidebar End ===== -->

            <!-- ===== Content Area Start ===== -->
            <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">

                <!-- ===== Header Start ===== -->
                <?php include VIEW_PATH . '/components/header.php'; ?>
                <!-- ===== Header End ===== -->

                <!-- ===== PJAX Progress Bar ===== -->
                <div id="pjax-progress" class="h-1 w-full bg-gray-200 dark:bg-gray-700 overflow-hidden relative z-50 transition-opacity duration-300" style="opacity: 0;">
                    <div class="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-blue-500 animate-pulse"
                         style="width: 0%; transition: width 0.3s ease-in-out; background-size: 200% 100%; animation: shimmer 1.5s infinite;"></div>
                </div>

                <!-- ===== Main Content Start ===== -->
                <main
                    class="flex-1 bg-gradient-to-br from-gray-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
                    <div id="main-content" class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
                        <!-- Alerts -->
                        <?php include VIEW_PATH . '/components/alerts.php'; ?>

                        <!-- Page Content -->
                        <?= $content ?>
                    </div>
                </main>
                <!-- ===== Main Content End ===== -->
            </div>
            <!-- ===== Content Area End ===== -->
        </div>
        <!-- ===== Page Wrapper End ===== -->

    <?php else: ?>
        <!-- Layout Non Autenticato -->
        <div class="min-h-full flex flex-col bg-gray-50 dark:bg-gray-900">
            <!-- Simple header per pagine pubbliche -->
            <header class="bg-white dark:bg-gray-800 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-4">
                        <div class="flex items-center">
                            <img class="h-8 w-8" src="<?= $this->url('assets/logo.png') ?>" alt="COREGRE">
                            <span class="ml-2 text-xl font-semibold text-gray-900 dark:text-white"><?= APP_NAME ?></span>
                        </div>

                        <!-- Dark mode toggle per login -->
                        <button @click="darkMode = !darkMode"
                            class="p-2 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            <i x-show="!darkMode" class="fas fa-moon"></i>
                            <i x-show="darkMode" class="fas fa-sun"></i>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main content -->
            <main class="flex-1">
                <!-- Alerts per pagine pubbliche -->
                <?php include VIEW_PATH . '/components/alerts.php'; ?>

                <!-- Page content -->
                <?= $content ?>
            </main>

            <!-- Simple footer -->
            <footer class="bg-white dark:bg-gray-800">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                        © <?= date('Y') ?>     <?= APP_NAME ?> - Versione <?= APP_VERSION ?>
                    </p>
                </div>
            </footer>
        </div>
    <?php endif; ?>

    <!-- JavaScript Globale -->
    <script>
        // Configurazione globale COREGRE
        window.COREGRE = {
            baseUrl: '<?= BASE_URL ?>',
            csrfToken: '<?= $this->generateCsrfToken() ?>',
            user: {
                id: <?= $_SESSION['user_id'] ?? 'null' ?>,
                username: '<?= $_SESSION['username'] ?? '' ?>',
                name: '<?= $_SESSION['nome'] ?? '' ?>',
                theme: '<?= $_SESSION['tema'] ?? 'primary' ?>',
                isAuthenticated: <?= $this->isAuthenticated() ? 'true' : 'false' ?>
            },

            // Alias per CoregreModals (per retrocompatibilità)
            openModal: function(modalId) {
                if (window.CoregreModals) {
                    return window.CoregreModals.openModal(modalId);
                }
            },

            closeModal: function(modalId, callback) {
                if (window.CoregreModals) {
                    return window.CoregreModals.closeModal(modalId, callback);
                }
            }
        };

        // Utility functions
        function showLoading() {
            document.body.classList.add('loading');
        }

        function hideLoading() {
            document.body.classList.remove('loading');
        }

        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alert-container');
            if (alertContainer) {
                const alertId = 'alert-' + Date.now();
                const iconMap = {
                    success: 'fa-check-circle',
                    error: 'fa-times-circle',
                    warning: 'fa-exclamation-triangle',
                    info: 'fa-info-circle'
                };

                const alert = document.createElement('div');
                alert.id = alertId;
                alert.className = `rounded-lg border p-4 ${getAlertClasses(type)} animate-fade-in`;
                alert.innerHTML = `
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas ${iconMap[type] || iconMap.info}"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">${message}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button onclick="document.getElementById('${alertId}').remove()" 
                                    class="inline-flex rounded-md p-1.5 hover:bg-black/5 focus:outline-none">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                alertContainer.appendChild(alert);

                // Auto-hide dopo 5 secondi
                setTimeout(() => {
                    const alertElement = document.getElementById(alertId);
                    if (alertElement) {
                        alertElement.style.opacity = '0';
                        alertElement.style.transform = 'translateY(-10px)';
                        setTimeout(() => alertElement.remove(), 300);
                    }
                }, 5000);
            }
        }

        function getAlertClasses(type) {
            const classes = {
                success: 'border-green-200 bg-green-50 text-green-800 dark:border-green-800 dark:bg-green-800/10 dark:text-green-300',
                error: 'border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-800/10 dark:text-red-300',
                warning: 'border-yellow-200 bg-yellow-50 text-yellow-800 dark:border-yellow-800 dark:bg-yellow-800/10 dark:text-yellow-300',
                info: 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-800 dark:bg-blue-800/10 dark:text-blue-300'
            };
            return classes[type] || classes.info;
        }

        // API helper con CSRF
        async function apiCall(url, options = {}) {
            showLoading();

            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.COREGRE.csrfToken
                }
            };

            const mergedOptions = { ...defaultOptions, ...options };
            if (mergedOptions.body && typeof mergedOptions.body === 'object') {
                mergedOptions.body = JSON.stringify(mergedOptions.body);
            }

            try {
                const response = await fetch(window.COREGRE.baseUrl + url, mergedOptions);
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || `HTTP ${response.status}`);
                }

                return data;
            } catch (error) {
                console.error('API Error:', error);
                throw error;
            } finally {
                hideLoading();
            }
        }

        // Gestione scroll
        window.addEventListener('scroll', function () {
            if (window.scrollY > 100) {
                document.body.classList.add('scrolled');
            } else {
                document.body.classList.remove('scrolled');
            }
        });

        // PJAX Navigation System
        class CoregrePjax {
            constructor() {
                this.mainContent = document.getElementById('main-content') || document.querySelector('main > div');
                this.currentUrl = window.location.href;
                this.isNavigating = false;

                this.init();
            }

            init() {
                // Intercetta tutti i click sui link
                document.addEventListener('click', (e) => this.handleLinkClick(e));

                // Gestisce il back/forward del browser
                window.addEventListener('popstate', (e) => this.handlePopState(e));

                // Imposta lo stato iniziale
                if (!window.history.state) {
                    window.history.replaceState({
                        url: this.currentUrl,
                        title: document.title
                    }, document.title, this.currentUrl);
                }
            }

            handleLinkClick(e) {
                const link = e.target.closest('a');
                if (!link) return;

                // Verifica se è un link interno
                if (!this.shouldIntercept(link)) return;

                e.preventDefault();
                this.navigateTo(link.href);
            }

            shouldIntercept(link) {
                // Non intercettare se:
                // - È un link esterno
                // - Ha target="_blank" 
                // - Ha data-no-pjax
                // - È un download
                // - È un anchor nella stessa pagina

                const href = link.getAttribute('href');
                if (!href || href.startsWith('#')) return false;
                if (link.target === '_blank') return false;
                if (link.hasAttribute('data-no-pjax')) return false;
                if (link.download) return false;
                if (href.startsWith('mailto:') || href.startsWith('tel:')) return false;

                // Verifica che sia un link interno
                try {
                    const url = new URL(href, window.location.origin);
                    return url.origin === window.location.origin;
                } catch {
                    return false;
                }
            }

            async navigateTo(url) {
                if (this.isNavigating || url === this.currentUrl) return;

                this.isNavigating = true;
                this.showLoading();

                try {
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-PJAX': 'true',
                            'Accept': 'application/json, text/html'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();

                    // Aggiorna il contenuto
                    this.updateContent(data);

                    // Aggiorna lo stato del browser
                    window.history.pushState({
                        url: url,
                        title: data.title
                    }, data.title, url);

                    this.currentUrl = url;
                    document.title = data.title;

                    // Scroll to top
                    window.scrollTo(0, 0);

                } catch (error) {
                    console.error('PJAX Navigation Error:', error);
                    // Fallback alla navigazione normale
                    window.location.href = url;
                } finally {
                    this.isNavigating = false;
                    this.hideLoading();
                }
            }

            handlePopState(e) {
                if (e.state && e.state.url) {
                    this.navigateTo(e.state.url);
                }
            }

            updateContent(data) {
                if (this.mainContent) {
                    // Animazione fade out
                    this.mainContent.style.opacity = '0.7';

                    setTimeout(() => {
                        // Salva il contenuto originale per poter estrarre gli script
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = data.content;

                        // Estrai e rimuovi tutti gli script dal contenuto
                        const scripts = Array.from(tempDiv.querySelectorAll('script'));
                        scripts.forEach(script => script.remove());

                        // Aggiorna il contenuto senza script
                        this.mainContent.innerHTML = tempDiv.innerHTML;

                        // Re-inizializza Alpine.js per il nuovo contenuto
                        if (window.Alpine) {
                            window.Alpine.initTree(this.mainContent);
                        }

                        // Esegue script specifici della pagina dal server
                        if (data.scripts) {
                            try {
                                new Function(data.scripts)();
                            } catch (e) {
                                console.warn('Error executing page scripts:', e);
                            }
                        }

                        // Esegue tutti gli script inline trovati nel contenuto
                        this.executeInlineScripts(scripts);

                        // Re-inizializza event listeners globali
                        this.reinitializeEventListeners();

                        // Animazione fade in
                        this.mainContent.style.opacity = '1';

                        // Aggiorna la sidebar attiva se necessario
                        this.updateActiveNavigation(data.url);

                    }, 150);
                }
            }

            executeInlineScripts(scripts) {
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');

                    // Copia attributi
                    Array.from(oldScript.attributes).forEach(attr => {
                        newScript.setAttribute(attr.name, attr.value);
                    });

                    // Copia contenuto
                    if (oldScript.src) {
                        // Script esterno
                        newScript.src = oldScript.src;
                        newScript.async = false; // Mantieni l'ordine di esecuzione
                    } else {
                        // Script inline
                        newScript.textContent = oldScript.textContent;
                    }

                    // Aggiungi temporaneamente al DOM e poi rimuovi
                    document.head.appendChild(newScript);

                    // Per script inline, rimuovi immediatamente
                    if (!oldScript.src) {
                        setTimeout(() => {
                            if (newScript.parentNode) {
                                newScript.parentNode.removeChild(newScript);
                            }
                        }, 100);
                    }
                });
            }

            reinitializeEventListeners() {
                // Re-inizializza eventi comuni che potrebbero essere persi

                // 1. Form submissions con CSRF
                this.mainContent.querySelectorAll('form[data-ajax="true"]').forEach(form => {
                    form.addEventListener('submit', this.handleAjaxForm.bind(this));
                });

                // 2. Bottoni con azioni specifiche
                this.mainContent.querySelectorAll('[data-action]').forEach(button => {
                    const action = button.getAttribute('data-action');
                    button.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.handleActionButton(action, button, e);
                    });
                });

                // 3. Tooltips e popovers (se presenti)
                if (window.initTooltips && typeof window.initTooltips === 'function') {
                    window.initTooltips();
                }

                // 4. Date pickers e altri widget
                if (window.initDatePickers && typeof window.initDatePickers === 'function') {
                    window.initDatePickers();
                }

                // 5. Selettori personalizzati
                if (window.initCustomSelects && typeof window.initCustomSelects === 'function') {
                    window.initCustomSelects();
                }

                // 6. Trigger evento personalizzato per le pagine
                this.mainContent.dispatchEvent(new CustomEvent('pjax:contentLoaded', {
                    bubbles: true,
                    detail: { container: this.mainContent }
                }));
            }

            handleAjaxForm(e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);

                // Aggiungi CSRF token se non presente
                if (!formData.has('csrf_token')) {
                    formData.append('csrf_token', window.COREGRE.csrfToken);
                }

                fetch(form.action, {
                    method: form.method || 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': window.COREGRE.csrfToken
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert(data.message || 'Operazione completata', 'success');
                            if (data.redirect) {
                                this.navigateTo(data.redirect);
                            }
                        } else {
                            showAlert(data.message || 'Errore durante l\'operazione', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Form submission error:', error);
                        showAlert('Errore durante l\'invio del form', 'error');
                    });
            }

            handleActionButton(action, button, e) {
                // Gestisce bottoni con data-action
                const target = button.getAttribute('data-target');
                const confirm = button.getAttribute('data-confirm');

                if (confirm && !window.confirm(confirm)) {
                    return;
                }

                switch (action) {
                    case 'delete':
                        this.handleDeleteAction(target, button);
                        break;
                    case 'export':
                        this.handleExportAction(target, button);
                        break;
                    case 'print':
                        this.handlePrintAction(target, button);
                        break;
                    default:
                        console.warn('Unknown action:', action);
                }
            }

            handleDeleteAction(target, button) {
                if (!target) return;

                fetch(target, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': window.COREGRE.csrfToken
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert(data.message || 'Elemento eliminato', 'success');
                            // Ricarica la pagina o rimuovi l'elemento
                            if (data.reload) {
                                location.reload();
                            } else {
                                const row = button.closest('tr, .card, .item');
                                if (row) row.remove();
                            }
                        } else {
                            showAlert(data.message || 'Errore durante l\'eliminazione', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Delete error:', error);
                        showAlert('Errore durante l\'eliminazione', 'error');
                    });
            }

            handleExportAction(target, button) {
                if (!target) return;
                window.open(target, '_blank');
            }

            handlePrintAction(target, button) {
                if (!target) return;
                window.open(target, '_blank');
            }

            updateActiveNavigation(url) {
                // Rimuove tutti gli stati attivi
                document.querySelectorAll('.nav-active, .sidebar-active').forEach(el => {
                    el.classList.remove('nav-active', 'sidebar-active');
                });

                // Trova e attiva il link corrispondente
                const currentLink = document.querySelector(`a[href="${url}"], a[href="${url.replace(window.COREGRE.baseUrl, '')}"]`);
                if (currentLink) {
                    currentLink.classList.add('nav-active');

                    // Attiva anche il parent se è un dropdown
                    const parentDropdown = currentLink.closest('.dropdown-content');
                    if (parentDropdown) {
                        const trigger = parentDropdown.previousElementSibling;
                        if (trigger) trigger.classList.add('sidebar-active');
                    }
                }
            }

            showLoading() {
                if (this.mainContent) {
                    // Forza il reflow per evitare stuttering
                    this.mainContent.offsetHeight;

                    this.mainContent.style.willChange = 'transform, opacity, filter';
                    // Fade-out veloce per completare prima del cambio contenuto
                    this.mainContent.style.transition = 'all 0.15s cubic-bezier(0.4, 0, 1, 1)';
                    this.mainContent.style.opacity = '0';
                    this.mainContent.style.transform = 'translateY(8px) scale(0.99)';
                    this.mainContent.style.filter = 'blur(1px)';
                }
                document.body.classList.add('pjax-loading');
                this.showProgressBar();
            }

            hideLoading() {
                if (this.mainContent) {
                    // Piccolo delay per assicurarsi che il contenuto sia cambiato
                    setTimeout(() => {
                        requestAnimationFrame(() => {
                            // Fade-in più lento e fluido
                            this.mainContent.style.transition = 'all 0.4s cubic-bezier(0.23, 1, 0.32, 1)';
                            this.mainContent.style.opacity = '1';
                            this.mainContent.style.transform = 'translateY(0px) scale(1)';
                            this.mainContent.style.filter = 'blur(0px)';

                            // Rimuovi will-change dopo l'animazione
                            setTimeout(() => {
                                this.mainContent.style.willChange = 'auto';
                            }, 400);
                        });
                    }, 30);
                }
                document.body.classList.remove('pjax-loading');
                this.hideProgressBar();
            }

            showProgressBar() {
                const progressBar = document.getElementById('pjax-progress');
                const progressFill = progressBar?.querySelector('div');

                if (progressBar && progressFill) {
                    progressBar.style.opacity = '1';
                    progressFill.style.width = '0%';

                    // Animazione del progress bar
                    let progress = 0;
                    const interval = setInterval(() => {
                        progress += Math.random() * 30;
                        if (progress > 90) {
                            progress = 90;
                            clearInterval(interval);
                        }
                        progressFill.style.width = progress + '%';
                    }, 100);

                    this.progressInterval = interval;
                }
            }

            hideProgressBar() {
                const progressBar = document.getElementById('pjax-progress');
                const progressFill = progressBar?.querySelector('div');

                if (this.progressInterval) {
                    clearInterval(this.progressInterval);
                }

                if (progressBar && progressFill) {
                    // Completa la barra velocemente
                    progressFill.style.width = '100%';

                    // Nasconde dopo un breve delay
                    setTimeout(() => {
                        progressBar.style.opacity = '0';
                        setTimeout(() => {
                            progressFill.style.width = '0%';
                        }, 300);
                    }, 200);
                }
            }

        }

        // Sistema di re-inizializzazione globale per le pagine
        window.COREGRE.pageInitializers = [];

        // Funzione per registrare initializzatori di pagina
        window.COREGRE.onPageLoad = function (callback) {
            if (typeof callback === 'function') {
                window.COREGRE.pageInitializers.push(callback);
            }
        };

        // Funzione per eseguire tutti gli initializzatori registrati
        window.COREGRE.runPageInitializers = function () {
            window.COREGRE.pageInitializers.forEach(callback => {
                try {
                    callback();
                } catch (e) {
                    console.warn('Page initializer error:', e);
                }
            });
        };

        // Event listener per il custom event del PJAX
        document.addEventListener('pjax:contentLoaded', (e) => {
            window.COREGRE.runPageInitializers();
            window.COREGRE.animateNewContent();
        });

        // Funzione per animare i nuovi contenuti caricati
        window.COREGRE.animateNewContent = function() {
            const mainContent = document.getElementById('main-content');
            if (mainContent) {
                // Aggiunge classe di animazione
                mainContent.classList.add('content-animate');

                // Anima singoli elementi con requestAnimationFrame
                const cards = mainContent.querySelectorAll('.card, .widget, .table-responsive, .form-container');

                // Prepara tutti gli elementi
                cards.forEach((card) => {
                    card.style.willChange = 'transform, opacity';
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(15px)';
                });

                // Anima con requestAnimationFrame per smoother performance
                cards.forEach((card, index) => {
                    const delay = index * 30 + 100; // Parte dopo che il fade-in principale è iniziato

                    setTimeout(() => {
                        requestAnimationFrame(() => {
                            card.style.transition = 'all 0.4s cubic-bezier(0.23, 1, 0.32, 1)';
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';

                            // Cleanup will-change
                            setTimeout(() => {
                                card.style.willChange = 'auto';
                            }, 400);
                        });
                    }, delay);
                });

                // Rimuove la classe dopo l'animazione
                setTimeout(() => {
                    mainContent.classList.remove('content-animate');
                }, 600);
            }
        };

        // Global Search - JavaScript Vanilla per persistenza PJAX
        window.GlobalSearch = {
            query: '',
            results: [],
            loading: false,
            showResults: false,
            searchInput: null,
            searchButton: null,
            resultsContainer: null,
            debounceTimer: null,

            init() {
                this.bindElements();
                this.attachEventListeners();
            },

            bindElements() {
                this.searchInput = document.querySelector('input[placeholder*="Cerca cartellini"]');
                this.searchButton = document.querySelector('button[type="button"] i.fa-search')?.parentElement;
                this.resultsContainer = document.querySelector('.absolute.top-full.mt-2');
            },

            attachEventListeners() {
                if (!this.searchInput) return;

                // Input event con debounce
                this.searchInput.addEventListener('input', (e) => {
                    this.query = e.target.value;
                    this.debounceSearch();
                });

                // Enter key
                this.searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.performSearch();
                    }
                });

                // Focus event
                this.searchInput.addEventListener('focus', () => {
                    if (this.results.length > 0 || this.query.length >= 2) {
                        this.showResults = true;
                        this.updateResultsDisplay();
                    }
                });

                // Click button
                if (this.searchButton) {
                    this.searchButton.addEventListener('click', () => {
                        this.performSearch();
                    });
                }

                // Click outside to hide results
                document.addEventListener('click', (e) => {
                    if (!this.searchInput?.contains(e.target) && !this.resultsContainer?.contains(e.target)) {
                        this.showResults = false;
                        this.updateResultsDisplay();
                    }
                });
            },

            debounceSearch() {
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    this.performSearch();
                }, 300);
            },

            async performSearch() {
                if (this.query.length < 2) {
                    this.results = [];
                    this.showResults = false;
                    this.updateResultsDisplay();
                    return;
                }

                this.loading = true;
                this.showResults = true;
                this.updateResultsDisplay();

                try {
                    const response = await fetch(`<?= $this->url('/api/search') ?>?q=${encodeURIComponent(this.query)}`);
                    const data = await response.json();

                    if (data.success) {
                        this.results = data.results;
                    } else {
                        this.results = [];
                        console.error('Search error:', data.error);
                    }
                } catch (error) {
                    console.error('Search request failed:', error);
                    this.results = [];
                } finally {
                    this.loading = false;
                    this.updateResultsDisplay();
                }
            },

            updateResultsDisplay() {
                if (!this.resultsContainer) return;

                if (!this.showResults || (this.results.length === 0 && !this.loading && this.query.length < 2)) {
                    this.resultsContainer.style.display = 'none';
                    return;
                }

                this.resultsContainer.style.display = 'block';

                let html = '';

                if (this.loading) {
                    html = `
                        <div class="p-4 text-center">
                            <i class="fas fa-spinner fa-spin text-blue-500"></i>
                            <span class="ml-2 text-gray-600 dark:text-gray-400">Ricerca in corso...</span>
                        </div>
                    `;
                } else if (this.query.length >= 2 && this.results.length === 0) {
                    html = `
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-search text-2xl mb-2"></i>
                            <p>Nessun risultato per "${this.query}"</p>
                        </div>
                    `;
                } else if (this.results.length > 0) {
                    html = '<div class="py-2">';
                    this.results.forEach(result => {
                        html += `
                            <a href="${result.url}" onclick="window.GlobalSearch.hideResults()"
                               class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                        <i class="${result.icon} text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${result.title}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${result.subtitle}</p>
                                </div>
                                <div class="ml-3 flex-shrink-0">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">${result.category}</span>
                                </div>
                            </a>
                        `;
                    });

                    html += `</div>
                        <div class="border-t border-gray-200 dark:border-gray-700 p-2">
                            <a href="<?= $this->url('/search') ?>?q=${encodeURIComponent(this.query)}"
                               onclick="window.GlobalSearch.hideResults()"
                               class="block w-full text-center py-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                Vedi tutti i risultati
                            </a>
                        </div>
                    `;
                }

                this.resultsContainer.innerHTML = html;
            },

            hideResults() {
                this.showResults = false;
                this.updateResultsDisplay();
            }
        };

        // Funzione per inizializzare la ricerca globale
        function initializeGlobalSearch() {
            if (window.GlobalSearch) {
                window.GlobalSearch.init();
            }
        }

        // Registra l'inizializzazione come page initializer
        window.COREGRE.onPageLoad(initializeGlobalSearch);

        // Inizializza PJAX quando il DOM è pronto
        if (window.COREGRE.user.isAuthenticated) {
            document.addEventListener('DOMContentLoaded', () => {
                window.pjax = new CoregrePjax();
                // Esegui inizializzatori anche al primo caricamento
                window.COREGRE.runPageInitializers();
                // Anima il contenuto iniziale
                setTimeout(() => window.COREGRE.animateNewContent(), 100);
            });
        }
    </script>

    <!-- CSS Custom per animazioni -->
    <style>
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        body.loading {
            cursor: wait;
        }

        body.loading * {
            pointer-events: none;
        }

        /* PJAX Loading States */
        body.pjax-loading {
            cursor: progress;
        }

        #main-content {
            transition: all 0.15s cubic-bezier(0.4, 0, 1, 1);
            transform-origin: center top;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transform: translateZ(0);
            will-change: auto;
        }

        body.pjax-loading #main-content {
            pointer-events: none;
        }

        /* Animazioni avanzate per i contenuti */
        .content-animate {
            animation: contentFadeIn 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        @keyframes contentFadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
                filter: blur(5px);
            }
            50% {
                opacity: 0.7;
                transform: translateY(10px) scale(0.98);
                filter: blur(2px);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }

        /* Effetto smooth per cards e widgets */
        .card, .widget, .table-responsive, .form-container {
            transition: all 0.35s cubic-bezier(0.23, 1, 0.32, 1);
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transform: translateZ(0);
        }

        body.pjax-loading .card,
        body.pjax-loading .widget,
        body.pjax-loading .table-responsive,
        body.pjax-loading .form-container {
            transform: translateY(3px) scale(0.99);
            opacity: 0.4;
        }

        /* PJAX Progress Bar Animation */
        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }
            100% {
                background-position: 200% 0;
            }
        }

        #pjax-progress div {
            background: linear-gradient(90deg, #3b82f6, #8b5cf6, #3b82f6);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        /* Hidden Scrollbar */
        .scrollbar-hidden {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* Internet Explorer 10+ */
        }

        .scrollbar-hidden::-webkit-scrollbar {
            width: 0;
            height: 0;
            display: none; /* Safari and Chrome */
        }

        /* Alert Styles */
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .alert-success {
            color: #065f46;
            background-color: #d1fae5;
            border-color: #a7f3d0;
        }

        .alert-error {
            color: #991b1b;
            background-color: #fee2e2;
            border-color: #fecaca;
        }

        .alert-warning {
            color: #92400e;
            background-color: #fef3c7;
            border-color: #fde68a;
        }

        .alert-info {
            color: #0c4a6e;
            background-color: #e0f2fe;
            border-color: #bae6fd;
        }

        /* Dark mode alert styles */
        .dark .alert-success {
            color: #a7f3d0;
            background-color: #064e3b;
            border-color: #065f46;
        }

        .dark .alert-error {
            color: #fecaca;
            background-color: #7f1d1d;
            border-color: #991b1b;
        }

        .dark .alert-warning {
            color: #fde68a;
            background-color: #78350f;
            border-color: #92400e;
        }

        .dark .alert-info {
            color: #bae6fd;
            background-color: #0c4a6e;
            border-color: #0369a1;
        }

        /* FORZA sidebar collapsed - quando w-16 su desktop */
        .sidebar-collapsed {
            width: 4rem !important;
            min-width: 4rem !important;
            max-width: 4rem !important;
        }

        /* Nascondi tutto il testo quando collassata */
        .sidebar-collapsed .sidebar-text {
            display: none !important;
        }

        /* Centra tutto quando collassata */
        .sidebar-collapsed .sidebar-item {
            justify-content: center !important;
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        /* Dropdown laterali quando sidebar collassata */
        .sidebar-collapsed .dropdown-menu {
            position: fixed !important;
            left: 4rem !important;
            z-index: 9999 !important;
            min-width: 200px !important;
            background: white !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1) !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Titolo nei dropdown laterali */
        .sidebar-collapsed .dropdown-menu::before {
            content: attr(data-title) !important;
            display: block !important;
            padding: 0.75rem 1rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: #374151 !important;
            border-bottom: 1px solid #e5e7eb !important;
            margin-bottom: 0.5rem !important;
            background: #f9fafb !important;
            border-radius: 0.75rem 0.75rem 0 0 !important;
        }

        /* Contenuto dropdown con padding per il titolo */
        .sidebar-collapsed .dropdown-menu>div {
            padding-top: 0.5rem !important;
            margin: 0.5rem !important;
        }

        .dark .sidebar-collapsed .dropdown-menu {
            background: #1f2937 !important;
            border-color: #374151 !important;
        }

        .dark .sidebar-collapsed .dropdown-menu::before {
            color: #d1d5db !important;
            border-bottom-color: #374151 !important;
            background: #111827 !important;
        }


        @media (prefers-reduced-motion: reduce) {
            .animate-fade-in {
                animation: none;
            }
        }
    </style>

    <!-- Custom scripts -->
    <?php if (isset($customScripts)): ?>
        <?php foreach ($customScripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Universal Components -->
    <?php include APP_ROOT . '/app/views/components/notifications.php'; ?>
    <?php include APP_ROOT . '/app/views/components/modals.php'; ?>

    <!-- Page specific scripts -->
    <?php if (isset($pageScripts)): ?>
        <script>
            <?= $pageScripts ?>
        </script>
    <?php endif; ?>
</body>

</html>