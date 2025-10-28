<?php
/**
 * SCM Admin - Form Laboratorio (Crea/Modifica)
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                <?= $isEdit ? 'Modifica' : 'Nuovo' ?> Laboratorio
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                <?= $isEdit ? 'Aggiorna i dati del laboratorio terzista' : 'Registra un nuovo laboratorio terzista nel sistema' ?>
            </p>
        </div>
        <a href="<?= $this->url('/scm-admin/laboratories') ?>" 
           class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Torna alla Lista
        </a>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
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
                <a href="<?= $this->url('/scm-admin') ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    SCM Admin
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/scm-admin/laboratories') ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    Laboratori
                </a>
            </div>
        </li>
        <?php if ($isEdit): ?>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Modifica <?= htmlspecialchars($laboratory['name']) ?></span>
            </div>
        </li>
        <?php else: ?>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nuovo Laboratorio</span>
            </div>
        </li>
        <?php endif; ?>
    </ol>
</nav>

<!-- Form -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Form -->
    <div class="lg:col-span-2">
        <form method="POST" action="<?= $isEdit ? $this->url('/scm-admin/laboratories/' . $laboratory['id'] . '/update') : $this->url('/scm-admin/laboratories/store') ?>" 
              class="space-y-8">
            
            <!-- Informazioni Base -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="flex items-center mb-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                        <i class="fas fa-building text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informazioni Laboratorio</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Dati identificativi del laboratorio terzista</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nome Laboratorio *
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="<?= htmlspecialchars($laboratory['name'] ?? '') ?>"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Es: Laboratorio Rossi S.r.l.">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Ragione sociale o nome commerciale del laboratorio
                        </p>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email *
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?= htmlspecialchars($laboratory['email'] ?? '') ?>"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                               placeholder="info@laboratorioresci.com">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Email per comunicazioni e notifiche
                        </p>
                    </div>
                </div>
            </div>

            <!-- Credenziali Accesso -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="flex items-center mb-6">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                        <i class="fas fa-key text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Credenziali di Accesso</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Username e password per accedere al frontend SCM</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Username *
                        </label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               value="<?= htmlspecialchars($laboratory['username'] ?? '') ?>"
                               required
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                               placeholder="laboratorio_rossi">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Username univoco per il login (solo lettere, numeri e underscore)
                        </p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Password <?= $isEdit ? '' : '*' ?>
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               <?= $isEdit ? '' : 'required' ?>
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                               placeholder="<?= $isEdit ? 'Lascia vuoto per mantenere la password attuale' : 'Password sicura' ?>">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <?= $isEdit ? 'Lascia vuoto se non vuoi modificare la password' : 'Minimo 8 caratteri, consigliato mix di lettere, numeri e simboli' ?>
                        </p>
                    </div>
                </div>

                <?php if ($isEdit): ?>
                    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                    Modifica Password
                                </h4>
                                <p class="text-sm text-blue-700 dark:text-blue-200">
                                    Se modifichi la password, comunica le nuove credenziali al laboratorio. 
                                    Il laboratorio dovrà usare le nuove credenziali al prossimo login.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="<?= $this->url('/scm-admin/laboratories') ?>" 
                   class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Annulla
                </a>
                <button type="submit" 
                        class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i>
                    <?= $isEdit ? 'Aggiorna Laboratorio' : 'Crea Laboratorio' ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-6">
        <!-- Info Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                Informazioni
            </h3>
            <div class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3 text-xs"></i>
                    <p>Il laboratorio potrà accedere al frontend SCM con le credenziali fornite</p>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3 text-xs"></i>
                    <p>Username ed email devono essere univoci nel sistema</p>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3 text-xs"></i>
                    <p>I laboratori inattivi non possono accedere al sistema</p>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3 text-xs"></i>
                    <p>Le password sono crittografate in modo sicuro</p>
                </div>
            </div>
        </div>

        <?php if ($isEdit): ?>
            <!-- Statistics Card -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class="fas fa-chart-bar mr-2 text-purple-500"></i>
                    Statistiche
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Creato il</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            <?= date('d/m/Y', strtotime($laboratory['created_at'])) ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Ultimo aggiornamento</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            <?= date('d/m/Y H:i', strtotime($laboratory['updated_at'])) ?>
                        </span>
                    </div>
                    <?php if ($laboratory['last_login']): ?>
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Ultimo accesso</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                <?= date('d/m/Y H:i', strtotime($laboratory['last_login'])) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Security Tips -->
        <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-6 shadow-lg dark:border-yellow-800 dark:bg-yellow-900/20">
            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-4 flex items-center">
                <i class="fas fa-shield-alt mr-2 text-yellow-600"></i>
                Sicurezza
            </h3>
            <div class="space-y-3 text-sm text-yellow-800 dark:text-yellow-200">
                <p>• Usa password complesse (min. 8 caratteri)</p>
                <p>• Username deve essere univoco</p>
                <p>• Email deve essere valida e raggiungibile</p>
                <p>• Verifica periodicamente gli accessi</p>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const usernameInput = document.getElementById('username');
    
    // Username validation
    usernameInput.addEventListener('input', function() {
        const value = this.value;
        const regex = /^[a-zA-Z0-9_]+$/;
        
        if (value && !regex.test(value)) {
            this.setCustomValidity('Username può contenere solo lettere, numeri e underscore');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
        
        if (!isEdit && password.length < 8) {
            e.preventDefault();
            WebgreNotifications.error('La password deve essere di almeno 8 caratteri');
            return false;
        }
        
        return true;
    });
});
</script>