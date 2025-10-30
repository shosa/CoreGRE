<?php
/**
 * Discovery Controller
 * Endpoint per auto-discovery dell'app mobile sulla rete aziendale
 */

class DiscoveryController extends BaseController
{
    /**
     * Endpoint discovery - Risponde con info server per identificazione
     * Accessibile senza autenticazione per permettere la discovery
     */
    public function discover()
    {
        // Non richiedere autenticazione per discovery
        $response = [
            'success' => true,
            'service' => 'CoreGre',
            'version' => '3.0',
            'app_name' => APP_NAME,
            'timestamp' => date('Y-m-d H:i:s'),
            'server_info' => [
                'hostname' => gethostname(),
                'php_version' => PHP_VERSION,
                'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'unknown'
            ]
        ];

        $this->json($response);
    }

    /**
     * Health check endpoint - Verifica stato server
     */
    public function health()
    {
        $status = [
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'service' => 'CoreGre',
            'database' => 'unknown'
        ];

        // Check database connection
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $status['database'] = 'connected';
        } catch (PDOException $e) {
            $status['database'] = 'error';
            $status['status'] = 'unhealthy';
        }

        $this->json($status);
    }

    /**
     * Ping endpoint - Risposta velocissima per network scan
     */
    public function ping()
    {
        $this->json([
            'pong' => true,
            'service' => 'CoreGre',
            'timestamp' => time()
        ]);
    }
}
