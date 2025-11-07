<?php
/**
 * Produzione Controller
 * Gestisce il sistema di produzione e spedizione
 */

use App\Models\ProductionRecord;
use App\Models\Setting;

class ProduzioneController extends BaseController
{
    /**
     * Index - Redirect al calendario
     */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        // Redirect al calendario
        $this->redirect($this->url('/produzione/calendar'));
    }

    /**
     * Calendario produzione
     */
    public function calendar()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        // Ottiene mese e anno dalla query string o usa quello corrente
        $currentDate = new DateTime();
        if (isset($_GET['month']) && isset($_GET['year'])) {
            $currentDate->setDate($_GET['year'], $_GET['month'], 1);
        }

        $currentMonth = (int) $currentDate->format('n');
        $currentYear = (int) $currentDate->format('Y');
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
        $firstDayOfWeek = (int) date('N', strtotime("$currentYear-$currentMonth-01"));

        $monthNames = [
            1 => 'GENNAIO',
            2 => 'FEBBRAIO',
            3 => 'MARZO',
            4 => 'APRILE',
            5 => 'MAGGIO',
            6 => 'GIUGNO',
            7 => 'LUGLIO',
            8 => 'AGOSTO',
            9 => 'SETTEMBRE',
            10 => 'OTTOBRE',
            11 => 'NOVEMBRE',
            12 => 'DICEMBRE'
        ];

        // Carica dati produzione del mese per evidenziare giorni con dati
        $produzioneDays = $this->getProduzioneByMonth($monthNames[$currentMonth], $currentYear);

        $data = [
            'pageTitle' => 'Calendario Produzione - ' . APP_NAME,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'monthNames' => $monthNames,
            'produzioneDays' => $produzioneDays
        ];

        $this->render('produzione.calendar', $data);
    }

    /**
     * Mostra dettaglio produzione per una data specifica
     * Parametri: ?date=YYYY-MM-DD (nuovo) o ?month=MESE&day=GIORNO (legacy, redirecta)
     */
    public function show()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        // Supporta sia nuovo formato (date) che legacy (month/day) con redirect
        $date = $_GET['date'] ?? null;
        $month = $_GET['month'] ?? null;
        $day = $_GET['day'] ?? null;


        // Se usa formato legacy, converti e redirecta
        if (!$date && $month && $day) {
            $year = $_GET['year'] ?? date('Y'); // Accetta l'anno o usa quello corrente
            $monthNumber = $this->getMonthNumber($month);
            $convertedDate = date('Y-m-d', mktime(0, 0, 0, $monthNumber, $day, $year));
            $this->redirect($this->url("/produzione/show?date=$convertedDate"));
            return;
        }

        if (!$date) {
            $this->redirect($this->url('/produzione/calendar'));
            return;
        }

        try {
            // Converti e valida la data
            $productionDate = date('Y-m-d', strtotime($date));
            if (!$productionDate || $productionDate === '1970-01-01') {
                throw new Exception('Data non valida');
            }

            // Ottiene destinatari email dalle impostazioni
            $smtpDestinatari = Setting::where('item', 'production_recipients')->first();

            // Carica i dati di produzione usando Eloquent
            $produzione = ProductionRecord::byDate($productionDate)->first();

            // Crea oggetto DateTime per formattazione
            $dateObj = new DateTime($productionDate);

            $data = [
                'pageTitle' => "Produzione " . $dateObj->format('d/m/Y') . " - " . APP_NAME,
                'date' => $productionDate,                    // Nuovo: data ISO
                'formatted_date' => $dateObj->format('d/m/Y'), // Per visualizzazione
                'day_name' => $this->getDayName($dateObj->format('N')), // Nome giorno
                'month_name' => $this->getMonthName($dateObj->format('n')), // Nome mese
                'produzione' => $produzione,
                'spedizioni' => [], // Non gestiamo più le spedizioni
                'destinatari' => $smtpDestinatari ? $smtpDestinatari->value : null,
                // Backward compatibility per view legacy
                'day' => $dateObj->format('d'),
                'month' => $this->getMonthName($dateObj->format('n'))
            ];

            $this->render('produzione.show', $data);

        } catch (Exception $e) {
            error_log("Errore show produzione: " . $e->getMessage());
            $this->setFlash('error', 'Data non valida: ' . $date);
            $this->redirect($this->url('/produzione/calendar'));
        }
    }

    /**
     * Form per creare nuova produzione
     */
    public function create()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        $monthNames = [
            'GENNAIO',
            'FEBBRAIO',
            'MARZO',
            'APRILE',
            'MAGGIO',
            'GIUGNO',
            'LUGLIO',
            'AGOSTO',
            'SETTEMBRE',
            'OTTOBRE',
            'NOVEMBRE',
            'DICEMBRE'
        ];

        $data = [
            'pageTitle' => 'Nuova Produzione - ' . APP_NAME,
            'monthNames' => $monthNames,
            'currentMonth' => date('F'),
            'currentDay' => date('d'),
            'csrfToken' => $this->generateCsrfToken()
        ];

        $this->render('produzione.create', $data);
    }

    /**
     * Salva nuova produzione
     */
    public function store()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/produzione/create'));
        }

        // Valida CSRF token
        $csrfToken = $this->input('csrf_token');
        if (!$this->validateCsrfToken($csrfToken)) {
            $this->setFlash('error', 'Token di sicurezza non valido.');
            $this->redirect($this->url('/produzione/create'));
        }

        try {
            $month = $this->input('month');
            $day = (int) $this->input('day');
            $year = (int) $this->input('year');

            // Costruisce la data
            $monthNumber = $this->getMonthNumber($month);
            $productionDate = date('Y-m-d', mktime(0, 0, 0, $monthNumber, $day, $year));

            // Dati del nuovo modello
            $data = [
                'production_date' => $productionDate,
                'manovia1' => $this->input('manovia1') !== '' ? (int) $this->input('manovia1') : 0,
                'manovia1_notes' => $this->input('note1') !== '' ? $this->input('note1') : null,
                'manovia2' => $this->input('manovia2') !== '' ? (int) $this->input('manovia2') : 0,
                'manovia2_notes' => $this->input('note2') !== '' ? $this->input('note2') : null,
                'orlatura1' => $this->input('orlatura1') !== '' ? (int) $this->input('orlatura1') : 0,
                'orlatura1_notes' => $this->input('orlaturanote1') !== '' ? $this->input('orlaturanote1') : null,
                'orlatura2' => $this->input('orlatura2') !== '' ? (int) $this->input('orlatura2') : 0,
                'orlatura2_notes' => $this->input('orlaturanote2') !== '' ? $this->input('orlaturanote2') : null,
                'orlatura3' => $this->input('orlatura3') !== '' ? (int) $this->input('orlatura3') : 0,
                'orlatura3_notes' => $this->input('orlaturanote3') !== '' ? $this->input('orlaturanote3') : null,
                'orlatura4' => $this->input('orlatura4') !== '' ? (int) $this->input('orlatura4') : 0,
                'orlatura4_notes' => $this->input('orlaturanote4') !== '' ? $this->input('orlaturanote4') : null,
                'orlatura5' => $this->input('orlatura5') !== '' ? (int) $this->input('orlatura5') : 0,
                'orlatura5_notes' => $this->input('orlaturanote5') !== '' ? $this->input('orlaturanote5') : null,
                'taglio1' => $this->input('taglio1') !== '' ? (int) $this->input('taglio1') : 0,
                'taglio1_notes' => $this->input('taglionote1') !== '' ? $this->input('taglionote1') : null,
                'taglio2' => $this->input('taglio2') !== '' ? (int) $this->input('taglio2') : 0,
                'taglio2_notes' => $this->input('taglionote2') !== '' ? $this->input('taglionote2') : null,
                'updated_by' => $_SESSION['user_id'] ?? null
            ];

            // Usa updateOrCreate per gestire inserimento/aggiornamento
            $record = ProductionRecord::updateOrCreate(
                ['production_date' => $productionDate],
                $data
            );

            $action = $record->wasRecentlyCreated ? 'CREATE' : 'UPDATE';
            $this->logActivity('PRODUZIONE', $action, "Salvataggio produzione $day $month $year");

            $this->setFlash('success', 'Dati produzione salvati con successo!');
            $this->redirect($this->url("/produzione/show?month=$month&day=$day&year=$year"));

        } catch (Exception $e) {
            error_log("Error saving produzione: " . $e->getMessage());
            $this->setFlash('error', 'Errore durante il salvataggio dei dati.');
            $this->redirect($this->url('/produzione/create'));
        }
    }

    /**
     * Genera PDF per una data specifica - CONVERTITO DA LEGACY A ELOQUENT
     * Parametri: ?date=YYYY-MM-DD (invece di month e day separati)
     */
    public function generatePdf()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        // Nuovo parametro: data completa invece di mese/giorno separati
        $date = $_GET['date'] ?? null;

        if (!$date) {
            $this->setFlash('error', 'Data non specificata.');
            $this->redirect($this->url('/produzione/calendar'));
        }

        try {
            // Converti data in formato Y-m-d se necessario
            $productionDate = date('Y-m-d', strtotime($date));

            // Query principale: record del giorno specificato
            $record = ProductionRecord::byDate($productionDate)->first();

            if (!$record) {
                // Crea PDF vuoto se non ci sono dati
                $this->generateEmptyPdf($date);
                return;
            }

            // Calcola la settimana (week number)
            $weekNumber = date('W', strtotime($productionDate));

            // CREA PDF - Configurazione IDENTICA al legacy
            $pdf = new \TCPDF("P", "mm", "A4", true, "UTF-8", false);
            $pdf->SetMargins(7, 7, 7);
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetTitle("PRODUZIONE DEL " . $record->production_date->format('d') . " " . $record->month_name);
            $pdf->AddPage();

            // Header principale - IDENTICO
            $pdf->SetLineWidth(0.5);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont("helvetica", "B", 20);
            $pdf->Cell(0, 3, "PRODUZIONE " . strtoupper($record->day_name . " " . $record->production_date->format('d') . " " . $record->month_name . " " . $record->production_date->format('Y')), 0, 1, "C", true);

            // Sezione dati giornalieri - IDENTICA
            $this->generateDailyDataSection($pdf, $record);

            // Sezione riepilogo settimana - IDENTICA
            $this->generateWeeklySection($pdf, $record, $weekNumber);

            // Sezione riepilogo mese - IDENTICA
            $this->generateMonthlySection($pdf, $record);

            // Totali finali - IDENTICI
            $this->generateFinalTotals($pdf, $record);

            // Footer aziendale - IDENTICO
            $pdf->Ln(5);
            $pdf->SetFont("helvetica", "B", 8);
            $pdf->Cell(190, 15, "CALZATURIFICIO EMMEGIEMME SHOES SRL", 0, 0, "R");

            // Output PDF
            $pdf->Output("PRODUZIONE.pdf", "I");

        } catch (Exception $e) {
            error_log("Errore generazione PDF produzione: " . $e->getMessage());
            $this->setFlash('error', 'Errore durante la generazione del PDF.');
            $this->redirect($this->url('/produzione/calendar'));
        }
    }

    /**
     * Genera sezione DATI GIORNALIERI - CORRETTA
     * Mostra tutti i reparti con quantità e note per il giorno specifico
     */
    private function generateDailyDataSection($pdf, $record)
    {
        // Rettangolo principale e intestazione nera - IDENTICI AL LEGACY
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect(7, 20, 196, 78, "DF");
        $pdf->SetFillColor(0, 0, 0);
        $pdf->Rect(7, 20, 62, 9.8, "DF");
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont("helvetica", "B", 17);
        $pdf->Ln(4);

        $col1Width = 25;
        $col2Width = 25;
        $col3Width = 15;

        $pdf->Cell($col1Width, 10, "DATI GIORNALIERI", 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(10);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetLineWidth(0.1);

        // Strisce alternate grigio/bianco - IDENTICHE AL LEGACY
        $isGray = false;
        $grayColor = [240, 240, 240];
        for ($i = 0; $i < 9; $i++) {
            if ($isGray) {
                $pdf->SetFillColor($grayColor[0], $grayColor[1], $grayColor[2]);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            $pdf->Rect(10, 30 + $i * 7, 190, 9, "DF");
            $isGray = !$isGray;
        }

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetLineWidth(0.5);

        // DATI DEL GIORNO SPECIFICO - Tutti i reparti con quantità e note
        // Aggiungo indentazione per spostare il testo più a destra
        $leftMargin = 13; // Margine sinistro per indentare il testo

        // MONTAGGIO (MANOVIA)
        $pdf->SetX($leftMargin);
        $pdf->SetFont("helvetica", "B", 9);
        $pdf->Cell($col1Width, 7, "MANOVIA 1:", 0, 0);
        $pdf->SetFont("helvetica", "", 11);
        $pdf->Cell($col2Width, 7, $record->manovia1 ?? '0', 0, 0);
        $pdf->SetFont("helvetica", "B", 9);
        $pdf->Cell($col1Width, 7, "NOTE:", 0, 0);
        $pdf->SetFont("helvetica", "", 7);
        $pdf->Cell($col2Width, 7, $record->manovia1_notes ?? '', 0, 1);

        $pdf->SetX($leftMargin);
        $pdf->SetFont("helvetica", "B", 9);
        $pdf->Cell($col1Width, 7, "MANOVIA 2:", 0, 0);
        $pdf->SetFont("helvetica", "", 11);
        $pdf->Cell($col2Width, 7, $record->manovia2 ?? '0', 0, 0);
        $pdf->SetFont("helvetica", "B", 9);
        $pdf->Cell($col3Width, 7, "NOTE:", 0, 0);
        $pdf->SetFont("helvetica", "", 7);
        $pdf->Cell($col2Width, 7, $record->manovia2_notes ?? '', 0, 1);

        // ORLATURA (5 reparti)
        for ($i = 1; $i <= 5; $i++) {
            $pdf->SetX($leftMargin);
            $pdf->SetFont("helvetica", "B", 9);
            $pdf->Cell($col1Width, 7, "ORLATURA $i:", 0, 0);
            $pdf->SetFont("helvetica", "", 11);
            $pdf->Cell($col2Width, 7, $record->{"orlatura$i"} ?? '0', 0, 0);
            $pdf->SetFont("helvetica", "B", 9);
            $pdf->Cell($col3Width, 7, "NOTE:", 0, 0);
            $pdf->SetFont("helvetica", "", 7);
            $pdf->Cell($col2Width, 7, $record->{"orlatura{$i}_notes"} ?? '', 0, 1);
        }

        // TAGLIO
        $pdf->SetX($leftMargin);
        $pdf->SetFont("helvetica", "B", 9);
        $pdf->Cell($col1Width, 7, "TAGLIO 1:", 0, 0);
        $pdf->SetFont("helvetica", "", 11);
        $pdf->Cell($col2Width, 7, $record->taglio1 ?? '0', 0, 0);
        $pdf->SetFont("helvetica", "B", 9);
        $pdf->Cell($col3Width, 7, "NOTE:", 0, 0);
        $pdf->SetFont("helvetica", "", 7);
        $pdf->Cell($col2Width, 7, $record->taglio1_notes ?? '', 0, 1);

        $pdf->SetX($leftMargin);
        $pdf->SetFont("helvetica", "B", 9);
        $pdf->Cell($col1Width, 7, "TAGLIO 2:", 0, 0);
        $pdf->SetFont("helvetica", "", 11);
        $pdf->Cell($col2Width, 7, $record->taglio2 ?? '0', 0, 0);
        $pdf->SetFont("helvetica", "B", 9);
        $pdf->Cell($col3Width, 7, "NOTE:", 0, 0);
        $pdf->SetFont("helvetica", "", 7);
        $pdf->Cell($col2Width, 7, $record->taglio2_notes ?? '', 0, 1);
    }

    /**
     * Genera sezione RIEPILOGO SETTIMANA - CORRETTA
     * Mostra tabella con tutti i giorni della settimana corrente (Lunedì-Sabato) e relativi totali
     */
    private function generateWeeklySection($pdf, $record, $weekNumber)
    {
        // Margine sinistro di 5mm per tabelle
        $leftMargin = 3;

        // Rettangolo e intestazione nera - con margine sinistro
        $pdf->Rect(7, 100, 196, 147, "DF");
        $pdf->SetFillColor(0, 0, 0);
        $pdf->Rect(7, 100, 77, 10, "DF");
        $pdf->SetFont("helvetica", "B", 12);
        $pdf->Ln(-2);
        $pdf->SetX(7);
        $pdf->Cell(185, 28, "SETTIMANA " . $weekNumber, 0, 0, "R");
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont("helvetica", "B", 17);
        $pdf->Ln(10);

        $col1Width = 35;
        $pdf->SetX(7);
        $pdf->Cell($col1Width, 10, "RIEPILOGO SETTIMANA", 0, 0);
        $pdf->SetTextColor(0, 0, 0);

        // Calcola range settimana corrente: Lunedì-Domenica della settimana che contiene la data record
        $currentDate = $record->production_date->format('Y-m-d');
        $startOfWeek = date('Y-m-d', strtotime('monday this week', strtotime($currentDate)));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week', strtotime($currentDate)));

        // Query tutti i giorni della settimana ESCLUSA DOMENICA - come nel legacy
        $weeklyRecords = ProductionRecord::betweenDates($startOfWeek, $endOfWeek)
            ->whereRaw('DAYOFWEEK(production_date) BETWEEN 2 AND 7') // Lunedì=2 a Sabato=7
            ->orderBy('production_date')
            ->get();

        $pdf->Ln(13);
        $pdf->SetLineWidth(0.3);

        if ($weeklyRecords->count() > 0) {
            // Header tabella - con margine sinistro
            $pdf->SetFont("helvetica", "B", 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(192, 192, 192);
            $pdf->SetFont("helvetica", "B", 8);
            $pdf->SetX(7 + $leftMargin);
            $pdf->Cell(15, 6, "DATA", 1, 0, "C", 1);
            $pdf->Cell(40, 6, "GIORNO", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "MONT 1", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "MONT 2", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "ORL 1", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "ORL 2", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "ORL 3", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "ORL 4", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "ORL 5", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "TAGL 1", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "TAGL 2", 1, 1, "C", 1);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont("helvetica", "", 10);

            // Dati della settimana - ogni giorno su una riga con margine sinistro
            foreach ($weeklyRecords as $weekRecord) {
                $pdf->SetX(7 + $leftMargin);
                $pdf->Cell(15, 8, $weekRecord->production_date->format('d'), 1, 0, "C");
                $pdf->Cell(40, 8, strtoupper($weekRecord->day_name), 1, 0, "C");
                $pdf->Cell(15, 8, $weekRecord->manovia1 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekRecord->manovia2 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekRecord->orlatura1 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekRecord->orlatura2 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekRecord->orlatura3 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekRecord->orlatura4 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekRecord->orlatura5 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekRecord->taglio1 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekRecord->taglio2 ?? '0', 1, 1, "C");
            }

            // Riga TOTALI SETTIMANA - IDENTICA AL LEGACY
            $weeklyTotals = [
                'TOTALEMANOVIA1' => $weeklyRecords->sum('manovia1'),
                'TOTALEMANOVIA2' => $weeklyRecords->sum('manovia2'),
                'TOTALEORLATURA1' => $weeklyRecords->sum('orlatura1'),
                'TOTALEORLATURA2' => $weeklyRecords->sum('orlatura2'),
                'TOTALEORLATURA3' => $weeklyRecords->sum('orlatura3'),
                'TOTALEORLATURA4' => $weeklyRecords->sum('orlatura4'),
                'TOTALEORLATURA5' => $weeklyRecords->sum('orlatura5'),
                'TOTALETAGLIO1' => $weeklyRecords->sum('taglio1'),
                'TOTALETAGLIO2' => $weeklyRecords->sum('taglio2')
            ];

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(192, 192, 192);
            $pdf->SetX(7 + $leftMargin);
            $pdf->Cell(55, 6, "TOTALI SETTIMANA", 1, 0, "C", 1);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(15, 6, $weeklyTotals['TOTALEMANOVIA1'], 1, 0, "C");
            $pdf->Cell(15, 6, $weeklyTotals['TOTALEMANOVIA2'], 1, 0, "C");
            $pdf->Cell(15, 6, $weeklyTotals['TOTALEORLATURA1'], 1, 0, "C");
            $pdf->Cell(15, 6, $weeklyTotals['TOTALEORLATURA2'], 1, 0, "C");
            $pdf->Cell(15, 6, $weeklyTotals['TOTALEORLATURA3'], 1, 0, "C");
            $pdf->Cell(15, 6, $weeklyTotals['TOTALEORLATURA4'], 1, 0, "C");
            $pdf->Cell(15, 6, $weeklyTotals['TOTALEORLATURA5'], 1, 0, "C");
            $pdf->Cell(15, 6, $weeklyTotals['TOTALETAGLIO1'], 1, 0, "C");
            $pdf->Cell(15, 6, $weeklyTotals['TOTALETAGLIO2'], 1, 1, "C");
        } else {
            // Se non ci sono dati per la settimana
            $pdf->Ln(13);
            $pdf->SetFont("helvetica", "", 12);
            $pdf->Cell(0, 10, "Nessun dato disponibile per questa settimana.", 0, 1, "C");
        }

        $pdf->SetLineWidth(0.5);
    }

    /**
     * Genera sezione RIEPILOGO MESE - CORRETTA
     * Mostra tabella con tutte le settimane del mese e relativi totali
     */
    private function generateMonthlySection($pdf, $record)
    {
        // Margine sinistro di 3mm per tabelle
        $leftMargin = 3;

        // Intestazione nera mese - con margine sinistro
        $pdf->SetFillColor(0, 0, 0);
        $pdf->Rect(7 + $leftMargin, 178, 60, 10, "DF");
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont("helvetica", "B", 17);
        $pdf->Ln(4);

        $col1Width = 35;
        $pdf->SetX(7 + $leftMargin);
        $pdf->Cell($col1Width, 10, "RIEPILOGO MESE", 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont("helvetica", "B", 12);
        $pdf->Cell(150 - $leftMargin, 16, strtoupper($record->month_name), 0, 0, "R");

        // Query del mese: raggruppa per settimana TUTTE le settimane del mese
        $month = $record->production_date->month;
        $year = $record->production_date->year;

        // Trova primo e ultimo giorno del mese
        $firstDayOfMonth = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
        $lastDayOfMonth = date('Y-m-t', mktime(0, 0, 0, $month, 1, $year));

        // Query per tutte le settimane del mese con totali
        $monthlyWeekData = ProductionRecord::whereBetween('production_date', [$firstDayOfMonth, $lastDayOfMonth])
            ->selectRaw('
                YEARWEEK(production_date, 1) as yearweek,
                WEEK(production_date, 1) as week_number,
                SUM(manovia1) as TOTALEMANOVIA1,
                SUM(manovia2) as TOTALEMANOVIA2,
                SUM(orlatura1) as TOTALEORLATURA1,
                SUM(orlatura2) as TOTALEORLATURA2,
                SUM(orlatura3) as TOTALEORLATURA3,
                SUM(orlatura4) as TOTALEORLATURA4,
                SUM(orlatura5) as TOTALEORLATURA5,
                SUM(taglio1) as TOTALETAGLIO1,
                SUM(taglio2) as TOTALETAGLIO2,
                MIN(production_date) as first_date,
                MAX(production_date) as last_date
            ')
            ->groupBy('yearweek', 'week_number')
            ->orderBy('week_number')
            ->get()
            ->map(function($item) {
                $item->settimana_display = 'Settimana ' . $item->week_number;
                return $item;
            });

        $pdf->Ln(14);
        $pdf->SetLineWidth(0.3);

        if ($monthlyWeekData->count() > 0) {
            // Header tabella mensile - con margine sinistro
            $pdf->SetFont("helvetica", "B", 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(192, 192, 192);
            $pdf->SetFont("helvetica", "B", 8);
            $pdf->SetX(7 + $leftMargin);
            $pdf->Cell(55, 6, "SETTIMANA", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "MONT 1", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "MONT 2", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "ORL 1", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "ORL 2", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "ORL 3", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "ORL 4", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "ORL 5", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "TAGL 1", 1, 0, "C", 1);
            $pdf->Cell(15, 6, "TAGL 2", 1, 1, "C", 1);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont("helvetica", "", 10);

            // Dati del mese - una riga per ogni settimana con margine sinistro
            foreach ($monthlyWeekData as $weekData) {
                $pdf->SetX(7 + $leftMargin);
                $pdf->Cell(55, 8, strtoupper($weekData->settimana_display), 1, 0, "C");
                $pdf->Cell(15, 8, $weekData->TOTALEMANOVIA1 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekData->TOTALEMANOVIA2 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekData->TOTALEORLATURA1 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekData->TOTALEORLATURA2 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekData->TOTALEORLATURA3 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekData->TOTALEORLATURA4 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekData->TOTALEORLATURA5 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekData->TOTALETAGLIO1 ?? '0', 1, 0, "C");
                $pdf->Cell(15, 8, $weekData->TOTALETAGLIO2 ?? '0', 1, 1, "C");
            }

            // Riga TOTALI MESE - IDENTICA AL LEGACY
            $monthlyTotals = ProductionRecord::byMonth($month, $year)
                ->selectRaw('
                    SUM(manovia1) as TOTALEMANOVIA1,
                    SUM(manovia2) as TOTALEMANOVIA2,
                    SUM(orlatura1) as TOTALEORLATURA1,
                    SUM(orlatura2) as TOTALEORLATURA2,
                    SUM(orlatura3) as TOTALEORLATURA3,
                    SUM(orlatura4) as TOTALEORLATURA4,
                    SUM(orlatura5) as TOTALEORLATURA5,
                    SUM(taglio1) as TOTALETAGLIO1,
                    SUM(taglio2) as TOTALETAGLIO2
                ')
                ->first();

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(192, 192, 192);
            $pdf->SetX(7 + $leftMargin);
            $pdf->Cell(55, 6, "TOTALI", 1, 0, "C", 1);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(15, 6, $monthlyTotals->TOTALEMANOVIA1 ?? '0', 1, 0, "C");
            $pdf->Cell(15, 6, $monthlyTotals->TOTALEMANOVIA2 ?? '0', 1, 0, "C");
            $pdf->Cell(15, 6, $monthlyTotals->TOTALEORLATURA1 ?? '0', 1, 0, "C");
            $pdf->Cell(15, 6, $monthlyTotals->TOTALEORLATURA2 ?? '0', 1, 0, "C");
            $pdf->Cell(15, 6, $monthlyTotals->TOTALEORLATURA3 ?? '0', 1, 0, "C");
            $pdf->Cell(15, 6, $monthlyTotals->TOTALEORLATURA4 ?? '0', 1, 0, "C");
            $pdf->Cell(15, 6, $monthlyTotals->TOTALEORLATURA5 ?? '0', 1, 0, "C");
            $pdf->Cell(15, 6, $monthlyTotals->TOTALETAGLIO1 ?? '0', 1, 0, "C");
            $pdf->Cell(15, 6, $monthlyTotals->TOTALETAGLIO2 ?? '0', 1, 1, "C");
        } else {
            // Se non ci sono dati per il mese
            $pdf->Ln(14);
            $pdf->SetFont("helvetica", "", 12);
            $pdf->Cell(0, 10, "Nessun dato disponibile per questo mese.", 0, 1, "C");
        }

        $pdf->SetLineWidth(0.5);
    }

    /**
     * Genera totali finali - IDENTICI al legacy
     */
    private function generateFinalTotals($pdf, $record)
    {
        $pdf->SetFont("helvetica", "", 13);
        $pdf->Ln(8);

        $col1Width = 48;
        $col2Width = 17;

        $pdf->SetTextColor(0, 0, 0);
        $pagewidth = $pdf->getPageWidth();
        $totalwidth = ($col1Width + $col2Width) * 3;
        $x = ($pagewidth - $totalwidth) / 2;
        $pdf->SetX($x);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetLineWidth(0.1);
        $pdf->SetLineWidth(0.5);
        $pdf->SetFont("helvetica", "B", 14);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(0, 0, 0);

        // Totale TAGLIO - IDENTICO
        $pdf->Cell($col1Width, 10, "TOT. TAGLIO:", 1, 0, "C", 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont("helvetica", "", 17);
        $pdf->Cell($col2Width, 10, $record->total_taglio, 1, 0, "C");

        // Totale ORLATURA - IDENTICO
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetFont("helvetica", "B", 14);
        $pdf->Cell($col1Width, 10, "TOT. ORLATURA:", 1, 0, "C", 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont("helvetica", "", 17);
        $pdf->Cell($col2Width, 10, $record->total_orlatura, 1, 0, "C");

        // Totale MONTAGGIO - IDENTICO
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetFont("helvetica", "B", 14);
        $pdf->Cell($col1Width, 10, "TOT. MONTAGGIO:", 1, 0, "C", 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont("helvetica", "", 17);
        $pdf->Cell($col2Width, 10, $record->total_montaggio, 1, 0, "C");

        $pdf->SetFillColor(255, 255, 255);
    }

    /**
     * Genera PDF vuoto quando non ci sono dati
     */
    private function generateEmptyPdf($date)
    {
        $pdf = new \TCPDF("P", "mm", "A4", true, "UTF-8", false);
        $pdf->SetMargins(7, 7, 7);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetTitle("PRODUZIONE DEL " . date('d/m/Y', strtotime($date)));
        $pdf->AddPage();

        $pdf->SetFont("times", "", 12);
        $pdf->Cell(0, 10, "Nessun dato disponibile per questo giorno.", 0, 1);

        $pdf->Output("PRODUZIONE.pdf", "I");
    }

    /**
     * Ottiene i dati di produzione per un mese - SOLO giorni con valori non vuoti
     */
    private function getProduzioneByMonth($month, $year)
    {
        try {
            // Converte nome mese in numero
            $monthNumber = $this->getMonthNumber($month);

            // Usa il nuovo modello Eloquent
            $records = ProductionRecord::byMonth($monthNumber, $year)
                ->withData()
                ->get(['production_date']);

            // Estrae solo i giorni del mese
            return $records->map(function ($record) {
                return $record->production_date->day;
            })->toArray();

        } catch (Exception $e) {
            error_log("Error getting produzione by month: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Converte nome mese in numero
     */
    private function getMonthNumber($monthName)
    {
        $months = [
            'GENNAIO' => 1,
            'FEBBRAIO' => 2,
            'MARZO' => 3,
            'APRILE' => 4,
            'MAGGIO' => 5,
            'GIUGNO' => 6,
            'LUGLIO' => 7,
            'AGOSTO' => 8,
            'SETTEMBRE' => 9,
            'OTTOBRE' => 10,
            'NOVEMBRE' => 11,
            'DICEMBRE' => 12
        ];

        return $months[$monthName] ?? 1;
    }

    /**
     * Ottiene nome mese in italiano da numero (1-12)
     */
    private function getMonthName($monthNumber)
    {
        $months = [
            1 => 'GENNAIO',
            2 => 'FEBBRAIO',
            3 => 'MARZO',
            4 => 'APRILE',
            5 => 'MAGGIO',
            6 => 'GIUGNO',
            7 => 'LUGLIO',
            8 => 'AGOSTO',
            9 => 'SETTEMBRE',
            10 => 'OTTOBRE',
            11 => 'NOVEMBRE',
            12 => 'DICEMBRE'
        ];

        return $months[$monthNumber] ?? 'GENNAIO';
    }

    /**
     * Ottiene nome giorno della settimana in italiano da numero (1=Lunedì, 7=Domenica)
     */
    private function getDayName($dayNumber)
    {
        $days = [
            1 => 'LUNEDÌ',
            2 => 'MARTEDÌ',
            3 => 'MERCOLEDÌ',
            4 => 'GIOVEDÌ',
            5 => 'VENERDÌ',
            6 => 'SABATO',
            7 => 'DOMENICA'
        ];

        return $days[$dayNumber] ?? 'LUNEDÌ';
    }

    /**
     * Genera il contenuto HTML per il PDF
     */
    private function generatePdfContent($produzione, $month, $day)
    {
        $html = '<h1 style="text-align: center;">REPORT PRODUZIONE</h1>';
        $html .= '<h2 style="text-align: center;">' . $day . ' ' . $month . '</h2>';
        $html .= '<hr><br>';

        // Sezione Manovia
        $html .= '<h3>MONTAGGIO</h3>';
        $html .= '<table border="1" cellpadding="5" style="width: 100%;">';
        $html .= '<tr><td width="25%"><strong>MANOVIA1:</strong></td><td width="25%">' . ($produzione['MANOVIA1'] ?: '-') . '</td><td width="50%">Note: ' . ($produzione['MANOVIA1NOTE'] ?: '-') . '</td></tr>';
        $html .= '<tr><td><strong>MANOVIA2:</strong></td><td>' . ($produzione['MANOVIA2'] ?: '-') . '</td><td>Note: ' . ($produzione['MANOVIA2NOTE'] ?: '-') . '</td></tr>';
        $html .= '</table><br>';

        // Sezione Orlatura
        $html .= '<h3>ORLATURA</h3>';
        $html .= '<table border="1" cellpadding="5" style="width: 100%;">';
        for ($i = 1; $i <= 5; $i++) {
            $orlatura = $produzione["ORLATURA$i"] ?: '-';
            $note = $produzione["ORLATURA{$i}NOTE"] ?: '-';
            $html .= "<tr><td width='25%'><strong>ORLATURA$i:</strong></td><td width='25%'>$orlatura</td><td width='50%'>Note: $note</td></tr>";
        }
        $html .= '</table><br>';

        // Sezione Taglio
        $html .= '<h3>TAGLIO</h3>';
        $html .= '<table border="1" cellpadding="5" style="width: 100%;">';
        $html .= '<tr><td width="25%"><strong>TAGLIO1:</strong></td><td width="25%">' . ($produzione['TAGLIO1'] ?: '-') . '</td><td width="50%">Note: ' . ($produzione['TAGLIO1NOTE'] ?: '-') . '</td></tr>';
        $html .= '<tr><td><strong>TAGLIO2:</strong></td><td>' . ($produzione['TAGLIO2'] ?: '-') . '</td><td>Note: ' . ($produzione['TAGLIO2NOTE'] ?: '-') . '</td></tr>';
        $html .= '</table><br>';

        // Totali
        $html .= '<h3>TOTALI</h3>';
        $html .= '<table border="1" cellpadding="5" style="width: 100%;">';
        $html .= '<tr><td width="33%"><strong>TOTALE TAGLIO:</strong></td><td width="33%">' . ($produzione['TOTALITAGLIO'] ?: '0') . '</td><td width="34%"></td></tr>';
        $html .= '<tr><td><strong>TOTALE ORLATURA:</strong></td><td>' . ($produzione['TOTALIORLATURA'] ?: '0') . '</td><td></td></tr>';
        $html .= '<tr><td><strong>TOTALE MONTAGGIO:</strong></td><td>' . ($produzione['TOTALIMONTAGGIO'] ?: '0') . '</td><td></td></tr>';
        $html .= '</table>';

        return $html;
    }

    /**
     * Form per modificare produzione esistente
     * Parametri: ?date=YYYY-MM-DD (nuovo) o ?month=MESE&day=GIORNO (legacy, redirecta)
     */
    public function edit()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        // Supporta sia nuovo formato (date) che legacy (month/day) con redirect
        $date = $_GET['date'] ?? null;
        $month = $_GET['month'] ?? null;
        $day = $_GET['day'] ?? null;

        // Se usa formato legacy, converti e redirecta
        if (!$date && $month && $day) {
            $monthNumber = $this->getMonthNumber($month);
            $year = date('Y');
            $convertedDate = date('Y-m-d', mktime(0, 0, 0, $monthNumber, $day, $year));
            $this->redirect($this->url("/produzione/edit?date=$convertedDate"));
            return;
        }

        if (!$date) {
            $this->redirect($this->url('/produzione/calendar'));
            return;
        }

        try {
            // Converti e valida la data
            $productionDate = date('Y-m-d', strtotime($date));
            if (!$productionDate || $productionDate === '1970-01-01') {
                throw new Exception('Data non valida');
            }

            // Carica i dati esistenti usando Eloquent
            $produzione = ProductionRecord::byDate($productionDate)->first();

            if (!$produzione) {
                $this->setFlash('error', 'Nessun dato di produzione trovato per questa data.');
                $this->redirect($this->url('/produzione/calendar'));
                return;
            }

            // Crea oggetto DateTime per formattazione
            $dateObj = new DateTime($productionDate);

            $monthNames = [
                'GENNAIO',
                'FEBBRAIO',
                'MARZO',
                'APRILE',
                'MAGGIO',
                'GIUGNO',
                'LUGLIO',
                'AGOSTO',
                'SETTEMBRE',
                'OTTOBRE',
                'NOVEMBRE',
                'DICEMBRE'
            ];

            $data = [
                'pageTitle' => "Modifica Produzione " . $dateObj->format('d/m/Y') . " - " . APP_NAME,
                'monthNames' => $monthNames,
                'produzione' => $produzione,
                'date' => $productionDate,                    // Nuovo: data ISO
                'formatted_date' => $dateObj->format('d/m/Y'), // Per visualizzazione
                'day_name' => $this->getDayName($dateObj->format('N')),
                'month_name' => $this->getMonthName($dateObj->format('n')),
                'csrfToken' => $this->generateCsrfToken(),
                // Backward compatibility per view legacy
                'day' => $dateObj->format('d'),
                'month' => $this->getMonthName($dateObj->format('n'))
            ];

            $this->render('produzione.edit', $data);

        } catch (Exception $e) {
            error_log("Errore edit produzione: " . $e->getMessage());
            $this->setFlash('error', 'Data non valida: ' . $date);
            $this->redirect($this->url('/produzione/calendar'));
        }
    }

    /**
     * Aggiorna produzione esistente
     */
    public function update()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->url('/produzione/calendar'));
        }

        // Valida CSRF token
        $csrfToken = $this->input('csrf_token');
        if (!$this->validateCsrfToken($csrfToken)) {
            $this->setFlash('error', 'Token di sicurezza non valido.');
            $this->redirect($this->url('/produzione/calendar'));
        }

        try {
            $month = $this->input('month');
            $day = (int) $this->input('day');
            $year = (int) $this->input('year');
            $id = (int) $this->input('id');

            // Trova il record usando Eloquent
            $produzione = ProductionRecord::find($id);

            if (!$produzione) {
                $this->setFlash('error', 'Record non trovato.');
                $this->redirect($this->url('/produzione/calendar'));
            }

            // Dati per l'aggiornamento
            $data = [
                'manovia1' => $this->input('manovia1') !== '' ? (int) $this->input('manovia1') : 0,
                'manovia1_notes' => $this->input('note1') !== '' ? $this->input('note1') : null,
                'manovia2' => $this->input('manovia2') !== '' ? (int) $this->input('manovia2') : 0,
                'manovia2_notes' => $this->input('note2') !== '' ? $this->input('note2') : null,
                'orlatura1' => $this->input('orlatura1') !== '' ? (int) $this->input('orlatura1') : 0,
                'orlatura1_notes' => $this->input('orlaturanote1') !== '' ? $this->input('orlaturanote1') : null,
                'orlatura2' => $this->input('orlatura2') !== '' ? (int) $this->input('orlatura2') : 0,
                'orlatura2_notes' => $this->input('orlaturanote2') !== '' ? $this->input('orlaturanote2') : null,
                'orlatura3' => $this->input('orlatura3') !== '' ? (int) $this->input('orlatura3') : 0,
                'orlatura3_notes' => $this->input('orlaturanote3') !== '' ? $this->input('orlaturanote3') : null,
                'orlatura4' => $this->input('orlatura4') !== '' ? (int) $this->input('orlatura4') : 0,
                'orlatura4_notes' => $this->input('orlaturanote4') !== '' ? $this->input('orlaturanote4') : null,
                'orlatura5' => $this->input('orlatura5') !== '' ? (int) $this->input('orlatura5') : 0,
                'orlatura5_notes' => $this->input('orlaturanote5') !== '' ? $this->input('orlaturanote5') : null,
                'taglio1' => $this->input('taglio1') !== '' ? (int) $this->input('taglio1') : 0,
                'taglio1_notes' => $this->input('taglionote1') !== '' ? $this->input('taglionote1') : null,
                'taglio2' => $this->input('taglio2') !== '' ? (int) $this->input('taglio2') : 0,
                'taglio2_notes' => $this->input('taglionote2') !== '' ? $this->input('taglionote2') : null,
                'updated_by' => $_SESSION['user_id'] ?? null
            ];

            // Aggiorna record usando Eloquent
            $produzione->update($data);

            $this->logActivity('PRODUZIONE', 'UPDATE', "Modifica produzione $day $month $year");

            $this->setFlash('success', 'Dati produzione aggiornati con successo!');
            $this->redirect($this->url("/produzione/show?month=$month&day=$day&year=$year"));

        } catch (Exception $e) {
            error_log("Error updating produzione: " . $e->getMessage());
            $this->setFlash('error', 'Errore durante l\'aggiornamento dei dati.');
            $this->redirect($this->url('/produzione/calendar'));
        }
    }

    /**
     * Invia email con PDF produzione - MODERNIZZATO CON ELOQUENT
     * Parametri: date (YYYY-MM-DD) invece di month/day separati
     */
    public function sendEmail()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }

        // Nuovo parametro: data unificata
        $date = $this->input('date');
        $recipients = $this->input('to');
        $subject = $this->input('subject');
        $body = $this->input('body');

        if (empty($date) || empty($recipients) || empty($subject)) {
            $this->json(['error' => 'Parametri obbligatori mancanti: date, to, subject'], 400);
        }

        try {
            // Converti data in formato Y-m-d se necessario
            $productionDate = date('Y-m-d', strtotime($date));

            // Query record usando Eloquent
            $record = ProductionRecord::byDate($productionDate)->first();

            if (!$record) {
                $this->json(['error' => 'Nessun dato di produzione trovato per questa data'], 404);
                return;
            }

            // Genera PDF come stringa riutilizzando la logica modernizzata
            $pdfContent = $this->generatePdfString($record);

            // Carica impostazioni SMTP usando Eloquent
            $smtpSettings = Setting::where('item', 'LIKE', 'production_sender%')->get();

            $smtpCredentials = [];
            foreach ($smtpSettings as $setting) {
                $smtpCredentials[$setting->item] = $setting->value;
            }

            if (empty($smtpCredentials)) {
                throw new Exception("Configurazione SMTP non trovata");
            }

            // Inizializza PHPMailer
            $mail = new \PHPMailer\PHPMailer\PHPMailer();
            $mail->isSMTP();
            $mail->Host = $smtpCredentials['production_senderSMTP'] ?? '';
            $mail->SMTPAuth = true;
            $mail->Username = $smtpCredentials['production_senderEmail'] ?? '';
            $mail->Password = $smtpCredentials['production_senderPassword'] ?? '';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = $smtpCredentials['production_senderPORT'] ?? 465;

            $mail->setFrom($smtpCredentials['production_senderEmail'] ?? '');

            // Aggiungi destinatari (separati da ;)
            $recipientArray = explode(';', $recipients);
            foreach ($recipientArray as $recipient) {
                $mail->addAddress(trim($recipient));
            }

            $mail->Subject = $subject;
            $emailBody = $body . "\n\nCalzaturificio Emmegiemme Shoes Srl";
            $mail->Body = $emailBody;

            // Allega il PDF
            $mail->addStringAttachment($pdfContent, 'PRODUZIONE.pdf');

            if ($mail->send()) {
                $formattedDate = $record->production_date->format('d/m/Y');
                $this->logActivity('PRODUZIONE', 'SEND_EMAIL', "Email inviata per produzione $formattedDate a: $recipients");
                $this->json(['success' => true, 'message' => 'Email inviata con successo']);
            } else {
                throw new Exception($mail->ErrorInfo);
            }

        } catch (Exception $e) {
            error_log("Errore invio email produzione {$date}: " . $e->getMessage());
            $this->json(['error' => 'Errore durante l\'invio dell\'email: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Genera PDF come stringa riutilizzando la logica modernizzata
     */
    private function generatePdfString($record)
    {
        // Calcola la settimana
        $weekNumber = date('W', strtotime($record->production_date->format('Y-m-d')));

        // Configurazione PDF identica
        $pdf = new \TCPDF("P", "mm", "A4", true, "UTF-8", false);
        $pdf->SetMargins(7, 7, 7);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetTitle("PRODUZIONE DEL " . $record->production_date->format('d') . " " . $record->month_name);
        $pdf->AddPage();

        // Header principale
        $pdf->SetLineWidth(0.5);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont("helvetica", "B", 20);
        $pdf->Cell(0, 3, "RAPPORTO DI PRODUZIONE " . $record->day_name . " " . $record->production_date->format('d') . " " . $record->month_name . " " . $record->production_date->format('Y'), 0, 1, "C", true);

        // Riutilizzo tutti i metodi già implementati
        $this->generateDailyDataSection($pdf, $record);
        $this->generateWeeklySection($pdf, $record, $weekNumber);
        $this->generateMonthlySection($pdf, $record);
        $this->generateFinalTotals($pdf, $record);

        // Footer
        $pdf->Ln(5);
        $pdf->SetFont("helvetica", "B", 8);
        $pdf->Cell(190, 15, "CALZATURIFICIO EMMEGIEMME SHOES SRL", 0, 0, "R");

        // Restituisci come stringa
        return $pdf->Output("", "S");
    }

    /**
     * CSV Upload - Report Produzione da CSV
     */
    public function csv()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        $data = [
            'pageTitle' => 'Report Produzione da CSV',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/'],
                ['title' => 'Produzione', 'url' => '/produzione'],
                ['title' => 'Report CSV', 'url' => '/produzione/csv']
            ]
        ];

        $this->render('produzione/csv', $data);
    }

    /**
     * Process CSV Upload
     */
    public function processCsv()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->json(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $this->requireAuth();
        $this->requirePermission('produzione');

        try {
            // Verifica upload
            if (!isset($_FILES['csvFile'])) {
                throw new Exception('File non presente nell\'upload');
            }

            if ($_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Errore durante l\'upload del file: ' . $_FILES['csvFile']['error']);
            }

            $csvFile = $_FILES['csvFile']['tmp_name'];
            $handle = fopen($csvFile, 'r');

            if (!$handle) {
                throw new Exception('Impossibile aprire il file CSV');
            }

            $processedData = [];
            $lineNumber = 0;

            // Salta l'header
            fgetcsv($handle, 1000, ';', '"', "\\");

            while (($data = fgetcsv($handle, 1000, ';', '"', "\\")) !== FALSE) {
                $lineNumber++;

                if (count($data) < 5) {
                    continue; // Salta righe incomplete
                }

                $commessaCsv = trim($data[0]);
                $fase = trim($data[1]);
                $dataStr = trim($data[2]);
                $articolo = trim($data[3]);
                $qta = (int) trim($data[4]);

                // Estrai numero commessa (es: "2025 - 40094695 - S" -> "94695")
                $commessaEstratta = $this->extractCommessaNumber($commessaCsv);

                // Cerca nella tabella dati
                $cliente = $this->findClienteByCartellino($commessaEstratta);

                $processedData[] = [
                    'commessa_csv' => $commessaCsv,
                    'commessa_estratta' => $commessaEstratta,
                    'fase' => $fase,
                    'data' => $dataStr,
                    'articolo' => $articolo,
                    'qta' => $qta,
                    'cliente' => $cliente
                ];
            }

            fclose($handle);

            // Salva i dati in sessione per la generazione del report
            $_SESSION['csv_data'] = $processedData;

            // Log dell'attività
            $this->logActivity('produzione', 'CSV_UPLOAD', "Caricato file CSV con {$lineNumber} righe per generazione report produzione");

            $this->json([
                'success' => true,
                'data' => $processedData,
                'message' => "Elaborati {$lineNumber} record"
            ]);

        } catch (Exception $e) {
            error_log("CSV Process Error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate CSV Report PDF
     */
    public function generateCsvReport()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        if (!isset($_SESSION['csv_data']) || empty($_SESSION['csv_data'])) {
            die('Nessun dato CSV disponibile. Carica prima un file CSV.');
        }

        try {


            $csvData = $_SESSION['csv_data'];

            // Raggruppa i dati per Fase e Data (solo data, ignorando ora)
            $groupedData = [];
            $totaleGenerale = 0;

            foreach ($csvData as $row) {
                $dataFormattata = $this->formatDate($row['data']);
                $key = $row['fase'] . '|' . $dataFormattata;

                if (!isset($groupedData[$key])) {
                    $groupedData[$key] = [
                        'fase' => $row['fase'],
                        'data' => $dataFormattata,
                        'items' => [],
                        'totale_qta' => 0
                    ];
                }

                $groupedData[$key]['items'][] = $row;
                $groupedData[$key]['totale_qta'] += $row['qta'];
                $totaleGenerale += $row['qta'];
            }

            // Genera il PDF
            $mpdf = new \Mpdf\Mpdf([
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'margin_header' => 10,
                'margin_footer' => 10,
                'tempDir' => __DIR__ . '/../../storage/cache/mpdf'
            ]);

            // Imposta le proprietà del documento
            $mpdf->SetTitle('Report Produzione da CSV');
            $mpdf->SetAuthor('CoreGRE - EMMEGIEMME');

            // Genera l'HTML per il PDF
            $html = $this->generateReportHTML($groupedData, $totaleGenerale);

            $mpdf->WriteHTML($html);

            // Nome file con timestamp
            $fileName = 'Report_Produzione_CSV_' . date('Y-m-d_H-i-s') . '.pdf';

            // Log dell'attività
            $this->logActivity('produzione', 'PDF_GENERATED', "Generato report PDF produzione da CSV con " . count($csvData) . " record");

            // Output del PDF
            $mpdf->Output($fileName, 'D'); // 'D' = download

        } catch (Exception $e) {
            die('Errore nella generazione del PDF: ' . $e->getMessage());
        }
    }

    /**
     * Estrae il numero di commessa dal formato "2025 - 40094695 - S"
     * Ritorna le ultime 5 cifre del numero centrale
     */
    private function extractCommessaNumber($commessaString)
    {
        // Pattern: trova il numero centrale tra i trattini
        preg_match('/\d+\s*-\s*(\d+)\s*-/', $commessaString, $matches);

        if (isset($matches[1])) {
            $numeroCompleto = $matches[1];
            // Prendi le ultime 5 cifre
            return substr($numeroCompleto, -5);
        }

        return null;
    }

    /**
     * Cerca il cliente nella tabella dati usando il cartellino
     */
    private function findClienteByCartellino($cartellino)
    {
        if (!$cartellino) {
            return null;
        }

        try {
            $stmt = $this->db->query("SELECT `Ragione Sociale`, `Commessa Cli` FROM dati WHERE Cartel = ? LIMIT 1", [$cartellino]);
            $result = $stmt->fetch();

            if ($result) {
                return $result['Ragione Sociale'] . ' (' . $result['Commessa Cli'] . ')';
            }

            return null;

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Genera l'HTML per il report PDF
     */
    private function generateReportHTML($groupedData, $totaleGenerale)
    {
        $dataCorrente = date('d/m/Y H:i');

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    font-size: 11px; 
                    line-height: 1.3;
                    margin: 20px;
                    color: #333;
                }
                .company-header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 10px;
                    border-bottom: 1px solid #ddd;
                }
                .company-name {
                    font-size: 16px;
                    font-weight: bold;
                    margin: 0;
                    color: #333;
                }
                .section-header {
                    background: #f5f5f5;
                    padding: 12px 15px;
                    margin: 25px 0 10px 0;
                    font-weight: bold;
                    font-size: 13px;
                    color: #333;
                    border-left: 4px solid #333;
                }
                .table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                .table th {
                    background: #f8f8f8;
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                    font-weight: bold;
                    font-size: 10px;
                }
                .table td {
                    border: 1px solid #ddd;
                    padding: 6px 8px;
                    font-size: 10px;
                }
                .table .qta {
                    text-align: center;
                    font-weight: bold;
                }
                .total-row {
                    background: #f0f0f0;
                    font-weight: bold;
                }
                .footer {
                    position: fixed;
                    bottom: 15px;
                    right: 15px;
                    font-size: 8px;
                    color: #888;
                }
            </style>
        </head>
        <body>
            <div class="company-header">
                <h1 class="company-name">EMMEGIEMME SHOES SRL</h1>
            </div>';

        foreach ($groupedData as $group) {
            $html .= '
            <div class="section-header">
                ' . htmlspecialchars($group['fase']) . ' - ' . $group['data'] . '
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Commessa Cliente</th>
                        <th>Articolo</th>
                        <th>Qta</th>
                    </tr>
                </thead>
                <tbody>';

            $totaleSezione = 0;
            foreach ($group['items'] as $item) {
                // Estrai solo la parte "Commessa Cli" dal campo cliente
                $commessaCli = '';
                if ($item['cliente']) {
                    // Cerca il pattern "(CODICE_COMMESSA)" nella stringa cliente
                    preg_match('/\(([^)]+)\)/', $item['cliente'], $matches);
                    $commessaCli = isset($matches[1]) ? $matches[1] : $item['commessa_estratta'];
                } else {
                    $commessaCli = $item['commessa_estratta'];
                }

                $html .= '
                    <tr>
                        <td>' . htmlspecialchars($commessaCli) . '</td>
                        <td>' . htmlspecialchars($item['articolo']) . '</td>
                        <td class="qta">' . $item['qta'] . '</td>
                    </tr>';

                $totaleSezione += $item['qta'];
            }

            $html .= '
                    <tr class="total-row">
                        <td colspan="2"><strong>Totale</strong></td>
                        <td class="qta"><strong>' . $totaleSezione . '</strong></td>
                    </tr>
                </tbody>
            </table>';
        }

        $html .= '
            <div class="footer">
                ' . $dataCorrente . '
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Formatta la data per la visualizzazione
     */
    private function formatDate($dateString)
    {
        try {
            $date = DateTime::createFromFormat('d/m/Y H:i', $dateString);
            if ($date) {
                return $date->format('d/m/Y');
            }

            // Prova altri formati
            $date = DateTime::createFromFormat('d/m/Y', $dateString);
            if ($date) {
                return $date->format('d/m/Y');
            }

            return $dateString; // Restituisce il formato originale se non riesce a parsarlo
        } catch (Exception $e) {
            return $dateString;
        }
    }

    /**
     * Dashboard Statistiche Produzione
     */
    public function statistics()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        $this->render('produzione.statistics', [
            'pageTitle' => 'Statistiche Produzione - ' . APP_NAME
        ]);
    }

    /**
     * API: Recupera statistiche generali
     */
    public function getStatistics()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        try {
            $today = date('Y-m-d');
            $currentWeekStart = date('Y-m-d', strtotime('monday this week'));
            $currentWeekEnd = date('Y-m-d', strtotime('saturday this week'));
            $currentMonthStart = date('Y-m-01');
            $currentMonthEnd = date('Y-m-t');

            // Produzione oggi
            $todayProduction = ProductionRecord::where('production_date', $today)->first();
            $todayTotal = $todayProduction ? $todayProduction->total_produzione : 0;

            // Produzione questa settimana
            $weekProduction = ProductionRecord::whereBetween('production_date', [$currentWeekStart, $currentWeekEnd])
                ->selectRaw('
                    SUM(total_montaggio) as total_montaggio,
                    SUM(total_orlatura) as total_orlatura,
                    SUM(total_taglio) as total_taglio
                ')
                ->first();

            // Produzione questo mese
            $monthProduction = ProductionRecord::whereBetween('production_date', [$currentMonthStart, $currentMonthEnd])
                ->selectRaw('
                    SUM(total_montaggio) as total_montaggio,
                    SUM(total_orlatura) as total_orlatura,
                    SUM(total_taglio) as total_taglio,
                    AVG(total_montaggio + total_orlatura + total_taglio) as avg_daily
                ')
                ->first();

            // Conta solo i giorni con dati reali (produzione > 0)
            $daysWithData = ProductionRecord::whereBetween('production_date', [$currentMonthStart, $currentMonthEnd])
                ->whereRaw('(total_montaggio > 0 OR total_orlatura > 0 OR total_taglio > 0)')
                ->count();

            // Giorni lavorativi nel mese (Lun-Ven)
            $workingDaysInMonth = $this->getWorkingDaysInMonth(date('Y'), date('m'));

            // Completamento dati: giorni con dati rispetto ai giorni lavorativi teorici
            $completionRate = $workingDaysInMonth > 0
                ? round(($daysWithData / $workingDaysInMonth) * 100, 1)
                : 0;

            // Calcola i totali produzione
            $weekTotal = ($weekProduction->total_montaggio ?? 0) +
                        ($weekProduction->total_orlatura ?? 0) +
                        ($weekProduction->total_taglio ?? 0);

            $monthTotal = ($monthProduction->total_montaggio ?? 0) +
                         ($monthProduction->total_orlatura ?? 0) +
                         ($monthProduction->total_taglio ?? 0);

            $this->json([
                'success' => true,
                'data' => [
                    'today' => [
                        'total' => $todayTotal,
                        'has_data' => $todayProduction !== null
                    ],
                    'week' => [
                        'total' => $weekTotal,
                        'montaggio' => $weekProduction->total_montaggio ?? 0,
                        'orlatura' => $weekProduction->total_orlatura ?? 0,
                        'taglio' => $weekProduction->total_taglio ?? 0
                    ],
                    'month' => [
                        'total' => $monthTotal,
                        'montaggio' => $monthProduction->total_montaggio ?? 0,
                        'orlatura' => $monthProduction->total_orlatura ?? 0,
                        'taglio' => $monthProduction->total_taglio ?? 0,
                        'avg_daily' => round($monthProduction->avg_daily ?? 0),
                        'days_with_data' => $daysWithData,
                        'working_days' => $workingDaysInMonth,
                        'completion_rate' => $completionRate
                    ]
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore nel recupero statistiche: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore nel caricamento statistiche'], 500);
        }
    }

    /**
     * API: Trend ultimi 30 giorni
     */
    public function getTrendData()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        try {
            $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
            $endDate = date('Y-m-d');
            $startDate = date('Y-m-d', strtotime("-$days days"));

            $records = ProductionRecord::whereBetween('production_date', [$startDate, $endDate])
                ->orderBy('production_date', 'asc')
                ->select('production_date', 'total_montaggio', 'total_orlatura', 'total_taglio')
                ->get();

            $labels = [];
            $montaggio = [];
            $orlatura = [];
            $taglio = [];
            $totals = [];

            foreach ($records as $record) {
                $labels[] = date('d/m', strtotime($record->production_date));
                $mont = $record->total_montaggio ?? 0;
                $orl = $record->total_orlatura ?? 0;
                $tagl = $record->total_taglio ?? 0;

                $montaggio[] = $mont;
                $orlatura[] = $orl;
                $taglio[] = $tagl;
                $totals[] = $mont + $orl + $tagl;
            }

            $this->json([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        'montaggio' => $montaggio,
                        'orlatura' => $orlatura,
                        'taglio' => $taglio,
                        'total' => $totals
                    ]
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore nel recupero trend: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore nel caricamento trend'], 500);
        }
    }

    /**
     * API: Performance macchine
     */
    public function getMachinePerformance()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        try {
            $month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
            $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

            $monthStart = date('Y-m-01', strtotime("$year-$month-01"));
            $monthEnd = date('Y-m-t', strtotime("$year-$month-01"));

            $performance = ProductionRecord::whereBetween('production_date', [$monthStart, $monthEnd])
                ->selectRaw('
                    SUM(manovia1) as manovia1_total,
                    SUM(manovia2) as manovia2_total,
                    SUM(orlatura1) as orlatura1_total,
                    SUM(orlatura2) as orlatura2_total,
                    SUM(orlatura3) as orlatura3_total,
                    SUM(orlatura4) as orlatura4_total,
                    SUM(orlatura5) as orlatura5_total,
                    SUM(taglio1) as taglio1_total,
                    SUM(taglio2) as taglio2_total
                ')
                ->first();

            $machines = [
                ['name' => 'Manovia 1', 'value' => $performance->manovia1_total ?? 0],
                ['name' => 'Manovia 2', 'value' => $performance->manovia2_total ?? 0],
                ['name' => 'Orlatura 1', 'value' => $performance->orlatura1_total ?? 0],
                ['name' => 'Orlatura 2', 'value' => $performance->orlatura2_total ?? 0],
                ['name' => 'Orlatura 3', 'value' => $performance->orlatura3_total ?? 0],
                ['name' => 'Orlatura 4', 'value' => $performance->orlatura4_total ?? 0],
                ['name' => 'Orlatura 5', 'value' => $performance->orlatura5_total ?? 0],
                ['name' => 'Taglio 1', 'value' => $performance->taglio1_total ?? 0],
                ['name' => 'Taglio 2', 'value' => $performance->taglio2_total ?? 0],
            ];

            $this->json([
                'success' => true,
                'data' => $machines
            ]);

        } catch (Exception $e) {
            error_log("Errore nel recupero performance macchine: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore nel caricamento performance'], 500);
        }
    }

    /**
     * API: Confronto periodi
     */
    public function getComparison()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        try {
            // Questo mese
            $currentMonthStart = date('Y-m-01');
            $currentMonthEnd = date('Y-m-t');

            // Mese scorso
            $lastMonthStart = date('Y-m-01', strtotime('first day of last month'));
            $lastMonthEnd = date('Y-m-t', strtotime('last day of last month'));

            $currentMonth = ProductionRecord::whereBetween('production_date', [$currentMonthStart, $currentMonthEnd])
                ->selectRaw('
                    SUM(total_montaggio) as total_montaggio,
                    SUM(total_orlatura) as total_orlatura,
                    SUM(total_taglio) as total_taglio
                ')
                ->first();

            $lastMonth = ProductionRecord::whereBetween('production_date', [$lastMonthStart, $lastMonthEnd])
                ->selectRaw('
                    SUM(total_montaggio) as total_montaggio,
                    SUM(total_orlatura) as total_orlatura,
                    SUM(total_taglio) as total_taglio
                ')
                ->first();

            $currentTotal = ($currentMonth->total_montaggio ?? 0) +
                           ($currentMonth->total_orlatura ?? 0) +
                           ($currentMonth->total_taglio ?? 0);

            $lastTotal = ($lastMonth->total_montaggio ?? 0) +
                        ($lastMonth->total_orlatura ?? 0) +
                        ($lastMonth->total_taglio ?? 0);

            $percentageChange = 0;
            if ($lastTotal > 0) {
                $percentageChange = round((($currentTotal - $lastTotal) / $lastTotal) * 100, 1);
            }

            $this->json([
                'success' => true,
                'data' => [
                    'current_month' => $currentTotal,
                    'last_month' => $lastTotal,
                    'change' => $percentageChange,
                    'trend' => $percentageChange >= 0 ? 'up' : 'down'
                ]
            ]);

        } catch (Exception $e) {
            error_log("Errore nel confronto periodi: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore nel caricamento confronto'], 500);
        }
    }

    /**
     * API: Grafico personalizzato
     */
    public function getCustomChart()
    {
        $this->requireAuth();
        $this->requirePermission('produzione');

        try {
            $dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-01-01');
            $dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
            $departments = isset($_GET['departments']) ? explode(',', $_GET['departments']) : [];
            $groupBy = isset($_GET['group_by']) ? $_GET['group_by'] : 'month';

            if (empty($departments)) {
                $this->json(['success' => false, 'message' => 'Seleziona almeno un reparto'], 400);
                return;
            }

            // Recupera tutti i record nel periodo
            $records = ProductionRecord::whereBetween('production_date', [$dateFrom, $dateTo])
                ->orderBy('production_date', 'asc')
                ->get();

            // Aggrega i dati in base al raggruppamento
            $aggregatedData = $this->aggregateProductionData($records, $departments, $groupBy);

            $this->json([
                'success' => true,
                'data' => $aggregatedData
            ]);

        } catch (Exception $e) {
            error_log("Errore nel recupero grafico personalizzato: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Errore nel caricamento dati'], 500);
        }
    }

    /**
     * Helper: Aggrega dati produzione in base al gruppo
     */
    private function aggregateProductionData($records, $departments, $groupBy)
    {
        $grouped = [];
        $datasets = [];

        // Inizializza datasets per ogni reparto
        foreach ($departments as $dept) {
            $datasets[$dept] = [];
        }

        // Raggruppa i record
        foreach ($records as $record) {
            $key = $this->getGroupKey($record->production_date, $groupBy);

            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
                foreach ($departments as $dept) {
                    $grouped[$key][$dept] = 0;
                }
            }

            // Somma i valori per ogni reparto
            foreach ($departments as $dept) {
                $grouped[$key][$dept] += $record->$dept ?? 0;
            }
        }

        // Ordina per chiave
        ksort($grouped);

        // Prepara labels e datasets
        $labels = [];
        foreach ($grouped as $key => $values) {
            $labels[] = $this->formatGroupLabel($key, $groupBy);
            foreach ($departments as $dept) {
                $datasets[$dept][] = $values[$dept];
            }
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }

    /**
     * Helper: Ottiene chiave di raggruppamento
     */
    private function getGroupKey($date, $groupBy)
    {
        $timestamp = strtotime($date);

        switch ($groupBy) {
            case 'day':
                return date('Y-m-d', $timestamp);
            case 'week':
                return date('Y', $timestamp) . '-W' . date('W', $timestamp);
            case 'month':
                return date('Y-m', $timestamp);
            case 'quarter':
                $quarter = ceil(date('n', $timestamp) / 3);
                return date('Y', $timestamp) . '-Q' . $quarter;
            default:
                return date('Y-m-d', $timestamp);
        }
    }

    /**
     * Helper: Formatta label del gruppo
     */
    private function formatGroupLabel($key, $groupBy)
    {
        switch ($groupBy) {
            case 'day':
                return date('d/m/Y', strtotime($key));
            case 'week':
                $parts = explode('-W', $key);
                return 'Sett. ' . $parts[1] . '/' . $parts[0];
            case 'month':
                $months = [
                    '01' => 'Gen', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                    '05' => 'Mag', '06' => 'Giu', '07' => 'Lug', '08' => 'Ago',
                    '09' => 'Set', '10' => 'Ott', '11' => 'Nov', '12' => 'Dic'
                ];
                $parts = explode('-', $key);
                return $months[$parts[1]] . ' ' . $parts[0];
            case 'quarter':
                $parts = explode('-Q', $key);
                return 'Q' . $parts[1] . ' ' . $parts[0];
            default:
                return $key;
        }
    }

    /**
     * Helper: Calcola giorni lavorativi in un mese (Lun-Ven)
     */
    private function getWorkingDaysInMonth($year, $month)
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $workingDays = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayOfWeek = date('N', strtotime("$year-$month-$day"));
            // Lun-Ven (1-5), escludi Sabato (6) e Domenica (7)
            if ($dayOfWeek <= 5) {
                $workingDays++;
            }
        }

        return $workingDays;
    }
}