<?php

use App\Models\CoreAnagrafica;

/**
 * Controller per la gestione delle etichette DYMO
 * Migrazione del sistema legacy con integrazione PJAX
 */
class EtichetteController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->requirePermission('etichette');
        // Temporarily disable permission check for testing
        // $this->requirePermission('etichette');
    }

    /**
     * Pagina principale per la stampa etichette
     */
    public function index()
    {
       
        
        $data = [
            'pageTitle' => 'Etichette DYMO - WEBGRE',
            'pageScripts' => $this->getDymoScripts()
        ];

        $this->render('etichette.index', $data);
    }

    /**
     * Pagina per creare liste di prelievo/versamento
     */
    public function decode()
    {
        $data = [
            'pageTitle' => 'Crea Lista - Etichette DYMO',
        ];

        $this->render('etichette.decode', $data);
    }

    /**
     * Processa la creazione del PDF per liste di prelievo/versamento
     */
    public function processDecodeForm()
    {
        if (!$this->isPost()) {
            $this->redirect($this->url('/etichette/decode'));
            return;
        }

        $barcodes = trim($this->input('barcodes'));
        $azione = $this->input('azione', 'PRELIEVO');

        if (empty($barcodes)) {
            $this->setFlash('error', 'Inserire almeno un barcode');
            $this->redirect($this->url('/etichette/decode'));
            return;
        }

        try {
            // Processa i barcode
            $barcodesArray = explode("\n", $barcodes);
            $barcodesArray = array_filter(array_map('trim', $barcodesArray));
            
            // Rimuovi prefisso MGM/mgm
            $barcodesArray = array_map(function($barcode) {
                return str_replace(['MGM', 'mgm'], '', $barcode);
            }, $barcodesArray);

            $barcodeCounts = array_count_values($barcodesArray);
            $uniqueBarcodes = array_keys($barcodeCounts);

            if (empty($uniqueBarcodes)) {
                $this->setFlash('error', 'Nessun barcode valido inserito');
                $this->redirect($this->url('/etichette/decode'));
                return;
            }

            // Query per ottenere i dati degli articoli con Eloquent
            $results = CoreAnagrafica::whereIn('barcode', $uniqueBarcodes)
                ->distinct()
                ->get(['barcode', 'art', 'des']);

            if (empty($results)) {
                $this->setFlash('error', 'Nessun articolo trovato per i barcode inseriti');
                $this->redirect($this->url('/etichette/decode'));
                return;
            }

            // Genera il PDF
            $this->generateDistintaPDF($results, $barcodeCounts, $azione);

            $this->logActivity('etichette', 'generate_pdf', "Generata distinta {$azione}", 
                "Barcode processati: " . count($uniqueBarcodes));

        } catch (Exception $e) {
            error_log("Errore generazione PDF etichette: " . $e->getMessage());
            $this->setFlash('error', 'Errore durante la generazione del PDF: ' . $e->getMessage());
            $this->redirect($this->url('/etichette/decode'));
        }
    }

    /**
     * API per ottenere suggerimenti di ricerca articoli
     */
    public function getSuggestions()
    {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Richiesta non valida'], 400);
            return;
        }

        $searchTerm = $this->input('q', '');
        
        if (strlen($searchTerm) < 2) {
            $this->json([]);
            return;
        }

        try {
            $searchParam = '%' . $searchTerm . '%';
            $suggestions = CoreAnagrafica::select(['art', 'des'])
                ->where('art', 'like', $searchParam)
                ->orWhere('des', 'like', $searchParam)
                ->distinct()
                ->orderBy('art')
                ->limit(20)
                ->get();

            $this->json($suggestions);

        } catch (Exception $e) {
            error_log("Errore ricerca suggerimenti: " . $e->getMessage());
            $this->json(['error' => 'Errore durante la ricerca'], 500);
        }
    }

    /**
     * API per ottenere dettagli articolo
     */
    public function getArticleDetails()
    {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Richiesta non valida'], 400);
            return;
        }

        $artCode = $this->input('art');
        
        if (empty($artCode)) {
            $this->json(['error' => 'Codice articolo richiesto'], 400);
            return;
        }

        try {
            $details = CoreAnagrafica::where('art', $artCode)->first(['cm', 'barcode', 'des', 'art']);

            if (!$details) {
                $this->json(['error' => 'Nessun articolo trovato con il codice specificato']);
                return;
            }

            $this->json($details);

        } catch (Exception $e) {
            error_log("Errore recupero dettagli articolo: " . $e->getMessage());
            $this->json(['error' => 'Errore durante il recupero dei dettagli'], 500);
        }
    }

    /**
     * API per creare nuovo articolo
     */
    public function createArticle()
    {
        if (!$this->isPost() || !$this->isAjax()) {
            $this->json(['error' => 'Richiesta non valida'], 400);
            return;
        }

        $cm = trim($this->input('cm'));
        $art = trim($this->input('art'));
        $des = trim($this->input('des'));

        // Validazione
        $errors = $this->validate([
            'cm' => $cm,
            'art' => $art,
            'des' => $des
        ], [
            'cm' => 'required',
            'art' => 'required',
            'des' => 'required|min:3'
        ]);

        if (!empty($errors)) {
            $this->json(['error' => 'Dati non validi: ' . implode(', ', $errors)], 400);
            return;
        }

        try {
            // Verifica che l'articolo non esista già
            $existing = CoreAnagrafica::where('art', $art)->exists();
            if ($existing) {
                $this->json(['error' => 'Articolo già esistente'], 400);
                return;
            }

            // Inserisci nuovo articolo
            CoreAnagrafica::create([
                'cm' => $cm,
                'art' => $art,
                'des' => $des
            ]);

            $this->logActivity('etichette', 'create_article', "Creato articolo: {$art}", 
                "CM: {$cm}, Descrizione: {$des}");

            $this->json(['success' => true, 'message' => 'Articolo creato con successo']);

        } catch (Exception $e) {
            error_log("Errore creazione articolo: " . $e->getMessage());
            $this->json(['error' => 'Errore durante la creazione dell\'articolo'], 500);
        }
    }

    /**
     * Genera il PDF per la distinta di prelievo/versamento
     */
    private function generateDistintaPDF($results, $barcodeCounts, $azione)
    {
        // Verifica se TCPDF è disponibile
        if (!class_exists('TCPDF')) {
            throw new Exception('TCPDF non disponibile');
        }

        $dicitura = ($azione === 'VERSAMENTO') ? 'Distinta di Versamento a Magazzino' : 'Distinta di Prelievo di Magazzino';

        $pdf = new TCPDF();
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();

        // Logo aziendale
        $logoPath = APP_ROOT . '/../img/logo.png';
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 10, 10, 50, '', 'PNG');
        }

        // Header
        $pdf->SetXY(60, 15);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 0, 'DOCUMENTO AD USO INTERNO', 0, 1, 'R');
        $pdf->Ln(15);

        // Titolo
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, $dicitura, 0, 1, 'L');
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Del _____________', 0, 1, 'R');
        $pdf->Ln(5);

        // Intestazioni tabella
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(50, 7, 'CODICE', 1, 0, 'L', false);
        $pdf->Cell(120, 7, 'DESCRIZIONE', 1, 0, 'L', false);
        $pdf->Cell(15, 7, 'QTA', 1, 1, 'C', false);

        // Dati
        $fill = false;
        foreach ($results as $row) {
            $qty = $barcodeCounts[$row->barcode] ?? 1;
            $pdf->Cell(50, 7, $row->art, 'LR', 0, 'L', $fill);
            $pdf->Cell(120, 7, $row->des, 'LR', 0, 'L', $fill);
            $pdf->Cell(15, 7, $qty, 'LR', 1, 'C', $fill);
            $fill = !$fill;
        }

        // Chiudi tabella
        $pdf->Cell(185, 0, '', 'T');

        // Output PDF
        $filename = 'DISTINTA_' . strtoupper($azione) . '_' . date('Y-m-d_H-i') . '.pdf';
        $pdf->Output($filename, 'I');
        exit;
    }

    /**
     * Ottiene gli script JavaScript necessari per DYMO
     */
    private function getDymoScripts()
    {
        return '
        // Carica gli script DYMO se non già presenti
        if (!window.dymo) {
            const dymoScript = document.createElement("script");
            dymoScript.src = "' . $this->url('/public/js/dymo.js') . '";
            dymoScript.charset = "UTF-8";
            document.head.appendChild(dymoScript);
        }

        // Inizializza il sistema etichette dopo il caricamento
        if (window.EtichetteManager && typeof window.EtichetteManager.init === "function") {
            window.EtichetteManager.init();
        }
        ';
    }
}