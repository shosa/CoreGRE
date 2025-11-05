<?php
// Funzioni helper (dalle originali)
function getUniqueDoganaleCodes($articoli)
{
    $codes = [];
    foreach ($articoli as $articolo) {
        if (!in_array($articolo->voce_doganale, $codes)) {
            $codes[] = $articolo->voce_doganale;
        }
    }
    return $codes;
}

// Funzione per separare gli articoli con voce doganale 64061010 dagli altri
function separateArticlesByVoceDoganale($articoli)
{
    $priorityArticles = [];
    $otherArticles = [];

    foreach ($articoli as $articolo) {
        if ($articolo->qta_reale > 0 && $articolo->is_mancante == 0) {
            if ($articolo->voce_doganale == '64061010') {
                $priorityArticles[] = $articolo;
            } else {
                $otherArticles[] = $articolo;
            }
        }
    }

    return ['priority' => $priorityArticles, 'others' => $otherArticles];
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DDT n° <?php echo $progressivo; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Nascondi righe vuote di riempimento nella visualizzazione normale */
        .empty-filler-row {
            display: none;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                margin-top: 0.5cm;
                margin-bottom: 0.5cm;
                margin-left: 1cm;
                margin-right: 1cm;
            }

            /* Mostra righe vuote di riempimento solo in stampa */
            .empty-filler-row {
                display: table-row !important;
            }

            /* Nascondi bordi orizzontali delle righe vuote, mantieni solo verticali */
            .empty-filler-row td {
                border-top: none !important;
                border-bottom: none !important;
            }

            /* Nasconde il totale in tutte le pagine tranne l'ultima */
            tfoot {
                display: table-row-group !important;
            }

            tfoot tr:last-child {
                display: table-row !important;
            }

            /* Per contenuti ripetuti su ogni pagina */
            thead {
                display: table-header-group;
            }
        }

        .right-align {
            text-align: right;
        }

        .no-border-right {
            border-right: none;
        }

        .no-border-left {
            border-left: none;
        }

        /* Riduci la larghezza delle celle per il terzista */
        .terzista-table td {
            width: 50%;
        }

        /* Riduci l'altezza delle righe */
        .table-bordered tbody tr {
            line-height: 1;
            height: 20px !important;
        }

        .table-bordered thead tr {
            line-height: 1;
            height: 20px !important;
        }

        /* Stile per la firma */
        .signature-block {
            margin-top: 30px;
            padding-top: 10px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container mt-6">
        <!-- Logo e dati terzista -->
        <div class="row">
            <div class="col-md-12 left-align">
                <table class="table table-bordered terzista-table">
                    <tbody>
                        <tr>
                            <td>
                                <div class="text-left">
                                    <img src="<?= $this->url('/assets/top_logo.jpg') ?>" alt="Logo" style="max-width: 400px;">
                                </div>
                            </td>
                            <td>
                                <h5>SPETT.LE:</h5>
                                <p>
                                <h4><?php echo htmlspecialchars($terzista->ragione_sociale ?? ''); ?></h4>
                                </p>
                                <p><?php echo htmlspecialchars($terzista->indirizzo_1 ?? ''); ?></p>
                                <?php if (!empty($terzista->indirizzo_2)): ?>
                                    <p><?php echo htmlspecialchars($terzista->indirizzo_2); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($terzista->indirizzo_3)): ?>
                                    <p><?php echo htmlspecialchars($terzista->indirizzo_3); ?></p>
                                <?php endif; ?>
                                <p><?php echo htmlspecialchars($terzista->nazione ?? ''); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Titolo del documento -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h2 class="mt-3">
                DDT VALORIZZATO n° <?php echo $progressivo; ?>
                <?php if (isset($documento->stato) && $documento->stato == 'Aperto'): ?>
                    <span class="text-danger font-weight-bold ml-3">PROVVISORIO DA CHIUDERE</span>
                <?php endif; ?>
            </h2>
        </div>

        <!-- Informazioni del documento -->
        <div class="row mt-4">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>TIPO DOCUMENTO:</strong></td>
                            <td>DDT VALORIZZATO</td>
                            <td><strong>N° DOCUMENTO:</strong></td>
                            <td><?php echo $progressivo; ?></td>
                            <td><strong>DATA DOCUMENTO:</strong></td>
                            <td><?php echo htmlspecialchars($documento->data ?? ''); ?></td>
                        </tr>
                        <tr>
                            <td><strong>TRASPORTATORE:</strong></td>
                            <td colspan="3"><?php echo htmlspecialchars($piede->trasportatore ?? ''); ?></td>
                            <td><strong>CONSEGNA:</strong></td>
                            <td colspan="1"><?php echo htmlspecialchars($terzista->consegna ?? ''); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabella articoli -->
        <div class="row mt-4">
            <div class="col-md-12">
                <table class="table table-bordered" style="font-size:9pt;">
                    <thead>
                        <tr>
                            <th>ARTICOLO</th>
                            <th class="no-border-right">DESCRIZIONE</th>
                            <th class="no-border-left">NOM.COM.</th>
                            <th>UM</th>
                            <th>QTA</th>
                            <th>VALORE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dati del DDT (articoli normali) -->
                        <?php
                        // Separa gli articoli con voce doganale 64061010 dagli altri
                        $separatedArticles = separateArticlesByVoceDoganale($articoli);
                        $priorityArticles = $separatedArticles['priority'];
                        $otherArticles = $separatedArticles['others'];

                        // Prima visualizza gli articoli con voce doganale 64061010 (se ce ne sono)
                        foreach ($priorityArticles as $articolo):
                            $subtotal = round(($articolo->qta_reale ?? 0) * ($articolo->prezzo_unitario ?? 0), 2);
                            ?>
                            <tr style="border:none !important;">
                                <td><?php echo htmlspecialchars($articolo->codice_articolo ?? ''); ?></td>
                                <td class="no-border-right"><?php echo htmlspecialchars($articolo->descrizione ?? ''); ?></td>
                                <td class="no-border-left"><?php echo htmlspecialchars($articolo->voce_doganale ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($articolo->um ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($articolo->qta_reale ?? '0'); ?></td>
                                <td><?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- Aggiungi riga di commento solo se ci sono articoli prioritari E articoli normali -->
                        <?php if (!empty($priorityArticles) && !empty($otherArticles)): ?>
                            <tr>
                                <td colspan="6"><strong>COMPLETE DI ACCESSORI:</strong></td>
                            </tr>
                        <?php endif; ?>

                        <!-- Visualizza tutti gli altri articoli -->
                        <?php foreach ($otherArticles as $articolo):
                            $subtotal = round(($articolo->qta_reale ?? 0) * ($articolo->prezzo_unitario ?? 0), 2);
                            ?>
                            <tr style="border:none !important;">
                                <td><?php echo htmlspecialchars($articolo->codice_articolo ?? ''); ?></td>
                                <td class="no-border-right"><?php echo htmlspecialchars($articolo->descrizione ?? ''); ?></td>
                                <td class="no-border-left"><?php echo htmlspecialchars($articolo->voce_doganale ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($articolo->um ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($articolo->qta_reale ?? '0'); ?></td>
                                <td><?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- Raggruppa i mancanti per DDT di origine -->
                        <?php
                        $mancantiByDDT = [];
                        foreach ($articoli as $articolo) {
                            if ($articolo->qta_reale > 0 && $articolo->is_mancante == 1) {
                                $rif = $articolo->rif_mancante ?: 'Senza riferimento';
                                if (!isset($mancantiByDDT[$rif])) {
                                    $mancantiByDDT[$rif] = [];
                                }
                                $mancantiByDDT[$rif][] = $articolo;
                            }
                        }
                        ?>

                        <!-- Visualizza i mancanti raggruppati per DDT di origine (solo se ci sono) -->
                        <?php if (!empty($mancantiByDDT)): ?>
                            <?php foreach ($mancantiByDDT as $rif => $mancanti): ?>
                                <tr>
                                    <td colspan="1"></td>
                                    <td class="no-border-right" colspan="5"><strong>MANCANTI SU <?php echo htmlspecialchars($rif ?? ''); ?></strong>
                                    </td>
                                </tr>
                                <?php foreach ($mancanti as $articolo):
                                    $subtotal = round(($articolo->qta_reale ?? 0) * ($articolo->prezzo_unitario ?? 0), 2);
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($articolo->codice_articolo ?? ''); ?></td>
                                        <td class="no-border-right"><?php echo htmlspecialchars($articolo->descrizione ?? ''); ?></td>
                                        <td class="no-border-left"><?php echo htmlspecialchars($articolo->voce_doganale ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($articolo->um ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($articolo->qta_reale ?? '0'); ?></td>
                                        <td><?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Righe vuote e Materiali Mancanti (sezione esistente per i mancanti) -->
                        <?php if (isset($datiMancanti) && count($datiMancanti) > 0): ?>
                            <tr>
                                <td colspan="6"></td>
                            </tr>
                            <tr>
                                <td colspan="6"><strong>MATERIALI MANCANTI</strong></td>
                            </tr>
                            <?php foreach ($datiMancanti as $mancante): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($mancante->codice_articolo ?? ''); ?></td>
                                    <td class="no-border-right"><?php echo htmlspecialchars($mancante->descrizione ?? ''); ?></td>
                                    <td class="no-border-left"><?php echo htmlspecialchars($mancante->voce_doganale ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($mancante->um ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($mancante->qta_mancante ?? '0'); ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Righe vuote per riempire la prima pagina (solo in stampa) -->
                        <?php
                        // Calcola il numero totale di righe stampate
                        $totalRows = 0;

                        // Conta articoli prioritari (64061010)
                        foreach ($articoli as $art) {
                            if ($art->qta_reale > 0 && $art->is_mancante == 0 && $art->voce_doganale == '64061010') {
                                $totalRows++;
                            }
                        }

                        // Conta altri articoli
                        foreach ($articoli as $art) {
                            if ($art->qta_reale > 0 && $art->is_mancante == 0 && $art->voce_doganale != '64061010') {
                                $totalRows++;
                            }
                        }

                        // Conta mancanti raggruppati (se presenti)
                        if (!empty($mancantiByDDT)) {
                            foreach ($mancantiByDDT as $rif => $mancanti) {
                                $totalRows++; // Riga header "MANCANTI SU..."
                                $totalRows += count($mancanti); // Righe mancanti
                            }
                        }

                        // Conta materiali mancanti (se presenti)
                        if (isset($datiMancanti) && count($datiMancanti) > 0) {
                            $totalRows++; // Riga vuota
                            $totalRows++; // Riga header "MATERIALI MANCANTI"
                            $totalRows += count($datiMancanti); // Righe mancanti
                        }

                        // Numero minimo di righe per riempire la prima pagina (puoi modificare questo valore)
                        $minRows = 16;
                        $emptyRowsNeeded = max(0, $minRows - $totalRows);

                        // Stampa righe vuote solo in modalità stampa
                        for ($i = 0; $i < $emptyRowsNeeded; $i++):
                        ?>
                            <tr class="empty-filler-row">
                                <td>&nbsp;</td>
                                <td class="no-border-right">&nbsp;</td>
                                <td class="no-border-left">&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        <?php endfor; ?>

                        <!-- Righe per voce e peso -->
                        <tr>
                            <td colspan="6"> </td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-center"><strong>RIEPILOGO PESI</strong></td>
                        </tr>
                        <?php for ($i = 1; $i <= 15; $i++): ?>
                            <?php if (!empty($piede->{'voce_' . $i}) && !empty($piede->{'peso_' . $i})): ?>
                                <tr>
                                    <td></td>
                                    <td style="text-align:right;">
                                        <?php if (($piede->{'voce_' . $i} ?? '') === 'SOTTOPIEDI'): ?>
                                            <b><u>SOTTOPIEDI</u></b> N.C. 56031480 PESO NETTO KG.
                                        <?php else: ?>
                                            N.C. <?php echo htmlspecialchars($piede->{'voce_' . $i} ?? ''); ?> PESO NETTO KG.
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($piede->{'peso_' . $i} ?? '0'); ?></td>
                                    <?php if ($i == 1): ?>
                                        <td rowspan="<?php echo count(value: array_filter(range(1, 15), function ($j) use ($piede) {
                                            return !empty($piede->voce_ . $j) && !empty($piede->peso_ . $j);
                                        })); ?>" colspan="3" style="vertical-align: middle; text-align: center;">
                                            <strong>TOT. COLLI <?php echo htmlspecialchars($piede->n_colli ?? '0'); ?>
                                                <?php echo htmlspecialchars($piede->aspetto_colli ?? ''); ?></strong><br><br><br><br>
                                            Tot. Peso Lordo kg. <?php echo htmlspecialchars($piede->tot_peso_lordo ?? '0'); ?><br><br>
                                            Tot. Peso Netto kg. <?php echo htmlspecialchars($piede->tot_peso_netto ?? '0'); ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <!-- Righe per autorizzazione -->
                        <tr>
                            <td colspan="6">
                                <?php if (!empty($piede->consegnato_per)): ?>
                                    <p><strong>Materiale consegnato per la realizzazione di
                                            <?php echo htmlspecialchars($piede->consegnato_per); ?>:</strong></p>
                                <?php endif; ?>
                                <ul>
                                    <?php
                                    if (!empty($lanci)) {
                                        foreach ($lanci as $lancio):
                                            ?>
                                            <li><strong>#</strong> <?php echo htmlspecialchars($lancio->lancio ?? ''); ?> | <strong>Articolo:</strong>
                                                <?php echo htmlspecialchars($lancio->articolo ?? ''); ?> | <strong>Paia:</strong>
                                                <?php echo htmlspecialchars($lancio->paia ?? '0'); ?></li>
                                            <?php
                                        endforeach;
                                    }
                                    ?>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-center"><?php echo htmlspecialchars($documento->autorizzazione ?? ''); ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Valore totale in €:</strong></td>
                            <td><?php echo number_format($total ?? 0, 2, ',', '.'); ?></td>
                        </tr>
                        <!-- Riga per la firma -->
                        <tr>
                            <td colspan="6">
                                <div class="d-flex justify-content-between align-items-end">
                                    <?php if (!empty($documento->commento)): ?>
                                        <div class="comment-block text-left" style="max-width: 60%;">

                                            <span class="font-weight-bold"
                                                style="font-size:12pt;"><?php echo htmlspecialchars($documento->commento ?? ''); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div></div> <!-- Div vuoto quando non c'è commento, per mantenere il layout flex -->
                                    <?php endif; ?>

                                    <div class="signature-block text-right">
                                        <div
                                            style="display: inline-block; margin-top: 50px; border-top: 1px solid #000; width: 250px; text-align: center;">
                                            <p style="margin-top: 5px;">Firma per accettazione</p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>