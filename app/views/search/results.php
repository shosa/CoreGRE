<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-search mr-3 text-blue-500"></i>
                Risultati Ricerca
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                <?php if (!empty($query)): ?>
                    Risultati per: "<span class="font-semibold"><?= htmlspecialchars($query) ?></span>"
                <?php else: ?>
                    Inserisci un termine di ricerca per iniziare
                <?php endif; ?>
            </p>
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
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Ricerca</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<!-- Search Form -->
<div class="mb-8">
    <div class="max-w-2xl">
        <form method="GET" class="relative">
            <div class="relative">
                <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 z-10">
                    <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600">
                        <i class="fas fa-search text-xs text-white"></i>
                    </div>
                </button>
                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($query ?? '') ?>"
                    placeholder="Cerca cartellini, articoli, utenti, DDT..."
                    class="w-full bg-white dark:bg-gray-800 pl-14 pr-4 py-4 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    autofocus
                />
            </div>
        </form>
    </div>
</div>

<!-- Results Container -->
<div x-data="searchResults('<?= htmlspecialchars($query ?? '') ?>')">
    <!-- Loading State -->
    <div x-show="loading" class="flex items-center justify-center py-12">
        <div class="text-center">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-4"></i>
            <p class="text-gray-600 dark:text-gray-400">Ricerca in corso...</p>
        </div>
    </div>

    <!-- Results -->
    <div x-show="!loading && results.length > 0" class="space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-gray-600 dark:text-gray-400">
                Trovati <span x-text="results.length" class="font-semibold"></span> risultati
            </p>
        </div>

        <!-- Results Grid -->
        <div class="grid gap-4">
            <template x-for="result in results" :key="result.type + '-' + result.title">
                <a :href="result.url" class="block">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                    <i :class="result.icon" class="text-white text-lg"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate" x-text="result.title"></h3>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 ml-3" x-text="result.category"></span>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 mt-1" x-text="result.subtitle"></p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-arrow-right text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </template>
        </div>
    </div>

    <!-- No Results -->
    <div x-show="!loading && results.length === 0 && query.length >= 2" class="text-center py-12">
        <div class="max-w-sm mx-auto">
            <i class="fas fa-search text-6xl text-gray-400 mb-6"></i>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Nessun risultato trovato</h3>
            <p class="text-gray-600 dark:text-gray-400">
                Non abbiamo trovato nessun risultato per "<span x-text="query" class="font-semibold"></span>".
                Prova con termini di ricerca diversi.
            </p>
        </div>
    </div>

    <!-- Search Tips -->
    <div x-show="!loading && query.length < 2" class="max-w-4xl mx-auto">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-8">
            <div class="text-center mb-8">
                <i class="fas fa-lightbulb text-4xl text-blue-500 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Suggerimenti per la ricerca</h3>
                <p class="text-gray-600 dark:text-gray-400">Scopri cosa puoi cercare nel sistema</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-qrcode text-blue-500 mr-3"></i>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Cartellini</h4>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Cerca per numero cartellino o ID</p>
                    <div class="mt-2 text-xs text-gray-500">Es: 12345, C001</div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-user text-green-500 mr-3"></i>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Utenti</h4>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Cerca per nome, username o email</p>
                    <div class="mt-2 text-xs text-gray-500">Es: Mario, admin, email@example.com</div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-file-export text-purple-500 mr-3"></i>
                        <h4 class="font-semibold text-gray-900 dark:text-white">DDT</h4>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Cerca documenti per numero</p>
                    <div class="mt-2 text-xs text-gray-500">Es: DDT001, 2024-001</div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-hammer text-red-500 mr-3"></i>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Riparazioni</h4>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Cerca riparazioni per ID o cartellino</p>
                    <div class="mt-2 text-xs text-gray-500">Es: R001, difetto</div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-box text-orange-500 mr-3"></i>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Materiali</h4>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Cerca materiali per codice o descrizione</p>
                    <div class="mt-2 text-xs text-gray-500">Es: MAT001, tessuto</div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-industry text-indigo-500 mr-3"></i>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Articoli</h4>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Cerca articoli per nome o descrizione</p>
                    <div class="mt-2 text-xs text-gray-500">Es: Camicia, ART001</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function searchResults(initialQuery) {
    return {
        query: initialQuery,
        results: [],
        loading: false,

        init() {
            if (this.query && this.query.length >= 2) {
                this.performSearch();
            }
        },

        async performSearch() {
            if (this.query.length < 2) {
                this.results = [];
                return;
            }

            this.loading = true;

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
            }
        }
    }
}
</script>