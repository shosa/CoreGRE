<?php
/**
 * Web Routes
 * Definisce tutte le routes dell'applicazione web
 */

// Impedisce accesso diretto
defined('APP_ROOT') or die('Access denied');

// Routes pubbliche (non richiedono autenticazione)
$router->get('/login', 'Auth@showLogin');
$router->post('/login', 'Auth@processLogin');
$router->get('/logout', 'Auth@logout');

// Discovery & Health Routes (pubbliche per network scan)
$router->get('/api/discovery', 'Discovery@discover');
$router->get('/api/health', 'Discovery@health');
$router->get('/api/ping', 'Discovery@ping');

// API Documentation Routes (pubbliche)
$router->get('/api-docs', 'ApiDocs@index');
$router->get('/api-docs.html', 'ApiDocs@redirect');
$router->get('/api-docs/openapi', 'ApiDocs@openapi');
$router->get('/api-docs/openapi.json', 'ApiDocs@openapiJson');
$router->get('/api-docs/discovery', 'ApiDocs@discovery');
$router->get('/api-docs/stats', 'ApiDocs@stats');

// Routes protette (richiedono autenticazione)

// Dashboard
$router->get('/', 'Home@index');
$router->get('/dashboard', 'Home@index');

// API Routes per dashboard
$router->get('/api/dashboard/preferences', 'Home@getCardPreferences');

// Search Routes
$router->get('/api/search', 'Search@search');
$router->get('/search', 'Search@results');

// Widget API Routes
$router->get('/api/widgets/available', 'Widget@getAvailableWidgets');
$router->get('/api/widgets/enabled', 'Widget@getEnabledWidgets');
$router->post('/api/widgets/update', 'Widget@updateWidget');
$router->post('/api/widgets/batch-update', 'Widget@batchUpdateWidgets');
$router->post('/api/widgets/reorder', 'Widget@updateWidgetOrder');
$router->post('/api/dashboard/update-preferences', 'Home@updateCardPreferences');
$router->get('/api/dashboard/recent-activities', 'Home@getRecentActivities');
$router->get('/api/dashboard/stats', 'Home@getStats');

// Routes per le riparazioni
$router->get('/riparazioni', 'Riparazioni@index');
$router->get('/riparazioni/list', 'Riparazioni@list');
$router->get('/riparazioni/create', 'Riparazioni@create');
$router->get('/riparazioni/create-step2', 'Riparazioni@createStep2');
$router->post('/riparazioni/store', 'Riparazioni@store');
$router->get('/riparazioni/{id}', 'Riparazioni@show');
$router->get('/riparazioni/{id}/edit', 'Riparazioni@edit');
$router->post('/riparazioni/{id}/update', 'Riparazioni@update');
$router->post('/riparazioni/{id}/complete', 'Riparazioni@complete');
$router->get('/riparazioni/{id}/print', 'Riparazioni@printPdf');
$router->get('/riparazioni/search', 'Riparazioni@search');

// API Routes per riparazioni
$router->get('/api/riparazioni/check-cartellino', 'Riparazioni@checkCartellino');
$router->get('/api/riparazioni/check-commessa', 'Riparazioni@checkCommessa');
$router->post('/api/riparazioni/delete', 'Riparazioni@delete');

// Routes per il controllo qualità - Sistema Hermes CQ Unificato
$router->get('/quality', 'Quality@index');
$router->get('/quality/records', 'Quality@records');
$router->get('/quality/departments', 'Quality@departments');
$router->get('/quality/defects', 'Quality@defects');
$router->get('/quality/reports', 'Quality@reports');

// API Routes per Quality Hermes Management (CRUD completo come legacy)
$router->post('/quality/manage-department', 'Quality@manageDepartment');
$router->post('/quality/manage-defect', 'Quality@manageDefect');
$router->get('/quality/generate-report', 'Quality@generateReport');
$router->post('/quality/save-defect-type', 'Quality@saveDefectType');
$router->post('/quality/delete-defect-type', 'Quality@deleteDefectType');
$router->get('/quality/hermes-data', 'Quality@getHermesData');

