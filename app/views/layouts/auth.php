<!DOCTYPE html>
<html lang="it" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $pageTitle ?? 'COREGRE' ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="<?= $this->url('css/app.css') ?>">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= $this->url('assets/favicon.ico') ?>">
</head>

<body class="h-full">
    <div class="min-h-full flex">
        <!-- Left side - Login Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <?= $content ?>
            </div>
        </div>

        <!-- Right side - Background Image/Pattern -->
        <div class="hidden lg:block relative w-0 flex-1">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-600 via-orange-400 to-orange-300">
                <!-- Pattern overlay -->
                <div class="absolute inset-0 bg-black bg-opacity-20"></div>

                <!-- Content overlay -->
                <div class="absolute inset-0 flex items-center justify-center p-12">
                    <div class="text-center text-white">
                        <div class="mb-8">
                            <img class="h-16 w-16 mx-auto mb-4" src="<?= $this->url('assets/logo-white.png') ?>"
                                alt="COREGRE">
                            <h1 class="text-4xl font-bold mb-2"><?= APP_NAME ?></h1>
                        </div>
                        <div class="mt-8 text-sm text-orange-200">
                            <p>Versione <?= APP_VERSION ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Configurazione globale
        window.COREGRE = {
            baseUrl: '<?= BASE_URL ?>',
            csrfToken: '<?= $csrfToken ?? '' ?>'
        };

        // Utility per gestire gli alert
        function showAlert(message, type = 'info') {
            // Questa funzione verrà implementata quando necessario
            console.log(`${type}: ${message}`);
        }
    </script>
</body>
<script>
    // Tailwind config esteso
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    primary: '#3b82f6',
                    success: '#10b981',
                    warning: '#f59e0b',
                    error: '#ef4444',
                    info: '#06b6d4'
                },
                fontFamily: {
                    sans: ['Inter', 'ui-sans-serif', 'system-ui']
                },
                fontSize: {
                    'title-sm': ['1.125rem', '1.75rem'],
                    'title-md': ['1.25rem', '1.75rem'],
                    'title-md2': ['1.5rem', '2rem'],
                    'title-lg': ['1.75rem', '2.25rem'],
                    'title-xl': ['2.25rem', '2.5rem'],
                    'title-xl2': ['3rem', '3.5rem']
                },
                zIndex: {
                    '99999': '99999',
                    '9999': '9999'
                }
            }
        }
    }
</script>

</html>