/**
 * Settings Page Tab System
 */
(function() {
    'use strict';

    // Tab switching function
    window.switchTab = function(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active state from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-600', 'text-blue-600', 'dark:border-blue-400', 'dark:text-blue-400');
            button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });

        // Show selected tab content
        const selectedContent = document.getElementById(`content-${tabName}`);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');
        }

        // Activate selected tab button
        const selectedTab = document.getElementById(`tab-${tabName}`);
        if (selectedTab) {
            selectedTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            selectedTab.classList.add('border-blue-600', 'text-blue-600', 'dark:border-blue-400', 'dark:text-blue-400');
        }

        // Store active tab in localStorage
        localStorage.setItem('activeSettingsTab', tabName);

        // Load content for specific tabs
        if (tabName === 'tabelle') {
            loadTabelleContent();
        } else if (tabName === 'email') {
            loadEmailSettings();
        }
    };

    // Initialize on page load
    function initSettingsPage() {
        // Always default to 'import' tab (Excel import is the heart of the app)
        switchTab('import');

        // Setup form handlers
        setupFormHandlers();
        setupMainXlsxUpload();
    }

    // Form handlers
    function setupFormHandlers() {
        // Sistema form
        const sistemaForm = document.getElementById('sistema-settings-form');
        if (sistemaForm) {
            sistemaForm.addEventListener('submit', function(e) {
                e.preventDefault();
                saveGeneralSettings(new FormData(this), 'sistema');
            });
        }

        // Notifiche form
        const notificheForm = document.getElementById('notifiche-settings-form');
        if (notificheForm) {
            notificheForm.addEventListener('submit', function(e) {
                e.preventDefault();
                saveGeneralSettings(new FormData(this), 'notifiche');
            });
        }

        // Email form
        const emailForm = document.getElementById('email-settings-form');
        if (emailForm) {
            emailForm.addEventListener('submit', function(e) {
                e.preventDefault();
                saveGeneralSettings(new FormData(this), 'email');
            });
        }
    }

    // Save general settings
    function saveGeneralSettings(formData, section) {
        const baseUrl = window.location.pathname.replace(/\/$/, '');

        fetch(`${baseUrl}/save`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addAlert(`Impostazioni ${section} salvate correttamente`, 'success');
            } else {
                addAlert(data.error || 'Errore nel salvataggio', 'error');
            }
        })
        .catch(error => {
            addAlert('Errore nella comunicazione', 'error');
            console.error('Error:', error);
        });
    }

    // Load email settings
    function loadEmailSettings() {
        const baseUrl = window.location.pathname.replace(/\/$/, '');

        fetch(`${baseUrl}/load-section`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'section=email'
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const smtpServer = doc.querySelector('input[name="production_senderSMTP"]');
            const smtpPort = doc.querySelector('input[name="production_senderPORT"]');
            const smtpEmail = doc.querySelector('input[name="production_senderEmail"]');
            const smtpPassword = doc.querySelector('input[name="production_senderPassword"]');
            const smtpRecipients = doc.querySelector('textarea[name="production_recipients"]');

            if (smtpServer) document.getElementById('smtp-server').value = smtpServer.value;
            if (smtpPort) document.getElementById('smtp-port').value = smtpPort.value;
            if (smtpEmail) document.getElementById('smtp-email').value = smtpEmail.value;
            if (smtpPassword) document.getElementById('smtp-password').value = smtpPassword.value;
            if (smtpRecipients) document.getElementById('smtp-recipients').value = smtpRecipients.value;
        })
        .catch(error => console.error('Error loading email settings:', error));
    }

    // Load tabelle content
    function loadTabelleContent() {
        const container = document.getElementById('tables-content');
        if (!container) return;

        const baseUrl = window.location.pathname.replace(/\/$/, '');

        fetch(`${baseUrl}/load-section`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'section=tables'
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Extract data
            const departments = extractTableData(doc, '#departments-tbody');
            const laboratories = extractTableData(doc, '#laboratories-tbody');
            const lines = extractLinesData(doc, '#lines-tbody');

            // Render
            container.innerHTML = `
                <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-xl border border-green-200 dark:border-green-800">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-green-900 dark:text-green-100">Reparti</h4>
                        <button onclick="addNewDepartment()" class="text-green-600 hover:text-green-700">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        ${renderTableItems(departments, 'department', 'green')}
                    </div>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-xl border border-purple-200 dark:border-purple-800">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-purple-900 dark:text-purple-100">Laboratori</h4>
                        <button onclick="addNewLaboratory()" class="text-purple-600 hover:text-purple-700">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        ${renderTableItems(laboratories, 'laboratory', 'purple')}
                    </div>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 p-6 rounded-xl border border-orange-200 dark:border-orange-800">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-orange-900 dark:text-orange-100">Linee Produzione</h4>
                        <button onclick="addNewLine()" class="text-orange-600 hover:text-orange-700">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        ${renderLineItems(lines, 'orange')}
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading tables:', error);
            container.innerHTML = '<div class="col-span-3 text-center text-red-500">Errore nel caricamento dei dati</div>';
        });
    }

    function extractTableData(doc, selector) {
        const tbody = doc.querySelector(selector);
        const items = [];
        if (tbody) {
            tbody.querySelectorAll('tr').forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 2) {
                    const input = cells[1].querySelector('input');
                    items.push({
                        id: cells[0].textContent.trim(),
                        nome: input ? input.value : cells[1].textContent.trim()
                    });
                }
            });
        }
        return items;
    }

    function extractLinesData(doc, selector) {
        const tbody = doc.querySelector(selector);
        const items = [];
        if (tbody) {
            tbody.querySelectorAll('tr').forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 3) {
                    const siglaInput = cells[1].querySelector('input');
                    const descInput = cells[2].querySelector('input');
                    items.push({
                        id: cells[0].textContent.trim(),
                        sigla: siglaInput ? siglaInput.value : '',
                        descrizione: descInput ? descInput.value : ''
                    });
                }
            });
        }
        return items;
    }

    function renderTableItems(items, type, color) {
        if (items.length === 0) {
            return `<div class="text-sm text-${color}-600 dark:text-${color}-400 text-center py-4">Nessun elemento</div>`;
        }
        return items.map(item => `
            <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-700 rounded-lg border border-${color}-200 dark:border-${color}-700">
                <span class="text-sm text-gray-900 dark:text-gray-100">${item.nome}</span>
                <div class="flex items-center space-x-2">
                    <button onclick="edit${capitalize(type)}(${item.id}, '${escapeHtml(item.nome)}')"
                        class="text-${color}-600 hover:text-${color}-700 text-xs">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="delete${capitalize(type)}(${item.id})" class="text-red-600 hover:text-red-700 text-xs">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    function renderLineItems(lines, color) {
        if (lines.length === 0) {
            return `<div class="text-sm text-${color}-600 dark:text-${color}-400 text-center py-4">Nessun elemento</div>`;
        }
        return lines.map(line => `
            <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-700 rounded-lg border border-${color}-200 dark:border-${color}-700">
                <div class="flex-1">
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">${line.sigla}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">${line.descrizione}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="editLine(${line.id}, '${escapeHtml(line.sigla)}', '${escapeHtml(line.descrizione)}')"
                        class="text-${color}-600 hover:text-${color}-700 text-xs">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteLine(${line.id})" class="text-red-600 hover:text-red-700 text-xs">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // CRUD functions for tables
    window.addNewDepartment = function() {
        CoregreModals.confirm({
            title: 'Nuovo Reparto',
            message: '<input type="text" id="new-dept-name" class="w-full px-4 py-2 border rounded-lg" placeholder="Nome reparto">',
            confirmText: 'Aggiungi',
            type: 'info',
            onConfirm: function() {
                const nome = document.getElementById('new-dept-name').value;
                if (nome) {
                    manageCrudOperation('department', 'create', { nome: nome });
                }
            }
        });
    };

    window.editDepartment = function(id, nome) {
        CoregreModals.confirm({
            title: 'Modifica Reparto',
            message: `<input type="text" id="edit-dept-name" value="${nome}" class="w-full px-4 py-2 border rounded-lg">`,
            confirmText: 'Salva',
            type: 'info',
            onConfirm: function() {
                const newNome = document.getElementById('edit-dept-name').value;
                if (newNome && newNome !== nome) {
                    manageCrudOperation('department', 'update', { id: id, nome: newNome });
                }
            }
        });
    };

    window.deleteDepartment = function(id) {
        CoregreModals.confirmDelete('Sei sicuro di voler eliminare questo reparto?', function() {
            manageCrudOperation('department', 'delete', { id: id });
        });
    };

    window.addNewLaboratory = function() {
        CoregreModals.confirm({
            title: 'Nuovo Laboratorio',
            message: '<input type="text" id="new-lab-name" class="w-full px-4 py-2 border rounded-lg" placeholder="Nome laboratorio">',
            confirmText: 'Aggiungi',
            type: 'info',
            onConfirm: function() {
                const nome = document.getElementById('new-lab-name').value;
                if (nome) {
                    manageCrudOperation('laboratory', 'create', { nome: nome });
                }
            }
        });
    };

    window.editLaboratory = function(id, nome) {
        CoregreModals.confirm({
            title: 'Modifica Laboratorio',
            message: `<input type="text" id="edit-lab-name" value="${nome}" class="w-full px-4 py-2 border rounded-lg">`,
            confirmText: 'Salva',
            type: 'info',
            onConfirm: function() {
                const newNome = document.getElementById('edit-lab-name').value;
                if (newNome && newNome !== nome) {
                    manageCrudOperation('laboratory', 'update', { id: id, nome: newNome });
                }
            }
        });
    };

    window.deleteLaboratory = function(id) {
        CoregreModals.confirmDelete('Sei sicuro di voler eliminare questo laboratorio?', function() {
            manageCrudOperation('laboratory', 'delete', { id: id });
        });
    };

    window.addNewLine = function() {
        CoregreModals.confirm({
            title: 'Nuova Linea Produzione',
            message: `<div class="space-y-3">
                <input type="text" id="new-line-sigla" maxlength="2" class="w-full px-4 py-2 border rounded-lg" placeholder="Sigla (2 caratteri)">
                <input type="text" id="new-line-descrizione" class="w-full px-4 py-2 border rounded-lg" placeholder="Descrizione">
            </div>`,
            confirmText: 'Aggiungi',
            type: 'info',
            onConfirm: function() {
                const sigla = document.getElementById('new-line-sigla').value;
                const descrizione = document.getElementById('new-line-descrizione').value;
                if (sigla && descrizione) {
                    manageCrudOperation('line', 'create', { sigla: sigla, descrizione: descrizione });
                }
            }
        });
    };

    window.editLine = function(id, sigla, descrizione) {
        CoregreModals.confirm({
            title: 'Modifica Linea Produzione',
            message: `<div class="space-y-3">
                <input type="text" id="edit-line-sigla" maxlength="2" value="${sigla}" class="w-full px-4 py-2 border rounded-lg" placeholder="Sigla (2 caratteri)">
                <input type="text" id="edit-line-descrizione" value="${descrizione}" class="w-full px-4 py-2 border rounded-lg" placeholder="Descrizione">
            </div>`,
            confirmText: 'Salva',
            type: 'info',
            onConfirm: function() {
                const newSigla = document.getElementById('edit-line-sigla').value;
                const newDescrizione = document.getElementById('edit-line-descrizione').value;
                if (newSigla && newDescrizione && (newSigla !== sigla || newDescrizione !== descrizione)) {
                    manageCrudOperation('line', 'update', { id: id, sigla: newSigla, descrizione: newDescrizione });
                }
            }
        });
    };

    window.deleteLine = function(id) {
        CoregreModals.confirmDelete('Sei sicuro di voler eliminare questa linea?', function() {
            manageCrudOperation('line', 'delete', { id: id });
        });
    };

    function manageCrudOperation(type, action, data) {
        const baseUrl = window.location.pathname.replace(/\/$/, '');
        const formData = new FormData();
        formData.append('action', action);

        for (const key in data) {
            formData.append(key, data[key]);
        }

        const endpoints = {
            'department': `${baseUrl}/manage-department`,
            'laboratory': `${baseUrl}/manage-laboratory`,
            'line': `${baseUrl}/manage-line`
        };

        fetch(endpoints[type], {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addAlert('Operazione completata correttamente', 'success');
                loadTabelleContent();
            } else {
                addAlert(data.error || 'Errore nell\'operazione', 'error');
            }
        })
        .catch(error => {
            addAlert('Errore nella comunicazione', 'error');
            console.error('Error:', error);
        });
    }

    // Clear file selection
    window.clearSelectedFileMain = function() {
        const fileInput = document.getElementById('xlsx-file-input-main');
        const uploadState = document.getElementById('upload-state-main');
        const selectedState = document.getElementById('file-selected-state-main');

        if (fileInput) fileInput.value = '';
        if (uploadState) uploadState.classList.remove('hidden');
        if (selectedState) selectedState.classList.add('hidden');

        window.droppedFileMain = null;
        addAlert('File deselezionato', 'info');
    };

    // Setup XLSX upload
    function setupMainXlsxUpload() {
        const fileInput = document.getElementById('xlsx-file-input-main');
        const form = document.getElementById('xlsx-upload-form-main');
        const dropZone = document.getElementById('drop-zone-main');
        const dragOverState = document.getElementById('drag-over-state-main');
        const uploadState = document.getElementById('upload-state-main');

        if (!fileInput || !form || !dropZone) return;

        dropZone.addEventListener('click', () => fileInput.click());

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                if (dragOverState) dragOverState.classList.remove('hidden');
                if (uploadState) uploadState.classList.add('hidden');
            });
        });

        dropZone.addEventListener('dragleave', (e) => {
            if (!dropZone.contains(e.relatedTarget)) {
                if (dragOverState) dragOverState.classList.add('hidden');
                if (uploadState) uploadState.classList.remove('hidden');
            }
        });

        dropZone.addEventListener('drop', (e) => {
            if (dragOverState) dragOverState.classList.add('hidden');
            if (uploadState) uploadState.classList.remove('hidden');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                showFileSelectedMain(files[0]);
                window.droppedFileMain = files[0];
            }
        });

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                showFileSelectedMain(file);
                window.droppedFileMain = null;
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const file = window.droppedFileMain || fileInput.files[0];
            if (!file) {
                addAlert('Seleziona un file prima di procedere', 'error');
                return;
            }
            handleUploadMain(file);
        });
    }

    function showFileSelectedMain(file) {
        const uploadState = document.getElementById('upload-state-main');
        const selectedState = document.getElementById('file-selected-state-main');
        const filename = document.getElementById('selected-filename-main');
        const filesize = document.getElementById('selected-filesize-main');

        const allowedTypes = ['.xlsx', '.xls'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        const maxSize = 10 * 1024 * 1024;

        if (!allowedTypes.includes(fileExtension)) {
            addAlert('Formato file non supportato. Utilizzare solo file Excel (.xlsx, .xls)', 'error');
            clearSelectedFileMain();
            return;
        }

        if (file.size > maxSize) {
            addAlert('File troppo grande. Dimensione massima: 10MB', 'error');
            clearSelectedFileMain();
            return;
        }

        if (filename) filename.textContent = file.name;
        if (filesize) filesize.textContent = formatFileSize(file.size);
        if (uploadState) uploadState.classList.add('hidden');
        if (selectedState) selectedState.classList.remove('hidden');

        addAlert('File selezionato: ' + file.name, 'success');
    }

    function handleUploadMain(file) {
        const baseUrl = window.location.pathname.replace(/\/$/, '');
        const progressDiv = document.getElementById('upload-progress-main');
        const uploadBtn = document.getElementById('upload-btn-main');
        const uploadIcon = document.getElementById('upload-icon-main');
        const uploadText = document.getElementById('upload-text-main');
        let pollingInterval = null;

        if (progressDiv) progressDiv.classList.remove('hidden');
        if (uploadBtn) uploadBtn.disabled = true;
        if (uploadIcon) uploadIcon.className = 'fas fa-spinner fa-spin mr-2';
        if (uploadText) uploadText.textContent = 'Caricamento in corso...';

        updateProgressMain(0, 'Caricamento file sul server...');

        const formData = new FormData();
        formData.append('xlsx_file', file);

        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 20);
                updateProgressMain(percentComplete, `Caricamento: ${formatFileSize(e.loaded)} / ${formatFileSize(e.total)}`);
            }
        });

        xhr.addEventListener('load', () => {
            clearInterval(pollingInterval);
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    updateProgressMain(100, 'Elaborazione completata!');
                    setTimeout(() => {
                        if (progressDiv) progressDiv.classList.add('hidden');
                        if (uploadBtn) uploadBtn.disabled = false;
                        if (data.success) {
                            if (uploadIcon) uploadIcon.className = 'fas fa-check mr-2';
                            if (uploadText) uploadText.textContent = 'Upload Completato';
                            addAlert('File elaborato con successo!', 'success');
                            setTimeout(() => {
                                if (window.pjax && typeof window.pjax.loadContent === 'function') {
                                    window.pjax.loadContent(window.location.href, true);
                                } else {
                                    window.location.reload();
                                }
                            }, 1500);
                        } else {
                            if (uploadIcon) uploadIcon.className = 'fas fa-exclamation-triangle mr-2';
                            if (uploadText) uploadText.textContent = 'Errore - Riprova';
                            addAlert('Errore: ' + (data.error || 'Errore sconosciuto'), 'error');
                            setTimeout(() => {
                                if (uploadIcon) uploadIcon.className = 'fas fa-upload mr-2';
                                if (uploadText) uploadText.textContent = 'Carica e Processa';
                            }, 3000);
                        }
                    }, 500);
                } catch (e) {
                    handleUploadErrorMain('Risposta server non valida');
                }
            } else {
                handleUploadErrorMain(`Errore server: ${xhr.status}`);
            }
        });

        xhr.addEventListener('error', () => handleUploadErrorMain('Errore di rete'));
        xhr.addEventListener('timeout', () => handleUploadErrorMain('Timeout'));

        xhr.open('POST', `${baseUrl}/upload-xlsx`);
        xhr.timeout = 360000;
        xhr.send(formData);

        pollingInterval = setInterval(() => {
            fetch(`${baseUrl}/import-progress`)
                .then(response => response.json())
                .then(progressData => {
                    if (progressData && progressData.total > 0) {
                        const backendPercentage = (progressData.processed / progressData.total) * 80;
                        const totalPercentage = 20 + backendPercentage;
                        updateProgressMain(Math.round(totalPercentage), `${progressData.text} (${progressData.processed} / ${progressData.total})`);
                    }
                })
                .catch(error => console.error('Polling error:', error));
        }, 1000);

        function handleUploadErrorMain(message) {
            clearInterval(pollingInterval);
            if (progressDiv) progressDiv.classList.add('hidden');
            if (uploadBtn) uploadBtn.disabled = false;
            if (uploadIcon) uploadIcon.className = 'fas fa-exclamation-triangle mr-2';
            if (uploadText) uploadText.textContent = 'Errore - Riprova';
            addAlert('Errore durante l\'upload: ' + message, 'error');
            setTimeout(() => {
                if (uploadIcon) uploadIcon.className = 'fas fa-upload mr-2';
                if (uploadText) uploadText.textContent = 'Carica e Processa';
            }, 3000);
        }
    }

    function updateProgressMain(percentage, text) {
        const progressBar = document.getElementById('progress-bar-main');
        const progressPercentage = document.getElementById('progress-percentage-main');
        const progressText = document.getElementById('progress-text-main');

        if (progressBar) progressBar.style.width = percentage + '%';
        if (progressPercentage) progressPercentage.textContent = percentage + '%';
        if (progressText) progressText.textContent = text;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Initialize on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSettingsPage);
    } else {
        initSettingsPage();
    }

    // Re-initialize on PJAX load
    if (window.COREGRE && window.COREGRE.onPageLoad) {
        window.COREGRE.onPageLoad(initSettingsPage);
    }

})();