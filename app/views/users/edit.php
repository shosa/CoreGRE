<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                Modifica Utente
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Modifica le informazioni di <?= htmlspecialchars($user->nome) ?>
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="<?= $this->url('/users/' . $user->id . '/permissions') ?>"
                class="inline-flex items-center rounded-lg border border-purple-300 bg-white px-4 py-2 text-sm font-medium text-purple-700 hover:bg-purple-50 dark:border-purple-600 dark:bg-gray-800 dark:text-purple-300 dark:hover:bg-purple-900/20 transition-colors">
                <i class="fas fa-user-cog mr-2"></i>
                Gestisci Permessi
            </a>
            <a href="<?= $this->url('/users') ?>"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alla Lista
            </a>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-8" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/') ?>"
                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/users') ?>"
                    class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    Utenti
                </a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                    Modifica <?= htmlspecialchars($user->nome) ?>
                </span>
            </div>
        </li>
    </ol>
</nav>
<!-- Form -->
<div
    class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-user-edit mr-3 text-blue-500"></i>
            Modifica Informazioni Utente
        </h3>
    </div>

    <form method="POST" action="<?= $this->url('/users/update') ?>" id="editUserForm" class="p-6">
        <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
        <input type="hidden" name="id" value="<?= $user->id ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Username -->
            <div>
                <label for="user_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Username <span class="text-red-500">*</span>
                </label>
                <input type="text" id="user_name" name="user_name" required
                    value="<?= htmlspecialchars($user->user_name) ?>"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Username univoco per l'accesso al sistema</p>
            </div>

            <!-- Nome Completo -->
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nome Completo <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($user->nome) ?>"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" required
                    value="<?= htmlspecialchars($user->mail ?: '') ?>"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Ruolo -->
            <div>
                <label for="admin_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Ruolo <span class="text-red-500">*</span>
                </label>
                <select id="admin_type" name="admin_type" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <?php foreach ($adminTypes as $type => $label): ?>
                        <option value="<?= $type ?>" <?= $user->admin_type === $type ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- User Info -->
        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">ID Utente:</span>
                    <span class="text-gray-600 dark:text-gray-400 ml-2">#<?= $user->id ?></span>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Creato il:</span>
                    <span class="text-gray-600 dark:text-gray-400 ml-2">
                        <?= $user->created_at ? date('d/m/Y H:i', strtotime($user->created_at)) : 'Non disponibile' ?>
                    </span>
                </div>
                <div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">Ultimo accesso:</span>
                    <span class="text-gray-600 dark:text-gray-400 ml-2">
                        <?= $user->last_login ? date('d/m/Y H:i', strtotime($user->last_login)) : 'Mai' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Password Section -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-key mr-2 text-orange-500"></i>
                Cambia Password (opzionale)
            </h4>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Lascia i campi vuoti se non vuoi modificare la password
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nuova Password
                    </label>
                    <div class="relative">
                        <input type="password" id="new_password" name="new_password" minlength="6"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                            placeholder="Inserisci nuova password">
                        <button type="button" onclick="togglePassword('new_password')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                id="new_password-icon"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimo 6 caratteri</p>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="confirm_new_password"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Conferma Nuova Password
                    </label>
                    <div class="relative">
                        <input type="password" id="confirm_new_password" name="confirm_new_password" minlength="6"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                            placeholder="Ripeti nuova password">
                        <button type="button" onclick="togglePassword('confirm_new_password')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                id="confirm_new_password-icon"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="<?= $this->url('/users') ?>"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>
                    Annulla
                </a>
                <button type="button" onclick="deleteUser()"
                    class="inline-flex items-center px-4 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 dark:bg-gray-700 dark:text-red-300 dark:border-red-600 dark:hover:bg-red-900/20">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Elimina Utente
                </button>
            </div>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-save mr-2"></i>
                Salva Modifiche
            </button>
        </div>
    </form>
</div>

<script>
    // Users Edit - JavaScript compatibile con PJAX
    (function () {
        // Variabili globali per cleanup
        let eventListeners = [];

        // Funzione di inizializzazione
        function initUsersEdit() {
            // Cleanup precedenti event listeners
            cleanupEventListeners();

            const form = document.getElementById('editUserForm');

            if (form) {
                function formSubmitHandler(e) {
                    // Validazione password se inserita
                    const newPassword = document.getElementById('new_password').value;
                    const confirmNewPassword = document.getElementById('confirm_new_password').value;

                    if (newPassword || confirmNewPassword) {
                        if (newPassword !== confirmNewPassword) {
                            e.preventDefault();
                            if (window.showAlert) {
                                window.showAlert('Le nuove password non coincidono', 'error');
                            } else {
                                CoregreNotifications.error('Le nuove password non coincidono');
                            }
                            return false;
                        }

                        if (newPassword.length < 6) {
                            e.preventDefault();
                            CoregreNotifications.error('La nuova password deve essere di almeno 6 caratteri');
                            return false;
                        }
                    }

                    // Validazione campi richiesti
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;

                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('border-red-500', 'focus:ring-red-500');
                        } else {
                            field.classList.remove('border-red-500', 'focus:ring-red-500');
                        }
                    });

                    if (!isValid) {
                        e.preventDefault();
                        CoregreNotifications.error('Per favore, compila tutti i campi obbligatori.');
                        return false;
                    }
                }

                form.addEventListener('submit', formSubmitHandler);
                eventListeners.push({ element: form, event: 'submit', handler: formSubmitHandler });
            }
        }

        function cleanupEventListeners() {
            eventListeners.forEach(({ element, event, handler }) => {
                element.removeEventListener(event, handler);
            });
            eventListeners = [];
        }

        // Funzione globale per mostrare/nascondere password
        window.togglePassword = function (fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');

            if (field && icon) {
                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        };

        // Funzione globale per eliminare utente
        window.deleteUser = function () {
            CoregreModals.confirmDelete(
                'Sei sicuro di voler eliminare questo utente? Questa azione non può essere annullata.',
                () => {
                    const loadingId = CoregreNotifications.loading('Eliminazione in corso...');

                    fetch('<?= $this->url('/users/delete') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                        },
                        body: JSON.stringify({ id: <?= $user->id ?> })
                    })
                        .then(response => response.json())
                        .then(data => {
                            CoregreNotifications.remove(loadingId);

                            if (data.success) {
                                CoregreNotifications.success('Utente eliminato con successo');
                                setTimeout(() => {
                                    if (window.pjax) {
                                        window.pjax.navigateTo('<?= $this->url('/users') ?>');
                                    } else {
                                        window.location.href = '<?= $this->url('/users') ?>';
                                    }
                                }, 1500);
                            } else {
                                CoregreNotifications.error(data.error || 'Errore durante l\'eliminazione');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            CoregreNotifications.remove(loadingId);
                            CoregreNotifications.error('Errore di rete durante l\'eliminazione');
                        });
                }
            );
        };

        // Registra l'inizializzatore per PJAX
        if (window.COREGRE && window.COREGRE.onPageLoad) {
            window.COREGRE.onPageLoad(initUsersEdit);
        }

        // Inizializza anche al primo caricamento
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initUsersEdit);
        } else {
            initUsersEdit();
        }
    })();
</script>