// Routes per InWork Admin - Gestione Operatori Mobile
$router->get('/inwork-admin', 'InWorkAdmin@index');
$router->get('/inwork-admin/create', 'InWorkAdmin@create');
$router->post('/inwork-admin/store', 'InWorkAdmin@store');
$router->get('/inwork-admin/{id}/edit', 'InWorkAdmin@edit');
$router->post('/inwork-admin/{id}/update', 'InWorkAdmin@update');
$router->post('/inwork-admin/delete', 'InWorkAdmin@delete');
$router->post('/inwork-admin/toggle', 'InWorkAdmin@toggle');

// Routes per la produzione
$router->get('/produzione', 'Produzione@index');
$router->get('/produzione/calendar', 'Produzione@calendar');
$router->get('/produzione/show', 'Produzione@show');
$router->get('/produzione/create', 'Produzione@create');
$router->get('/produzione/new', 'Produzione@create'); // Alias per retrocompatibilità
$router->post('/produzione/store', 'Produzione@store');
$router->get('/produzione/edit', 'Produzione@edit');
$router->post('/produzione/update', 'Produzione@update');
$router->get('/produzione/generate-pdf', 'Produzione@generatePdf');
$router->post('/produzione/send-email', 'Produzione@sendEmail');

// CSV Report Routes
$router->get('/produzione/csv', 'Produzione@csv');
$router->post('/produzione/process-csv', 'Produzione@processCsv');
$router->get('/produzione/generate-csv-report', 'Produzione@generateCsvReport');

// Routes per Export/DDT
$router->get('/export', 'Export@index');
$router->get('/export/dashboard', 'Export@dashboard');
$router->get('/export/create', 'Export@create');
$router->post('/export/create', 'Export@create');
$router->get('/export/upload/{progressivo}', 'Export@upload');
$router->get('/export/preview/{progressivo}', 'Export@preview');
$router->get('/export/continue/{progressivo}', 'Export@continue');
$router->get('/export/view/{progressivo}', 'Export@viewDocument');
$router->get('/export/terzisti', 'Export@terzisti');
$router->get('/export/terzisti/create', 'Export@createTerzista');
$router->post('/export/terzisti/store', 'Export@storeTerzista');
$router->get('/export/terzisti/{id}/edit', 'Export@editTerzista');
$router->post('/export/terzisti/{id}/update', 'Export@updateTerzista');
$router->post('/export/terzisti/delete', 'Export@deleteTerzista');
$router->post('/export/getTerzistaDetails', 'Export@getTerzistaDetails');
$router->get('/export/getDdtDetails', 'Export@getDdtDetails');
$router->post('/export/delete', 'Export@delete');

// API Routes per Export
$router->post('/export/api/upload', 'Export@handleFileUpload');
$router->get('/export/api/processExcel', 'Export@processExcelFile');
$router->post('/export/api/saveExcel', 'Export@saveExcelData');
$router->post('/export/api/generaDdt', 'Export@generateDdt');
$router->get('/export/segnacolli/{progressivo}', 'Export@generateSegnacolli');

// New API Routes for continue.php functionality
$router->post('/export/completa_ddt', 'Export@completaDdt');
$router->post('/export/cerca_nc_costi', 'Export@cercaNcECosti');
$router->post('/export/elabora_mancanti', 'Export@elaboraMancanti');
$router->post('/export/update_data', 'Export@updateData');
$router->post('/export/save_piede_documento', 'Export@savePiedeDocumento');
$router->post('/export/save_commento', 'Export@saveCommento');
$router->post('/export/save_autorizzazione', 'Export@saveAutorizzazione');
$router->post('/export/aggiungi_mancanti', 'Export@aggiungiMancanti');
$router->post('/export/get_doganale_data', 'Export@getDoganaleData');
$router->post('/export/update_doganale_weight', 'Export@updateDoganaleWeight');
$router->post('/export/get_piede_documento', 'Export@getPiedeDocumento');
$router->post('/export/get_mancanti', 'Export@getMancanti');
$router->post('/export/reset_piede_documento', 'Export@resetPiedeDocumento');
$router->get('/export/download/{progressivo}/{filename}', 'Export@downloadFile');
$router->get('/export/download_all/{progressivo}', 'Export@downloadAllFiles');
$router->get('/export/pdf/{progressivo}', 'Export@generatePdf');



