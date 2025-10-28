<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .fase-cell {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 130px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .fase-cell:hover {
            transform: scale(1.05) translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            z-index: 10;
        }

        .fase-cell::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .fase-cell:hover::before {
            opacity: 1;
        }

        .stato-NON_INIZIATA {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #64748b;
            border: 2px solid #cbd5e1;
            box-shadow: 0 4px 6px rgba(148, 163, 184, 0.1);
        }

        .stato-IN_CORSO {
            background: linear-gradient(135deg, #fef7cd 0%, #fde047 100%);
            color: #ca8a04;
            border: 2px solid #eab308;
            box-shadow: 0 4px 6px rgba(234, 179, 8, 0.2);
        }

        .stato-COMPLETATA {
            background: linear-gradient(135deg, #dcfce7 0%, #16a34a 100%);
            color: white;
            border: 2px solid #16a34a;
            box-shadow: 0 4px 6px rgba(22, 163, 74, 0.3);
        }

        .stato-BLOCCATA {
            background: linear-gradient(135deg, #fee2e2 0%, #ef4444 100%);
            color: white;
            border: 2px solid #ef4444;
            box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3);
        }

        .lancio-completato {
            opacity: 0.6;
            cursor: not-allowed !important;
            filter: grayscale(0.3);
        }

        .lancio-completato:hover {
            transform: none !important;
            box-shadow: none !important;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen">
    <!-- Header - Stile WEBGRE Moderno -->
    <nav class="bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 shadow-2xl">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center">
                    <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm mr-4">
                        <i class="fas fa-industry text-white text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-white text-xl font-bold">SCM Terzisti</div>
                        <div class="text-blue-200 text-sm">Lavorazione Lancio</div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="<?= $this->url('/scm/dashboard') ?>"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-medium rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Dashboard
                    </a>
                    <a href="<?= $this->url('/scm/logout') ?>"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-sm font-medium rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Esci
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- Lancio Header - Stile WEBGRE Moderno -->
        <div
            class="relative overflow-hidden bg-gradient-to-br from-white via-blue-50 to-indigo-100 rounded-2xl shadow-2xl border border-white/50 p-8 mb-8">
            <div class="absolute inset-0 bg-gradient-to-br from-white/40 to-transparent"></div>
            <div class="relative">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="p-4 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg mr-4">
                                <i class="fas fa-rocket text-white text-2xl"></i>
                            </div>
                            <div>
                                <h1
                                    class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-blue-900 bg-clip-text text-transparent mb-1">
                                    <?= htmlspecialchars($lancio->launch_number) ?>
                                </h1>
                                <div class="text-gray-600 text-sm font-medium">Lancio di Produzione</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-6 text-sm">
                            <div class="flex items-center text-gray-600">
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <i class="fas fa-calendar text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">
                                        <?= date('d/m/Y', strtotime($lancio->launch_date)) ?></div>
                                    <div class="text-xs text-gray-500">Data Lancio</div>
                                </div>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                                    <i class="fas fa-box text-purple-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900"><?= count($articoli) ?></div>
                                    <div class="text-xs text-gray-500">Articoli</div>
                                </div>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <div class="p-2 bg-green-100 rounded-lg mr-3">
                                    <i class="fas fa-list text-green-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900"><?= count($fasi_ciclo) ?></div>
                                    <div class="text-xs text-gray-500">Fasi</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    $stato_styles = [
                        'IN_PREPARAZIONE' => ['bg' => 'from-amber-400 to-yellow-500', 'icon' => 'fa-clock'],
                        'LANCIATO' => ['bg' => 'from-blue-400 to-cyan-500', 'icon' => 'fa-rocket'],
                        'IN_LAVORAZIONE' => ['bg' => 'from-orange-400 to-red-500', 'icon' => 'fa-cogs'],
                        'COMPLETO' => ['bg' => 'from-emerald-400 to-green-500', 'icon' => 'fa-check-circle'],
                        'SOSPESO' => ['bg' => 'from-red-400 to-rose-500', 'icon' => 'fa-pause-circle']
                    ];
                    $stato_style = $stato_styles[$lancio->status] ?? $stato_styles['LANCIATO'];
                    ?>

                    <div class="text-center">
                        <div
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r <?= $stato_style['bg'] ?> text-white text-sm font-bold rounded-2xl shadow-xl">
                            <i class="fas <?= $stato_style['icon'] ?> mr-2 text-lg"></i>
                            <?= str_replace('_', ' ', $lancio->status) ?>
                        </div>
                    </div>
                </div>

                <!-- Fasi del Ciclo -->
                <?php if (!empty($fasi_ciclo)): ?>
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-route mr-2 text-blue-600"></i>
                            Ciclo di Lavorazione
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <?php foreach ($fasi_ciclo as $i => $fase): ?>
                                <div class="relative">
                                    <span
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-medium rounded-xl shadow-lg">
                                        <span
                                            class="w-6 h-6 bg-white/20 rounded-full flex items-center justify-center text-xs font-bold mr-2">
                                            <?= $i + 1 ?>
                                        </span>
                                        <?= htmlspecialchars($fase) ?>
                                    </span>
                                    <?php if ($i < count($fasi_ciclo) - 1): ?>
                                        <div class="absolute top-1/2 -right-2 transform -translate-y-1/2">
                                            <i class="fas fa-chevron-right text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Avviso Lancio Completato -->
        <?php if ($lancio->status === 'COMPLETO'): ?>
            <div
                class="relative overflow-hidden bg-gradient-to-br from-green-50 to-emerald-100 rounded-2xl shadow-lg border border-green-200 mb-8">
                <div class="absolute inset-0 bg-gradient-to-br from-green-100/30 to-transparent"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-500 rounded-xl shadow-lg mr-4">
                            <i class="fas fa-check-circle text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-green-800 mb-2">Lancio Completato</h3>
                            <p class="text-green-700 text-sm">
                                Tutte le fasi di lavorazione sono state completate. Le modifiche alle fasi non sono più
                                consentite.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Avviso Articoli Bloccati -->
        <?php if (!empty($articoli_bloccati) && $lancio->status !== 'COMPLETO'): ?>
            <div
                class="relative overflow-hidden bg-gradient-to-br from-amber-50 to-yellow-100 rounded-2xl shadow-lg border border-amber-200 mb-8">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-100/30 to-transparent"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-amber-500 rounded-xl shadow-lg mr-4">
                            <i class="fas fa-lock text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-amber-800 mb-2">Articoli Completati</h3>
                            <p class="text-amber-700 text-sm">
                                Alcuni articoli hanno completato l'ultima fase e sono bloccati. Le loro righe nella matrice
                                non sono più modificabili.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Matrice Articoli × Fasi -->
        <div
            class="relative overflow-hidden bg-gradient-to-br from-white to-slate-50 rounded-2xl shadow-2xl border border-white/50 mb-8">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 to-transparent"></div>
            <div class="relative bg-gradient-to-r from-slate-800 via-blue-900 to-indigo-900 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm mr-4">
                            <i class="fas fa-table text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-1">Matrice Lavorazione</h2>
                            <p class="text-blue-200 text-sm">Clicca su ogni cella per aggiornare lo stato della fase</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-white/90 text-sm font-medium">Articoli × Fasi</div>
                        <div class="text-blue-200 text-xs"><?= count($articoli) ?> × <?= count($fasi_ciclo) ?> =
                            <?= count($articoli) * count($fasi_ciclo) ?> celle</div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                                Articolo
                            </th>
                            <?php foreach ($fasi_ciclo as $fase): ?>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r">
                                    <?= htmlspecialchars($fase) ?>
                                </th>
                            <?php endforeach; ?>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Progresso
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($articoli as $articolo): ?>
                            <?php $isArticoloBloccato = isset($articoli_bloccati[$articolo->id]); ?>
                            <tr class="<?= $isArticoloBloccato ? 'bg-green-50 hover:bg-green-100' : 'hover:bg-gray-50' ?>">
                                <?php if ($isArticoloBloccato): ?><!-- Articolo Bloccato: Completato --><?php endif; ?>
                                <!-- Colonna Articolo -->
                                <td class="px-4 py-4 border-r <?= $isArticoloBloccato ? 'bg-green-100' : 'bg-gray-50' ?>">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 flex items-center">
                                            <?= htmlspecialchars($articolo->article_name) ?>
                                            <?php if ($isArticoloBloccato): ?>
                                                <span
                                                    class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-lock mr-1"></i>
                                                    Completato
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-gray-500">
                                            <?= number_format($articolo->total_pairs) ?> paia
                                        </div>
                                    </div>
                                </td>

                                <!-- Colonne Fasi -->
                                <?php foreach ($fasi_ciclo as $fase): ?>
                                    <?php
                                    $stato = $matriceStati[$fase][$articolo->id] ?? null;
                                    $statoFase = ($stato && isset($stato['stato_fase'])) ? $stato['stato_fase'] : 'NON_INIZIATA';
                                    $notesFase = ($stato && isset($stato['note_fase'])) ? $stato['note_fase'] : '';
                                    $iconeFase = [
                                        'NON_INIZIATA' => 'fa-circle',
                                        'IN_CORSO' => 'fa-play-circle',
                                        'COMPLETATA' => 'fa-check-circle',
                                        'BLOCCATA' => 'fa-times-circle'
                                    ];
                                    ?>
                                    <td class="px-2 py-4 text-center border-r">
                                        <div class="fase-cell stato-<?= $statoFase ?> rounded-lg py-3 px-3 mx-1 <?= $lancio->status === 'COMPLETO' || $isArticoloBloccato ? 'lancio-completato' : '' ?>"
                                            <?= $lancio->status === 'COMPLETO' || $isArticoloBloccato ? '' : "onclick=\"apriModal('" . htmlspecialchars($fase) . "', " . $articolo->id . ", '" . htmlspecialchars($articolo->article_name) . "', '" . $statoFase . "', '" . htmlspecialchars($notesFase ?? '') . "')\"" ?>>
                                            <div class="text-xs font-semibold mb-1">
                                                <i class="fas <?= $iconeFase[$statoFase] ?> mr-1"></i>
                                                <?= str_replace('_', ' ', $statoFase ?? 'NON_INIZIATA') ?>
                                            </div>
                                            <?php if ($stato && isset($stato['data_completamento']) && $stato['data_completamento']): ?>
                                                <div class="text-xs opacity-75">
                                                    <?= date('d/m', strtotime($stato['data_completamento'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                <?php endforeach; ?>

                                <!-- Colonna Progresso -->
                                <td class="px-4 py-4 text-center">
                                    <div class="flex items-center justify-center">
                                        <div class="w-16 text-sm font-medium text-gray-900">
                                            <?= $articolo->percentuale ?>%
                                        </div>
                                        <div class="w-20 bg-gray-200 rounded-full h-2 ml-2">
                                            <div class="bg-blue-600 h-2 rounded-full"
                                                style="width: <?= $articolo->percentuale ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Note del Lancio -->
        <?php if (!empty($note)): ?>
            <div
                class="relative overflow-hidden bg-gradient-to-br from-white to-yellow-50 rounded-2xl shadow-2xl border border-white/50">
                <div class="absolute inset-0 bg-gradient-to-br from-yellow-50/30 to-transparent"></div>
                <div class="relative bg-gradient-to-r from-amber-500 via-yellow-500 to-orange-500 px-8 py-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm mr-4">
                            <i class="fas fa-comments text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-1">Note del Lancio</h3>
                            <p class="text-yellow-100 text-sm">Comunicazioni e annotazioni</p>
                        </div>
                    </div>
                </div>
                <div class="relative p-8 max-h-80 overflow-y-auto">
                    <div class="space-y-4">
                        <?php foreach ($note as $nota): ?>
                            <div
                                class="bg-gradient-to-r from-white to-yellow-50 rounded-xl p-6 border border-yellow-200 shadow-lg hover:shadow-xl transition-all duration-200">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-900">
                                                <?= htmlspecialchars($nota['mittente']) ?>
                                            </span>
                                            <div class="text-xs text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>
                                                <?= $nota['data_nota'] ? date('d/m/Y H:i', strtotime($nota['data_nota'])) : 'N/A' ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-white/50 rounded-lg p-4 border border-yellow-100">
                                    <p class="text-gray-800 leading-relaxed">
                                        <?= nl2br(htmlspecialchars($nota['nota'])) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Aggiornamento Fase -->
    <div id="modalAggiornamento"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div
            class="relative overflow-hidden bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform scale-95 opacity-0 transition-all duration-300">
            <div class="absolute inset-0 bg-gradient-to-br from-white/40 to-transparent"></div>
            <div class="relative bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm mr-3">
                            <i class="fas fa-edit text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white">
                            Aggiorna Lavorazione
                        </h3>
                    </div>
                    <button onclick="chiudiModal()" class="text-white/80 hover:text-white transition-colors p-2">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <form id="formAggiornamento" class="relative p-8">
                <input type="hidden" id="nomeFase" name="nome_fase">
                <input type="hidden" id="articoloId" name="articolo_id">

                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <span class="text-sm font-semibold text-blue-900">Dettagli Lavorazione</span>
                    </div>
                    <div class="text-sm space-y-1">
                        <div><strong class="text-gray-700">Fase:</strong> <span id="infoFase"
                                class="text-blue-800 font-medium"></span></div>
                        <div><strong class="text-gray-700">Articolo:</strong> <span id="infoArticolo"
                                class="text-blue-800 font-medium"></span></div>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="nuovoStato" class="block text-sm font-semibold text-gray-800 mb-3">
                        <i class="fas fa-cogs mr-1 text-blue-600"></i>
                        Stato Lavorazione
                    </label>
                    <select id="nuovoStato" name="nuovo_stato" required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm">
                        <option value="NON_INIZIATA">🔵 Non Iniziata</option>
                        <option value="IN_CORSO">🟡 In Corso</option>
                        <option value="COMPLETATA">🟢 Completata</option>
                        <option value="BLOCCATA">🔴 Bloccata</option>
                    </select>
                </div>

                <div class="mb-8">
                    <label for="noteAggiornamento" class="block text-sm font-semibold text-gray-800 mb-3">
                        <i class="fas fa-sticky-note mr-1 text-yellow-600"></i>
                        Note (opzionale)
                    </label>
                    <textarea id="noteAggiornamento" name="note" rows="3"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm resize-none"
                        placeholder="Aggiungi note sulla lavorazione..."></textarea>
                </div>

                <div class="flex space-x-4">
                    <button type="button" onclick="chiudiModal()"
                        class="flex-1 bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-times mr-2"></i>
                        Annulla
                    </button>
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        Salva
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Conferma Sequenza Fasi -->
    <div id="modalSequenza" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div
            class="relative overflow-hidden bg-gradient-to-br from-white to-amber-50 rounded-2xl shadow-2xl max-w-lg w-full mx-4 transform scale-95 opacity-0 transition-all duration-300">
            <div class="absolute inset-0 bg-gradient-to-br from-white/40 to-transparent"></div>
            <div class="relative bg-gradient-to-r from-amber-500 via-yellow-500 to-orange-500 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm mr-3">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white">
                            Completamento Automatico Fasi
                        </h3>
                    </div>
                    <button onclick="chiudiModalSequenza()"
                        class="text-white/80 hover:text-white transition-colors p-2">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="relative p-8">
                <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-xl p-5 mb-6">
                    <div class="flex items-start">
                        <div class="p-2 bg-amber-100 rounded-lg mr-4">
                            <i class="fas fa-info-circle text-amber-600 text-lg"></i>
                        </div>
                        <div class="text-sm">
                            <p class="font-bold text-amber-800 mb-3">⚠️ Attenzione:</p>
                            <p class="text-amber-700 leading-relaxed">
                                Per avanzare alla fase <strong id="faseTarget" class="text-amber-900"></strong> è
                                necessario completare automaticamente
                                tutte le fasi precedenti per l'articolo <strong id="articoloTarget"
                                    class="text-amber-900"></strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-gray-50 to-slate-50 border border-gray-200 rounded-xl p-5 mb-6">
                    <h4 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-list mr-2 text-blue-600"></i>
                        Fasi che verranno completate automaticamente:
                    </h4>
                    <div id="fasiPrecedenti" class="space-y-2"></div>
                </div>

                <div class="flex space-x-4">
                    <button type="button" onclick="chiudiModalSequenza()"
                        class="flex-1 bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-times mr-2"></i>
                        Annulla
                    </button>
                    <button type="button" onclick="confermaSequenza()"
                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-check mr-2"></i>
                        Procedi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Array delle fasi in ordine sequenziale
        const fasiCiclo = <?= json_encode($fasi_ciclo) ?>;

        // Dati della matrice stati per controlli JavaScript
        const matriceStati = <?= json_encode($matriceStati) ?>;

        // Variabili globali per il modal sequenza
        let datiAggiornamentoSequenza = null;

        function apriModal(fase, articoloId, articoloCodice, statoAttuale, noteAttuali) {
            // Mostra sempre il modal normale - il controllo sequenza avverrà quando l'utente cambia lo stato
            document.getElementById('nomeFase').value = fase;
            document.getElementById('articoloId').value = articoloId;
            document.getElementById('infoFase').textContent = fase;
            document.getElementById('infoArticolo').textContent = articoloCodice;
            document.getElementById('nuovoStato').value = statoAttuale;
            document.getElementById('noteAggiornamento').value = noteAttuali;

            const modal = document.getElementById('modalAggiornamento');
            const content = modal.querySelector('.transform');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.add('scale-100', 'opacity-100');
                content.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function mostraModalSequenza(fase, articoloId, articoloCodice, fasiDaCompletare, nuovoStato, noteAttuali) {
            // Salva i dati per l'aggiornamento
            datiAggiornamentoSequenza = {
                nome_fase: fase,
                articolo_id: articoloId,
                nuovo_stato: nuovoStato,
                note: noteAttuali
            };

            // Popola il modal
            document.getElementById('faseTarget').textContent = fase;
            document.getElementById('articoloTarget').textContent = articoloCodice;

            const containerFasi = document.getElementById('fasiPrecedenti');
            containerFasi.innerHTML = '';

            fasiDaCompletare.forEach(fasePrecedente => {
                const div = document.createElement('div');
                div.className = 'text-xs text-gray-600 flex items-center';
                div.innerHTML = `<i class="fas fa-arrow-right mr-2 text-green-500"></i> ${fasePrecedente}`;
                containerFasi.appendChild(div);
            });

            const modal = document.getElementById('modalSequenza');
            const content = modal.querySelector('.transform');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.add('scale-100', 'opacity-100');
                content.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function chiudiModal() {
            const modal = document.getElementById('modalAggiornamento');
            const content = modal.querySelector('.transform');
            content.classList.add('scale-95', 'opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        function chiudiModalSequenza() {
            const modal = document.getElementById('modalSequenza');
            const content = modal.querySelector('.transform');
            content.classList.add('scale-95', 'opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                datiAggiornamentoSequenza = null;
            }, 300);
        }

        async function confermaSequenza() {
            if (!datiAggiornamentoSequenza) return;

            // Debug logging
            console.log('=== DEBUG confermaSequenza ===');
            console.log('URL:', '<?= $this->url("/scm/update-progress-sequence/" . $lancio["id"]) ?>');
            console.log('Data da inviare:', datiAggiornamentoSequenza);
            console.log('URLSearchParams:', new URLSearchParams(datiAggiornamentoSequenza).toString());

            try {
                const response = await fetch('<?= $this->url("/scm/update-progress-sequence/" . $lancio["id"]) ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(datiAggiornamentoSequenza)
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                const responseText = await response.text();
                console.log('Response text (raw):', responseText);

                let result;
                try {
                    result = JSON.parse(responseText);
                    console.log('Parsed JSON:', result);
                } catch (jsonError) {
                    console.error('JSON parse error:', jsonError);
                    console.error('Response was:', responseText);
                    alert('Errore JSON: ' + jsonError.message + '\n\nRisposta server:\n' + responseText.substring(0, 500));
                    return;
                }

                if (result.success) {
                    alert('Fasi aggiornate con successo!');
                    window.location.reload();
                } else {
                    alert('Errore: ' + result.error);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                alert('Errore di connessione: ' + error.message);
            }

            chiudiModalSequenza();
        }

        document.getElementById('formAggiornamento').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch('<?= $this->url("/scm/update-progress/" . $lancio["id"]) ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(data)
                });

                const result = await response.json();

                if (result.success) {
                    alert('Stato aggiornato con successo!');
                    window.location.reload();
                } else {
                    alert('Errore: ' + result.error);
                }
            } catch (error) {
                alert('Errore di connessione: ' + error.message);
            }
        });

        // Intercepta il cambio dello select per controllare la sequenza
        document.getElementById('nuovoStato').addEventListener('change', function () {
            const nuovoStato = this.value;
            const fase = document.getElementById('nomeFase').value;
            const articoloId = parseInt(document.getElementById('articoloId').value);

            if ((nuovoStato === 'IN_CORSO' || nuovoStato === 'COMPLETATA') && fase && articoloId) {
                const indiceFaseCorrente = fasiCiclo.indexOf(fase);
                const fasiDaCompletare = [];

                for (let i = 0; i < indiceFaseCorrente; i++) {
                    const fasePrecedente = fasiCiclo[i];
                    const statoFasePrecedente = matriceStati[fasePrecedente] && matriceStati[fasePrecedente][articoloId]
                        ? matriceStati[fasePrecedente][articoloId].stato_fase
                        : 'NON_INIZIATA';

                    if (statoFasePrecedente !== 'COMPLETATA') {
                        fasiDaCompletare.push(fasePrecedente);
                    }
                }

                if (fasiDaCompletare.length > 0) {
                    const infoArticolo = document.getElementById('infoArticolo');
                    const noteAttuali = document.getElementById('noteAggiornamento').value;

                    chiudiModal();
                    setTimeout(() => {
                        mostraModalSequenza(fase, articoloId, infoArticolo.textContent, fasiDaCompletare, nuovoStato, noteAttuali);
                    }, 350); // Aspetta che il primo modal si chiuda
                }
            }
        });

        // Chiudi modal cliccando fuori
        document.getElementById('modalAggiornamento').addEventListener('click', function (e) {
            if (e.target === this) {
                chiudiModal();
            }
        });

        document.getElementById('modalSequenza').addEventListener('click', function (e) {
            if (e.target === this) {
                chiudiModalSequenza();
            }
        });
    </script>
</body>

</html>