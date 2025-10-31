<div>
    <!-- Logo per mobile -->
    <div class="lg:hidden text-center mb-8">
        <img class="h-12 mx-auto mb-4" src="<?= $this->url('assets/logo.png') ?>" alt="COREGRE">
        <h2 class="text-2xl font-bold text-gray-900"><?= APP_NAME ?></h2>
    </div>

    <!-- Header -->
    <div>
        <h2 class="mt-6 text-3xl font-bold text-gray-900">
            Accesso
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Inserisci le tue credenziali per accedere al sistema
        </p>
    </div>

    <!-- Alerts -->
    <div class="mt-6">
        <?php include VIEW_PATH . '/components/alerts.php'; ?>
    </div>

    <!-- Form -->
    <div class="mt-6">
        <form class="space-y-6" method="POST" action="<?= $this->url('/login') ?>" id="loginForm">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <!-- Username -->
            <div>
                <label for="username" class="form-label">
                    <i class="fas fa-user mr-2"></i>
                    Username
                </label>
                <div class="mt-1 relative">
                    <input id="username" name="username" type="text" autocomplete="username" required
                        class="form-input pr-10" placeholder="Inserisci il tuo username">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="form-label">
                    <i class="fas fa-lock mr-2"></i>
                    Password
                </label>
                <div class="mt-1 relative">
                    <input id="password" name="passwd" type="password" autocomplete="current-password" required
                        class="form-input pr-10" placeholder="Inserisci la tua password">
                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        onclick="togglePassword()">
                        <i id="passwordIcon" class="fas fa-eye text-gray-400"></i>
                    </button>
                </div>
            </div>

            <!-- Remember me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="form-checkbox">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Ricordami su questo dispositivo
                    </label>
                </div>
            </div>

            <!-- Submit button -->
            <div>
                <button type="submit" class="btn btn-primary bg-orange-400 hover:bg-orange-500 w-full py-3 text-base"
                    id="loginButton">
                    <span id="loginButtonText">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Accedi
                    </span>
                    <span id="loginButtonLoading" class="hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Accesso in corso...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Footer info -->
    <div class="mt-8 text-center">
        <div class="text-xs text-gray-500 space-y-1">
            <p>© <?= date('Y') ?> <?= APP_NAME ?> - Versione <?= APP_VERSION ?></p>
            <p>Per supporto contattare l'amministratore di sistema</p>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const passwordIcon = document.getElementById('passwordIcon');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            passwordIcon.classList.remove('fa-eye');
            passwordIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            passwordIcon.classList.remove('fa-eye-slash');
            passwordIcon.classList.add('fa-eye');
        }
    }

    // Form submission handling
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        const loginButton = document.getElementById('loginButton');
        const loginButtonText = document.getElementById('loginButtonText');
        const loginButtonLoading = document.getElementById('loginButtonLoading');

        // Disable button and show loading
        loginButton.disabled = true;
        loginButton.classList.add('opacity-75', 'cursor-not-allowed');
        loginButtonText.classList.add('hidden');
        loginButtonLoading.classList.remove('hidden');
    });

    // Auto-focus su username al caricamento
    document.addEventListener('DOMContentLoaded', function () {
        const usernameField = document.getElementById('username');
        if (usernameField) {
            usernameField.focus();
        }
    });

    // Enter key handling
    document.getElementById('username').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            document.getElementById('password').focus();
        }
    });

    // Validation client-side
    function validateForm() {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;

        if (username === '') {
            showAlert('Username è obbligatorio', 'error');
            return false;
        }

        if (password === '') {
            showAlert('Password è obbligatoria', 'error');
            return false;
        }

        return true;
    }

    // Enhanced form submission with validation
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
    });
</script>