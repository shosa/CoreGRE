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
     * Dashboard principale Quality - Unificata con Hermes
     */
    public function index()
    {
        $this->logActivity('QUALITY', 'VIEW_DASHBOARD', 'Visualizzazione dashboard Quality');

        $selectedDate = $this->input('date') ?: date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week', strtotime($selectedDate)));
        $weekEnd = date('Y-m-d', strtotime('sunday this week', strtotime($selectedDate)));

        $data = [
            'pageTitle' => 'Dashboard Controllo Qualità',
            'selectedDate' => $selectedDate,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'stats' => $this->getHermesStats(),
            'weeklyControls' => $this->getWeeklyControls($weekStart, $weekEnd),
            'departmentExceptions' => $this->getDepartmentExceptions(),
            'dailyRecords' => $this->getDailyRecords($selectedDate),
            'reparti' => $this->getHermesDepartments(),
            'tipiDifetti' => $this->getHermesDefectTypes()
        ];

        $this->render('quality.index', $data);
    }

    /**
     * Vista Consulto Record CQ - Tabella completa record con filtri
     */
    public function records()
    {
        $this->logActivity('QUALITY', 'VIEW_RECORDS', 'Visualizzazione consulto record CQ');

        $startDate = $this->input('start_date') ?: date('Y-m-d', strtotime('-7 days'));
        $endDate = $this->input('end_date') ?: date('Y-m-d');
        $reparto = $this->input('reparto');
        $operatore = $this->input('operatore');

        $query = QualityRecord::whereBetween('data_controllo', [$startDate, $endDate])
            ->byType('GRIFFE')
            ->with('qualityExceptions');

        if ($reparto) {
            $query->where('reparto', $reparto);
        }

        if ($operatore) {
            $query->where('operatore', $operatore);
        }

        $records = $query->orderByDesc('data_controllo')->get();

        $data = [
            'pageTitle' => 'Consulto Record CQ',
            'records' => $records,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedReparto' => $reparto,
            'selectedOperatore' => $operatore,
            'reparti' => $this->getHermesDepartments(),
            'operatori' => $this->getUniqueOperators()
        ];

        $this->render('quality.records', $data);
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
     * Genera PDF del report - Stile SAP/Console ASCII
     */
    private function generatePdfReport($data, $reportType)
    {
        // Configura TCPDF - Orientamento orizzontale con font monospace
        $pdf = new TCPDF("L", "mm", "A4", true, "UTF-8", false);
        $pdf->SetMargins(8, 8, 8);
        $pdf->SetAutoPageBreak(true, 8);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetTitle($data['title']);
        $pdf->AddPage();

        // Stile SAP/Console - Font Monospace
        $css = '
        <style>
            body {
                font-family: "Courier New", "Courier", monospace;
                font-size: 8pt;
                line-height: 1.2;
                color: #000;
                background-color: #fff;
            }
            pre {
                font-family: "Courier New", "Courier", monospace;
                font-size: 8pt;
                line-height: 1.2;
                margin: 0;
                padding: 0;
                white-space: pre;
            }
        </style>';

        // Genera contenuto in stile SAP ASCII
        $content = $this->generateSapStyleContent($data, $reportType);

        // Output come pre-formattato
        $html = $css . '<pre>' . $content . '</pre>';

        // Scrivi HTML nel PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output del PDF
        $filename = 'CQ_Hermes_Report_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }

    /**
     * Genera contenuto in stile SAP/Console ASCII
     */
    private function generateSapStyleContent($data, $reportType)
    {
        $output = '';
        $width = 130; // Larghezza totale

        // Box header con ASCII art
        $output .= str_repeat('=', $width) . "\n";
        $output .= $this->centerText('COREGRE SISTEMA GESTIONALE', $width) . "\n";
        $output .= $this->centerText('REPORT CONTROLLO QUALITA\' HERMES', $width) . "\n";
        $output .= str_repeat('=', $width) . "\n";
        $output .= "\n";

        // Informazioni header
        $output .= "Data Generazione: " . date('d/m/Y H:i:s') . "\n";
        if ($reportType === 'daily') {
            $output .= "Data Report    : " . date('d/m/Y', strtotime($data['date'])) . "\n";
        } else {
            $output .= "Periodo        : " . date('d/m/Y', strtotime($data['start_date'])) . " - " . date('d/m/Y', strtotime($data['end_date'])) . "\n";
        }
        $output .= "\n";
        $output .= str_repeat('-', $width) . "\n";

        // Statistiche riepilogative
        $totalRecords = $data['records']->count();
        $totalExceptions = 0;
        foreach ($data['records'] as $record) {
            if ($record->ha_eccezioni) $totalExceptions++;
        }
        $totalOk = $totalRecords - $totalExceptions;
        $percentExceptions = $totalRecords > 0 ? round(($totalExceptions / $totalRecords) * 100, 1) : 0;

        $output .= "RIEPILOGO STATISTICHE\n";
        $output .= str_repeat('-', $width) . "\n";
        $output .= "\n";
        $output .= sprintf("  ┌─────────────────────────────────────────┐\n");
        $output .= sprintf("  │ Totale Controlli       : %6d       │\n", $totalRecords);
        $output .= sprintf("  │ Controlli OK           : %6d       │\n", $totalOk);
        $output .= sprintf("  │ Controlli con Eccezioni: %6d       │\n", $totalExceptions);
        $output .= sprintf("  │ Percentuale Eccezioni  : %6.1f%%     │\n", $percentExceptions);
        $output .= sprintf("  └─────────────────────────────────────────┘\n");
        $output .= "\n";

        // Statistiche per reparto se disponibili
        if (!empty($data['stats']['by_department'])) {
            $output .= "DISTRIBUZIONE PER REPARTO\n";
            $output .= str_repeat('-', $width) . "\n";
            $output .= "\n";
            foreach ($data['stats']['by_department'] as $dept) {
                $deptName = str_pad($dept['reparto'], 30);
                $count = sprintf('%5d', $dept['count']);
                $bar = str_repeat('█', min(50, $dept['count']));
                $output .= sprintf("  %s : %s %s\n", $deptName, $count, $bar);
            }
            $output .= "\n";
        }

        $output .= str_repeat('=', $width) . "\n";
        $output .= "DETTAGLIO RECORD CONTROLLI\n";
        $output .= str_repeat('=', $width) . "\n";
        $output .= "\n";

        // Header tabella
        $output .= sprintf("%-6s | %-12s | %-20s | %-15s | %-15s | %-6s | %-10s | %-16s\n",
            'ID', 'CARTELLINO', 'REPARTO', 'OPERATORE', 'ARTICOLO', 'PAIA', 'STATO', 'DATA/ORA'
        );
        $output .= str_repeat('-', $width) . "\n";

        // Dati tabella
        if (!empty($data['records'])) {
            foreach ($data['records'] as $record) {
                $stato = $record->ha_eccezioni ? '[X] ECCEZ.' : '[√] OK';
                $output .= sprintf("%-6s | %-12s | %-20s | %-15s | %-15s | %-6s | %-10s | %s\n",
                    substr($record->id, 0, 6),
                    substr($record->numero_cartellino, 0, 12),
                    substr($record->reparto, 0, 20),
                    substr($record->operatore, 0, 15),
                    substr($record->articolo, 0, 15),
                    substr($record->paia_totali, 0, 6),
                    $stato,
                    date('d/m/Y H:i', strtotime($record->data_controllo))
                );

                // Se ci sono eccezioni, mostrale indentate
                if ($record->ha_eccezioni && $record->qualityExceptions) {
                    foreach ($record->qualityExceptions as $exc) {
                        $output .= sprintf("       └─> DIFETTO: %s", substr($exc->tipo_difetto, 0, 40));
                        if ($exc->paia_difettose) {
                            $output .= sprintf(" (Paia: %d)", $exc->paia_difettose);
                        }
                        $output .= "\n";
                    }
                }
            }
        } else {
            $output .= $this->centerText('Nessun record trovato per il periodo selezionato', $width) . "\n";
        }

        $output .= str_repeat('-', $width) . "\n";
        $output .= "\n";

        // Footer
        $output .= str_repeat('=', $width) . "\n";
        $output .= $this->centerText('Fine Report', $width) . "\n";
        $output .= $this->centerText('COREGRE Sistema Gestionale - Report CQ Hermes', $width) . "\n";
        $output .= str_repeat('=', $width) . "\n";

        return $output;
    }

    /**
     * Helper per centrare testo
     */
    private function centerText($text, $width)
    {
        $textLen = strlen($text);
        if ($textLen >= $width) return $text;
        $padding = floor(($width - $textLen) / 2);
        return str_repeat(' ', $padding) . $text;
    }

    /**
     * Genera Excel del report - Stile Minimalista
     */
    private function generateExcelReport($data, $reportType)
    {


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Imposta orientamento orizzontale
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Font monospace per tutta la sheet
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Courier New')->setSize(9);

        // Imposta titolo del documento
        $spreadsheet->getProperties()
            ->setTitle($data['title'])
            ->setCreator('COREGRE CQ System')
            ->setDescription('Report CQ Hermes - Stile Console');

        $currentRow = 1;

        // Header stile console
        $sheet->setCellValue("A{$currentRow}", str_repeat('=', 130));
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", '                       COREGRE SISTEMA GESTIONALE');
        $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", '                   REPORT CONTROLLO QUALITA\' HERMES');
        $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", str_repeat('=', 130));
        $currentRow += 2;

        // Info header
        if ($reportType === 'daily') {
            $sheet->setCellValue("A{$currentRow}", 'Data Report    : ' . date('d/m/Y', strtotime($data['date'])));
        } else {
            $sheet->setCellValue("A{$currentRow}", 'Periodo        : ' . date('d/m/Y', strtotime($data['start_date'])) . ' - ' . date('d/m/Y', strtotime($data['end_date'])));
        }
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", 'Data Generazione: ' . date('d/m/Y H:i:s'));
        $currentRow += 2;

        // Statistiche
        $totalRecords = $data['records']->count();
        $totalExceptions = 0;
        foreach ($data['records'] as $record) {
            if ($record->ha_eccezioni) $totalExceptions++;
        }

        $sheet->setCellValue("A{$currentRow}", str_repeat('-', 50));
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", 'RIEPILOGO STATISTICHE');
        $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", str_repeat('-', 50));
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", sprintf('Totale Controlli       : %6d', $totalRecords));
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", sprintf('Controlli OK           : %6d', $totalRecords - $totalExceptions));
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", sprintf('Controlli con Eccezioni: %6d', $totalExceptions));
        $currentRow++;
        $percentExceptions = $totalRecords > 0 ? round(($totalExceptions / $totalRecords) * 100, 1) : 0;
        $sheet->setCellValue("A{$currentRow}", sprintf('Percentuale Eccezioni  : %6.1f%%', $percentExceptions));
        $currentRow += 2;

        // Tabella record
        $sheet->setCellValue("A{$currentRow}", str_repeat('=', 110));
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", 'DETTAGLIO RECORD CONTROLLI');
        $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", str_repeat('=', 110));
        $currentRow += 2;

        // Headers
        $headers = ['ID', 'CARTELLINO', 'REPARTO', 'OPERATORE', 'ARTICOLO', 'PAIA', 'STATO', 'DATA/ORA'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        foreach ($headers as $idx => $header) {
            $sheet->setCellValue("{$cols[$idx]}{$currentRow}", $header);
            $sheet->getStyle("{$cols[$idx]}{$currentRow}")->getFont()->setBold(true);
        }
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", str_repeat('-', 110));
        $currentRow++;

        // Dati
        if (!empty($data['records'])) {
            foreach ($data['records'] as $record) {
                $stato = $record->ha_eccezioni ? '[X] ECCEZ.' : '[√] OK';
                $sheet->setCellValue("A{$currentRow}", $record->id);
                $sheet->setCellValue("B{$currentRow}", $record->numero_cartellino);
                $sheet->setCellValue("C{$currentRow}", $record->reparto);
                $sheet->setCellValue("D{$currentRow}", $record->operatore);
                $sheet->setCellValue("E{$currentRow}", $record->articolo);
                $sheet->setCellValue("F{$currentRow}", $record->paia_totali);
                $sheet->setCellValue("G{$currentRow}", $stato);
                $sheet->setCellValue("H{$currentRow}", date('d/m/Y H:i', strtotime($record->data_controllo)));

                // Evidenzia eccezioni
                if ($record->ha_eccezioni) {
                    $sheet->getStyle("G{$currentRow}")->getFont()->setBold(true);
                }

                $currentRow++;

                // Eccezioni indentate
                if ($record->ha_eccezioni && $record->qualityExceptions) {
                    foreach ($record->qualityExceptions as $exc) {
                        $defectText = '       └─> DIFETTO: ' . $exc->tipo_difetto;
                        if ($exc->paia_difettose) {
                            $defectText .= ' (Paia: ' . $exc->paia_difettose . ')';
                        }
                        $sheet->setCellValue("A{$currentRow}", $defectText);
                        $sheet->mergeCells("A{$currentRow}:H{$currentRow}");
                        $currentRow++;
                    }
                }
            }
        } else {
            $sheet->setCellValue("A{$currentRow}", 'Nessun record trovato per il periodo selezionato');
            $currentRow++;
        }

        $sheet->setCellValue("A{$currentRow}", str_repeat('-', 110));
        $currentRow += 2;

        // Footer
        $sheet->setCellValue("A{$currentRow}", str_repeat('=', 50));
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", 'Fine Report - COREGRE Sistema Gestionale');
        $currentRow++;
        $sheet->setCellValue("A{$currentRow}", str_repeat('=', 50));

        // Auto-size colonne
        foreach ($cols as $col) {
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
     * Operatori unici per filtro consulto
     */
    private function getUniqueOperators()
    {
        return QualityRecord::selectRaw('DISTINCT operatore')
            ->whereNotNull('operatore')
            ->where('operatore', '!=', '')
            ->orderBy('operatore')
            ->get()
            ->pluck('operatore');
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