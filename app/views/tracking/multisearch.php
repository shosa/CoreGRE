<?php
/**
 * Tracking - Associazione per Ricerca Multipla
 * Replica esatta del sistema legacy multiSearch.php
 */
?>

<!-- Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div class="mr-4 bg-gradient-to-r from-green-500 to-green-600 text-white p-3 rounded-2xl shadow-lg">
                    <i class="fas fa-search-plus fa-2x"></i>
                </div>
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Associa per Ricerca
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Utilizza i filtri di ricerca per selezionare automaticamente i cartellini dalla tabella dati
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?= $this->url('/tracking') ?>"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                Torna alla Home
            </a>
        </div>
    </div>
</div>

<!-- Breadcrumb Style Info -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500 dark:text-gray-400">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/tracking') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-home mr-2"></i>
                Tracking
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Associa per Ricerca</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Search Form Card -->
<div
    class="rounded-2xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-800/40 backdrop-blur-sm mb-6">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-filter mr-3 text-green-500"></i>
            Ricerca Cartellini
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Cartellino -->
            <div>
                <label for="cartel-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Cartellino
                </label>
                <input type="text" id="cartel-filter" placeholder="Cartellino (Inizia per..)"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            </div>

            <!-- Commessa Cliente -->
            <div>
                <label for="commessa-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Commessa Cliente
                </label>
                <input type="text" id="commessa-filter" placeholder="Commessa Cliente (Inizia per..)"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            </div>

            <!-- Codice Articolo -->
            <div>
                <label for="articolo-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Codice Articolo
                </label>
                <input type="text" id="articolo-filter" placeholder="Codice Articolo"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            </div>

            <!-- Descrizione Articolo -->
            <div>
                <label for="descrizione-articolo-filter"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Descrizione Articolo
                </label>
                <input type="text" id="descrizione-articolo-filter" placeholder="Descrizione Articolo"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            </div>

            <!-- Linea -->
            <div>
                <label for="ln-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Linea
                </label>
                <input type="text" id="ln-filter" placeholder="Linea"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            </div>

            <!-- Ragione Sociale -->
            <div class="md:col-span-2">
                <label for="ragione-sociale-filter"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Ragione Sociale
                </label>
                <input type="text" id="ragione-sociale-filter" placeholder="Ragione Sociale"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            </div>

            <!-- Ordine -->
            <div>
                <label for="ordine-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Ordine
                </label>
                <input type="text" id="ordine-filter" placeholder="Ordine"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <button onclick="searchCommesse()"
                class="flex-1 inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-green-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-green-600 hover:to-green-700 transition-all duration-200">
                <i class="fas fa-search mr-2"></i>
                Cerca
            </button>
            <button id="proceed-btn" onclick="processSelected()" disabled
                class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-3 text-sm font-medium text-white shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed">
                <i class="fas fa-check mr-2"></i>
                PROCEDI
            </button>
        </div>
    </div>
</div>

<!-- Results Container -->
<div id="results-container" class="space-y-4">
    <!-- Risultati della ricerca verranno inseriti qui -->
</div>

<!-- Form nascosto per processare i dati selezionati -->
<form id="selected-form" action="<?= $this->url('/tracking/process-links') ?>" method="POST" style="display: none;">
    <input type="hidden" name="selectedCartels" id="selectedCartels">
    <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
</form>

