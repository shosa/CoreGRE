<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                Produzione del <?= htmlspecialchars($formatted_date) ?>
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Dettaglio produzione e spedizione
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <?php if ($produzione): ?>
                <button onclick="generatePDF('<?= htmlspecialchars($date) ?>')"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-file-pdf mr-2 text-red-500"></i>
                    Scarica PDF
                </button>

                <button onclick="openEmailModal('<?= htmlspecialchars($date) ?>')"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-envelope mr-2 text-blue-500"></i>
                    Invia Email
                </button>
            <?php endif; ?>


            <a href="<?= $this->url('/produzione/create') ?>"
                class="inline-flex items-center rounded-lg border border-primary bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i>
                Nuova Produzione
            </a>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500 dark:text-gray-400">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/produzione/calendar') ?>" class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    Produzione
                </a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    <?= htmlspecialchars($formatted_date) ?>
                </span>
            </div>
        </li>
    </ol>
</nav>

<?php if ($produzione): ?>

    <!-- Main Content -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- Production Details -->
        <div class="lg:col-span-2">
            <div class="space-y-6">

                <!-- Montaggio Section -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-cogs mr-3 text-blue-500"></i>
                        Montaggio
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Manovia 1 -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">MANOVIA 1</label>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    <?= $produzione->manovia1 ?? '0' ?>
                                </div>
                                <?php if (!empty($produzione->manovia1_notes)): ?>
                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-sticky-note mr-1"></i>
                                        <?= htmlspecialchars($produzione->manovia1_notes) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Manovia 2 -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">MANOVIA 2</label>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    <?= $produzione->manovia2 ?? '0' ?>
                                </div>
                                <?php if (!empty($produzione->manovia2_notes)): ?>
                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-sticky-note mr-1"></i>
                                        <?= htmlspecialchars($produzione->manovia2_notes) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orlatura Section -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-cut mr-3 text-green-500"></i>
                        Orlatura
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php
                        $orlature = [
                            1 => ['value' => $produzione->orlatura1, 'notes' => $produzione->orlatura1_notes],
                            2 => ['value' => $produzione->orlatura2, 'notes' => $produzione->orlatura2_notes],
                            3 => ['value' => $produzione->orlatura3, 'notes' => $produzione->orlatura3_notes],
                            4 => ['value' => $produzione->orlatura4, 'notes' => $produzione->orlatura4_notes],
                            5 => ['value' => $produzione->orlatura5, 'notes' => $produzione->orlatura5_notes]
                        ];
                        foreach ($orlature as $i => $orlatura): ?>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">ORLATURA <?= $i ?></label>
                                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="text-xl font-bold text-green-600 dark:text-green-400">
                                        <?= $orlatura['value'] ?? '0' ?>
                                    </div>
                                    <?php if (!empty($orlatura['notes'])): ?>
                                        <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                            <i class="fas fa-sticky-note mr-1"></i>
                                            <?= htmlspecialchars($orlatura['notes']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Taglio Section -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-scissors mr-3 text-orange-500"></i>
                        Taglio
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Taglio 1 -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">TAGLIO 1</label>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                    <?= $produzione->taglio1 ?? '0' ?>
                                </div>
                                <?php if (!empty($produzione->taglio1_notes)): ?>
                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-sticky-note mr-1"></i>
                                        <?= htmlspecialchars($produzione->taglio1_notes) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Taglio 2 -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">TAGLIO 2</label>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                    <?= $produzione->taglio2 ?? '0' ?>
                                </div>
                                <?php if (!empty($produzione->taglio2_notes)): ?>
                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-sticky-note mr-1"></i>
                                        <?= htmlspecialchars($produzione->taglio2_notes) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="space-y-6">

                <!-- Totals -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-calculator mr-3 text-purple-500"></i>
                        Totali
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Montaggio</span>
                            <span class="text-xl font-bold text-blue-600 dark:text-blue-400">
                                <?= $produzione->total_montaggio ?? '0' ?>
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Orlatura</span>
                            <span class="text-xl font-bold text-green-600 dark:text-green-400">
                                <?= $produzione->total_orlatura ?? '0' ?>
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Taglio</span>
                            <span class="text-xl font-bold text-orange-600 dark:text-orange-400">
                                <?= $produzione->total_taglio ?? '0' ?>
                            </span>
                        </div>

                        <div
                            class="flex items-center justify-between p-3 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg border-2 border-dashed border-purple-200 dark:border-purple-700">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Totale Generale</span>
                            <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                <?= ($produzione->total_montaggio ?? 0) + ($produzione->total_orlatura ?? 0) + ($produzione->total_taglio ?? 0) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-3 text-blue-500"></i>
                        Informazioni
                    </h3>

                    <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-center justify-between">
                            <span>Data:</span>
                            <span class="font-medium"><?= htmlspecialchars($day . ' ' . $month) ?></span>
                        </div>

                        <?php if (isset($produzione->production_date->weekOfYear)): ?>
                            <div class="flex items-center justify-between">
                                <span>Settimana:</span>
                                <span class="font-medium"><?= $produzione->production_date->weekOfYear ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($produzione->day_name)): ?>
                            <div class="flex items-center justify-between">
                                <span>Giorno:</span>
                                <span class="font-medium"><?= htmlspecialchars($produzione->day_name) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-cog mr-3 text-gray-500"></i>
                        Azioni
                    </h3>

                    <div class="space-y-3">
                        <a href="<?= $this->url('/produzione/edit?date=' . urlencode($date)) ?>"
                            class="flex items-center w-full p-3 text-left border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 dark:border-gray-700 dark:hover:bg-blue-900/20 dark:hover:border-blue-500 transition-all duration-200">
                            <i class="fas fa-edit text-blue-500 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Modifica</span>
                        </a>

                        <button onclick="generatePDF('<?= htmlspecialchars($date) ?>')"
                            class="flex items-center w-full p-3 text-left border border-gray-200 rounded-lg hover:bg-red-50 hover:border-red-300 dark:border-gray-700 dark:hover:bg-red-900/20 dark:hover:border-red-500 transition-all duration-200">
                            <i class="fas fa-file-pdf text-red-500 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Genera PDF</span>
                        </button>

                        <button onclick="openEmailModal('<?= htmlspecialchars($date) ?>')"
                            class="flex items-center w-full p-3 text-left border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 dark:border-gray-700 dark:hover:bg-blue-900/20 dark:hover:border-blue-500 transition-all duration-200">
                            <i class="fas fa-envelope text-blue-500 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Invia Email</span>
                        </button>

                        <a href="<?= $this->url('/produzione/calendar') ?>"
                            class="flex items-center w-full p-3 text-left border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:border-gray-600 transition-all duration-200">
                            <i class="fas fa-calendar text-gray-500 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Torna al Calendario</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>

