<?php
// Colori per stati
$stato_colors = [
    'IN_PREPARAZIONE' => ['bg' => '#fff3cd', 'text' => '#856404', 'border' => '#ffeaa7'],
    'LANCIATO' => ['bg' => '#d1ecf1', 'text' => '#0c5460', 'border' => '#a8dadc'],
    'IN_LAVORAZIONE' => ['bg' => '#fff4e6', 'text' => '#e67e22', 'border' => '#f39c12'],
    'COMPLETO' => ['bg' => '#d4edda', 'text' => '#155724', 'border' => '#27ae60'],
    'SOSPESO' => ['bg' => '#f8d7da', 'text' => '#721c24', 'border' => '#e74c3c']
];

$colors = $stato_colors[$lancio->status] ?? $stato_colors['IN_PREPARAZIONE'];
$percentuale_completamento = $lancio->percentuale_completamento ?? 0;

// Conteggio fasi
$fasi = array_filter(explode(';', $lancio->phases_cycle));
$totaleFasi = count($fasi);
?>

<tr style="border-bottom: 1px solid #ddd;" onmouseover="this.style.backgroundColor='#f8f9fa'"
    onmouseout="this.style.backgroundColor='white'">
    <!-- Numero Lancio -->
    <td style="padding: 12px; vertical-align: middle;">
        <div style="font-weight: bold; color: #2c3e50; font-size: 14px;">
            <?= htmlspecialchars($lancio->launch_number) ?>
        </div>
        <div style="font-size: 11px; color: #7f8c8d;">
            <?= date('d/m/Y', strtotime($lancio->launch_date)) ?>
        </div>
    </td>

    <!-- Stato -->
    <td style="padding: 12px; vertical-align: middle; text-align: center;">
        <span style="
            padding: 4px 8px; 
            border-radius: 12px; 
            font-size: 10px; 
            font-weight: bold; 
            background-color: <?= $colors['bg'] ?>; 
            color: <?= $colors['text'] ?>; 
            border: 1px solid <?= $colors['border'] ?>;
            text-transform: uppercase;
        ">
            <?= str_replace('_', ' ', $lancio->status) ?>
        </span>
    </td>

    <!-- Articoli -->
    <td style="padding: 12px; vertical-align: middle;">
        <div style="font-size: 12px; color: #2c3e50;">
            <strong><?= $lancio->totale_articoli ?></strong> articoli
        </div>
        <div style="font-size: 11px; color: #7f8c8d;">
            <strong><?= number_format($lancio->totale_paia) ?></strong> paia totali
        </div>

        <?php if (!empty($lancio->articoli) && count($lancio->articoli) > 0): ?>
            <div style="margin-top: 4px; font-size: 10px; color: #7f8c8d;">
                <?php foreach ($lancio->articoli->take(2) as $i => $articolo): ?>
                    <?php if ($i > 0)
                        echo '<br>'; ?>
                    <span style="background: #ecf0f1; padding: 2px 4px; border-radius: 3px; margin-right: 2px;">
                        <?= htmlspecialchars(substr($articolo->article_name, 0, 25)) ?>
                        <?php if (strlen($articolo->article_name) > 25)
                            echo '...'; ?>
                        (<?= number_format($articolo->total_pairs) ?>)
                    </span>
                <?php endforeach; ?>
                <?php if (count($lancio->articoli) > 2): ?>
                    <br><span style="color: #95a5a6; font-style: italic;">
                        ... e altri <?= count($lancio->articoli) - 2 ?> articoli
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </td>

    <!-- Fasi -->
    <td style="padding: 12px; vertical-align: middle;">
        <div style="font-size: 12px; color: #2c3e50; margin-bottom: 2px;">
            <strong><?= $totaleFasi ?></strong> fasi
        </div>
        <?php if ($totaleFasi > 0): ?>
            <div style="font-size: 10px; color: #7f8c8d;">
                <?php foreach (array_slice($fasi, 0, 3) as $i => $fase): ?>
                    <?php if ($i > 0)
                        echo ' • '; ?>
                    <span><?= htmlspecialchars(trim($fase)) ?></span>
                <?php endforeach; ?>
                <?php if ($totaleFasi > 3): ?>
                    <br><span style="color: #95a5a6;">... +<?= $totaleFasi - 3 ?> altre</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </td>

    <!-- Avanzamento -->
    <td style="padding: 12px; vertical-align: middle;">
        <div style="margin-bottom: 4px;">
            <div style="font-size: 12px; font-weight: bold; color: #2c3e50;">
                <?= $percentuale_completamento ?>%
            </div>
        </div>
        <div style="width: 100%; height: 6px; background-color: #ecf0f1; border-radius: 3px; overflow: hidden;">
            <div style="
                height: 100%; 
                background: linear-gradient(90deg, #3498db 0%, #2980b9 100%); 
                width: <?= $percentuale_completamento ?>%; 
                transition: width 0.3s ease;
            "></div>
        </div>
        <div style="font-size: 10px; color: #7f8c8d; margin-top: 2px;">
            Completamento totale
        </div>
    </td>

    <!-- Note -->
    <td style="padding: 12px; vertical-align: middle; text-align: center;">
        <?php if ($lancio->numero_note > 0): ?>
            <span style="
                background-color: #fff3cd; 
                color: #856404; 
                padding: 2px 6px; 
                border-radius: 10px; 
                font-size: 10px; 
                font-weight: bold;
                border: 1px solid #ffeaa7;
            ">
                <?= $lancio->numero_note ?>
            </span>
        <?php else: ?>
            <span style="color: #bdc3c7; font-size: 10px;">-</span>
        <?php endif; ?>
    </td>

    <!-- Azioni -->
    <td style="padding: 12px; vertical-align: middle; text-align: center;">
        <div style="display: flex; gap: 4px; justify-content: center;">
            <a href="<?= $this->url('/scm/lavora/' . $lancio->id) ?>" style="
                   display: inline-flex; 
                   align-items: center; 
                   padding: 6px 10px; 
                   background: #3498db; 
                   color: white; 
                   text-decoration: none; 
                   border-radius: 4px; 
                   font-size: 11px; 
                   font-weight: bold;
                   transition: background-color 0.2s;
               " onmouseover="this.style.backgroundColor='#2980b9'" onmouseout="this.style.backgroundColor='#3498db'">
                <i class="fas fa-eye" style="margin-right: 4px; font-size: 10px;"></i>
                Dettagli
            </a>

            <?php if (in_array($lancio->status, ['LANCIATO', 'IN_LAVORAZIONE'])): ?>
                <a href="<?= $this->url('/scm/lavora/' . $lancio->id) ?>" style="
                       display: inline-flex; 
                       align-items: center; 
                       padding: 6px 10px; 
                       background: #27ae60; 
                       color: white; 
                       text-decoration: none; 
                       border-radius: 4px; 
                       font-size: 11px; 
                       font-weight: bold;
                       transition: background-color 0.2s;
                   " onmouseover="this.style.backgroundColor='#219a52'"
                    onmouseout="this.style.backgroundColor='#27ae60'">
                    <i class="fas fa-cogs" style="margin-right: 4px; font-size: 10px;"></i>
                    Lavora
                </a>
            <?php endif; ?>
        </div>
    </td>
</tr>