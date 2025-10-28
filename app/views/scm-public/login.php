<!DOCTYPE html>
<html lang="it" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCM Terzisti - Login | <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .glass-morphism {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="h-full">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <div
                    class="mx-auto h-20 w-20 flex items-center justify-center rounded-full bg-white/20 backdrop-blur-sm mb-6">
                    <i class="fas fa-industry text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-white">
                    SCM Terzisti
                </h2>
                <p class="mt-2 text-lg text-white/90">
                    Emmegiemme - Accesso Laboratori
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="glass-morphism rounded-2xl px-8 py-10 shadow-2xl">
                <?php if (isset($_SESSION['scm_login_error'])): ?>
                    <div class="mb-6 bg-red-500/90 backdrop-blur-sm border border-red-300 text-white px-4 py-3 rounded-xl">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle mr-3"></i>
                            <?= htmlspecialchars($_SESSION['scm_login_error']) ?>
                        </div>
                    </div>
                    <?php unset($_SESSION['scm_login_error']); ?>
                <?php endif; ?>

                <form method="POST" action="<?= $this->url('/scm/login') ?>" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-white/90 mb-2">
                            <i class="fas fa-user mr-2"></i>Username
                        </label>
                        <input id="username" name="username" type="text" required
                            value="<?= htmlspecialchars($_SESSION['scm_login_username'] ?? '') ?>"
                            class="block w-full px-4 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all"
                            placeholder="Inserisci il tuo username">
                        <?php unset($_SESSION['scm_login_username']); ?>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-white/90 mb-2">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <input id="password" name="password" type="password" required
                            class="block w-full px-4 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all"
                            placeholder="Inserisci la tua password">
                    </div>

                    <div>
                        <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-xl text-white bg-white/20 hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white/50 transition-all duration-200 transform hover:-translate-y-1 hover:shadow-xl backdrop-blur-sm">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt group-hover:text-white/90"></i>
                            </span>
                            <span class="font-medium">Accedi alla Dashboard</span>
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-white/20 text-center">
                    <p class="text-sm text-white/70">
                        <i class="fas fa-info-circle mr-1"></i>
                        Per problemi di accesso contattare l'amministrazione
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-white/60 text-sm">
                © <?= date('Y') ?> <?= APP_NAME ?>. Supply Chain Management System.
            </p>
        </div>
    </div>
</body>

</html>