<?php else: ?>

    <!-- No Data -->
    <div class="text-center py-12">
        <div
            class="rounded-2xl border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm max-w-md mx-auto">
            <i class="fas fa-calendar-times text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                Nessun dato di produzione
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Non sono stati trovati dati di produzione per il <?= htmlspecialchars($day . ' ' . $month) ?>.
            </p>
            <a href="<?= $this->url('/produzione/create?date=' . urlencode($date)) ?>"
                class="inline-flex items-center rounded-lg border border-primary bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i>
                Crea Produzione
            </a>
        </div>
    </div>

<?php endif; ?>

<!-- Modal Email -->
<div id="emailModal" class="fixed inset-0 z-99999 overflow-y-auto hidden" aria-labelledby="emailModalLabel"
    role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
            onclick="closeEmailModal()"></div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-gray-800">
            <form id="emailForm" onsubmit="sendEmail(event)">
                <!-- Header -->
                <div class="bg-blue-50 dark:bg-blue-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="emailModalLabel">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>
                            Invia Email con PDF
                        </h3>
                        <button type="button" onclick="closeEmailModal()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-4 space-y-4">
                    <!-- Destinatari -->
                    <div>
                        <label for="emailTo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Destinatari <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="emailTo" name="to" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500"
                            placeholder="email1@example.com; email2@example.com" value="<?= htmlspecialchars($destinatari) ?>">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Separa più email con punto e virgola
                            (;)</p>
                    </div>

                    <!-- Oggetto -->
                    <div>
                        <label for="emailSubject"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Oggetto <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="emailSubject" name="subject" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500"
                            value="Produzione <?= htmlspecialchars($day . ' ' . $month) ?>">
                    </div>

                    <!-- Messaggio -->
                    <div>
                        <label for="emailBody" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Messaggio
                        </label>
                        <textarea id="emailBody" name="body" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500"
                            placeholder="Inserisci qui il tuo messaggio...">Si allega il rapporto di produzione del <?= htmlspecialchars($day . ' ' . $month) ?>.</textarea>
                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" id="emailDate" name="date" value="">
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-paperclip mr-1"></i>
                        Allegato: PRODUZIONE.pdf
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeEmailModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-lg hover:bg-gray-300 focus:ring-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-700 transition-colors">
                            Annulla
                        </button>
                        <button type="submit" id="sendEmailBtn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Invia Email
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Funzioni della pagina produzione - compatibili con PJAX
    (function () {
        // Genera PDF per la data corrente
        window.generatePDF = function (date) {
            const url = `<?= $this->url('/produzione/generate-pdf') ?>?date=${encodeURIComponent(date)}`;

            // Apri il PDF in una nuova finestra
            window.open(url, '_blank');

            // Log dell'attività
            console.log('PDF generation requested for:', date);
        };

        // Apre il modal per l'invio email
        window.openEmailModal = function (date) {
            const emailModal = document.getElementById('emailModal');
            const emailDate = document.getElementById('emailDate');
            const emailTo = document.getElementById('emailTo');

            if (emailModal && emailDate) {
                emailDate.value = date;
                emailModal.classList.remove('hidden');

                // Focus sul primo campo
                setTimeout(() => {
                    if (emailTo) emailTo.focus();
                }, 100);
            }
        };

        // Chiude il modal email
        window.closeEmailModal = function () {
            const emailModal = document.getElementById('emailModal');
            const emailForm = document.getElementById('emailForm');
            const emailSubject = document.getElementById('emailSubject');
            const emailBody = document.getElementById('emailBody');

            if (emailModal) {
                emailModal.classList.add('hidden');
            }

            // Reset form
            if (emailForm) {
                emailForm.reset();
            }

            // Ripristina valori di default
            if (emailSubject) {
                emailSubject.value = 'Rapporto Produzione <?= htmlspecialchars($day . " " . $month) ?>';
            }
            if (emailBody) {
                emailBody.value = 'Si allega il rapporto di produzione del <?= htmlspecialchars($day . " " . $month) ?>.';
            }
        };

        // Invia email con PDF
        window.sendEmail = function (event) {
            event.preventDefault();

            const form = document.getElementById('emailForm');
            const button = document.getElementById('sendEmailBtn');

            if (!form || !button) return;

            const formData = new FormData(form);

            // Disabilita il pulsante durante l'invio
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Invio in corso...';

            // Invio AJAX
            fetch('<?= $this->url('/produzione/send-email') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : ''
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        CoregreNotifications.success('Email inviata con successo!');
                        closeEmailModal();
                    } else {
                        const errorMsg = data.error || 'Errore durante l\'invio dell\'email';
                        CoregreNotifications.error(errorMsg);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorMsg = 'Errore di rete durante l\'invio dell\'email';
                    CoregreNotifications.error(errorMsg);
                })
                .finally(() => {
                    // Riabilita il pulsante
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Invia Email';
                });
        };

        // Funzione showNotification rimossa - usa il sistema CoregreNotifications universale

        // Gestione eventi - compatibile con PJAX
        let escapeListener = null;

        function initPageEvents() {
            // Rimuovi listener esistenti per evitare duplicati
            if (escapeListener) {
                document.removeEventListener('keydown', escapeListener);
            }

            // Chiudi modal con Esc
            escapeListener = function (event) {
                if (event.key === 'Escape') {
                    closeEmailModal();
                }
            };
            document.addEventListener('keydown', escapeListener);
        }

        // Registra l'inizializzatore per PJAX
        if (window.COREGRE && window.COREGRE.onPageLoad) {
            window.COREGRE.onPageLoad(initPageEvents);
        } else {
            // Fallback per il primo caricamento se PJAX non è ancora disponibile
            document.addEventListener('DOMContentLoaded', initPageEvents);
        }

        // Inizializza subito se il DOM è già pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPageEvents);
        } else {
            initPageEvents();
        }
    })();
</script>