<?php
// Definisci i comandi per le azioni rapide e i loro colori
$quickCommands = [
    'db:status' => 'bg-cyan-600 hover:bg-cyan-700',
    'migrate:status' => 'bg-blue-600 hover:bg-blue-700',
    'migrate' => 'bg-green-600 hover:bg-green-700',
    'migrate:rollback' => 'bg-red-600 hover:bg-red-700',
    'dump-autoload' => 'bg-purple-600 hover:bg-purple-700',
    'cache:clear' => 'bg-orange-600 hover:bg-orange-700',
];

// Raggruppa i comandi per prefisso
$groupedCommands = [];
foreach ($commands as $command => $description) {
    $parts = explode(':', $command);
    $group = count($parts) > 1 ? $parts[0] : 'generale';
    $groupedCommands[$group][$command] = $description;
}
ksort($groupedCommands);

$groupColors = [
    'db' => 'text-cyan-400',
    'make' => 'text-green-400',
    'schema' => 'text-yellow-400',
    'migrate' => 'text-blue-400',
    'cache' => 'text-orange-400',
    'dump-autoload' => 'text-purple-400',
    'generale' => 'text-gray-400',
];

?>

<!-- Page Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                <i class="fas fa-terminal mr-3 text-blue-600"></i>
                Artisan Console
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Interfaccia web per gestire migrazioni, modelli e database
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3">
            <button onclick="clearConsole()"
                    class="btn btn-secondary">
                <i class="fas fa-trash mr-2"></i>
                Pulisci Console
            </button>
        </div>
    </div>
</div>

<!-- PowerShell Console -->
<div class="bg-slate-900 rounded-lg shadow-xl overflow-hidden">
    <!-- Console Header -->
    <div class="bg-slate-800 px-4 py-2 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="flex space-x-1.5">
                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
            </div>
            <span class="text-gray-300 text-sm font-mono">COREGRE Artisan Console</span>
        </div>
        <div class="text-xs text-gray-400 font-mono">
            v3.0 | <?= date('Y-m-d H:i:s') ?>
        </div>
    </div>

    <!-- Quick Actions Toolbar -->
    <div class="bg-slate-800 border-b border-slate-700 px-4 py-2">
        <div class="flex flex-wrap gap-2">
            <?php foreach ($quickCommands as $command => $color): ?>
                <?php if (array_key_exists($command, $commands)): ?>
                    <button onclick="executeQuickCommand('<?= $command ?>')"
                            class="px-3 py-1 <?= $color ?> text-white text-xs rounded font-mono transition-colors">
                        <?= explode(':', $command)[1] ?? $command ?>
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Console Output -->
    <div id="console-output" class="h-96 overflow-y-auto p-4 font-mono text-sm bg-slate-900 text-gray-100">
        <div class="text-cyan-400">COREGRE Artisan Console v3.0</div>
        <div class="text-gray-400">Pronto per eseguire comandi...</div>
        <div class="text-gray-600">Digita 'list' per l'elenco dei comandi disponibili</div>
        <div class="text-gray-600">───────────────────────────────────</div>
    </div>

    <!-- Command Input -->
    <div class="bg-slate-800 border-t border-slate-700 p-4">
        <div class="flex items-center space-x-2">
            <span class="text-green-400 font-mono text-sm">PS C:\coregre></span>
            <div class="flex-1 relative">
                <input type="text"
                       id="commandInput"
                       placeholder="list"
                       class="w-full bg-slate-700 text-gray-100 font-mono text-sm px-3 py-2 rounded border border-slate-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                       autocomplete="off">
                <div id="suggestions" class="absolute bottom-full left-0 right-0 bg-slate-700 border border-slate-600 rounded-t hidden z-10 max-h-48 overflow-y-auto">
                    <!-- Auto-suggestions will be populated here -->
                </div>
            </div>
            <button onclick="executeCommand()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-mono text-sm transition-colors">
                <i class="fas fa-play mr-1"></i>
                Esegui
            </button>
        </div>
    </div>
</div>



<!-- Available Commands Reference -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($groupedCommands as $group => $commandList): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chevron-right mr-2 <?= $groupColors[$group] ?? 'text-gray-400' ?>"></i>
                <?= ucfirst($group) ?>
            </h3>
            <div class="space-y-2 text-sm font-mono">
                <?php foreach ($commandList as $command => $description): ?>
                    <div class="flex justify-between items-center">
                        <code class="<?= $groupColors[$group] ?? 'text-gray-400' ?>"><?= $command ?></code>
                        <span class="text-gray-500 text-right text-xs"><?= $description ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
/* PowerShell-style scrollbar */
#console-output::-webkit-scrollbar {
    width: 8px;
}

#console-output::-webkit-scrollbar-track {
    background: #1e293b;
}

#console-output::-webkit-scrollbar-thumb {
    background: #475569;
    border-radius: 4px;
}

