<?php
/**
 * SCM Admin - Impostazioni Sistema
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                Impostazioni SCM
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Configurazione generale del sistema Supply Chain Management
            </p>
        </div>
    </div>
</div>
<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
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
                <a href="<?= $this->url('/scm-admin') ?>"
                    class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    SCM Admin
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Impostazioni</span>
            </div>
        </li>
    </ol>
</nav>
<form method="POST" action="<?= $this->url('/scm-admin/settings/save') ?>">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Settings -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Configurazioni Generali -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="flex items-center mb-6">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 shadow-lg">
                        <i class="fas fa-cogs text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Configurazioni Generali</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Impostazioni base del sistema</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="system_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nome Sistema
                        </label>
                        <input type="text" id="system_name" name="system_name"
                            value="<?= htmlspecialchars($settings['system_name'] ?? 'SCM Emmegiemme') ?>"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Nome visualizzato nel sistema
                        </p>
                    </div>

                    <div>
                        <label for="company_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nome Azienda
                        </label>
                        <input type="text" id="company_name" name="company_name"
                            value="<?= htmlspecialchars($settings['company_name'] ?? 'Emmegiemme S.r.l.') ?>"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fuso Orario
                        </label>
                        <select id="timezone" name="timezone"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="Europe/Rome" <?= ($settings['timezone'] ?? 'Europe/Rome') === 'Europe/Rome' ? 'selected' : '' ?>>Europa/Roma</option>
                            <option value="Europe/London" <?= ($settings['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' ?>>Europa/Londra</option>
                            <option value="UTC" <?= ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' ?>>UTC
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Lingua Sistema
                        </label>
                        <select id="language" name="language"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="it" <?= ($settings['language'] ?? 'it') === 'it' ? 'selected' : '' ?>>Italiano
                            </option>
                            <option value="en" <?= ($settings['language'] ?? '') === 'en' ? 'selected' : '' ?>>English
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Configurazioni Lanci -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="flex items-center mb-6">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-green-600 shadow-lg">
                        <i class="fas fa-rocket text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Configurazioni Lanci</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Impostazioni per la gestione dei lanci</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="launch_number_prefix"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Prefisso Numero Lancio
                        </label>
                        <input type="text" id="launch_number_prefix" name="launch_number_prefix"
                            value="<?= htmlspecialchars($settings['launch_number_prefix'] ?? 'LAN') ?>" maxlength="5"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Es: LAN-2024-001
                        </p>
                    </div>

                    <div>
                        <label for="auto_start_phases"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Avvio Automatico Fasi
                        </label>
                        <select id="auto_start_phases" name="auto_start_phases"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="1" <?= ($settings['auto_start_phases'] ?? 1) == 1 ? 'selected' : '' ?>>
                                Attivo</option>
                            <option value="0" <?= ($settings['auto_start_phases'] ?? 1) == 0 ? 'selected' : '' ?>>
                                Disattivo</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Avvia automaticamente le fasi in sequenza
                        </p>
                    </div>

                    <div>
                        <label for="require_phase_notes"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Note Obbligatorie per Fasi
                        </label>
                        <select id="require_phase_notes" name="require_phase_notes"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="0" <?= ($settings['require_phase_notes'] ?? 0) == 0 ? 'selected' : '' ?>>
                                Opzionali</option>
                            <option value="1" <?= ($settings['require_phase_notes'] ?? 0) == 1 ? 'selected' : '' ?>>
                                Obbligatorie</option>
                        </select>
                    </div>

                    <div>
                        <label for="max_articles_per_launch"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Max Articoli per Lancio
                        </label>
                        <input type="number" id="max_articles_per_launch" name="max_articles_per_launch"
                            value="<?= htmlspecialchars($settings['max_articles_per_launch'] ?? '50') ?>" min="1"
                            max="1000"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Notifiche -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="flex items-center mb-6">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg">
                        <i class="fas fa-bell text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifiche</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Configurazione delle notifiche email</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Notifica Lancio Completato
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Invia email quando un lancio viene
                                completato</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_launch_completed" value="1"
                                <?= ($settings['notify_launch_completed'] ?? 1) == 1 ? 'checked' : '' ?>
                                class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                            </div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Notifica Fase Bloccata</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Invia email quando una fase rimane
                                bloccata</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_phase_blocked" value="1"
                                <?= ($settings['notify_phase_blocked'] ?? 1) == 1 ? 'checked' : '' ?>
                                class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                            </div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Notifica Login Laboratorio
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Invia email quando un laboratorio
                                effettua il login</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notify_laboratory_login" value="1"
                                <?= ($settings['notify_laboratory_login'] ?? 0) == 1 ? 'checked' : '' ?>
                                class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="notification_email"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email Destinatario Notifiche
                    </label>
                    <input type="email" id="notification_email" name="notification_email"
                        value="<?= htmlspecialchars($settings['notification_email'] ?? '') ?>"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Email principale per ricevere le notifiche del sistema
                    </p>
                </div>
            </div>

            <!-- Sicurezza -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <div class="flex items-center mb-6">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-red-600 shadow-lg">
                        <i class="fas fa-shield-alt text-white text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sicurezza</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Impostazioni di sicurezza del sistema</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="session_timeout"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Timeout Sessione (minuti)
                        </label>
                        <input type="number" id="session_timeout" name="session_timeout"
                            value="<?= htmlspecialchars($settings['session_timeout'] ?? '120') ?>" min="15" max="480"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="max_login_attempts"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Max Tentativi Login
                        </label>
                        <input type="number" id="max_login_attempts" name="max_login_attempts"
                            value="<?= htmlspecialchars($settings['max_login_attempts'] ?? '5') ?>" min="3" max="10"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="password_min_length"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Lunghezza Minima Password
                        </label>
                        <input type="number" id="password_min_length" name="password_min_length"
                            value="<?= htmlspecialchars($settings['password_min_length'] ?? '8') ?>" min="5" max="20"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="require_password_symbols"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Richiedi Simboli in Password
                        </label>
                        <select id="require_password_symbols" name="require_password_symbols"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="0" <?= ($settings['require_password_symbols'] ?? 0) == 0 ? 'selected' : '' ?>>No</option>
                            <option value="1" <?= ($settings['require_password_symbols'] ?? 0) == 1 ? 'selected' : '' ?>>Sì</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i>
                    Salva Impostazioni
                </button>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Sistema Info -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Info Sistema
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Versione SCM</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">v1.0.0</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Database</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">MySQL</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Ultima modifica</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            <?= date('d/m/Y H:i') ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class="fas fa-chart-bar mr-2 text-green-500"></i>
                    Statistiche Rapide
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Laboratori attivi</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            <?= $systemStats['active_laboratories'] ?? 0 ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Lanci totali</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            <?= $systemStats['total_launches'] ?? 0 ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Fasi completate</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            <?= $systemStats['completed_phases'] ?? 0 ?>
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<script>

    // Validazione form
    document.querySelector('form').addEventListener('submit', function (e) {
        const email = document.getElementById('notification_email').value;
        if (email && !isValidEmail(email)) {
            e.preventDefault();
            alert('Inserisci un indirizzo email valido');
            return false;
        }

        const minLength = document.getElementById('password_min_length').value;
        if (minLength < 6 || minLength > 20) {
            e.preventDefault();
            alert('La lunghezza minima password deve essere tra 6 e 20 caratteri');
            return false;
        }

        return true;
    });

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
</script>