// Routes per SCM Terzisti (Area Pubblica) - DEVE essere prima delle route admin!
$router->get('/scm', 'SCM@index');
$router->post('/scm/login', 'SCM@login');
$router->get('/scm/dashboard', 'SCM@dashboard');
$router->get('/scm/lavora/{id}', 'SCM@lavora');
$router->get('/scm/logout', 'SCM@logout');

// API SCM Terzisti
$router->post('/scm/update-progress/{id}', 'SCM@updateProgress');
$router->post('/scm/update-progress-sequence/{id}', 'SCM@updateProgressSequence');
$router->get('/scm/test-endpoint/{id}', 'SCM@testEndpoint');
$router->post('/scm/add-note/{id}', 'SCM@addNote');

// Routes per SCM Admin (router aggiunge "Controller" automaticamente)
$router->get('/scm-admin', 'SCMAdmin@index');

// Laboratori
$router->get('/scm-admin/laboratories', 'SCMAdmin@laboratories');
$router->get('/scm-admin/laboratories/create', 'SCMAdmin@createLaboratory');
$router->post('/scm-admin/laboratories/store', 'SCMAdmin@storeLaboratory');
$router->get('/scm-admin/laboratories/{id}/edit', 'SCMAdmin@editLaboratory');
$router->post('/scm-admin/laboratories/{id}/update', 'SCMAdmin@updateLaboratory');
$router->post('/scm-admin/laboratories/{id}/toggle', 'SCMAdmin@toggleLaboratory');

// Lanci
$router->get('/scm-admin/launches', 'SCMAdmin@launches');
$router->get('/scm-admin/launches/create', 'SCMAdmin@createLaunch');
$router->post('/scm-admin/launches/store', 'SCMAdmin@storeLaunch');
$router->get('/scm-admin/launches/pending', 'SCMAdmin@pendingLaunches');
$router->get('/scm-admin/launches/{id}/start', 'SCMAdmin@startLaunch');
$router->post('/scm-admin/launches/{id}/start', 'SCMAdmin@startLaunch');
$router->get('/scm-admin/launches/{id}', 'SCMAdmin@showLaunch');
$router->get('/scm-admin/launches/{id}/edit', 'SCMAdmin@editLaunch');
$router->post('/scm-admin/launches/{id}/update', 'SCMAdmin@updateLaunch');
$router->get('/scm-admin/launches/{id}/delete', 'SCMAdmin@deleteLaunch');

// Monitoraggio
$router->get('/scm-admin/monitoring', 'SCMAdmin@monitoring');
$router->get('/scm-admin/monitoring/laboratories', 'SCMAdmin@monitoringLaboratories');
$router->get('/scm-admin/monitoring/launches', 'SCMAdmin@monitoringLaunches');

// Fasi Standard
$router->get('/scm-admin/standard-phases', 'SCMAdmin@standardPhases');
$router->get('/scm-admin/standard-phases/load', 'SCMAdmin@loadStandardPhases');
$router->post('/scm-admin/standard-phases/create', 'SCMAdmin@createStandardPhase');
$router->post('/scm-admin/standard-phases/{id}/update', 'SCMAdmin@updateStandardPhase');
$router->post('/scm-admin/standard-phases/{id}/duplicate', 'SCMAdmin@duplicateStandardPhase');
$router->delete('/scm-admin/standard-phases/{id}/delete', 'SCMAdmin@deleteStandardPhase');
$router->post('/scm-admin/standard-phases/reorder', 'SCMAdmin@reorderStandardPhases');
$router->post('/scm-admin/standard-phases/load-template/{templateName}', 'SCMAdmin@loadTemplate');

// Impostazioni
$router->get('/scm-admin/settings', 'SCMAdmin@settings');
$router->post('/scm-admin/settings/save', 'SCMAdmin@saveSettings');

// API SCM Admin
$router->get('/api/scm/launches/{id}/details', 'SCMAdmin@getLaunchDetails');
$router->post('/api/scm/launches/{id}/status', 'SCMAdmin@updateLaunchStatus');

