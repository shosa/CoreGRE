<?php
/**
 * Excel Processor Utility
 * Riutilizza la logica collaudata per manipolare file Excel
 * Migrazione da process-excel.php del sistema legacy
 */
class ExcelProcessor
{
    private $tempDir;
    private $srcDir;

    public function __construct()
    {
        // Use storage directory in the new project structure
        $this->tempDir = APP_ROOT . '/storage/export/temp/';
        $this->srcDir = APP_ROOT . '/storage/export/src/';
        
        // Assicurati che le directory esistano
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
        if (!is_dir($this->srcDir)) {
            mkdir($this->srcDir, 0777, true);
        }
    }

    /**
     * Processa un file Excel e estrae i dati
     * Logica riutilizzata da process-excel.php
     */
    public function processExcelFile($fileName, $progressivo = null)
    {
        $filePath = $this->tempDir . $fileName;

        // Se il file non è in temp e abbiamo un progressivo, cerca in src
        if (!file_exists($filePath) && $progressivo) {
            $srcPath = $this->srcDir . $progressivo . '/' . $fileName;
            if (file_exists($srcPath)) {
                $filePath = $srcPath;
            }
        }

        if (!file_exists($filePath)) {
            throw new Exception('File non trovato: ' . $fileName . ' (cercato in temp e src)');
        }

        

        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            $headers = [];
            $rows = [
                'taglio' => [],
                'orlatura' => []
            ];

            $isTaglio = false;
            $isOrlatura = false;
            $modello = '';

            foreach ($worksheet->getRowIterator() as $index => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);

                $rowData = [];

                foreach ($cellIterator as $cell) {
                    $value = $cell->getValue();
                    // Sanitizza ogni valore della cella usando la logica legacy
                    $rowData[] = $this->sanitizeText($value);
                }

                if ($index == 1) {
                    $modello = isset($rowData[1]) ? $this->sanitizeText($rowData[1]) : '';
                    continue;
                }

                if (isset($rowData[0]) && $rowData[0] == "02 - 1 TAGLIO") {
                    $isTaglio = true;
                    $isOrlatura = false;
                    continue;
                }

                if (isset($rowData[0]) && $rowData[0] == "04 - 1 ORLATURA") {
                    $isOrlatura = true;
                    $isTaglio = false;
                    continue;
                }

                if (count($rowData) > 0) {
                    $colonnaA = isset($rowData[0]) ? trim($rowData[0]) : '';
                    $colonnaB = isset($rowData[1]) ? trim($rowData[1]) : '';
                    
                    // Controlla se questa riga contiene "06 - 1 MONTAGGIO"
                    $containsMontaggio = false;
                    foreach ($rowData as $cell) {
                        $cellValue = $this->sanitizeText($cell);
                        if (stripos($cellValue, "06 - 1 MONTAGGIO") !== false || 
                            stripos($cellValue, "06 - MONTAGGIO") !== false ||
                            stripos($cellValue, "06-1 MONTAGGIO") !== false ||
                            stripos($cellValue, "06-MONTAGGIO") !== false) {
                            $containsMontaggio = true;
                            break;
                        }
                    }
                    
                    // Se troviamo MONTAGGIO, smetti di processare
                    if ($containsMontaggio) {
                        break;
                    }
                    
                    if (empty($colonnaA)) {
                        if (!empty($colonnaB)) {
                            $rowData[0] = "ALTRO";
                        } else {
                            continue;
                        }
                    }

                    // Assicurati che l'array abbia esattamente 5 elementi
                    while (count($rowData) < 5) {
                        $rowData[] = '';
                    }

                    $processedRow = array_slice($rowData, 0, 5);
                    
                    // Ulteriore sanitizzazione per sicurezza
                    foreach ($processedRow as $key => $value) {
                        $processedRow[$key] = $this->sanitizeText($value);
                    }
                    
                    $hasContent = false;
                    foreach ($processedRow as $cell) {
                        if (!empty(trim($cell))) {
                            $hasContent = true;
                            break;
                        }
                    }
                    
                    if ($hasContent) {
                        if ($isTaglio && !$isOrlatura) {
                            $rows['taglio'][] = $processedRow;
                        }

                        if ($isOrlatura) {
                            $rows['orlatura'][] = $processedRow;
                        }
                    }
                }
            }

            // Ottieni headers dalla 6a riga
            $headerRow = $worksheet->getRowIterator(6)->current();
            if ($headerRow !== null) {
                $cellIterator = $headerRow->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);

                foreach ($cellIterator as $cell) {
                    $value = $cell->getValue();
                    $headers[] = $this->sanitizeText($value);
                }

