<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-user-circle mr-3 text-blue-500"></i>
                Profilo Utente
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Gestisci le tue informazioni personali e le impostazioni dell'account
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                <i class="fas fa-shield-alt mr-1"></i>
                Sicuro
            </span>
        </div>
    </div>
    
    <!-- Breadcrumb -->
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
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Profilo</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Profile Info Card -->
    <div class="lg:col-span-1">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 text-center">
                <div class="mx-auto h-24 w-24 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg mb-4">
                    <?= strtoupper(substr($user->nome ?? 'U', 0, 1)) ?>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($user->nome ?? '') ?></h3>
                <p class="text-gray-600 dark:text-gray-400"><?= htmlspecialchars($user->mail ?? '') ?></p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">
                    <i class="fas fa-user-tag mr-1"></i>
                    <?= htmlspecialchars($user->user_name ?? '') ?>
                </p>
                
                <!-- User Stats -->
                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="text-center p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                        <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                            <?= htmlspecialchars($user->admin_type ?? 'USER') ?>
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Tipo Account</div>
                    </div>
                    <div class="text-center p-3 rounded-lg bg-green-50 dark:bg-green-900/20">
                        <div class="text-lg font-bold text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Attivo</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Profile Information Form -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-edit mr-3 text-blue-500"></i>
                    Informazioni Personali
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Aggiorna le tue informazioni di base
                </p>
            </div>
            <div class="p-6">
                <form action="<?= $this->url('/profile/update') ?>" method="POST" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-user mr-1 text-blue-500"></i>
                                Nome Completo *
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="<?= htmlspecialchars($user->nome ?? '') ?>"
                                required
                                class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                placeholder="Inserisci il tuo nome completo"
                            >
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-envelope mr-1 text-green-500"></i>
                                Email *
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="<?= htmlspecialchars($user->mail ?? '') ?>"
                                required
                                class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                placeholder="Inserisci la tua email"
                            >
                        </div>

                        <!-- Username (readonly) -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-at mr-1 text-purple-500"></i>
                                Username
                            </label>
                            <input 
                                type="text" 
                                id="username" 
                                value="<?= htmlspecialchars($user->user_name ?? '') ?>"
                                readonly
                                class="w-full px-4 py-3 rounded-lg bg-gray-100 dark:bg-gray-600 border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 cursor-not-allowed"
                            >
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Il username non può essere modificato
                            </p>
                        </div>

                        <!-- Theme Preference -->
                        <div>
                            <label for="theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-palette mr-1 text-orange-500"></i>
                                Tema
                            </label>
                            <select 
                                id="theme" 
                                name="theme"
                                class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                onchange="updateTheme(this.value)"
                            >
                                <option value="auto" <?= ($user->theme_color ?? 'auto') === 'auto' ? 'selected' : '' ?>>
                                    <i class="fas fa-magic"></i> Automatico
                                </option>
                                <option value="light" <?= ($user->theme_color ?? '') === 'light' ? 'selected' : '' ?>>
                                    <i class="fas fa-sun"></i> Chiaro
                                </option>
                                <option value="dark" <?= ($user->theme_color ?? '') === 'dark' ? 'selected' : '' ?>>
                                    <i class="fas fa-moon"></i> Scuro
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button 
                            type="submit"
                            class="inline-flex items-center px-6 py-3 rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
                        >
                            <i class="fas fa-save mr-2"></i>
                            Salva Modifiche
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Password Change Form -->
        <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-lock mr-3 text-red-500"></i>
                    Cambia Password
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Aggiorna la tua password per mantenere l'account sicuro
                </p>
            </div>
            <div class="p-6">
                <form action="<?= $this->url('/profile/change-password') ?>" method="POST" class="space-y-6">
                    <input type="hidden" name="csrf_token_password" value="<?= $this->generateCsrfToken() ?>">
                    
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-key mr-1 text-gray-500"></i>
                            Password Attuale *
                        </label>
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            required
                            class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                            placeholder="Inserisci la password attuale"
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- New Password -->
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-lock mr-1 text-green-500"></i>
                                Nuova Password *
                            </label>
                            <input 
                                type="password" 
                                id="new_password" 
                                name="new_password" 
                                required
                                minlength="6"
                                class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                placeholder="Inserisci la nuova password"
                            >
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-lock mr-1 text-blue-500"></i>
                                Conferma Password *
                            </label>
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                required
                                minlength="6"
                                class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                                placeholder="Conferma la nuova password"
                            >
                        </div>
                    </div>

                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                    Requisiti password
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Almeno 6 caratteri</li>
                                        <li>Combinazione di lettere e numeri consigliata</li>
                                        <li>Evita informazioni personali facilmente indovinabili</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button 
                            type="submit"
                            class="inline-flex items-center px-6 py-3 rounded-lg bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
                        >
                            <i class="fas fa-key mr-2"></i>
                            Cambia Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Theme update function
async function updateTheme(theme) {
    try {
        const response = await fetch('<?= $this->url('/profile/update-theme') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `theme=${theme}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Apply theme immediately if possible
            if (window.applyTheme) {
                window.applyTheme(theme);
            }
            
            // Show success message
            CoregreNotifications.success('Tema aggiornato con successo');
        } else {
            console.error('Error updating theme:', result.message);
        }
    } catch (error) {
        console.error('Error updating theme:', error);
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const passwordForm = document.querySelector('form[action*="change-password"]');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                CoregreNotifications.error('Le password non coincidono');
            }
        });
    }
});
</script>