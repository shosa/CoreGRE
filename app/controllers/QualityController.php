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
     * Genera PDF del report - Stile Windows 95 Bold Colorato
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

        // Stile Windows 95 - Bold e Colorato
        $css = '
        <style>
            body {
                font-family: "Arial", "Helvetica", sans-serif;
                font-size: 10pt;
                line-height: 1.4;
                color: #000;
                background-color: #c0c0c0;
            }
            .win95-window {
                background: linear-gradient(to bottom, #000080 0%, #1084d0 100%);
                border: 3px outset #fff;
                padding: 3px;
                margin-bottom: 15px;
            }
            .win95-titlebar {
                background: linear-gradient(to right, #000080, #1084d0);
                color: white;
                font-weight: bold;
                padding: 4px 8px;
                font-size: 12pt;
                border-bottom: 2px solid #fff;
            }
            .win95-content {
                background-color: #c0c0c0;
                border: 2px inset #808080;
                padding: 10px;
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin: 10px 0;
                background-color: white;
                border: 3px outset #808080;
            }
            th {
                background: linear-gradient(to bottom, #0000aa, #0000ff);
                color: white;
                font-weight: bold;
                padding: 8px 6px;
                text-align: left;
                font-size: 9pt;
                border: 2px outset #fff;
            }
            td {
                padding: 6px;
                border: 1px solid #808080;
                font-size: 9pt;
                background-color: #fff;
            }
            .stat-box {
                display: inline-block;
                background: linear-gradient(to bottom, #dfdfdf, #c0c0c0);
                border: 3px outset #fff;
                padding: 8px 12px;
                margin: 5px;
                font-weight: bold;
                box-shadow: 2px 2px 0px #000;
            }
            .stat-label {
                color: #000080;
                font-size: 9pt;
                font-weight: bold;
            }
            .stat-value {
                color: #ff0000;
                font-size: 14pt;
                font-weight: bold;
            }
            .chart-bar {
                display: inline-block;
                height: 20px;
                background: linear-gradient(to bottom, #0000ff, #0000aa);
                border: 2px outset #fff;
                margin: 2px 0;
            }
            .exception-row {
                background-color: #ff6b6b !important;
                font-weight: bold;
            }
            .ok-row {
                background-color: #90ee90 !important;
            }
            h2 {
                background: linear-gradient(to right, #000080, #0000ff);
                color: white;
                padding: 6px 10px;
                font-size: 14pt;
                font-weight: bold;
                border: 3px outset #fff;
                margin: 15px 0 10px 0;
                box-shadow: 3px 3px 0px #000;
            }
            .footer {
                background-color: #c0c0c0;
                border: 3px outset #fff;
                padding: 8px;
                text-align: center;
                font-weight: bold;
                margin-top: 15px;
            }
        </style>';

        // Genera contenuto Windows 95
        $content = $this->generateWin95StyleContent($data, $reportType);

        // Output HTML
        $html = $css . $content;

        // Scrivi HTML nel PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output del PDF
        $filename = 'CQ_Hermes_Report_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }

    /**
     * Genera contenuto in stile Windows 95 Colorato
     */
    private function generateWin95StyleContent($data, $reportType)
    {
        $html = '';

        // Header Win95 Window
        $html .= '<div class="win95-window">';
        $html .= '<div class="win95-titlebar">COREGRE - Report Controllo Qualità Hermes</div>';
        $html .= '<div class="win95-content">';

        // Info header
        $html .= '<p style="font-weight: bold; margin: 5px 0;">';
        $html .= '<span style="color: #000080;">Data Generazione:</span> ' . date('d/m/Y H:i:s') . '<br>';
        if ($reportType === 'daily') {
            $html .= '<span style="color: #000080;">Data Report:</span> ' . date('d/m/Y', strtotime($data['date']));
        } else {
            $html .= '<span style="color: #000080;">Periodo:</span> ' . date('d/m/Y', strtotime($data['start_date'])) . ' - ' . date('d/m/Y', strtotime($data['end_date']));
        }
        $html .= '</p>';
        $html .= '</div></div>';

        // Calcola statistiche
        $totalRecords = $data['records']->count();
        $totalExceptions = 0;
        foreach ($data['records'] as $record) {
            if ($record->ha_eccezioni) $totalExceptions++;
        }
        $totalOk = $totalRecords - $totalExceptions;
        $percentExceptions = $totalRecords > 0 ? round(($totalExceptions / $totalRecords) * 100, 1) : 0;

        // Stat Boxes Windows 95 Style
        $html .= '<h2>📊 STATISTICHE RIEPILOGATIVE</h2>';
        $html .= '<div style="text-align: center; background-color: #c0c0c0; padding: 10px;">';

        $html .= '<div class="stat-box">';
        $html .= '<div class="stat-label">TOTALE CONTROLLI</div>';
        $html .= '<div class="stat-value">' . $totalRecords . '</div>';
        $html .= '</div>';

        $html .= '<div class="stat-box">';
        $html .= '<div class="stat-label">CONTROLLI OK</div>';
        $html .= '<div class="stat-value" style="color: #008000;">' . $totalOk . '</div>';
        $html .= '</div>';

        $html .= '<div class="stat-box">';
        $html .= '<div class="stat-label">CON ECCEZIONI</div>';
        $html .= '<div class="stat-value" style="color: #ff0000;">' . $totalExceptions . '</div>';
        $html .= '</div>';

        $html .= '<div class="stat-box">';
        $html .= '<div class="stat-label">% ECCEZIONI</div>';
        $html .= '<div class="stat-value" style="color: #ff6600;">' . $percentExceptions . '%</div>';
        $html .= '</div>';

        $html .= '</div>';

        // Grafici a barre per reparto
        if (!empty($data['stats']['by_department'])) {
            $html .= '<h2>📈 DISTRIBUZIONE PER REPARTO</h2>';
            $html .= '<div style="background-color: white; border: 3px inset #808080; padding: 15px; margin: 10px 0;">';

            $colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff', '#ff6600', '#6600ff'];
            $colorIndex = 0;

            foreach ($data['stats']['by_department'] as $dept) {
                $barWidth = min(100, ($dept['count'] / $totalRecords) * 100);
                $color = $colors[$colorIndex % count($colors)];
                $colorIndex++;

                $html .= '<div style="margin: 8px 0;">';
                $html .= '<strong style="color: #000080; display: inline-block; width: 150px;">' . htmlspecialchars($dept['reparto']) . ':</strong> ';
                $html .= '<div class="chart-bar" style="width: ' . $barWidth . '%; background: linear-gradient(to bottom, ' . $color . ', ' . $color . '99);"></div>';
                $html .= ' <strong>' . $dept['count'] . '</strong>';
                $html .= '</div>';
            }

            $html .= '</div>';
        }

        // Tabella Record ORDINATA PER DATA/ORA
        $html .= '<h2>📋 DETTAGLIO RECORD CONTROLLI</h2>';

        if (!empty($data['records'])) {
            // Ordina record per data_controllo
            $records = $data['records']->sortByDesc('data_controllo')->values();

            $html .= '<table>';
            $html .= '<thead><tr>';
            $html .= '<th style="width: 6%;">ID</th>';
            $html .= '<th style="width: 12%;">CARTELLINO</th>';
            $html .= '<th style="width: 15%;">REPARTO</th>';
            $html .= '<th style="width: 12%;">OPERATORE</th>';
            $html .= '<th style="width: 15%;">ARTICOLO</th>';
            $html .= '<th style="width: 8%;">PAIA</th>';
            $html .= '<th style="width: 10%;">STATO</th>';
            $html .= '<th style="width: 15%;">DATA/ORA</th>';
            $html .= '</tr></thead><tbody>';

            foreach ($records as $record) {
                $rowClass = $record->ha_eccezioni ? 'exception-row' : 'ok-row';
                $html .= '<tr class="' . $rowClass . '">';
                $html .= '<td><strong>' . $record->id . '</strong></td>';
                $html .= '<td><strong>' . htmlspecialchars($record->numero_cartellino) . '</strong></td>';
                $html .= '<td>' . htmlspecialchars($record->reparto) . '</td>';
                $html .= '<td>' . htmlspecialchars($record->operatore) . '</td>';
                $html .= '<td>' . htmlspecialchars($record->articolo) . '</td>';
                $html .= '<td style="text-align: center;"><strong>' . $record->paia_totali . '</strong></td>';
                $html .= '<td style="text-align: center; font-weight: bold;">' . ($record->ha_eccezioni ? '❌ ECCEZ.' : '✓ OK') . '</td>';
                $html .= '<td><strong>' . date('d/m/Y H:i', strtotime($record->data_controllo)) . '</strong></td>';
                $html .= '</tr>';

                // Mostra eccezioni
                if ($record->ha_eccezioni && $record->qualityExceptions) {
                    foreach ($record->qualityExceptions as $exc) {
                        $html .= '<tr style="background-color: #ffe6e6;">';
                        $html .= '<td colspan="8" style="padding-left: 30px; font-style: italic; color: #cc0000;">';
                        $html .= '➥ DIFETTO: <strong>' . htmlspecialchars($exc->tipo_difetto) . '</strong>';
                        if ($exc->paia_difettose) {
                            $html .= ' (Paia difettose: ' . $exc->paia_difettose . ')';
                        }
                        $html .= '</td></tr>';
                    }
                }
            }

            $html .= '</tbody></table>';
        } else {
            $html .= '<p style="text-align: center; padding: 20px; font-weight: bold; color: #ff0000;">Nessun record trovato per il periodo selezionato.</p>';
        }

        // Footer Win95
        $html .= '<div class="footer">';
        $html .= '<span style="color: #000080;">COREGRE Sistema Gestionale</span> - Report generato il ' . date('d/m/Y H:i:s');
        $html .= '</div>';

        return $html;
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
     * Genera Excel del report - Solo Dati
     */
    private function generateExcelReport($data, $reportType)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Imposta titolo del documento
        $spreadsheet->getProperties()
            ->setTitle($data['title'])
            ->setCreator('COREGRE CQ System')
            ->setDescription('Report CQ Hermes - Dati');

        $currentRow = 1;

        // Headers
        $headers = ['ID', 'Data/Ora Controllo', 'Cartellino', 'Articolo', 'Reparto', 'Operatore', 'Paia Totali', 'Ha Eccezioni', 'Tipo Difetto', 'Paia Difettose'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

        foreach ($headers as $idx => $header) {
            $sheet->setCellValue("{$cols[$idx]}{$currentRow}", $header);
        }
        $currentRow++;

        // Dati - ORDINATI PER DATA/ORA
        if (!empty($data['records'])) {
            // Ordina record per data_controllo DESC
            $records = $data['records']->sortByDesc('data_controllo')->values();

            foreach ($records as $record) {
                // Se non ha eccezioni, una sola riga
                if (!$record->ha_eccezioni || $record->qualityExceptions->count() === 0) {
                    $sheet->setCellValue("A{$currentRow}", $record->id);
                    $sheet->setCellValue("B{$currentRow}", date('d/m/Y H:i:s', strtotime($record->data_controllo)));
                    $sheet->setCellValue("C{$currentRow}", $record->numero_cartellino);
                    $sheet->setCellValue("D{$currentRow}", $record->articolo);
                    $sheet->setCellValue("E{$currentRow}", $record->reparto);
                    $sheet->setCellValue("F{$currentRow}", $record->operatore);
                    $sheet->setCellValue("G{$currentRow}", $record->paia_totali);
                    $sheet->setCellValue("H{$currentRow}", 'NO');
                    $sheet->setCellValue("I{$currentRow}", '');
                    $sheet->setCellValue("J{$currentRow}", '');
                    $currentRow++;
                } else {
                    // Se ha eccezioni, una riga per ogni eccezione
                    foreach ($record->qualityExceptions as $exc) {
                        $sheet->setCellValue("A{$currentRow}", $record->id);
                        $sheet->setCellValue("B{$currentRow}", date('d/m/Y H:i:s', strtotime($record->data_controllo)));
                        $sheet->setCellValue("C{$currentRow}", $record->numero_cartellino);
                        $sheet->setCellValue("D{$currentRow}", $record->articolo);
                        $sheet->setCellValue("E{$currentRow}", $record->reparto);
                        $sheet->setCellValue("F{$currentRow}", $record->operatore);
                        $sheet->setCellValue("G{$currentRow}", $record->paia_totali);
                        $sheet->setCellValue("H{$currentRow}", 'SI');
                        $sheet->setCellValue("I{$currentRow}", $exc->tipo_difetto);
                        $sheet->setCellValue("J{$currentRow}", $exc->paia_difettose ?? '');
                        $currentRow++;
                    }
                }
            }
        }

        // Auto-size colonne
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output Excel
        $filename = 'CQ_Hermes_Data_' . date('Y-m-d_H-i-s') . '.xlsx';

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