                $headers = array_slice($headers, 0, 5);
                while (count($headers) < 5) {
                    $headers[] = '';
                }
            } else {
                $headers = ['Colonna 1', 'Colonna 2', 'Colonna 3', 'Colonna 4', 'Colonna 5'];
            }

            // Estrai anche lancio e quantità se il file è già processato
            $lancio = '';
            $qty = '';
            try {
                $lancio = $worksheet->getCell('B2')->getValue() ?: '';
                $qty = $worksheet->getCell('B3')->getValue() ?: '';
            } catch (Exception $e) {
                // Ignora errori nella lettura di lancio/qty
            }

            return [
                'success' => true,
                'modello' => $modello,
                'lancio' => $lancio,
                'qty' => $qty,
                'headers' => $headers,
                'rows' => $rows
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Errore nel processamento del file: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Salva dati Excel processati come file Excel nella cartella temp
     * Logica riutilizzata da save-excel.php (NON salva nel database!)
     */
    public function saveExcelData($data, Database $db = null)
    {
        try {
            
            
            $modello = $data['modello'] ?? '';
            $lancio = $data['lancio'] ?? '';
            $qty = floatval($data['qty'] ?? 1);
            $tableTaglio = $data['tableTaglio'] ?? [];
            $tableOrlatura = $data['tableOrlatura'] ?? [];
            $id_documento = $data['id_documento'] ?? null;
            $originalFileName = $data['originalFileName'] ?? null;

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator("Calzaturificio Emmegiemme Shoes Srl")
                ->setLastModifiedBy("Calzaturificio Emmegiemme Shoes Srl")
                ->setTitle("Scheda Tecnica")
                ->setCategory("Excel");

            // Add data to the sheet
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('SCHEDA TECNICA');

            $sheet->setCellValue('A1', 'ARTICOLO:');
            $sheet->setCellValue('B1', $modello);
            $sheet->setCellValue('A2', 'LANCIO:');
            $sheet->setCellValue('B2', $lancio);
            $sheet->setCellValue('A3', 'PAIA DA PRODURRE:');
            $sheet->setCellValue('B3', $qty);

            $sheet->setCellValue('A5', 'TIPO');
            $sheet->setCellValue('B5', 'CODICE');
            $sheet->setCellValue('C5', 'DESCRIZIONE');
            $sheet->setCellValue('D5', 'UM');
            $sheet->setCellValue('E5', 'CONS/PA');
            $sheet->setCellValue('F5', 'TOTALE');

            // Add the "TAGLIO" row before the TAGLIO table
            $sheet->insertNewRowBefore(6);
            $sheet->setCellValue('A6', 'TAGLIO');
            $sheet->getStyle('A6:F6')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('A6:F6')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('e6f5fa');
            $sheet->getStyle('A6:F6')
                ->getFont()
                ->setBold(true)
                ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));

            $rowIndex = 7;
            foreach ($tableTaglio as $row) {
                $colIndex = 1;
                foreach ($row as $cell) {
                    $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $cell);
                    $colIndex++;
                }
                $rowIndex++;
            }

            // Add the "ORLATURA" row
            $sheet->setCellValue('A' . $rowIndex, 'ORLATURA');
            $sheet->getStyle('A' . $rowIndex . ':F' . $rowIndex)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('A' . $rowIndex . ':F' . $rowIndex)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('e6f5fa');
            $sheet->getStyle('A' . $rowIndex . ':F' . $rowIndex)
                ->getFont()
                ->setBold(true)
                ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));

            $rowIndex++;

            foreach ($tableOrlatura as $row) {
                $colIndex = 1;
                foreach ($row as $cell) {
                    $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $cell);
                    $colIndex++;
                }
                $rowIndex++;
            }

            // Recupera l'autorizzazione se abbiamo un ID documento
            $autorizzazione = "";
            if ($id_documento && $db) {
                try {
                    $stmt = $db->query(
                        "SELECT autorizzazione FROM exp_documenti WHERE id = :id_documento",
                        [':id_documento' => $id_documento]
                    );
                    $result = $stmt->fetch();
                    
                    if ($result && isset($result['autorizzazione'])) {
                        $autorizzazione = $result['autorizzazione'];
                    }
                } catch (Exception $e) {
                    // Log error or handle it
                    error_log("Errore nel recupero dell'autorizzazione: " . $e->getMessage());
                }
            }

            // Lascia una riga vuota dopo la tabella
            $rowIndex += 2;
            
            // Aggiungi l'autorizzazione al foglio
            if (!empty($autorizzazione)) {
                $sheet->setCellValue('A' . $rowIndex, 'AUTORIZZAZIONE:');
                $sheet->getStyle('A' . $rowIndex)
                    ->getFont()
                    ->setBold(true);
                
                // Unisci le celle per l'autorizzazione che può essere lunga
                $sheet->mergeCells('B' . $rowIndex . ':F' . $rowIndex);
                $sheet->setCellValue('B' . $rowIndex, $autorizzazione);
                
                // Imposta lo stile per le celle dell'autorizzazione
                $sheet->getStyle('A' . $rowIndex . ':F' . $rowIndex)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    
                $sheet->getStyle('B' . $rowIndex)
                    ->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                    
                // Imposta l'altezza della riga in base al contenuto
                $sheet->getRowDimension($rowIndex)->setRowHeight(-1);
            }

            // Remove the 7th column if exists
            if ($sheet->getHighestColumn() === 'G') {
                $sheet->removeColumn('G');
            }

            // Autosize columns
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Elimina il file originale se esiste (per evitare duplicati)
            if ($originalFileName && file_exists($this->tempDir . $originalFileName)) {
                unlink($this->tempDir . $originalFileName);
            }

            // Salva il file processato con il nome del modello (come nel sistema legacy)
            $filename = $this->tempDir . $modello . '.xlsx';
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filename);

            return ['success' => true, 'filename' => basename($filename)];

        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Errore nel salvataggio: ' . $e->getMessage()];
        }
    }

    /**
     * Genera DDT finale elaborando tutti i file temporary
     * Logica identica a genera_ddt.php del sistema legacy
     */
    public function generaDDT($progressivo, Database $db)
    {
        try {
            
            
            $db->beginTransaction();
            
            // Elimina i dati articoli esistenti
            $db->execute("DELETE FROM exp_dati_articoli WHERE id_documento = :id_documento", [':id_documento' => $progressivo]);
            
            // Crea la cartella di destinazione se non esiste
            $destDir = $this->srcDir . $progressivo . '/';
            if (!file_exists($destDir)) {
                mkdir($destDir, 0777, true);
            }
            
            // Ottieni la lista dei file nella directory temporanea
            $files = scandir($this->tempDir);
            $files = array_diff($files, array('.', '..'));
            
            // Debug: log dei file trovati
            error_log("generaDDT: File trovati in temp: " . json_encode($files));
            
            foreach ($files as $file) {
                $filePath = $this->tempDir . $file;
                $destFilePath = $destDir . $file;

                // Sposta il file nella cartella di destinazione (come da legacy)
                if (file_exists($filePath)) {
                    rename($filePath, $destFilePath);
                    
                    // Leggi il file Excel spostato
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($destFilePath);
                    $spreadsheet = $reader->load($destFilePath);
                    $worksheet = $spreadsheet->getActiveSheet();

                    // Estrai i dati del lancio (celle fisse come da legacy)
                    $lancio = $worksheet->getCell('B2')->getValue();
                    $articolo = $worksheet->getCell('B1')->getValue();
                    $paia = $worksheet->getCell('B3')->getValue();

                    // Inserisci i dati del lancio
                    $db->execute(
                        "INSERT INTO exp_dati_lanci_ddt (id_doc, lancio, articolo, paia) VALUES (:id_doc, :lancio, :articolo, :paia)",
                        [
                            ':id_doc' => $progressivo,
                            ':lancio' => $lancio,
                            ':articolo' => $articolo,
                            ':paia' => (int) $paia
                        ]
                    );

                    // Estrai i dati degli articoli (identico a legacy)
                    $rows = $worksheet->toArray();
                    $rows = array_slice($rows, 6); // Salta le prime 6 righe di intestazione

                    foreach ($rows as $row) {
                        // Salta righe specifiche (identico a legacy)
                        if (in_array($row[0], ['ORLATURA', 'AUTORIZZAZIONE:']) || empty($row[1])) {
                            error_log("generaDDT: Saltata riga: " . json_encode($row[0]));
                            continue;
                        }

                        // Debug: log della riga da inserire
                        error_log("generaDDT: Inserimento riga - Codice: {$row[1]}, Desc: {$row[2]}, UM: {$row[3]}, Qty: {$row[5]}");

                        $db->execute(
                            "INSERT INTO exp_dati_articoli (id_documento, codice_articolo, descrizione, um, qta_originale, qta_reale) 
                             VALUES (:id_documento, :codice_articolo, :descrizione, :um, :qta_originale, :qta_reale)",
                            [
                                ':id_documento' => $progressivo,
                                ':codice_articolo' => $row[1],
                                ':descrizione' => $row[2],
                                ':um' => $row[3],
                                ':qta_originale' => $row[5], // Colonna F (totale)
                                ':qta_reale' => $row[5]
                            ]
                        );
                    }
                }
            }
            
            // AGGREGAZIONE: Rimuovi i duplicati e aggiorna le quantità (CRUCIALE!)
            error_log("generaDDT: Avvio consolidamento articoli per documento $progressivo");
            $this->consolidateArticles($progressivo, $db);
            
            $db->commit();
            
            error_log("generaDDT: DDT generato con successo per documento $progressivo");
            return ['success' => true, 'message' => 'DDT generato con successo'];
            
        } catch (Exception $e) {
            if ($db) {
                $db->rollback();
            }
            
            error_log("Errore nell'elaborazione dei file Excel per documento {$progressivo}: " . $e->getMessage());
            
            return [
                'success' => false, 
                'message' => 'Errore durante l\'elaborazione dei file Excel: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Gestisce upload file temporaneo
     */
    public function handleFileUpload($uploadedFile)
    {
        try {
            // Valida il file
            if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Errore nel caricamento del file');
            }

            if (!$this->isValidExcelFile($uploadedFile['tmp_name'])) {
                throw new Exception('Il file non è un Excel valido (.xlsx)');
            }

            // Genera nome file unico
            $fileName = uniqid('excel_') . '_' . time() . '.xlsx';
            $destination = $this->tempDir . $fileName;

            if (!move_uploaded_file($uploadedFile['tmp_name'], $destination)) {
                throw new Exception('Impossibile salvare il file');
            }

            return ['success' => true, 'fileName' => $fileName];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Funzione per sanitizzare il testo (da process-excel.php)
     */
    private function sanitizeText($text)
    {
        if ($text === null) return '';
        
        // Converti in stringa se non lo è già
        $text = (string) $text;
        
        // Rimuovi caratteri di controllo e non stampabili
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Limita la lunghezza per evitare problemi di memoria
        if (strlen($text) > 500) {
            $text = substr($text, 0, 497) . '...';
        }
        
        // Escape caratteri problematici per JSON
        $text = str_replace(['"', "'", '\\'], ['\"', "\'", '\\\\'], $text);
        
        return trim($text);
    }



    /**
     * Consolida gli articoli rimuovendo duplicati (da genera_ddt.php)
     */
    private function consolidateArticles($progressivo, Database $db)
    {
        // Prima ottieni i dati aggregati per codice articolo
        $stmt = $db->query(
            "SELECT codice_articolo, descrizione, um, voce_doganale, 
             ROUND(SUM(qta_originale), 2) as qta_originale, 
             ROUND(SUM(qta_reale), 2) as qta_reale
             FROM exp_dati_articoli
             WHERE id_documento = :id_documento
             GROUP BY codice_articolo, descrizione, um, voce_doganale",
            [':id_documento' => $progressivo]
        );
        $aggregatedItems = $stmt->fetchAll();
        
        // Elimina tutti i record esistenti
        $db->execute("DELETE FROM exp_dati_articoli WHERE id_documento = :id_documento", [':id_documento' => $progressivo]);
        
        // Reinserisci i dati aggregati
        foreach ($aggregatedItems as $item) {
            $db->execute(
                "INSERT INTO exp_dati_articoli 
                 (id_documento, codice_articolo, descrizione, voce_doganale, um, qta_originale, qta_reale) 
                 VALUES (:id_documento, :codice_articolo, :descrizione, :voce_doganale, :um, :qta_originale, :qta_reale)",
                [
                    ':id_documento' => $progressivo,
                    ':codice_articolo' => $item['codice_articolo'],
                    ':descrizione' => $item['descrizione'],
                    ':voce_doganale' => $item['voce_doganale'],
                    ':um' => $item['um'],
                    ':qta_originale' => $item['qta_originale'],
                    ':qta_reale' => $item['qta_reale']
                ]
            );
        }
    }

    /**
     * Valida se il file è un Excel valido
     */
    private function isValidExcelFile($filePath)
    {
        $mimeType = mime_content_type($filePath);
        return in_array($mimeType, [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel'
        ]);
    }

    /**
     * Cleanup file temporanei
     */
    public function cleanupTempFiles()
    {
        $files = glob($this->tempDir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Estrae dettagli da un file Excel (per preview)
     */
    public function getFileDetails($fileName)
    {
        $filePath = $this->tempDir . $fileName;
        
        if (!file_exists($filePath)) {
            return ['LANCIO' => 'N/A', 'PAIA_DA_PRODURRE' => 'N/A'];
        }

        try {
            
            
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $worksheet = $reader->load($filePath)->getActiveSheet();

            return [
                'LANCIO' => $worksheet->getCell('B2')->getValue() ?: 'N/A',
                'PAIA_DA_PRODURRE' => $worksheet->getCell('B3')->getValue() ?: 'N/A'
            ];
            
        } catch (Exception $e) {
            error_log("Errore nell'elaborazione del file Excel: " . $e->getMessage());
            return ['LANCIO' => 'N/A', 'PAIA_DA_PRODURRE' => 'N/A'];
        }
    }
}