// Routes per il tracking
$router->get('/tracking', 'Tracking@index');
$router->get('/tracking/search', 'Tracking@search');
$router->get('/tracking/orders', 'Tracking@orders');


// Routes amministrative

// Gestione utenti
$router->get('/users', 'Users@index');
$router->get('/users/create', 'Users@create');
$router->post('/users/store', 'Users@store');
$router->get('/users/{id}/edit', 'Users@edit');
$router->post('/users/update', 'Users@update');
$router->post('/users/delete', 'Users@delete');
$router->get('/users/{id}/permissions', 'Users@permissions');
$router->post('/users/update-permissions', 'Users@updatePermissions');

// Tracking - Monitoraggio Lotti
$router->get('/tracking', 'Tracking@index');
$router->get('/tracking/multisearch', 'Tracking@multiSearch');
$router->get('/tracking/ordersearch', 'Tracking@orderSearch');
$router->get('/tracking/treeview', 'Tracking@treeView');
$router->get('/tracking/lotdetail', 'Tracking@lotDetailManager');
$router->post('/tracking/lotdetail', 'Tracking@lotDetailManager');
$router->get('/tracking/packinglist', 'Tracking@packingList');
$router->get('/tracking/makefiches', 'Tracking@makeFiches');

// Tracking API
$router->post('/tracking/process-links', 'Tracking@processLinks');
$router->post('/tracking/save-links', 'Tracking@saveLinks');
$router->post('/tracking/search-data', 'Tracking@searchData');
$router->get('/tracking/get-tree-data', 'Tracking@getTreeData');
$router->post('/tracking/update-lot', 'Tracking@updateLot');
$router->post('/tracking/delete-lot', 'Tracking@deleteLot');
$router->get('/tracking/search-lot-details', 'Tracking@searchLotDetails');
$router->get('/tracking/search-order-details', 'Tracking@searchOrderDetails');
$router->get('/tracking/search-articolo-details', 'Tracking@searchArticoloDetails');
$router->post('/tracking/check-cartel', 'Tracking@checkCartel');
$router->post('/tracking/load-summary', 'Tracking@loadSummary');
$router->post('/tracking/report-lot-pdf', 'Tracking@generateReportLot');
$router->post('/tracking/report-lot-excel', 'Tracking@generateExcelLot');
$router->post('/tracking/report-cartel-pdf', 'Tracking@generateReportCartel');
$router->post('/tracking/report-cartel-excel', 'Tracking@generateExcelCartel');
$router->post('/tracking/report-fiches-pdf', 'Tracking@generateReportFiches');

// Log attività
$router->get('/logs', 'ActivityLog@index');
$router->get('/logs/{id}', 'ActivityLog@show');
$router->get('/logs/export', 'ActivityLog@export');
$router->post('/logs/delete', 'ActivityLog@delete');
$router->post('/logs/cleanup', 'ActivityLog@cleanup');

// Database Manager
$router->get('/database', 'Database@index');
$router->get('/database/table/{table}', 'Database@table');
$router->get('/database/console', 'Database@console');
$router->post('/database/execute-query', 'Database@executeQuery');
$router->get('/database/backup', 'Database@backup');
$router->get('/database/export', 'Database@export');
$router->post('/database/table-operation', 'Database@tableOperation');
$router->post('/database/table-preview', 'Database@tablePreview');

// Migration System - rimosso sistema custom, ora si usa Artisan per le migrazioni
// System Dashboard - Monitoring, Logs, Performance, Quick Actions

// Etichette DYMO System
$router->get('/etichette', 'Etichette@index');
$router->get('/etichette/decode', 'Etichette@decode');
$router->post('/etichette/process-decode', 'Etichette@processDecodeForm');
$router->get('/etichette/suggestions', 'Etichette@getSuggestions');
$router->get('/etichette/article-details', 'Etichette@getArticleDetails');
$router->post('/etichette/create-article', 'Etichette@createArticle');

// Database Record API
$router->post('/database/record/create', 'Database@createRecord');
$router->post('/database/record/update', 'Database@updateRecord');
$router->post('/database/record/delete', 'Database@deleteRecord');
$router->post('/database/delete-record', 'Database@deleteRecord'); // Alias
$router->get('/database/record/get', 'Database@getRecord');


