<?php
/**
 * WEBGRE Application Entry Point
 * Punto di ingresso principale dell'applicazione MVC
 */

// Definisce la costante APP_ROOT per prevenire accessi diretti ai file
define('APP_ROOT', __DIR__);

// Include la configurazione principale
require_once APP_ROOT . '/config/config.php';

// Gestisci CORS per tutte le richieste API
if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? null;

    // Permetti specificamente localhost per Capacitor
    if ($origin && (strpos($origin, 'localhost') !== false || strpos($origin, '127.0.0.1') !== false)) {
        header("Access-Control-Allow-Origin: $origin");
    } else {
        header("Access-Control-Allow-Origin: *");
    }

    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-App-Type, X-Requested-With");

    // Gestisci richieste OPTIONS (preflight)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header("Access-Control-Max-Age: 86400"); // Cache preflight for 24 hours
        http_response_code(200);
        exit;
    }
}

// Avvia la sessione
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Inizializza il router
    $router = new Router();
    
    // Carica le routes
    require_once APP_ROOT . '/routes/web.php';
    
    // Risolvi la route corrente
    $router->resolve();
    
} catch (Exception $e) {
    // Gestione degli errori globali
    if (APP_DEBUG) {
        // In development, mostra l'errore completo
        echo '<div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 0.25rem; padding: 1rem; margin: 1rem; font-family: monospace;">';
        echo '<h3 style="color: #dc3545; margin: 0 0 1rem 0;">Application Error</h3>';
        echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . '</p>';
        echo '<p><strong>Line:</strong> ' . $e->getLine() . '</p>';
        echo '<h4 style="color: #6c757d; margin: 1rem 0 0.5rem 0;">Stack Trace:</h4>';
        echo '<pre style="background: #ffffff; border: 1px solid #dee2e6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem;">';
        echo htmlspecialchars($e->getTraceAsString());
        echo '</pre>';
        echo '</div>';
    } else {
        // In production, mostra una pagina di errore generica
        http_response_code(500);
        
        if (class_exists('ErrorController')) {
            $errorController = new ErrorController();
            $errorController->serverError();
        } else {
            echo '<!DOCTYPE html>
                <html lang="it">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Errore - WEBGRE</title>
                    <script src="https://cdn.tailwindcss.com"></script>
                </head>
                <body class="bg-gray-50">
                    <div class="min-h-screen flex items-center justify-center">
                        <div class="text-center">
                            <div class="mx-auto h-24 w-24 text-gray-400 mb-6">
                                <i class="fas fa-exclamation-triangle text-6xl"></i>
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">Errore del Server</h1>
                            <p class="text-gray-600 mb-6">Si è verificato un errore interno. Riprova più tardi.</p>
                            <a href="/" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                <i class="fas fa-home mr-2"></i>
                                Torna alla Home
                            </a>
                        </div>
                    </div>
                </body>
                </html>';
        }
        
        // Log dell'errore per debugging
        error_log("Application Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    }
    
    exit;
}