<script>
    var selectedCartels = [];
    var selectedTot = 0;

    // Crea il badge floating e lo appende al body per evitare problemi di overflow
    (function() {
        var badge = document.createElement('div');
        badge.id = 'selection-info';
        badge.className = 'fixed bottom-8 right-8 bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-2xl shadow-2xl hidden border-2 border-blue-400';
        badge.style.zIndex = '99999';
        badge.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas fa-shopping-cart text-2xl"></i>
                <div>
                    <div class="text-2xl font-bold" id="selection-count">0 Cartellini</div>
                    <div class="text-xs text-blue-200 mt-1" id="selection-pairs">0 Paia totali</div>
                </div>
            </div>
        `;
        document.body.appendChild(badge);
    })();

    function searchCommesse() {
        // Reset delle selezioni precedenti
        selectedCartels = [];
        selectedTot = 0;
        updateSelectionInfo();

        var cartel = document.getElementById('cartel-filter').value;
        var commessa = document.getElementById('commessa-filter').value;
        var articolo = document.getElementById('articolo-filter').value;
        var descrizioneArticolo = document.getElementById('descrizione-articolo-filter').value;
        var ln = document.getElementById('ln-filter').value;
        var ragioneSociale = document.getElementById('ragione-sociale-filter').value;
        var ordine = document.getElementById('ordine-filter').value;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= $this->url("/tracking/search-data") ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response;
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (e) {
                        console.error('Errore di parsing JSON:', e);
                        response = [];
                    }

                    if (!Array.isArray(response)) {
                        console.error('La risposta non è un array:', response);
                        response = [];
                    }

                    var resultsContainer = document.getElementById('results-container');
                    resultsContainer.innerHTML = '<div class="mb-4"><h5 class="text-lg font-semibold text-gray-900 dark:text-white">Risultati:</h5></div>';

                    var groupedResults = groupResultsByDescrizioneArticolo(response);

                    for (var descrizioneArticolo in groupedResults) {
                        var group = groupedResults[descrizioneArticolo];
                        var groupElement = createGroupElement(descrizioneArticolo, group);
                        resultsContainer.appendChild(groupElement);
                    }
                    console.log('Dati ricevuti dal server:', response);
                } else {
                    console.error('Errore nella ricerca delle commesse');
                }
            }
        };
        xhr.send('cartel=' + encodeURIComponent(cartel) +
            '&commessa=' + encodeURIComponent(commessa) +
            '&articolo=' + encodeURIComponent(articolo) +
            '&descrizioneArticolo=' + encodeURIComponent(descrizioneArticolo) +
            '&ln=' + encodeURIComponent(ln) +
            '&ragioneSociale=' + encodeURIComponent(ragioneSociale) +
            '&ordine=' + encodeURIComponent(ordine));
    }

    function groupResultsByDescrizioneArticolo(results) {
        var groupedResults = {};
        results.forEach(function (row) {
            var descrizioneArticolo = row['Descrizione Articolo'];
            if (!groupedResults[descrizioneArticolo]) {
                groupedResults[descrizioneArticolo] = [];
            }
            groupedResults[descrizioneArticolo].push(row);
        });
        return groupedResults;
    }

    function createGroupElement(descrizioneArticolo, group) {
        var groupDiv = document.createElement('div');
        groupDiv.className = 'group mb-4';

        // Calcola il totale paia per questo gruppo
        var totalPairs = group.reduce(function(total, row) {
            return total + parseInt(row.Tot || 0);
        }, 0);

        var groupHeader = document.createElement('div');
        groupHeader.className = 'group-header bg-gray-100 dark:bg-gray-700 p-4 rounded-lg cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors';
        groupHeader.setAttribute('data-expanded', 'false');
        groupHeader.innerHTML = '<div class="flex items-center justify-between"><span class="toggle text-blue-600 dark:text-blue-400 font-bold text-lg mr-3">+</span><span class="group-name font-semibold text-gray-900 dark:text-white">' + descrizioneArticolo + ' <span class="text-sm text-gray-600 dark:text-gray-400">(<u>' + group[0].Articolo + '</u>) (' + group.length + ' Voci) - <span class="font-bold text-green-600 dark:text-green-400">' + totalPairs + ' Paia</span></span></span></div>';

        groupHeader.onclick = function () {
            toggleGroup(groupBody, groupHeader);
        };
        groupDiv.appendChild(groupHeader);

        var groupBody = document.createElement('div');
        groupBody.className = 'group-body bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-b-lg p-4';
        groupBody.style.display = 'none';

        // Aggiungi il link "Seleziona Tutto" all'inizio del gruppo
        var selectAllDiv = document.createElement('div');
        selectAllDiv.className = 'mb-3 pb-3 border-b border-gray-200 dark:border-gray-600';
        var selectAllLink = document.createElement('a');
        selectAllLink.href = 'javascript:void(0);';
        selectAllLink.className = 'text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium';
        selectAllLink.textContent = 'Seleziona Tutto';
        selectAllLink.onclick = function (e) {
            e.stopPropagation();
            selectAllInGroup(groupBody);
        };
        selectAllDiv.appendChild(selectAllLink);
        groupBody.appendChild(selectAllDiv);

        group.forEach(function (row) {
            var item = document.createElement('div');
            item.className = 'item p-3 border border-gray-200 dark:border-gray-600 rounded-lg mb-2 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors';
            item.setAttribute('data-cartel', row.Cartel);
            item.innerHTML = '<div class="flex items-center space-x-3"><input type="checkbox" class="form-check-input w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" onchange="selectRow(this, \'' + row.Cartel + '\')">' +
                '<label class="form-check-label flex-1 text-gray-900 dark:text-white cursor-pointer"><strong>' + row.Cartel + '</strong> (' + (row['Commessa Cli'] ? row['Commessa Cli'] : '') + ') | PAIA <span class="font-bold text-green-600 dark:text-green-400">' + row.Tot + '</span></label></div>';
            item.onclick = function (e) {
                if (e.target.tagName.toLowerCase() !== 'input') {
                    var checkbox = this.querySelector('.form-check-input');
                    checkbox.checked = !checkbox.checked;
                    selectRow(checkbox, row.Cartel);
                }
            };
            groupBody.appendChild(item);
        });

        groupDiv.appendChild(groupBody);
        return groupDiv;
    }

    function toggleGroup(groupBody, groupHeader) {
        var expanded = groupHeader.getAttribute('data-expanded') === 'true';
        if (!expanded) {
            groupBody.style.display = 'block';
            groupHeader.querySelector('.toggle').textContent = '-';
            groupHeader.setAttribute('data-expanded', 'true');
        } else {
            groupBody.style.display = 'none';
            groupHeader.querySelector('.toggle').textContent = '+';
            groupHeader.setAttribute('data-expanded', 'false');
        }
    }

    function selectRow(checkbox, cartel) {
        var totMatch = checkbox.parentNode.parentNode.textContent.match(/PAIA (\d+)/);
        if (totMatch) {
            var tot = parseInt(totMatch[1]);
            if (checkbox.checked) {
                if (!selectedCartels.includes(cartel)) {
                    selectedCartels.push(cartel);
                    selectedTot += tot;
                }
            } else {
                selectedCartels = selectedCartels.filter(function (item) {
                    return item !== cartel;
                });
                selectedTot -= tot;
            }
            updateSelectionInfo();
        }
    }

    function selectAllInGroup(groupBody) {
        var checkboxes = groupBody.getElementsByClassName('form-check-input');
        for (var i = 0; i < checkboxes.length; i++) {
            if (!checkboxes[i].checked) {
                checkboxes[i].checked = true;
                var cartel = checkboxes[i].closest('.item').getAttribute('data-cartel');
                var totMatch = checkboxes[i].parentNode.parentNode.textContent.match(/PAIA (\d+)/);
                if (totMatch && !selectedCartels.includes(cartel)) {
                    selectedCartels.push(cartel);
                    selectedTot += parseInt(totMatch[1]);
                }
            }
        }
        updateSelectionInfo();
    }

    function updateSelectionInfo() {
        var selectionInfo = document.getElementById('selection-info');
        var selectionCount = document.getElementById('selection-count');
        var selectionPairs = document.getElementById('selection-pairs');
        var proceedBtn = document.getElementById('proceed-btn');

        // Aggiorna i contatori separati
        selectionCount.textContent = selectedCartels.length + ' Cartellini';
        selectionPairs.textContent = selectedTot + ' Paia totali';

        if (selectedCartels.length > 0) {
            selectionInfo.style.display = 'block';
        } else {
            selectionInfo.style.display = 'none';
        }

        proceedBtn.disabled = selectedCartels.length === 0;
    }

    // Reset completo quando la pagina viene caricata
    window.addEventListener('DOMContentLoaded', function() {
        selectedCartels = [];
        selectedTot = 0;
        updateSelectionInfo();
    });

    function processSelected() {
        var selectedCartelsInput = document.getElementById('selectedCartels');
        selectedCartelsInput.value = JSON.stringify(selectedCartels);
        document.getElementById('selected-form').submit();
    }

    <?= $pageScripts ?>
</script>