// Compatibilità con vecchi URL
$router->get('/activity-log', 'ActivityLog@index');
$router->get('/activity-log/export', 'ActivityLog@export');

// Impostazioni Sistema COREGRE
$router->get('/settings', 'Settings@index');
$router->post('/settings/load-section', 'Settings@loadSection');
$router->post('/settings/save', 'Settings@save');
$router->post('/settings/upload-xlsx', 'Settings@uploadXlsx');
$router->get('/settings/import-progress', 'Settings@getImportProgress');

// Rotte per il login e logout
$router->post('/settings/manage-department', 'Settings@manageDepartment');
$router->post('/settings/manage-laboratory', 'Settings@manageLaboratory');
$router->post('/settings/manage-line', 'Settings@manageLine');
$router->post('/settings/refresh-logs', 'Settings@refreshLogs');

// Profilo utente
$router->get('/profile', 'Profile@index');
$router->post('/profile/update', 'Profile@update');
$router->post('/profile/change-password', 'Profile@changePassword');
$router->post('/profile/update-theme', 'Profile@updateTheme');

// API Routes generiche
$router->post('/api/search', 'API@search');

// Notifications Routes
$router->get('/notifications', 'Notifications@index');
$router->get('/notifications/api/list', 'Notifications@apiList');
$router->get('/notifications/api/unread-count', 'Notifications@apiUnreadCount');
$router->post('/notifications/api/mark-read/{id}', 'Notifications@apiMarkRead');
$router->post('/notifications/api/mark-unread/{id}', 'Notifications@apiMarkUnread');
$router->post('/notifications/api/mark-all-read', 'Notifications@apiMarkAllRead');
$router->delete('/notifications/api/delete/{id}', 'Notifications@apiDelete');
$router->delete('/notifications/api/delete-all-read', 'Notifications@apiDeleteAllRead');
$router->post('/notifications/api/create', 'Notifications@apiCreate');
$router->post('/notifications/api/notify-all', 'Notifications@apiNotifyAll');
$router->post('/notifications/api/notify-admins', 'Notifications@apiNotifyAdmins');

// Mobile API Unificato - Nuovo sistema centralizzato
$router->post('/api/mobile/login', 'MobileApi@login');
$router->get('/api/mobile/profile', 'MobileApi@getProfile');
$router->get('/api/mobile/daily-summary', 'MobileApi@getDailySummary');
$router->get('/api/mobile/system-data', 'MobileApi@getSystemData');
$router->post('/api/mobile/check-data', 'MobileApi@checkData');
$router->get('/api/mobile/enabled-modules', 'MobileApi@getEnabledModules');

// Quality Control API Routes (per app Android) - MANTENIAMO PER RETROCOMPATIBILITÀ
$router->post('/api/quality/login', 'QualityApi@login');
$router->post('/api/quality/check-cartellino', 'QualityApi@checkCartellino');
$router->post('/api/quality/check-commessa', 'QualityApi@checkCommessa');
$router->post('/api/quality/options', 'QualityApi@getOptions');
$router->post('/api/quality/save-hermes-cq', 'QualityApi@saveHermesCq');
$router->post('/api/quality/cartellino-details', 'QualityApi@getCartellinoDetails');
$router->get('/api/quality/operator-daily-summary', 'QualityApi@getOperatorDailySummary');
$router->post('/api/quality/save-test', 'QualityApi@saveTest');
$router->get('/api/quality/record-details', 'QualityApi@getRecordDetails');
$router->post('/api/quality/upload-photo', 'QualityApi@uploadPhoto');