#console-output::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}

/* Smooth typing animation */
.typing {
    border-right: 2px solid #10b981;
    animation: blink 1s infinite;
}

@keyframes blink {
    0%, 50% { border-color: #10b981; }
    51%, 100% { border-color: transparent; }
}

/* Command suggestions */
.suggestion {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #475569;
    color: white !important;
}

.suggestion:hover {
    background-color: #475569;
}

.suggestion:last-child {
    border-bottom: none;
}

/* Syntax highlighting classes */
.ps-command { color: #fbbf24; }     /* Yellow for commands */
.ps-flag { color: #a78bfa; }        /* Purple for flags */
.ps-string { color: #34d399; }      /* Green for strings */
.ps-number { color: #60a5fa; }      /* Blue for numbers */
.ps-error { color: #f87171; }       /* Red for errors */
.ps-success { color: #10b981; }     /* Green for success */
.ps-warning { color: #f59e0b; }     /* Orange for warnings */
.ps-info { color: #06b6d4; }        /* Cyan for info */
.ps-prompt { color: #10b981; }      /* Green for prompt */
.ps-path { color: #8b5cf6; }        /* Purple for paths */
</style>

<script>
// Available commands for autocomplete (now dynamic)
const availableCommands = <?= json_encode(array_keys($commands)) ?>;

// Command history
let commandHistory = [];
let historyIndex = -1;

// Initialize console
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('commandInput');

    // Focus input on load
    input.focus();

    // Handle Enter key
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            executeCommand();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            navigateHistory('up');
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            navigateHistory('down');
        } else if (e.key === 'Tab') {
            e.preventDefault();
            autoComplete();
        }
    });

    // Auto-suggestions
    input.addEventListener('input', function() {
        showSuggestions(this.value);
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#commandInput') && !e.target.closest('#suggestions')) {
            hideSuggestions();
        }
    });
    
    // Auto-focus input when clicking anywhere in console
    document.getElementById('console-output').addEventListener('click', function() {
        document.getElementById('commandInput').focus();
    });

    });

window.executeCommand = function() {
    const input = document.getElementById('commandInput');
    const command = input.value.trim();

    if (!command) return;

    commandHistory.unshift(command);
    if (commandHistory.length > 50) commandHistory.pop();
    historyIndex = -1;

    addToConsole('<span class="ps-prompt">PS C:\\coregre></span> <span class="ps-command">php artisan ' + command + '</span>');
    input.value = '';
    addToConsole('<span class="typing">Executing...</span>');

    fetch(window.location.pathname + '/execute', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.COREGRE ? window.COREGRE.csrfToken : '',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ command: command })
    })
    .then(response => response.json())
    .then(data => {
        removeLastLine();
        if (data.success) {
            addFormattedOutput(data.output, command);
        } else {
            addToConsole('<span class="ps-error">Error: ' + (data.output || data.message || 'Command failed') + '</span>');
        }
        addToConsole('');
        scrollToBottom();
    })
    .catch(error => {
        removeLastLine();
        addToConsole('<span class="ps-error">Network Error: ' + error.message + '</span>');
        addToConsole('');
        scrollToBottom();
    });
}

window.executeQuickCommand = function(command) {
    const input = document.getElementById('commandInput');
    input.value = command;
    executeCommand();
}

window.clearConsole = function() {
    const output = document.getElementById('console-output');
    output.innerHTML =
        '<div class="text-cyan-400">COREGRE Artisan Console v3.0</div>' +
        '<div class="text-gray-400">Console cleared - Ready for new commands...</div>' +
        '<div class="text-gray-600">───────────────────────────────────</div>';
    document.getElementById('commandInput').focus();
}



window.selectSuggestion = function(command) {
    document.getElementById('commandInput').value = command;
    hideSuggestions();
    document.getElementById('commandInput').focus();
}

function addFormattedOutput(output, command) {
    let formattedOutput = processAnsiCodes(output);
    if (formattedOutput.includes('|') && formattedOutput.includes('-')) {
        formattedOutput = formatTable(formattedOutput);
    }
    addToConsole(formattedOutput);
}

function processAnsiCodes(text) {
    let result = text.replace(/\\u001b/g, '\u001b');
    const ansiMappings = [
        { pattern: /\u001b\[0;31m/g, replacement: '<span class="ps-error">' },
        { pattern: /\u001b\[0;32m/g, replacement: '<span class="ps-success">' },
        { pattern: /\u001b\[0;33m/g, replacement: '<span class="ps-warning">' },
        { pattern: /\u001b\[0;36m/g, replacement: '<span class="text-cyan-400">' },
        { pattern: /\u001b\[0m/g, replacement: '</span>' },
        { pattern: /\u001b\[39m/g, replacement: '</span>' },
    ];
    ansiMappings.forEach(mapping => {
        result = result.replace(mapping.pattern, mapping.replacement);
    });
    result = result.replace(/\u001b\[[0-9;]*m/g, '');
    return result;
}

function formatTable(output) {
    const lines = output.split('\n');
    return lines.map((line, index) => {
        if (line.trim() === '') return '';
        if (line.includes('|')) {
            if (index === 0 || line.includes('---')) {
                return '<span class="text-cyan-400">' + line + '</span>';
            } else {
                let coloredLine = line;
                if (line.includes('Executed')) coloredLine = line.replace(/Executed/g, '<span class="ps-success">Executed</span>');
                if (line.includes('Pending')) coloredLine = line.replace(/Pending/g, '<span class="ps-warning">Pending</span>');
                return coloredLine;
            }
        }
        return line;
    }).join('\n');
}

function addToConsole(text) {
    const output = document.getElementById('console-output');
    const div = document.createElement('div');
    // Replace newline characters with <br> tags to preserve formatting
    div.innerHTML = (text || '&nbsp;').replace(/\n/g, '<br>');
    output.appendChild(div);
    scrollToBottom();
}

function removeLastLine() {
    const output = document.getElementById('console-output');
    if (output.lastElementChild) {
        output.removeChild(output.lastElementChild);
    }
}

function scrollToBottom() {
    const output = document.getElementById('console-output');
    output.scrollTop = output.scrollHeight;
}

function navigateHistory(direction) {
    const input = document.getElementById('commandInput');
    if (direction === 'up') {
        if (historyIndex < commandHistory.length - 1) {
            historyIndex++;
            input.value = commandHistory[historyIndex];
        }
    } else if (direction === 'down') {
        if (historyIndex > 0) {
            historyIndex--;
            input.value = commandHistory[historyIndex];
        } else if (historyIndex === 0) {
            historyIndex = -1;
            input.value = '';
        }
    }
}

function showSuggestions(value) {
    const suggestions = document.getElementById('suggestions');
    if (!value) {
        hideSuggestions();
        return;
    }
    const matches = availableCommands.filter(cmd => cmd.toLowerCase().includes(value.toLowerCase()));
    if (matches.length === 0) {
        hideSuggestions();
        return;
    }
    suggestions.innerHTML = matches.map(cmd =>
        `<div class="suggestion" onclick="selectSuggestion('${cmd}')">${cmd}</div>`
    ).join('');
    suggestions.classList.remove('hidden');
}

function hideSuggestions() {
    document.getElementById('suggestions').classList.add('hidden');
}

function autoComplete() {
    const input = document.getElementById('commandInput');
    const value = input.value.toLowerCase();
    const match = availableCommands.find(cmd => cmd.toLowerCase().startsWith(value));
    if (match) {
        input.value = match;
    }
}

function loadDocumentation() {
    fetch(window.location.pathname + '/../documentation.md')
        .then(response => response.text())
        .then(markdown => {
            const html = parseMarkdown(markdown);
            document.getElementById('markdownContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('markdownContent').innerHTML = '<p class="text-red-500">Errore nel caricamento della documentazione.</p>';
        });
}

function parseMarkdown(markdown) {
    let html = markdown;

    // Headers
    html = html.replace(/^### (.*$)/gim, '<h3 class="text-xl font-bold text-gray-900 dark:text-white mt-8 mb-4">$1</h3>');
    html = html.replace(/^## (.*$)/gim, '<h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-10 mb-6">$1</h2>');
    html = html.replace(/^# (.*$)/gim, '<h1 class="text-3xl font-bold text-gray-900 dark:text-white mt-12 mb-8">$1</h1>');

    // Code blocks
    html = html.replace(/```(\w+)?\n([\s\S]*?)```/g, function(match, lang, code) {
        return '<pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto my-4"><code class="language-' + (lang || 'text') + '">' +
               code.trim().replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</code></pre>';
    });

    // Inline code
    html = html.replace(/`([^`]+)`/g, '<code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-sm font-mono">$1</code>');

    // Lists
    html = html.replace(/^\* (.*$)/gim, '<li>$1</li>');
    html = html.replace(/^(\d+)\. (.*$)/gim, '<li>$1</li>');
    html = html.replace(/(<li>.*<\/li>)+/gs, '<ul>$1</ul>');

    // Blockquotes
    html = html.replace(/^> (.*$)/gim, '<blockquote class="border-l-4 border-blue-500 pl-4 italic my-4">$1</blockquote>');

    // Paragraphs
    html = html.replace(/\n\n/g, '</p><p class="mb-4">');
    html = '<p class="mb-4">' + html + '</p>';
    html = html.replace(/<p class="mb-4">\s*<([uol|h|pre|blockquote])/g, '<$1');
    html = html.replace(/<\/([uol|h|pre|blockquote])>\s*<\/p>/g, '</$1>');

    return html;
}
</script>