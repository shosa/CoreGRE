<?php
/**
 * API Documentation Controller
 * Gestisce la documentazione API Swagger/OpenAPI
 */

class ApiDocsController extends BaseController
{
    /**
     * Mostra la documentazione Swagger UI
     * GET /api-docs
     */
    public function index()
    {
        // Debug
        error_log("ApiDocsController::index() called");

        // Redirect to static file (served by nginx directly)
        //header('Location: /api-docs.html');
        //exit;

        // Alternative: serve directly
        $html_path = APP_ROOT . '/public/api-docs.html';

        if (file_exists($html_path)) {
            header('Content-Type: text/html; charset=UTF-8');
            readfile($html_path);
            exit;
        } else {
            http_response_code(404);
            echo 'File documentazione non trovato: ' . $html_path;
            exit;
        }
    }

    /**
     * Serve il file OpenAPI YAML
     * GET /api-docs/openapi
     */
    public function openapi()
    {
        $yaml_path = APP_ROOT . '/public/openapi.yaml';

        if (file_exists($yaml_path)) {
            header('Content-Type: application/x-yaml; charset=UTF-8');
            header('Access-Control-Allow-Origin: *');
            readfile($yaml_path);
            exit;
        } else {
            http_response_code(404);
            echo 'File OpenAPI non trovato: ' . $yaml_path;
            exit;
        }
    }

    /**
     * Serve il file OpenAPI in formato JSON
     * GET /api-docs/openapi.json
     */
    public function openapiJson()
    {
        $yaml_path = APP_ROOT . '/public/openapi.yaml';

        if (!file_exists($yaml_path)) {
            $this->json(['error' => 'File OpenAPI non trovato'], 404);
            return;
        }

        // Leggi YAML e converti in JSON se possibile
        try {
            // Se hai installato una libreria YAML, puoi parsare e convertire
            // Per ora, serviamo un messaggio
            $this->json([
                'message' => 'OpenAPI JSON conversion not implemented',
                'yaml_url' => BASE_URL . '/openapi.yaml',
                'docs_url' => BASE_URL . '/api-docs'
            ]);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API Discovery - Informazioni sulle API disponibili
     * GET /api-docs/discovery
     */
    public function discovery()
    {
        $this->json([
            'service' => 'CoreGRE API',
            'version' => '1.0.0',
            'description' => 'Sistema ERP per l\'industria calzaturiera',
            'documentation' => [
                'swagger_ui' => BASE_URL . '/api-docs',
                'openapi_spec' => BASE_URL . '/openapi.yaml',
                'format' => 'OpenAPI 3.0.3'
            ],
            'api_groups' => [
                'discovery' => [
                    'name' => 'Discovery & Health',
                    'description' => 'Endpoints pubblici per discovery e health check',
                    'endpoints' => [
                        '/api/discovery',
                        '/api/health',
                        '/api/ping'
                    ]
                ],
                'mobile' => [
                    'name' => 'Mobile API',
                    'description' => 'API unificate per app mobile',
                    'endpoints' => [
                        '/api/mobile/login',
                        '/api/mobile/profile',
                        '/api/mobile/daily-summary',
                        '/api/mobile/system-data',
                        '/api/mobile/check-data'
                    ]
                ],
                'quality' => [
                    'name' => 'Quality API',
                    'description' => 'API controllo qualità (legacy)',
                    'endpoints' => [
                        '/api/quality/login',
                        '/api/quality/check-cartellino',
                        '/api/quality/check-commessa',
                        '/api/quality/cartellino-details',
                        '/api/quality/options',
                        '/api/quality/save-hermes-cq',
                        '/api/quality/operator-daily-summary',
                        '/api/quality/record-details',
                        '/api/quality/upload-photo'
                    ]
                ],
                'repairs' => [
                    'name' => 'Riparazioni API',
                    'description' => 'API riparazioni interne',
                    'endpoints' => [
                        '/api/riparazioni-interne',
                        '/api/riparazioni-interne/show',
                        '/api/riparazioni-interne/update',
                        '/api/riparazioni-interne/complete',
                        '/api/riparazioni-interne/delete',
                        '/api/riparazioni-interne/check-cartellino',
                        '/api/riparazioni-interne/stats'
                    ]
                ],
                'dashboard' => [
                    'name' => 'Dashboard API',
                    'description' => 'API per dashboard e widgets',
                    'endpoints' => [
                        '/api/dashboard/preferences',
                        '/api/dashboard/stats',
                        '/api/dashboard/recent-activities',
                        '/api/widgets/available',
                        '/api/widgets/enabled'
                    ]
                ]
            ],
            'contact' => [
                'support_email' => 'support@coregre.local'
            ],
            'license' => 'Proprietary'
        ]);
    }

    /**
     * Statistiche API - Informazioni di utilizzo
     * GET /api-docs/stats
     */
    public function stats()
    {
        // Richiede autenticazione admin
        if (!$this->isAdmin()) {
            $this->json(['error' => 'Accesso non autorizzato'], 403);
            return;
        }

        // TODO: Implementa statistiche reali da log o database
        $this->json([
            'total_endpoints' => 50,
            'api_groups' => 6,
            'version' => '1.0.0',
            'status' => 'operational',
            'uptime' => '99.9%',
            'avg_response_time' => '45ms',
            'note' => 'Statistiche simulate - implementare raccolta dati reale'
        ]);
    }



}