// Riparazioni Interne API Routes (per mobile app) - Simplified URLs
$router->get('/api/riparazioni-interne', 'RiparazioniInterneApi@index');
$router->get('/api/riparazioni-interne/show', 'RiparazioniInterneApi@show');
$router->post('/api/riparazioni-interne', 'RiparazioniInterneApi@store');
$router->post('/api/riparazioni-interne/update', 'RiparazioniInterneApi@update');
$router->post('/api/riparazioni-interne/delete', 'RiparazioniInterneApi@delete');
$router->post('/api/riparazioni-interne/complete', 'RiparazioniInterneApi@complete');
$router->post('/api/riparazioni-interne/check-cartellino', 'RiparazioniInterneApi@checkCartellino');
$router->post('/api/riparazioni-interne/check-commessa', 'RiparazioniInterneApi@checkCommessa');
$router->post('/api/riparazioni-interne/options', 'RiparazioniInterneApi@getOptions');
$router->post('/api/riparazioni-interne/cartellino-details', 'RiparazioniInterneApi@getCartellinoDetails');
$router->get('/api/riparazioni-interne/stats', 'RiparazioniInterneApi@getStats');
$router->get('/api/riparazioni-interne/pdf', 'RiparazioniInterneApi@generatePdf');

// Operators API Routes (per mobile app) - MANTENIAMO PER RETROCOMPATIBILITÀ
$router->post('/api/operators/login', 'OperatorsApi@login');
$router->get('/api/operators/profile', 'OperatorsApi@getProfile');
$router->get('/api/operators/reparti', 'OperatorsApi@getReparti');
$router->get('/api/operators/linee', 'OperatorsApi@getLinee');
$router->get('/api/operators/taglie', 'OperatorsApi@getTaglie');
$router->get('/api/operators/daily-summary', 'OperatorsApi@getDailySummary');

// Routes per i file e upload
$router->post('/api/upload', 'FileUpload@upload');
$router->get('/files/{path}', 'FileUpload@serve');

// Error pages
$router->get('/error/404', 'Error@notFound');
$router->get('/error/403', 'Error@forbidden');
$router->get('/error/500', 'Error@serverError');


// Routes per MRP (Material Requirements Planning)
$router->get('/mrp', 'Mrp@index');
$router->get('/mrp/import', 'Mrp@import');
$router->post('/mrp/upload-excel', 'Mrp@uploadExcel');
$router->get('/mrp/materials', 'Mrp@materials');
$router->get('/mrp/material/{id}', 'Mrp@material');
$router->post('/mrp/material/{id}/add-order', 'Mrp@addOrder');
$router->post('/mrp/material/{id}/add-arrival', 'Mrp@addArrival');
$router->post('/mrp/order/delete', 'Mrp@deleteOrder');
$router->post('/mrp/arrival/delete', 'Mrp@deleteArrival');
$router->post('/mrp/material/delete', 'Mrp@deleteMaterial');
$router->get('/mrp/categories', 'Mrp@categories');
$router->post('/mrp/categories/store', 'Mrp@storeCategory');
$router->post('/mrp/categories/update', 'Mrp@updateCategory');
$router->post('/mrp/categories/delete', 'Mrp@deleteCategory');

// Debug routes (solo in development)
if (APP_ENV === 'development') {
    $router->get('/debug/phpinfo', function () {
        phpinfo();
    });

    $router->get('/debug/session', function () {
        echo '<pre>';
        var_dump($_SESSION);
        echo '</pre>';
    });

    $router->get('/debug/routes', function () use ($router) {
        echo '<pre>';
        echo "Available Routes:\n";
        echo "=================\n\n";

        $reflection = new ReflectionClass($router);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routes = $routesProperty->getValue($router);

        foreach ($routes as $route) {
            echo sprintf(
                "%-8s %-30s %s\n",
                $route['method'],
                $route['path'],
                is_string($route['handler']) ? $route['handler'] : 'Closure'
            );
        }
        echo '</pre>';
    });
}

// System Console - Artisan + Shell/SSH (solo admin)
$router->get('/system', 'System@index');
$router->post('/system/execute', 'System@execute');
$router->post('/system/execute-shell', 'System@executeShell');
$router->get('/system/metrics', 'System@metrics');

// Cron Management Routes (solo admin)
$router->get('/cron', 'Cron@index');
$router->get('/cron/show', 'Cron@show');
$router->post('/cron/run', 'Cron@run');
$router->get('/cron/log-detail', 'Cron@logDetail');
$router->post('/cron/clean-logs', 'Cron@cleanLogs');
$router->get('/cron/stats', 'Cron@stats');
$router->get('/cron/test', 'Cron@test');
