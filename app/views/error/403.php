<div class="min-h-screen px-4 py-16 sm:px-6 sm:py-24 md:grid md:place-items-center lg:px-8">
    <div class="max-w-max mx-auto">
        <main class="sm:flex">
            <p class="text-4xl font-bold text-red-600 sm:text-5xl">403</p>
            <div class="sm:ml-6">
                <div class="sm:border-l sm:border-gray-200 sm:pl-6">
                    <h1 class="text-4xl font-bold text-gray-900 tracking-tight sm:text-5xl">
                        Accesso Negato
                    </h1>
                    <p class="mt-1 text-base text-gray-500">
                        Non hai i permessi necessari per visualizzare questa sezione.
                    </p>
                </div>
                <div class="mt-10 flex space-x-3 sm:border-l sm:border-transparent sm:pl-6">
                    <a href="<?= $this->url('/') ?>" class="btn btn-danger">
                        <i class="fas fa-home mr-2"></i>
                        Torna alla Home
                    </a>
                    <button onclick="history.back()" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Torna indietro
                    </button>
                </div>
            </div>
        </main>
    </div>
</div>