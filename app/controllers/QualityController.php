<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\QualityRecord;
use App\Models\QualityException;
use App\Models\QualityDepartment;
use App\Models\QualityDefectType;
use App\Models\ActivityLog;

/**
 * Quality Controller - Sistema CQ Hermes (come legacy manageHermes)
 * Gestisce il controllo qualit Hermes con tutte le funzioni del legacy
 * Focus su: Dashboard, Record consultazione, Gestione Operatori, Gestione Reparti, Tipi Difetti, Barcode
 */
class QualityController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->requirePermission('quality');
    }

    /**
     * Dashboard principale Quality Hermes - con tabs come legacy
     */
    public function index()
    {

        $data = [
            'pageTitle' => 'CQ Hermes - Sistema Controllo Qualit Luxury',
            'stats' => $this->getHermesStats()
        ];

        $this->render('quality.index', $data);
    }

    /**
     * Vista Hermes CQ - Tab Records del legacy
     */
    public function hermes()
    {
        $this->logActivity('QUALITY', 'VIEW_HERMES', 'Visualizzazione dashboard Hermes CQ');

        $selectedDate = $this->input('date') ?: date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week', strtotime($selectedDate)));
        $weekEnd = date('Y-m-d', strtotime('sunday this week', strtotime($selectedDate)));

        $data = [
            'pageTitle' => 'Dashboard Hermes CQ',
            'selectedDate' => $selectedDate,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'weeklyControls' => $this->getWeeklyControls($weekStart, $weekEnd),
            'departmentExceptions' => $this->getDepartmentExceptions(),
            'dailyRecords' => $this->getDailyRecords($selectedDate),
            'reparti' => $this->getHermesDepartments(),
            'tipiDifetti' => $this->getHermesDefectTypes()
        ];

        $this->render('quality.hermes', $data);
    }

    /**
     * Gestione Reparti CQ
     */
    public function departments()
    {
        $this->logActivity('QUALITY', 'VIEW_DEPARTMENTS', 'Visualizzazione reparti CQ');

        $data = [
            'pageTitle' => 'Reparti CQ - Gestione Reparti',
            'departments' => $this->getAllDepartments()
        ];

        $this->render('quality.departments', $data);
    }

    /**
     * Gestione Tipi Difetti CQ
     */
    public function defects()
    {
        $this->logActivity('QUALITY', 'VIEW_DEFECTS', 'Visualizzazione tipi difetti CQ');

        $data = [
            'pageTitle' => 'Tipi Difetti CQ - Gestione Difetti',
            'defects' => $this->getAllDefects()
        ];

        $this->render('quality.defects', $data);
    }

    /**
     * Gestione Report CQ
     */
    public function reports()
    {
        $this->logActivity('QUALITY', 'VIEW_REPORTS', 'Visualizzazione pagina report CQ');

        $data = [
            'pageTitle' => 'Report CQ - Generazione Report'
        ];

        $this->render('quality.reports', $data);
    }

    /**
     * API: Gestione Reparti (CRUD completo come legacy)
     */
    public function manageDepartment()
    {
        if (!$this->isPost() || !$this->isAjax()) {
            $this->json(['error' => 'Richiesta non valida'], 400);
            return;
        }

        try {
            $action = $this->input('action');

            if ($action === 'create') {
                $data = [
                    'nome_reparto' => $this->input('nome_reparto'),
                    'attivo' => $this->input('attivo') ? 1 : 0,
                    'ordine' => (int) $this->input('ordine', 0)
                ];

                $this->createDepartment($data);
                $this->logActivity(
                    'QUALITY',
                    'CREATE_DEPARTMENT',
                    'Creato reparto CQ',
                    "Nome: {$data['nome_reparto']}"
                );

            } elseif ($action === 'update') {
                $id = (int) $this->input('id');
                $data = [
                    'nome_reparto' => $this->input('nome_reparto'),
                    'attivo' => $this->input('attivo') ? 1 : 0,
                    'ordine' => (int) $this->input('ordine', 0)
                ];

                $this->updateDepartment($id, $data);
                $this->logActivity(
                    'QUALITY',
                    'UPDATE_DEPARTMENT',
                    'Modificato reparto CQ',
                    "ID: {$id}, Nome: {$data['nome_reparto']}"
                );

            } elseif ($action === 'delete') {
                $id = (int) $this->input('id');
                $this->deleteDepartment($id);
                $this->logActivity(
                    'QUALITY',
                    'DELETE_DEPARTMENT',
                    'Eliminato reparto CQ',
                    "ID: {$id}"
                );
            }

            $this->json(['success' => true]);

        } catch (Exception $e) {
            error_log("Errore gestione reparto: " . $e->getMessage());
            $this->json(['error' => 'Errore durante la gestione del reparto'], 500);
        }
    }

    /**
     * API: Gestione Tipi Difetti (CRUD completo come legacy)
     */
    public function manageDefect()
    {
        if (!$this->isPost() || !$this->isAjax()) {
            $this->json(['error' => 'Richiesta non valida'], 400);
            return;
        }

        try {
            $action = $this->input('action');

            if ($action === 'create') {
                $data = [
                    'descrizione' => $this->input('descrizione'),
                    'categoria' => $this->input('categoria'),
                    'attivo' => $this->input('attivo') ? 1 : 0,
                    'ordine' => (int) $this->input('ordine', 0)
                ];

                $this->createDefect($data);
                $this->logActivity(
                    'QUALITY',
                    'CREATE_DEFECT',
                    'Creato tipo difetto CQ',
                    "Descrizione: {$data['descrizione']}"
                );

            } elseif ($action === 'update') {
                $id = (int) $this->input('id');
                $data = [
                    'descrizione' => $this->input('descrizione'),
                    'categoria' => $this->input('categoria'),
                    'attivo' => $this->input('attivo') ? 1 : 0,
                    'ordine' => (int) $this->input('ordine', 0)
                ];

                $this->updateDefect($id, $data);
                $this->logActivity(
                    'QUALITY',
                    'UPDATE_DEFECT',
                    'Modificato tipo difetto CQ',
                    "ID: {$id}, Descrizione: {$data['descrizione']}"
                );

            } elseif ($action === 'delete') {
                $id = (int) $this->input('id');
                $this->deleteDefect($id);
                $this->logActivity(
                    'QUALITY',
                    'DELETE_DEFECT',
                    'Eliminato tipo difetto CQ',
                    "ID: {$id}"
                );
            }

            $this->json(['success' => true]);

        } catch (Exception $e) {
            error_log("Errore gestione tipo difetto: " . $e->getMessage());
            $this->json(['error' => 'Errore durante la gestione del tipo difetto'], 500);
        }
    }

    /**
     * Metodi privati per consultazione e gestione dati
     */

    /**
     * Statistiche dashboard Hermes
     */
    private function getHermesStats()
    {
        try {
            $stats = [];

            // Record Hermes oggi - solo GRIFFE
            $stats['today_records'] = QualityRecord::today()
                ->byType('GRIFFE')
                ->count();

            // Record questa settimana - solo GRIFFE
            $stats['week_records'] = QualityRecord::thisWeek()
                ->byType('GRIFFE')
                ->count();

            // Eccezioni questo mese
            $stats['month_exceptions'] = QualityException::thisMonth()->count();

            // Reparti attivi
            $stats['active_departments'] = QualityDepartment::active()->count();

            return $stats;
        } catch (Exception $e) {
            error_log("Errore calcolo statistiche Hermes: " . $e->getMessage());
            return [
                'today_records' => 0,
                'week_records' => 0,
                'month_exceptions' => 0,
                'active_departments' => 0
            ];
        }
    }

    /**
     * Record Hermes recenti - solo GRIFFE
     */
    private function getRecentHermesRecords($limit = 10)
    {
        return QualityRecord::where('tipo_cq', 'GRIFFE')
            ->orderByDesc('data_controllo')
            ->limit($limit)
            ->get();
    }

    /**
     * Reparti Hermes
     */
    private function getHermesDepartments()
    {
        return QualityDepartment::where('attivo', 1)
            ->orderBy('ordine')
            ->orderBy('nome_reparto')
            ->get();
    }

    /**
     * Tipi difetti Hermes
     */
    private function getHermesDefectTypes()
    {
        return QualityDefectType::where('attivo', 1)
            ->orderBy('ordine')
            ->orderBy('descrizione')
            ->get();
    }

    /**
     * Tutti i reparti
     */
    private function getAllDepartments()
    {
        return QualityDepartment::orderBy('ordine')
            ->orderBy('nome_reparto')
            ->get();
    }

    /**
     * Tutti i tipi difetti
     */
    private function getAllDefects()
    {
        return QualityDefectType::orderBy('categoria')
            ->orderBy('ordine')
            ->orderBy('descrizione')
            ->get();
    }


    /**
     * CRUD Reparti (come legacy)
     */
    private function createDepartment($data)
    {
        return QualityDepartment::create([
            'nome_reparto' => $data['nome_reparto'],
            'attivo' => $data['attivo'],
            'ordine' => $data['ordine']
        ]);
    }

    private function updateDepartment($id, $data)
    {
        $department = QualityDepartment::find($id);
        if (!$department) return false;

        return $department->update([
            'nome_reparto' => $data['nome_reparto'],
            'attivo' => $data['attivo'],
            'ordine' => $data['ordine']
        ]);
    }

    private function deleteDepartment($id)
    {
        $department = QualityDepartment::find($id);
        if (!$department) return false;

        return $department->delete();
    }

    /**
     * CRUD Tipi Difetti (come legacy)
     */
    private function createDefect($data)
    {
        return QualityDefectType::create([
            'descrizione' => $data['descrizione'],
            'categoria' => $data['categoria'],
            'attivo' => $data['attivo'],
            'ordine' => $data['ordine']
        ]);
    }

    private function updateDefect($id, $data)
    {
        $defect = QualityDefectType::find($id);
        if (!$defect) return false;

        return $defect->update([
            'descrizione' => $data['descrizione'],
            'categoria' => $data['categoria'],
            'attivo' => $data['attivo'],
            'ordine' => $data['ordine']
        ]);
    }

    private function deleteDefect($id)
    {
        $defect = QualityDefectType::find($id);
        if (!$defect) return false;

        return $defect->delete();
    }

    /**
     * API: Generazione Report
     */
    public function generateReport()
    {
        try {
            $action = $this->input('action');
            $format = $this->input('format', 'pdf');

            // Determina l'azione dai parametri se non specificata
            if (!$action) {
                if ($this->input('report_date')) {
                    $action = 'daily_report';
                } elseif ($this->input('start_date') && $this->input('end_date')) {
                    $action = 'period_report';
                } else {
                    throw new Exception('Parametri report mancanti');
                }
            }

            if ($action === 'daily_report') {
                $reportDate = $this->input('report_date', date('Y-m-d'));
                $reportType = $this->input('report_type', 'summary');

                $this->logActivity(
                    'QUALITY',
                    'generate_daily_report',
                    'Generato report giornaliero CQ',
                    "Data: {$reportDate}, Tipo: {$reportType}, Formato: {$format}"
                );

                $this->generateDailyReport($reportDate, $reportType, $format);

            } elseif ($action === 'period_report') {
                $startDate = $this->input('start_date', date('Y-m-d', strtotime('-7 days')));
                $endDate = $this->input('end_date', date('Y-m-d'));
                $reportType = $this->input('report_type', 'period_summary');

                $this->logActivity(
                    'QUALITY',
                    'generate_period_report',
                    'Generato report periodo CQ',
                    "Da: {$startDate} A: {$endDate}, Tipo: {$reportType}, Formato: {$format}"
                );

                $this->generatePeriodReport($startDate, $endDate, $reportType, $format);
            } else {
                throw new Exception('Tipo report non riconosciuto');
            }

        } catch (Exception $e) {
            error_log("Errore generazione report: " . $e->getMessage());
            $this->json(['error' => 'Errore durante la generazione del report: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Genera report giornaliero
     */
    private function generateDailyReport($date, $type, $format)
    {
        // Usa Eloquent per dati del giorno
        $records = QualityRecord::whereDate('data_controllo', $date)
            ->byType('GRIFFE')
            ->orderByDesc('data_controllo')
            ->get();

        $reportData = [
            'title' => 'Report CQ Hermes - ' . date('d/m/Y', strtotime($date)),
            'date' => $date,
            'type' => $type,
            'records' => $records,
            'stats' => $this->getDailyStats($date)
        ];

        if ($format === 'pdf') {
            $this->generatePdfReport($reportData, 'daily');
        } else {
            $this->generateExcelReport($reportData, 'daily');
        }
    }

    /**
     * Genera report di periodo
     */
    private function generatePeriodReport($startDate, $endDate, $type, $format)
    {
        // Usa Eloquent per dati del periodo
        $records = QualityRecord::whereBetween('data_controllo', [$startDate, $endDate])
            ->byType('GRIFFE')
            ->orderByDesc('data_controllo')
            ->get();

        $reportData = [
            'title' => 'Report CQ Hermes - Periodo ' . date('d/m/Y', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate)),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'type' => $type,
            'records' => $records,
            'stats' => $this->getPeriodStats($startDate, $endDate)
        ];

        if ($format === 'pdf') {
            $this->generatePdfReport($reportData, 'period');
        } else {
            $this->generateExcelReport($reportData, 'period');
        }
    }

    /**
     * Statistiche giornaliere
     */
    private function getDailyStats($date)
    {
        $stats = [];

        // Totale controlli usando Eloquent
        $stats['total_controls'] = QualityRecord::whereDate('data_controllo', $date)
            ->byType('GRIFFE')
            ->count();

        // Controlli per reparto usando Eloquent
        $stats['by_department'] = QualityRecord::whereDate('data_controllo', $date)
            ->byType('GRIFFE')
            ->selectRaw('reparto, COUNT(*) as count')
            ->groupBy('reparto')
            ->get();

        return $stats;
    }

    /**
     * Statistiche periodo
     */
    private function getPeriodStats($startDate, $endDate)
    {
        $stats = [];

        // Totale controlli usando Eloquent
        $stats['total_controls'] = QualityRecord::whereBetween('data_controllo', [$startDate, $endDate])
            ->byType('GRIFFE')
            ->count();

        // Controlli per giorno usando Eloquent
        $stats['by_day'] = QualityRecord::whereBetween('data_controllo', [$startDate, $endDate])
            ->byType('GRIFFE')
            ->selectRaw('DATE(data_controllo) as day, COUNT(*) as count')
            ->groupByRaw('DATE(data_controllo)')
            ->orderBy('day')
            ->get();

        return $stats;
    }

    /**
     * Genera PDF del report
     */
    private function generatePdfReport($data, $reportType)
    {


        // Configura TCPDF - Orientamento orizzontale
        $pdf = new TCPDF("L", "mm", "A4", true, "UTF-8", false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetTitle($data['title']);
        $pdf->AddPage();

        // CSS per il report - Layout migliorato e pi leggibile
        $css = '
        <style>
            body {
                font-family: "DejaVu Sans", helvetica, arial, sans-serif;
                font-size: 11pt;
                line-height: 1.4;
                color: #333;
            }
            h1 {
                font-size: 20pt;
                color: #2563eb;
                text-align: center;
                margin-bottom: 25px;
                font-weight: bold;
                border-bottom: 2px solid #2563eb;
                padding-bottom: 10px;
            }
            h2 {
                font-size: 16pt;
                color: #1e40af;
                margin: 20px 0 15px 0;
                border-bottom: 1px solid #cbd5e1;
                padding-bottom: 5px;
                font-weight: bold;
            }
            h3 {
                font-size: 14pt;
                color: #475569;
                margin: 15px 0 10px 0;
                font-weight: bold;
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin: 15px 0;
                border: 1px solid #e2e8f0;
                table-layout: auto;
            }
            th {
                background-color: #3b82f6;
                color: white;
                font-weight: bold;
                padding: 12px 8px;
                text-align: left;
                font-size: 10pt;
                border: 1px solid #2563eb;
                white-space: nowrap;
            }
            td {
                padding: 10px 8px;
                border: 1px solid #e2e8f0;
                font-size: 10pt;
                background-color: #ffffff;
                white-space: nowrap;
            }
            .stats-grid {
                background-color: #f0f9ff;
                padding: 20px;
                margin: 20px 0;
                border: 1px solid #bae6fd;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .stat-item {
                display: inline-block;
                margin: 5px 15px 5px 0;
                padding: 10px 15px;
                background: white;
                border-radius: 6px;
                border-left: 4px solid #3b82f6;
                font-size: 11pt;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            .exception-row {
                background-color: #ffffff !important;
            }
            .ok-row {
                background-color: #ffffff !important;
            }
            .header-info {
                text-align: center;
                background-color: #f1f5f9;
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 8px;
                border: 1px solid #cbd5e1;
            }
            .footer {
                margin-top: 40px;
                text-align: center;
                font-size: 9pt;
                color: #64748b;
                border-top: 1px solid #e2e8f0;
                padding-top: 15px;
            }
        </style>';

        // Header informativo
        $html = $css;
        $html .= '<div class="header-info">';
        $html .= '<h1>' . htmlspecialchars($data['title']) . '</h1>';
        $html .= '<p><strong>Generato il:</strong> ' . date('d/m/Y \a\l\l\e H:i:s') . '</p>';
        if ($reportType === 'daily') {
            $html .= '<p><strong>Data:</strong> ' . date('d/m/Y', strtotime($data['date'])) . '</p>';
        } else {
            $html .= '<p><strong>Periodo:</strong> Dal ' . date('d/m/Y', strtotime($data['start_date'])) . ' al ' . date('d/m/Y', strtotime($data['end_date'])) . '</p>';
        }
        $html .= '</div>';

        // Statistiche in grid
        if (!empty($data['stats'])) {
            $html .= '<div class="stats-grid">';
            $html .= '<h2> Riepilogo Statistiche</h2>';
            $html .= '<div class="stat-item"> <strong>Totale Controlli:</strong> ' . ($data['stats']['total_controls'] ?? $data['records']->count()) . '</div>';

            $totalExceptions = 0;
            foreach ($data['records'] as $record) {
                if ($record->ha_eccezioni) $totalExceptions++;
            }
            $html .= '<div class="stat-item"> <strong>Controlli con Eccezioni:</strong> ' . $totalExceptions . '</div>';
            $html .= '<div class="stat-item"> <strong>Controlli OK:</strong> ' . ($data['records']->count() - $totalExceptions) . '</div>';

            if ($totalExceptions > 0) {
                $percentageExceptions = round(($totalExceptions / $data['records']->count()) * 100, 1);
                $html .= '<div class="stat-item"> <strong>% Eccezioni:</strong> ' . $percentageExceptions . '%</div>';
            }

            // Statistiche per reparto
            if (!empty($data['stats']['by_department'])) {
                $html .= '<h3> Controlli per Reparto</h3>';
                foreach ($data['stats']['by_department'] as $dept) {
                    $html .= '<div class="stat-item"> ' . htmlspecialchars($dept['reparto']) . ': <strong>' . $dept['count'] . '</strong> controlli</div>';
                }
            }
            $html .= '</div>';
        }

        // Tabella record
        if (!empty($data['records'])) {
            $html .= '<h2>Record Controlli Qualit</h2>';
            $html .= '<table>';
            $html .= '<thead><tr>';
            $html .= '<th>ID</th>';
            $html .= '<th>Cartellino</th>';
            $html .= '<th>Reparto</th>';
            $html .= '<th>Operatore</th>';
            $html .= '<th>Articolo</th>';
            $html .= '<th>Paia</th>';
            $html .= '<th>Eccezioni</th>';
            $html .= '<th>Data</th>';
            $html .= '</tr></thead><tbody>';

            foreach ($data['records'] as $record) {
                $rowClass = $record->ha_eccezioni ? 'exception-row' : 'ok-row';
                $html .= '<tr class="' . $rowClass . '">';
                $html .= '<td>' . htmlspecialchars($record->id) . '</td>';
                $html .= '<td>' . htmlspecialchars($record->numero_cartellino) . '</td>';
                $html .= '<td>' . htmlspecialchars($record->reparto) . '</td>';
                $html .= '<td>' . htmlspecialchars($record->operatore) . '</td>';
                $html .= '<td>' . htmlspecialchars($record->articolo) . '</td>';
                $html .= '<td>' . htmlspecialchars($record->paia_totali) . '</td>';
                $html .= '<td style="font-weight: bold; color: ' . ($record->ha_eccezioni ? '#dc2626' : '#16a34a') . ';">' . ($record->ha_eccezioni ? ' S' : ' No') . '</td>';
                $html .= '<td>' . date('d/m/Y H:i', strtotime($record->data_controllo)) . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
        } else {
            $html .= '<p>Nessun record trovato per il periodo selezionato.</p>';
        }

        // Footer professionale
        $html .= '<div class="footer">';
        $html .= '<p><strong>Report CQ Hermes</strong> - Sistema Controllo Qualit</p>';
        $html .= '<p>Report generato il ' . date('d/m/Y \a\l\l\e H:i:s') . ' | COREGRE Sistema Gestionale</p>';
        $html .= '</div>';

        // Scrivi HTML nel PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output del PDF
        $filename = 'CQ_Hermes_Report_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }

    /**
     * Genera Excel del report
     */
    private function generateExcelReport($data, $reportType)
    {


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Imposta orientamento orizzontale
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Imposta titolo del documento
        $spreadsheet->getProperties()
            ->setTitle($data['title'])
            ->setCreator('COREGRE CQ System')
            ->setDescription('Report CQ Hermes generato automaticamente');

        // Titolo principale
        $sheet->setCellValue('A1', $data['title']);
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new Color('FF6B35'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $currentRow = 3;

        if ($reportType === 'daily' && isset($data['stats'])) {
            // Sezione statistiche
            $sheet->setCellValue("A{$currentRow}", 'STATISTICHE GIORNALIERE');
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(12);
            $currentRow++;

            $sheet->setCellValue("A{$currentRow}", 'Totale Controlli:');
            $sheet->setCellValue("B{$currentRow}", $data['stats']['total_controls']);
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
            $currentRow++;

            if (!empty($data['stats']['by_department'])) {
                $sheet->setCellValue("A{$currentRow}", 'Per Reparto:');
                $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
                $currentRow++;

                foreach ($data['stats']['by_department'] as $dept) {
                    $sheet->setCellValue("B{$currentRow}", $dept['reparto']);
                    $sheet->setCellValue("C{$currentRow}", $dept['count'] . ' controlli');
                    $currentRow++;
                }
            }

            $currentRow += 2; // Spazio
        }

        // Sezione dati
        if (!empty($data['records'])) {
            $sheet->setCellValue("A{$currentRow}", 'RECORD CONTROLLI QUALIT');
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(12);
            $currentRow += 2;

            // Headers tabella
            $headers = ['ID', 'Cartellino', 'Reparto', 'Operatore', 'Articolo', 'Paia', 'Eccezioni', 'Data'];
            $headerRow = $currentRow;

            foreach ($headers as $col => $header) {
                $cellAddress = chr(65 + $col) . $headerRow;
                $sheet->setCellValue($cellAddress, $header);
            }

            // Stile headers
            $sheet->getStyle("A{$headerRow}:H{$headerRow}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FF6B35');
            $sheet->getStyle("A{$headerRow}:H{$headerRow}")->getFont()
                ->setBold(true)
                ->setColor(new Color('FFFFFF'));

            $currentRow++;

            // Dati
            foreach ($data['records'] as $record) {
                $sheet->setCellValue("A{$currentRow}", $record->id);
                $sheet->setCellValue("B{$currentRow}", $record->numero_cartellino);
                $sheet->setCellValue("C{$currentRow}", $record->reparto);
                $sheet->setCellValue("D{$currentRow}", $record->operatore);
                $sheet->setCellValue("E{$currentRow}", $record->articolo);
                $sheet->setCellValue("F{$currentRow}", $record->paia_totali);
                $sheet->setCellValue("G{$currentRow}", $record->ha_eccezioni ? 'S' : 'No');
                $sheet->setCellValue("H{$currentRow}", date('d/m/Y H:i', strtotime($record->data_controllo)));

                // Rimuovo colori alternati - sfondo bianco uniforme

                $currentRow++;
            }
        } else {
            $sheet->setCellValue("A{$currentRow}", 'Nessun record trovato per il periodo selezionato.');
            $sheet->getStyle("A{$currentRow}")->getFont()->setItalic(true);
            $currentRow++;
        }

        // Timestamp generazione
        $currentRow += 2;
        $sheet->setCellValue("A{$currentRow}", 'Report generato il ' . date('d/m/Y \a\l\l\e H:i:s'));
        $sheet->getStyle("A{$currentRow}")->getFont()->setSize(9)->setColor(new Color('666666'));

        // Auto-size colonne
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output Excel
        $filename = 'CQ_Hermes_Report_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Controlli settimanali per il grafico
     */
    private function getWeeklyControls($weekStart, $weekEnd)
    {
        return QualityRecord::whereBetween('data_controllo', [$weekStart, $weekEnd])
            ->selectRaw('DATE(data_controllo) as data, COUNT(*) as controlli')
            ->groupByRaw('DATE(data_controllo)')
            ->orderBy('data')
            ->get();
    }

    /**
     * Eccezioni per reparto
     */
    private function getDepartmentExceptions()
    {
        // Ottieni i record degli ultimi 30 giorni con le loro eccezioni
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));

        return QualityRecord::with(['qualityExceptions', 'department'])
            ->where('data_controllo', '>=', $thirtyDaysAgo)
            ->get()
            ->groupBy('reparto')
            ->map(function($records, $reparto) {
                $totaleControlli = $records->count();
                $totaleEccezioni = $records->sum(function($record) {
                    return $record->qualityExceptions->count();
                });

                $percentualeEccezioni = $totaleControlli > 0
                    ? round(($totaleEccezioni / $totaleControlli) * 100, 2)
                    : 0;

                return [
                    'reparto' => $reparto,
                    'totale_controlli' => $totaleControlli,
                    'totale_eccezioni' => $totaleEccezioni,
                    'percentuale_eccezioni' => $percentualeEccezioni
                ];
            })
            ->filter(function($item) {
                return $item['totale_controlli'] > 0;
            })
            ->sortByDesc('percentuale_eccezioni')
            ->values()
            ->toArray();
    }

    /**
     * Record del giorno selezionato
     */
    private function getDailyRecords($selectedDate)
    {
        return QualityRecord::with('qualityExceptions')
            ->whereDate('data_controllo', $selectedDate)
            ->orderByDesc('data_controllo')
            ->get()
            ->map(function($record) {
                $record->numero_eccezioni = $record->qualityExceptions->count();
                $record->tipi_difetti = $record->qualityExceptions->pluck('tipo_difetto')->implode(', ');
                return $record;
            });
    }

    /**
     * Salva un nuovo tipo di difetto
     */
    public function saveDefectType()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $descrizione = trim($this->input('descrizione'));
        $categoria = trim($this->input('categoria'));

        if (empty($descrizione)) {
            $this->json(['success' => false, 'message' => 'Descrizione richiesta']);
            return;
        }

        try {
            // Verifica se esiste già usando Eloquent
            $existing = QualityDefectType::where('descrizione', $descrizione)->first();

            if ($existing) {
                $this->json(['success' => false, 'message' => 'Tipo di difetto già esistente']);
                return;
            }

            // Ottieni il prossimo ordine usando Eloquent
            $maxOrder = QualityDefectType::max('ordine') ?? 0;
            $ordine = $maxOrder + 1;

            // Inserisci nuovo tipo usando Eloquent
            QualityDefectType::create([
                'descrizione' => $descrizione,
                'categoria' => $categoria ?: null,
                'ordine' => $ordine
            ]);

            $this->logActivity('QUALITY', 'ADD_DEFECT_TYPE', 'Aggiunto tipo difetto: ' . $descrizione);
            $this->json(['success' => true, 'message' => 'Tipo di difetto aggiunto con successo']);

        } catch (Exception $e) {
            error_log("Errore salvataggio tipo difetto: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore del server']);
        }
    }

    /**
     * Elimina un tipo di difetto
     */
    public function deleteDefectType()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Metodo non consentito'], 405);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int) ($data['id'] ?? 0);

        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID richiesto']);
            return;
        }

        try {
            // Trova il tipo di difetto
            $defect = QualityDefectType::find($id);

            if (!$defect) {
                $this->json(['success' => false, 'message' => 'Tipo di difetto non trovato']);
                return;
            }

            // Verifica se è utilizzato nelle eccezioni usando Eloquent
            $used = QualityException::where('tipo_difetto', $defect->descrizione)->count();

            if ($used > 0) {
                $this->json(['success' => false, 'message' => 'Impossibile eliminare: tipo di difetto in uso']);
                return;
            }

            // Salva descrizione prima di eliminare
            $descrizione = $defect->descrizione;

            // Elimina usando Eloquent
            $defect->delete();

            $this->logActivity('QUALITY', 'DELETE_DEFECT_TYPE', 'Eliminato tipo difetto: ' . $descrizione);
            $this->json(['success' => true, 'message' => 'Tipo di difetto eliminato con successo']);

        } catch (Exception $e) {
            error_log("Errore eliminazione tipo difetto: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore del server']);
        }
    }

    /**
     * API per recuperare dati dinamici della dashboard Hermes
     */
    public function getHermesData()
    {
        $selectedDate = $this->input('date') ?: date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week', strtotime($selectedDate)));
        $weekEnd = date('Y-m-d', strtotime('sunday this week', strtotime($selectedDate)));

        try {
            $data = [
                'selectedDate' => $selectedDate,
                'weekStart' => $weekStart,
                'weekEnd' => $weekEnd,
                'weeklyControls' => $this->getWeeklyControls($weekStart, $weekEnd),
                'departmentExceptions' => $this->getDepartmentExceptions(),
                'dailyRecords' => $this->getDailyRecords($selectedDate),
                'stats' => [
                    'controlliOggi' => $this->getDailyRecords($selectedDate)->count(),
                    'eccezioniOggi' => $this->getDailyRecords($selectedDate)->sum('numero_eccezioni'),
                    'reparti' => $this->getHermesDepartments()->count(),
                    'tipiDifetti' => $this->getHermesDefectTypes()->count()
                ]
            ];

            $this->json(['success' => true, 'data' => $data]);

        } catch (Exception $e) {
            error_log("Errore getHermesData: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore del server']);
        }
    }
}