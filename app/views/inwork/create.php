<!-- Header -->
<div class="mb-8">
    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
        Nuovo Operatore Mobile
    </h1>
    <p class="mt-2 text-gray-600 dark:text-gray-400">
        Crea un nuovo operatore per l'app CoreInWork
    </p>
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
                <a href="<?= $this->url('/inwork-admin') ?>"
                    class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    Operatori InWork
                </a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                    Nuovo Operatore
                </span>
            </div>
        </li>
    </ol>
</nav>

<!-- Form -->
<form method="POST" action="<?= $this->url('/inwork-admin/store') ?>" class="space-y-6">
    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">

    <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <!-- Header Card -->
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-user-circle mr-2"></i>
                Dati Operatore
            </h2>
        </div>

        <div class="p-6 space-y-6">
            <!-- Grid 2 colonne -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Username -->
                <div>
                    <label for="user" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="user" id="user" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 transition-all duration-200"
                        placeholder="es: mario.rossi">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Username univoco per il login</p>
                </div>

                <!-- Nome Completo -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nome Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="full_name" id="full_name" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 transition-all duration-200"
                        placeholder="es: Mario Rossi">
                </div>

                <!-- PIN -->
                <div>
                    <label for="pin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        PIN (4-6 cifre) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="pin" id="pin" required min="1000" max="999999"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 transition-all duration-200"
                        placeholder="es: 1234">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">PIN numerico per accesso mobile</p>
                </div>

                <!-- Reparto -->
                <div>
                    <label for="reparto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reparto <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="reparto" id="reparto" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 transition-all duration-200"
                        placeholder="es: Produzione">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email <span class="text-gray-400">(opzionale)</span>
                    </label>
                    <input type="email" name="email" id="email"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 transition-all duration-200"
                        placeholder="mario.rossi@example.com">
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Telefono <span class="text-gray-400">(opzionale)</span>
                    </label>
                    <input type="tel" name="phone" id="phone"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 transition-all duration-200"
                        placeholder="+39 123 4567890">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Note <span class="text-gray-400">(opzionale)</span>
                </label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 transition-all duration-200"
                    placeholder="Note amministrative..."></textarea>
            </div>
        </div>
    </div>

    <!-- Permessi Moduli -->
    <div class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-mobile-alt mr-2"></i>
                Moduli Abilitati
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Seleziona i moduli a cui l'operatore può accedere dall'app mobile
            </p>
        </div>

        <div class="p-6 space-y-4">
            <?php foreach ($modules as $moduleId => $moduleName): ?>
                <div class="flex items-center rounded-lg border border-gray-200 p-4 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/40 transition-colors duration-150">
                    <input type="checkbox" name="enabled_modules[]" value="<?= $moduleId ?>"
                        id="module_<?= $moduleId ?>" checked
                        class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800">
                    <label for="module_<?= $moduleId ?>" class="ml-3 flex-1 cursor-pointer">
                        <div class="flex items-center">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg <?= $moduleId === 'quality' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-amber-100 dark:bg-amber-900/30' ?> mr-3">
                                <i class="fas <?= $moduleId === 'quality' ? 'fa-clipboard-check text-green-600 dark:text-green-400' : 'fa-tools text-amber-600 dark:text-amber-400' ?>"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?= $moduleName ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <?= $moduleId === 'quality' ? 'Controllo Qualità e gestione difetti' : 'Riparazioni Interne e tracciamento' ?>
                                </p>
                            </div>
                        </div>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Azioni -->
    <div class="flex items-center justify-end gap-3">
        <a href="<?= $this->url('/inwork-admin') ?>"
            class="rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-150">
            <i class="fas fa-times mr-2"></i>
            Annulla
        </a>
        <button type="submit"
            class="rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
            <i class="fas fa-save mr-2"></i>
            Salva Operatore
        </button>
    </div>
</form>
