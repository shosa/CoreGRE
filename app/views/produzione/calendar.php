<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                Produzione & Spedizione
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Gestione calendario produzione e spedizioni
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
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
                <span class="text-gray-700 dark:text-gray-300">Produzione</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Main Content -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-4">

    <!-- Calendar -->
    <div class="lg:col-span-3">
        <div
            class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">

            <!-- Calendar Header -->
            <div class="flex items-center justify-between mb-6">
                <a href="<?= $this->url('/produzione/calendar?month=' . ($currentMonth - 1) . '&year=' . $currentYear) ?>"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-chevron-left"></i>
                </a>

                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    <?= $monthNames[$currentMonth] . ' ' . $currentYear ?>
                </h2>

                <a href="<?= $this->url('/produzione/calendar?month=' . ($currentMonth + 1) . '&year=' . $currentYear) ?>"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>

            <!-- Calendar Grid -->
            <div class="calendar-grid">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Lun</th>
                            <th class="py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Mar</th>
                            <th class="py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Mer</th>
                            <th class="py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Gio</th>
                            <th class="py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Ven</th>
                            <th class="py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Sab</th>
                            <th class="py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Dom</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dayCounter = 1;
                        $today = (int) date('d');
                        $currentCalendarMonth = (int) date('m');
                        $currentCalendarYear = (int) date('Y');

                        // Prima settimana - giorni vuoti
                        echo '<tr>';
                        for ($i = 1; $i < $firstDayOfWeek; $i++) {
                            echo '<td class="h-20 border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800"></td>';
                        }

                        // Giorni del mese
                        for ($day = 1; $day <= $daysInMonth; $day++) {
                            $isToday = ($day === $today && $currentMonth === $currentCalendarMonth && $currentYear === $currentCalendarYear);
                            $isFutureDate = strtotime("$currentYear-$currentMonth-$day") > time() && !$isToday;
                            $hasData = in_array($day, $produzioneDays);

                            $cellClasses = 'h-20 border border-gray-100 dark:border-gray-700 relative transition-all duration-200';

                            if ($isToday) {
                                $cellClasses .= ' bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-800/40 cursor-pointer';
                            } elseif ($isFutureDate) {
                                $cellClasses .= ' bg-red-50 dark:bg-red-900/20 cursor-not-allowed';
                            } else {
                                $cellClasses .= ' hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer';
                            }

                            if ($hasData) {
                                $cellClasses .= ' bg-green-50 dark:bg-green-900/20';
                            }

                            $monthName = $monthNames[$currentMonth];
                            $year = $_GET['year'] ?? $currentYear;

                            $clickAction = $isFutureDate
                                ? ''
                                : "onclick=\"navigateToProduction('$monthName', '$day', '$year')\"";

                            echo "<td class=\"$cellClasses\" $clickAction>";
                            echo "<div class=\"p-2\">";
                            echo "<span class=\"text-sm font-medium text-gray-900 dark:text-white\">$day</span>";

                            if ($hasData) {
                                echo "<div class=\"absolute top-1 right-1\">";
                                echo "<span class=\"inline-flex h-2 w-2 rounded-full bg-green-500\"></span>";
                                echo "</div>";
                            }

                            if ($isToday) {
                                echo "<div class=\"absolute bottom-1 left-1 text-xs text-blue-600 dark:text-blue-400 font-semibold\">Oggi</div>";
                            }

                            echo "</div>";
                            echo "</td>";

                            // Fine settimana
                            if (($firstDayOfWeek - 1 + $day) % 7 === 0) {
                                echo '</tr>';
                                if ($day !== $daysInMonth) {
                                    echo '<tr>';
                                }
                            }
                        }

                        // Giorni finali vuoti
                        $remainingCells = 7 - (($firstDayOfWeek - 1 + $daysInMonth) % 7);
                        if ($remainingCells < 7) {
                            for ($i = 0; $i < $remainingCells; $i++) {
                                echo '<td class="h-20 border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800"></td>';
                            }
                        }
                        echo '</tr>';
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <div class="space-y-6">

            <!-- Quick Stats -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                    Statistiche Mese
                </h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Giorni con dati</span>
                        <span class="text-lg font-bold text-green-600 dark:text-green-400">
                            <?= count($produzioneDays) ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Giorni totali</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                            <?= $daysInMonth ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Completamento</span>
                        <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                            <?= round((count($produzioneDays) / $daysInMonth) * 100) ?>%
                        </span>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Legenda
                </h3>

                <div class="space-y-3">
                    <div class="flex items-center">
                        <span class="inline-flex h-4 w-4 rounded bg-blue-100 dark:bg-blue-900/30 mr-3"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Oggi</span>
                    </div>

                    <div class="flex items-center">
                        <span class="inline-flex h-4 w-4 rounded bg-green-50 dark:bg-green-900/20 mr-3 relative">
                            <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-green-500"></span>
                        </span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Con dati produzione</span>
                    </div>

                    <div class="flex items-center">
                        <span class="inline-flex h-4 w-4 rounded bg-red-50 dark:bg-red-900/20 mr-3"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Date future</span>
                    </div>

                    <div class="flex items-center">
                        <span
                            class="inline-flex h-4 w-4 rounded bg-white hover:bg-blue-50 border border-gray-200 mr-3"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Disponibile</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                    Azioni Rapide
                </h3>

                <div class="space-y-3">
                    <a href="<?= $this->url('/produzione/create') ?>"
                        class="flex items-center w-full p-3 text-left border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 dark:border-gray-700 dark:hover:bg-blue-900/20 dark:hover:border-blue-500 transition-all duration-200">
                        <i class="fas fa-plus text-blue-500 mr-3"></i>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Nuova Produzione</span>
                    </a>

                    <button onclick="navigateToToday()"
                        class="flex items-center w-full p-3 text-left border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 dark:border-gray-700 dark:hover:bg-green-900/20 dark:hover:border-green-500 transition-all duration-200">
                        <i class="fas fa-calendar-day text-green-500 mr-3"></i>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Vai a Oggi</span>
                    </button>


                </div>
            </div>

        </div>
    </div>

</div>

<script>
    // Navigazione verso una data specifica
    function navigateToProduction(month, day, year) {
        const url = `<?= $this->url('/produzione/show') ?>?month=${encodeURIComponent(month)}&day=${day}&year=${encodeURIComponent(year)}`;

        if (window.pjax && typeof window.pjax.navigateTo === 'function') {
            window.pjax.navigateTo(url);
        } else {
            window.location.href = url;
        }
    }

    // Naviga alla data di oggi
    function navigateToToday() {
        const today = new Date();
        const currentMonth = today.getMonth() + 1;
        const currentYear = today.getFullYear();

        const url = `<?= $this->url('/produzione/calendar') ?>?month=${currentMonth}&year=${currentYear}`;

        if (window.pjax && typeof window.pjax.navigateTo === 'function') {
            window.pjax.navigateTo(url);
        } else {
            window.location.href = url;
        }
    }

    // Evidenzia il giorno corrente
    document.addEventListener('DOMContentLoaded', function () {
        const today = new Date();
        const currentDay = today.getDate();
        const currentMonth = today.getMonth() + 1;
        const currentYear = today.getFullYear();

        // Se siamo nel mese corrente, evidenzia il giorno
        if (currentMonth === <?= $currentMonth ?> && currentYear === <?= $currentYear ?>) {
            console.log('Highlighting current day:', currentDay);
        }
    });
</script>