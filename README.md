# COREGRE - Sistema ERP per la Gestione Aziendale

**Sistema di gestione completo per aziende manifatturiere con architettura MVC personalizzata, API mobile unificate e interfaccia web moderna.**

---

## Indice

1. [Panoramica del Sistema](#panoramica-del-sistema)
2. [Architettura Tecnica](#architettura-tecnica)
3. [Stack Tecnologico](#stack-tecnologico)
4. [Struttura della Codebase](#struttura-della-codebase)
5. [Moduli Funzionali](#moduli-funzionali)
6. [Sistema di Routing](#sistema-di-routing)
7. [BaseController: API Completa](#basecontroller-api-completa)
8. [Sistema dei Componenti Universali](#sistema-dei-componenti-universali)
9. [Database e Modelli Eloquent](#database-e-modelli-eloquent)
10. [Sistema di Autenticazione e Permessi](#sistema-di-autenticazione-e-permessi)
11. [Sistema Cron per Task Automatici](#sistema-cron-per-task-automatici)
12. [API Mobile Unificata](#api-mobile-unificata)
13. [Sistema PJAX per Navigazione](#sistema-pjax-per-navigazione)
14. [Widget Dashboard](#widget-dashboard)
15. [Guide How-To](#guide-how-to)
16. [Installazione e Configurazione](#installazione-e-configurazione)
17. [Development e Debugging](#development-e-debugging)
18. [Best Practices](#best-practices)

---

## Panoramica del Sistema

COREGRE è un sistema ERP completo progettato per aziende manifatturiere, con focus sul settore calzaturiero. Il sistema gestisce l'intero ciclo produttivo dall'ordine fino alla spedizione, includendo controllo qualità, riparazioni, tracking genealogico dei prodotti e gestione terzisti.

### Caratteristiche Principali

- **Custom MVC Framework**: Framework PHP personalizzato ottimizzato per performance
- **Eloquent ORM Standalone**: Utilizzo di Laravel Eloquent come ORM standalone
- **Navigazione PJAX**: Single Page Application experience mantenendo SEO
- **API Mobile Unificate**: Sistema centralizzato per gestire multiple app mobile
- **Sistema di Migrazione**: Database versionato con rollback
- **Componenti Universali**: Notification e Modal system riutilizzabili
- **Dark Mode Nativo**: Supporto completo tema scuro
- **Widget Personalizzabili**: Dashboard configurabile per utente
- **Sistema Cron**: Gestione task automatici con scheduling avanzato

---

## Architettura Tecnica

### Principi Architetturali

Il sistema segue un'architettura MVC (Model-View-Controller) personalizzata con separazione netta delle responsabilità:

- **Model Layer**: Gestione dati con Eloquent ORM, business logic, relazioni
- **View Layer**: Template PHP con componenti riutilizzabili
- **Controller Layer**: Logica applicativa, validazione, orchestrazione
- **Core Layer**: Framework base (Router, Database, BaseController, BaseModel)

### Pattern Utilizzati

1. **Singleton Pattern**: Database connection, CronManager
2. **Factory Pattern**: Creazione dinamica controller e modelli
3. **Repository Pattern**: Astrazione accesso dati tramite Eloquent
4. **Decorator Pattern**: BaseController esteso da controller specifici
5. **Observer Pattern**: Eventi Eloquent per operazioni automatiche

---

## Stack Tecnologico

### Backend

```
PHP 8.0+                  - Linguaggio principale con type declarations
PDO                       - Database abstraction layer
Eloquent ORM (v10.0)      - Laravel Database component standalone
Custom MVC Framework      - Framework proprietario ottimizzato
Composer                  - Dependency management
```

### Frontend

```
Tailwind CSS 3.x          - Utility-first CSS framework
Alpine.js                 - Lightweight JavaScript framework
Vanilla JavaScript        - Custom interactions
PJAX                      - AJAX navigation system
Chart.js                  - Grafici e visualizzazioni
```

### Database

```
MySQL 8.0+                - Database principale
Migration System          - Schema versioning
Query Builder             - Eloquent query construction
```

### Librerie Terze Parti

```
bacon/bacon-qr-code       - Generazione QR codes
mpdf/mpdf                 - Generazione PDF
phpmailer/phpmailer       - Invio email
phpoffice/phpspreadsheet  - Gestione file Excel
pichesi/tcpdf-php8        - Generazione PDF alternativo
```

---

## Struttura della Codebase

```
coregre/
├── app/
│   ├── controllers/              # Controller applicativi (26 files)
│   │   ├── HomeController.php              # Dashboard e homepage
│   │   ├── LoginController.php             # Autenticazione
│   │   ├── RiparazioniController.php       # Gestione riparazioni
│   │   ├── QualityController.php           # Controllo qualità
│   │   ├── QualityApiController.php        # API quality mobile
│   │   ├── ProduzioneController.php        # Produzione
│   │   ├── ExportController.php            # Export/DDT
│   │   ├── SCMAdminController.php          # SCM amministrazione
│   │   ├── SCMPublicController.php         # SCM area pubblica
│   │   ├── TrackingController.php          # Tracking genealogia
│   │   ├── MrpController.php               # MRP planning
│   │   ├── EtichetteController.php         # Sistema etichette
│   │   ├── UsersController.php             # Gestione utenti
│   │   ├── SettingsController.php          # Impostazioni
│   │   ├── MobileApiController.php         # API mobile unificata
│   │   └── ...
│   ├── models/                   # Modelli Eloquent (47 files)
│   │   ├── BaseModel.php                   # Model base con sanitization
│   │   ├── User.php                        # Utenti sistema
│   │   ├── Permission.php                  # Permessi utenti
│   │   ├── InternalRepair.php              # Riparazioni interne
│   │   ├── QualityRecord.php               # Record controlli qualità
│   │   ├── QualityException.php            # Eccezioni qualità
│   │   ├── QualityOperator.php             # Operatori CQ
│   │   ├── ProductionRecord.php            # Record produzione
│   │   ├── ExportDocument.php              # Documenti export
│   │   ├── ExportArticle.php               # Articoli export
│   │   ├── ScmLaboratory.php               # Laboratori SCM
│   │   ├── ScmLaunch.php                   # Lanci produzione SCM
│   │   ├── MrpMaterial.php                 # Materiali MRP
│   │   ├── CoreData.php                    # Dati core produzione
│   │   ├── ActivityLog.php                 # Log attività
│   │   ├── AvailableWidget.php             # Widget disponibili
│   │   ├── UserWidget.php                  # Widget utente
│   │   └── ...
│   ├── views/                    # Template view modulari
│   │   ├── layouts/                        # Layout principali
│   │   │   ├── main.php                    # Layout principale con sidebar
│   │   │   └── public.php                  # Layout area pubblica
│   │   ├── components/                     # Componenti universali
│   │   │   ├── notifications.php           # Sistema notifiche
│   │   │   ├── modals.php                  # Sistema modali
│   │   │   ├── sidebar.php                 # Sidebar navigazione
│   │   │   └── header.php                  # Header applicazione
│   │   ├── riparazioni/                    # Views riparazioni
│   │   ├── quality/                        # Views controllo qualità
│   │   ├── produzione/                     # Views produzione
│   │   ├── export/                         # Views export/DDT
│   │   ├── scm-admin/                      # Views SCM admin
│   │   ├── scm-public/                     # Views SCM pubblico
│   │   ├── tracking/                       # Views tracking
│   │   ├── mrp/                            # Views MRP
│   │   └── ...
│   ├── cron/                     # Cron jobs (3 files)
│   │   ├── CleanupTempFilesJob.php         # Pulizia file temporanei
│   │   ├── DatabaseBackupJob.php           # Backup database
│   │   └── CleanupCronLogsJob.php          # Pulizia log cron
│   └── utils/                    # Utility classes
├── core/                         # Framework MVC core (8 files)
│   ├── Router.php                          # Sistema routing
│   ├── Database.php                        # PDO wrapper singleton
│   ├── BaseController.php                  # Controller base
│   ├── BaseModel.php                       # Model base per sanitization
│   ├── CronManager.php                     # Gestore cron jobs
│   ├── CronJob.php                         # Classe base job
│   ├── CronSchedule.php                    # Parser espressioni cron
│   └── EloquentBootstrap.php               # Inizializzazione Eloquent
├── config/                       # File configurazione
│   ├── config.php                          # Configurazione principale
│   └── cron.php                            # Configurazione cron jobs
├── database/
│   ├── migrations/                         # 58 migration files
│   └── backups/                            # Backup automatici
├── routes/
│   └── web.php                             # Definizione 418 routes
├── public/                       # Web-accessible files
│   ├── index.php                           # Entry point
│   ├── css/                                # Styles
│   ├── js/                                 # JavaScript
│   └── uploads/                            # User uploads
├── storage/                      # File storage
│   ├── logs/                               # Application logs
│   ├── tmp/                                # Temporary files
│   ├── backups/                            # Database backups
│   └── cron/                               # Cron locks e metadata
├── vendor/                       # Composer dependencies
├── .env                          # Environment configuration
├── .env.example                  # Environment template
├── composer.json                 # Dependencies definition
├── cron.php                      # Cron entry point
└── README.md                     # Questa documentazione
```

---

## Moduli Funzionali

### Gestione Riparazioni

**Controller**: `RiparazioniController.php`
**Models**: `InternalRepair.php`, `Repair.php`
**Views**: `app/views/riparazioni/`

#### Funzionalita

- Creazione riparazioni interne con dettaglio taglie (P01-P20)
- Assegnazione a operatori e reparti
- Tracking stato lavorazione
- Generazione PDF cedola riparazione con barcode
- API mobile per app riparazioni
- Storico completo riparazioni per articolo/operatore

#### Workflow Tipico

```
1. Creazione riparazione
   POST /riparazioni-interne
   - Dati articolo, codice, taglie, causale
   - Assegnazione operatore e reparto

2. Lavorazione
   - Operatore visualizza riparazioni assegnate
   - Aggiornamento stato tramite web o mobile app

3. Completamento
   POST /riparazioni-interne/update
   - Marcatura come completata
   - Log attivita automatico

4. Stampa cedola
   GET /riparazioni-interne/pdf?id=000001
   - Genera PDF con barcode e dettagli
```

---

### Controllo Qualita (Hermes CQ)

**Controller**: `QualityController.php`, `QualityApiController.php`
**Models**: `QualityRecord.php`, `QualityException.php`, `QualityDefectType.php`, `QualityOperator.php`
**Views**: `app/views/quality/`

#### Funzionalita

- Dashboard interattiva con statistiche real-time
- Registrazione controlli qualita (interno/griffe)
- Gestione eccezioni e difetti con foto
- Mobile app per operatori CQ
- Reporting avanzato con export PDF/Excel
- Analytics difettosita per reparto/operatore/articolo

---

### Sistema di Routing

Il sistema utilizza un router personalizzato (`core/Router.php`) con supporto parametri dinamici e fallback intelligente.

#### Caratteristiche Router

1. **Path Normalization**: Gestisce installazioni in subdirectory
2. **Parameter Extraction**: Pattern {param} per parametri dinamici
3. **Method-based Routing**: GET, POST, PUT, DELETE support
4. **Fallback Routing**: Parsing automatico URI come Controller/Action/Params
5. **Base Path Calculation**: Risoluzione automatica per APP_SUBDIRECTORY

#### Definizione Routes (routes/web.php)

```php
// Route semplice
$router->get('/', 'Home@index');

// Route con parametro
$router->get('/users/{id}', 'Users@show');

// Route POST
$router->post('/users/store', 'Users@store');

// Route PUT/DELETE
$router->put('/users/{id}', 'Users@update');
$router->delete('/users/{id}', 'Users@delete');

// Route con closure
$router->get('/test', function() {
    echo "Test route";
});
```

#### Come Aggiungere una Nuova Route

1. Apri `routes/web.php`
2. Aggiungi la route:

```php
$router->get('/nuovo-modulo', 'NuovoModulo@index');
$router->get('/nuovo-modulo/{id}', 'NuovoModulo@show');
$router->post('/nuovo-modulo/store', 'NuovoModulo@store');
```

3. Il router cerca automaticamente `NuovoModuloController` in `app/controllers/`

---

## BaseController: API Completa

Tutti i controller devono estendere `BaseController` che fornisce metodi helper comuni.

### Rendering e View

#### render($viewName, $data = [], $layout = 'main')

Renderizza una view con layout completo. Supporta PJAX automaticamente.

```php
public function index()
{
    $this->requireAuth();

    $data = Model::all();

    $this->render('modulo.index', [
        'pageTitle' => 'Titolo Pagina',
        'data' => $data
    ]);
}
```

#### view($viewName, $data = [])

Renderizza solo il contenuto della view senza layout.

```php
$this->view('components.widget', ['widget' => $widgetData]);
```

### Autenticazione e Permessi

#### requireAuth()

Richiede che l'utente sia autenticato. Redirect a /login se non autenticato.

```php
public function index()
{
    $this->requireAuth();
    // ... codice protetto
}
```

#### requirePermission($permission)

Richiede permesso specifico. I permessi disponibili sono definiti in `Permission` model.

```php
public function index()
{
    $this->requirePermission('riparazioni');
    // ... solo utenti con permesso 'riparazioni'
}
```

#### requireAdmin()

Richiede privilegi amministratore.

```php
public function delete($id)
{
    $this->requireAdmin();
    // ... solo admin
}
```

#### isAuthenticated()

Verifica se utente e autenticato (boolean).

#### hasPermission($permission)

Verifica se utente ha un permesso specifico (boolean).

#### isAdmin()

Verifica se utente e amministratore (boolean).

### Response Methods

#### json($data, $statusCode = 200)

Ritorna risposta JSON. Pulisce output buffer e imposta headers corretti.

```php
public function apiEndpoint()
{
    $this->setCorsHeaders();

    $data = Model::all();

    $this->json([
        'status' => 'success',
        'data' => $data
    ]);
}
```

#### redirect($url, $statusCode = 302)

Reindirizza a URL specifico.

```php
public function store()
{
    Model::create($data);
    $this->setFlash('success', 'Creato con successo');
    $this->redirect('/modulo');
}
```

#### redirectBack($default = '/')

Reindirizza alla pagina precedente o a default.

```php
$this->redirectBack('/dashboard');
```

### Request Helpers

#### input($key = null, $default = null)

Ottiene input dalla richiesta (GET + POST). Sanitizza automaticamente.

```php
// Tutti gli input
$allData = $this->input();

// Input specifico
$name = $this->input('name');

// Con default
$page = $this->input('page', 1);
```

#### isPost()

Verifica se richiesta e POST.

```php
if ($this->isPost()) {
    // Elabora form
}
```

#### isGet()

Verifica se richiesta e GET.

#### isAjax()

Verifica se richiesta e AJAX.

#### isPjax()

Verifica se richiesta e PJAX (usato automaticamente da render()).

### Flash Messages

#### setFlash($type, $message)

Imposta messaggio flash per prossima richiesta.

```php
$this->setFlash('success', 'Operazione completata');
$this->setFlash('error', 'Si e verificato un errore');
$this->setFlash('warning', 'Attenzione');
$this->setFlash('info', 'Informazione');
```

I messaggi flash vengono visualizzati automaticamente nel layout.

#### getFlash($type)

Ottiene e rimuove messaggio flash.

### Validazione

#### validate($data, $rules)

Valida dati con regole. Ritorna array errori (vuoto se validazione OK).

```php
$errors = $this->validate($_POST, [
    'nome' => 'required|min:3|max:100',
    'email' => 'required|email',
    'password' => 'required|min:8'
]);

if (!empty($errors)) {
    $this->setFlash('error', implode(', ', $errors));
    $this->redirectBack();
}
```

Regole disponibili:
- `required`: Campo obbligatorio
- `email`: Email valida
- `min:N`: Lunghezza minima
- `max:N`: Lunghezza massima

#### sanitize($input)

Sanitizza input manualmente (chiamato automaticamente da input()).

```php
$clean = $this->sanitize($_GET['query']);
```

### CSRF Protection

#### generateCsrfToken()

Genera token CSRF per form.

```php
<input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
```

#### validateCsrfToken($token)

Valida token CSRF.

```php
if (!$this->validateCsrfToken($_POST['csrf_token'])) {
    $this->setFlash('error', 'Token CSRF non valido');
    $this->redirectBack();
}
```

### CORS Headers

#### setCorsHeaders()

Imposta headers CORS per API mobile. Gestisce richieste OPTIONS preflight.

```php
public function mobileApi()
{
    $this->setCorsHeaders();

    // ... logica API

    $this->json($response);
}
```

### Activity Logging

#### logActivity($category, $activityType, $description, $note = '', $textQuery = '')

Registra attivita utente nel database.

```php
$this->logActivity(
    'riparazioni',
    'create',
    'Creata riparazione #12345',
    'Dettagli aggiuntivi',
    'INSERT INTO ...'
);
```

### URL Generation

#### url($path = '', $params = [])

Genera URL completo considerando BASE_URL e subdirectory.

```php
$editUrl = $this->url('/modulo/edit/123');
$apiUrl = $this->url('/api/data', ['filter' => 'active']);
```

### Utility Methods

#### timeAgo($datetime)

Formatta datetime in formato "tempo fa".

```php
echo $this->timeAgo('2025-09-19 14:30:00'); // "2 ore fa"
```

---

## Sistema dei Componenti Universali

Il sistema include componenti JavaScript riutilizzabili disponibili in tutte le pagine.

### Notifications System

**File**: `app/views/components/notifications.php`

Sistema di notifiche toast con animazioni fluide.

#### API JavaScript

```javascript
// Mostra notifica
CoregreNotifications.show(message, type, duration);

// Shortcuts
CoregreNotifications.success('Operazione completata!');
CoregreNotifications.error('Si e verificato un errore');
CoregreNotifications.warning('Attenzione!');
CoregreNotifications.info('Informazione');

// Notifica senza auto-hide
const id = CoregreNotifications.loading('Caricamento in corso...');

// Rimuovi notifica specifica
CoregreNotifications.remove(id);

// Rimuovi per testo
CoregreNotifications.removeByText('Caricamento');
```

#### Parametri

- `message` (string): Testo da visualizzare
- `type` (string): 'success', 'error', 'warning', 'info'
- `duration` (number): Millisecondi prima di auto-hide (0 = nessun hide)

#### Esempio Uso

```javascript
// In una funzione AJAX
fetch('/api/save', {
    method: 'POST',
    body: JSON.stringify(data)
})
.then(response => response.json())
.then(data => {
    if (data.status === 'success') {
        CoregreNotifications.success('Dati salvati con successo');
    } else {
        CoregreNotifications.error(data.message);
    }
});
```

### Modal System

**File**: `app/views/components/modals.php`

Sistema di modali con animazioni e tipologie predefinite.

#### API JavaScript

##### Modale di Conferma

```javascript
CoregreModals.confirm({
    title: 'Conferma Operazione',
    message: 'Sei sicuro di voler procedere?',
    confirmText: 'Conferma',
    cancelText: 'Annulla',
    type: 'info', // 'info', 'warning', 'danger'
    onConfirm: function() {
        // Azione da eseguire
    },
    onCancel: function() {
        // Opzionale
    }
});
```

##### Modale di Eliminazione

```javascript
CoregreModals.confirmDelete(
    'Sei sicuro di voler eliminare questo elemento?',
    function() {
        // Esegui eliminazione
    },
    1 // count elementi
);
```

##### Modale Alert

```javascript
CoregreModals.alert(
    'Titolo',
    'Messaggio informativo',
    function() {
        // Callback opzionale alla chiusura
    }
);
```

##### Gestione Modali Statici HTML

Per modali definiti nell'HTML:

```javascript
// Apri modale
CoregreModals.openModal('myModalId');

// Chiudi modale
CoregreModals.closeModal('myModalId', function() {
    // Callback opzionale
});
```

#### Tipi Modale

- `info`: Modale informativo (blu)
- `warning`: Modale di avvertimento (giallo)
- `danger`: Modale di pericolo (rosso)

#### Esempio HTML Modale Statico

```html
<div id="editModal" class="hidden fixed inset-0 z-[99999]" aria-labelledby="modal-title" role="dialog">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="CoregreModals.closeModal('editModal')"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-6">
            <!-- Contenuto modale -->
            <button onclick="CoregreModals.closeModal('editModal')">Chiudi</button>
        </div>
    </div>
</div>
```

---

## Sistema Cron per Task Automatici

Il sistema include un gestore cron completo per l'esecuzione di task automatici.

### Architettura

**File Core**:
- `core/CronManager.php`: Gestore centrale dei job
- `core/CronJob.php`: Classe base per creare job
- `core/CronSchedule.php`: Parser espressioni cron
- `config/cron.php`: Configurazione job attivi
- `cron.php`: Entry point

**Job Predefiniti**:
- `app/cron/CleanupTempFilesJob.php`: Pulizia file temporanei (daily 3:00)
- `app/cron/DatabaseBackupJob.php`: Backup database (daily 2:00)
- `app/cron/CleanupCronLogsJob.php`: Pulizia log cron (weekly Sunday 4:00)

### Setup Cron sul Server

Aggiungere una singola entry al crontab del server:

```bash
# Apri crontab
crontab -e

# Aggiungi questa linea (esegue ogni minuto)
* * * * * php /path/to/coregre/cron.php >> /dev/null 2>&1
```

Il CronManager gestisce internamente quali job eseguire e quando.

### Come Creare un Nuovo Cron Job

#### Passo 1: Crea la Classe Job

Crea nuovo file in `app/cron/NomeJob.php`:

```php
<?php

class MyCustomJob extends CronJob
{
    public function name()
    {
        return 'My Custom Job';
    }

    public function description()
    {
        return 'Descrizione di cosa fa il job';
    }

    /**
     * Espressione cron per scheduling
     * Format: "minute hour day month weekday"
     *
     * Esempi:
     * '* * * * *'    - Ogni minuto
     * '0 * * * *'    - Ogni ora
     * '0 0 * * *'    - Ogni giorno a mezzanotte
     * '0 2 * * *'    - Ogni giorno alle 2:00
     * '0 0 * * 0'    - Ogni domenica a mezzanotte
     */
    public function schedule()
    {
        return '0 3 * * *'; // Ogni giorno alle 3:00
    }

    public function handle()
    {
        $this->log("Inizio esecuzione job");

        // Logica del job
        $deleted = Model::where('created_at', '<', date('Y-m-d', strtotime('-30 days')))
                        ->delete();

        $this->log("Eliminati {$deleted} record");

        return "Job completato: {$deleted} record eliminati";
    }

    // Callbacks opzionali
    public function before() { return true; }
    public function after($result = null, $exception = null) { }
    public function onSuccess($result) { }
    public function onFailure($exception) { }
    public function timeout() { return 300; } // 5 minuti
    public function isEnabled() { return true; }
}
```

#### Passo 2: Registra il Job

Modifica `config/cron.php`:

```php
<?php

return [
    CleanupTempFilesJob::class,
    DatabaseBackupJob::class,
    CleanupCronLogsJob::class,
    MyCustomJob::class,  // Aggiungi nuovo job
];
```

#### Passo 3: Test del Job

```bash
# Test esecuzione manuale
php cron.php

# Visualizza log
tail -f storage/logs/cron-$(date +%Y-%m-%d).log
```

### Monitoring e Logging

#### Database Logs

I job vengono loggati nella tabella `cron_logs`. Accedi a `/cron` (solo admin) per visualizzare storico esecuzioni e statistiche.

#### Features

- **Lock System**: Previene esecuzioni sovrapposte
- **Error Handling**: Eccezioni catturate e loggate automaticamente
- **Performance Monitoring**: Durata esecuzione tracciata

---

## Guide How-To

### Come Aggiungere un Nuovo Controller

#### Passo 1: Crea il File Controller

Crea `app/controllers/NomeModuloController.php`:

```php
<?php

class NomeModuloController extends BaseController
{
    /**
     * Lista elementi
     */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('nome_modulo');

        $items = NomeModuloModel::all();

        $this->render('nome_modulo.index', [
            'pageTitle' => 'Nome Modulo',
            'items' => $items
        ]);
    }

    /**
     * Visualizza singolo elemento
     */
    public function show($id)
    {
        $this->requireAuth();

        $item = NomeModuloModel::findOrFail($id);

        $this->render('nome_modulo.show', [
            'pageTitle' => 'Dettaglio',
            'item' => $item
        ]);
    }

    /**
     * Form creazione
     */
    public function create()
    {
        $this->requireAuth();

        $this->render('nome_modulo.create', [
            'pageTitle' => 'Nuovo Elemento'
        ]);
    }

    /**
     * Salva nuovo elemento
     */
    public function store()
    {
        $this->requireAuth();

        // Validazione
        $errors = $this->validate($_POST, [
            'nome' => 'required|min:3',
            'descrizione' => 'required'
        ]);

        if (!empty($errors)) {
            $this->setFlash('error', implode(', ', $errors));
            $this->redirectBack();
        }

        // Crea elemento
        $item = NomeModuloModel::create([
            'nome' => $this->input('nome'),
            'descrizione' => $this->input('descrizione')
        ]);

        // Log attivita
        $this->logActivity('nome_modulo', 'create', "Creato elemento #{$item->id}");

        $this->setFlash('success', 'Elemento creato con successo');
        $this->redirect('/nome-modulo');
    }

    /**
     * Form modifica
     */
    public function edit($id)
    {
        $this->requireAuth();

        $item = NomeModuloModel::findOrFail($id);

        $this->render('nome_modulo.edit', [
            'pageTitle' => 'Modifica Elemento',
            'item' => $item
        ]);
    }

    /**
     * Aggiorna elemento
     */
    public function update($id)
    {
        $this->requireAuth();

        $item = NomeModuloModel::findOrFail($id);

        $item->update([
            'nome' => $this->input('nome'),
            'descrizione' => $this->input('descrizione')
        ]);

        $this->logActivity('nome_modulo', 'update', "Aggiornato elemento #{$id}");

        $this->setFlash('success', 'Elemento aggiornato');
        $this->redirect('/nome-modulo');
    }

    /**
     * Elimina elemento
     */
    public function delete($id)
    {
        $this->requireAdmin();

        $item = NomeModuloModel::findOrFail($id);
        $item->delete();

        $this->logActivity('nome_modulo', 'delete', "Eliminato elemento #{$id}");

        $this->setFlash('success', 'Elemento eliminato');
        $this->redirect('/nome-modulo');
    }
}
```

#### Passo 2: Aggiungi le Routes

In `routes/web.php`:

```php
// Nome Modulo Routes
$router->get('/nome-modulo', 'NomeModulo@index');
$router->get('/nome-modulo/create', 'NomeModulo@create');
$router->post('/nome-modulo/store', 'NomeModulo@store');
$router->get('/nome-modulo/{id}', 'NomeModulo@show');
$router->get('/nome-modulo/edit/{id}', 'NomeModulo@edit');
$router->post('/nome-modulo/update/{id}', 'NomeModulo@update');
$router->delete('/nome-modulo/{id}', 'NomeModulo@delete');
```

#### Passo 3: Crea le View

Crea directory `app/views/nome_modulo/` con i file:

- `index.php`: Lista elementi
- `show.php`: Dettaglio elemento
- `create.php`: Form creazione
- `edit.php`: Form modifica

#### Passo 4: Aggiungi Permesso

Se necessario, aggiungi il permesso in `Permission` model e tabella `auth_permissions`.

---

### Come Aggiungere un Nuovo Model Eloquent

#### Passo 1: Crea il File Model

Crea `app/models/NomeModello.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NomeModello extends BaseModel
{
    /**
     * Tabella associata
     */
    protected $table = 'nome_tabella';

    /**
     * Chiave primaria
     */
    protected $primaryKey = 'id';

    /**
     * Auto-increment (false se chiave non numerica)
     */
    public $incrementing = true;

    /**
     * Tipo chiave primaria
     */
    protected $keyType = 'int';

    /**
     * Timestamps automatici (created_at, updated_at)
     */
    public $timestamps = true;

    /**
     * Campi mass-assignable
     */
    protected $fillable = [
        'nome',
        'descrizione',
        'valore',
        'attivo'
    ];

    /**
     * Campi protetti (non mass-assignable)
     */
    protected $guarded = ['id'];

    /**
     * Cast automatici
     */
    protected $casts = [
        'attivo' => 'boolean',
        'valore' => 'float',
        'data_scadenza' => 'datetime',
        'configurazione' => 'array'
    ];

    /**
     * Campi nascosti in serializzazione
     */
    protected $hidden = [
        'password',
        'token_segreto'
    ];

    /**
     * Relazione: has many
     */
    public function dettagli(): HasMany
    {
        return $this->hasMany(Dettaglio::class, 'parent_id');
    }

    /**
     * Relazione: belongs to
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Scope: solo attivi
     */
    public function scopeAttivo($query)
    {
        return $query->where('attivo', true);
    }

    /**
     * Scope: filtro per categoria
     */
    public function scopeByCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    /**
     * Accessor: nome formattato
     */
    public function getNomeCompletoAttribute()
    {
        return strtoupper($this->nome);
    }

    /**
     * Mutator: normalizza nome
     */
    public function setNomeAttribute($value)
    {
        $this->attributes['nome'] = ucfirst(strtolower(trim($value)));
    }

    /**
     * Metodo business logic custom
     */
    public function calcolaValore()
    {
        return $this->valore * 1.22; // Esempio con IVA
    }

    /**
     * Metodo statico helper
     */
    public static function attivi()
    {
        return static::where('attivo', true)->get();
    }
}
```

#### Passo 2: Uso del Model

```php
// Create
$item = NomeModello::create([
    'nome' => 'Test',
    'descrizione' => 'Descrizione',
    'attivo' => true
]);

// Read
$item = NomeModello::find(1);
$items = NomeModello::all();
$attiviCategoria = NomeModello::attivo()->byCategoria(5)->get();

// Update
$item->update(['nome' => 'Nuovo Nome']);

// Delete
$item->delete();

// Query con relazioni
$item = NomeModello::with('dettagli', 'categoria')->find(1);

// Accessors
echo $item->nome_completo; // Usa l'accessor

// Scopes
$attivi = NomeModello::attivo()->get();
```

---

### Come Aggiungere un Widget alla Dashboard

#### Passo 1: Registra Widget in Database

```sql
INSERT INTO widg_available (widget_key, widget_name, widget_icon, widget_description, widget_color, widget_size)
VALUES ('my_widget', 'My Widget', 'fas fa-star', 'Descrizione widget', 'blue', 'medium');
```

#### Passo 2: Aggiungi Rendering in BaseController

In `core/BaseController.php`, nel metodo `renderWidget()`, aggiungi case nello switch:

```php
switch ($widgetKey) {
    // ... altri widget
    case 'my_widget':
        $html .= $this->renderMyWidget($widget, $data);
        break;
}
```

#### Passo 3: Implementa Metodo Rendering

```php
private function renderMyWidget($widget, $data)
{
    $html = '<div class="mt-5">';
    $html .= '<span class="text-sm text-gray-500 dark:text-gray-400">My Widget</span>';
    $html .= '<h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">';
    $html .= '<span class="counter-animate" data-target="' . ($data['count'] ?? 0) . '">0</span>';
    $html .= '</h4>';
    $html .= '</div>';
    return $html;
}
```

#### Passo 4: Fornisci Dati nel HomeController

In `app/controllers/HomeController.php`, metodo `index()`:

```php
case 'my_widget':
    $widgetData = [
        'count' => Model::count()
    ];
    break;
```

---

### Come Generare PDF

Il sistema supporta sia mPDF che TCPDF. Esempio con mPDF:

```php
<?php

use Mpdf\Mpdf;

class ReportController extends BaseController
{
    public function generatePdf($id)
    {
        $this->requireAuth();

        $data = Model::findOrFail($id);

        // Crea istanza mPDF
        $mpdf = new Mpdf([
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20
        ]);

        // Genera HTML
        ob_start();
        include VIEW_PATH . '/reports/template.php';
        $html = ob_get_clean();

        // Scrivi HTML nel PDF
        $mpdf->WriteHTML($html);

        // Output
        $filename = "report_{$id}_" . date('Ymd') . ".pdf";
        $mpdf->Output($filename, 'D'); // 'D' = Download, 'I' = Inline, 'F' = File

        exit;
    }
}
```

Template PDF (`app/views/reports/template.php`):

```php
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Report <?= $data->nome ?></h1>

    <table>
        <thead>
            <tr>
                <th>Campo</th>
                <th>Valore</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Descrizione</td>
                <td><?= htmlspecialchars($data->descrizione) ?></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
```

---

### Come Gestire Upload File

```php
public function uploadFile()
{
    $this->requireAuth();

    if (!isset($_FILES['file'])) {
        $this->setFlash('error', 'Nessun file caricato');
        $this->redirectBack();
    }

    $file = $_FILES['file'];

    // Validazione
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'xlsx'];
    $maxSize = 10 * 1024 * 1024; // 10MB

    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        $this->setFlash('error', 'Tipo file non consentito');
        $this->redirectBack();
    }

    if ($file['size'] > $maxSize) {
        $this->setFlash('error', 'File troppo grande (max 10MB)');
        $this->redirectBack();
    }

    // Nome file univoco
    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $uploadDir = APP_ROOT . '/public/uploads/';
    $filepath = $uploadDir . $filename;

    // Crea directory se non esiste
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Sposta file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Salva riferimento in database
        $upload = Upload::create([
            'filename' => $filename,
            'original_name' => $file['name'],
            'filepath' => '/uploads/' . $filename,
            'filesize' => $file['size'],
            'mime_type' => $file['type'],
            'user_id' => $_SESSION['user_id']
        ]);

        $this->logActivity('uploads', 'create', "Caricato file: {$file['name']}");

        $this->setFlash('success', 'File caricato con successo');
        $this->redirect('/uploads');
    } else {
        $this->setFlash('error', 'Errore durante upload');
        $this->redirectBack();
    }
}
```

---

## Installazione e Configurazione

### Requisiti Sistema

```
PHP >= 8.0
- Estensioni: PDO, pdo_mysql, mbstring, gd, curl, zip, fileinfo
MySQL >= 8.0 o MariaDB >= 10.3
Apache/Nginx con mod_rewrite
Composer >= 2.0
```

### Installazione Passo-Passo

#### 1. Clone Repository

```bash
cd /var/www  # o C:\xampp\htdocs su Windows
git clone [repository-url] coregre
cd coregre
```

#### 2. Installa Dipendenze

```bash
# Produzione
composer install --no-dev --optimize-autoloader

# Sviluppo
composer install
```

#### 3. Configura Environment

```bash
cp .env.example .env
nano .env  # o vim .env
```

Configura `.env`:

```env
# Database
DB_HOST=localhost
DB_NAME=coregre_production
DB_USER=coregre_user
DB_PASS=your_secure_password

# Application
APP_ENV=production
APP_DEBUG=false
APP_NAME="COREGRE"
APP_SUBDIRECTORY=""  # vuoto se in document root, "coregre" se in subdirectory
APP_TIMEZONE=Europe/Rome

# Security
SESSION_LIFETIME=7200
HASH_COST=12
CSRF_TOKEN_LENGTH=32

# Upload
MAX_UPLOAD_SIZE=50M
ALLOWED_EXTENSIONS=jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx
```

#### 4. Crea Database

```sql
CREATE DATABASE coregre_production
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'coregre_user'@'localhost'
IDENTIFIED BY 'your_secure_password';

GRANT ALL PRIVILEGES ON coregre_production.*
TO 'coregre_user'@'localhost';

FLUSH PRIVILEGES;
```

#### 5. Esegui Migrazioni

Le migrazioni sono nella directory `database/migrations/`. Il sistema gestisce automaticamente l'esecuzione in ordine cronologico.

```bash
# Via web: accedi a /migrations (solo admin)
# Via CLI (se disponibile):
php artisan migrate
```

#### 6. Configura Permissions Directory

```bash
# Linux/Mac
chmod -R 755 storage/
chmod -R 755 public/uploads/
chown -R www-data:www-data storage/
chown -R www-data:www-data public/uploads/

# Windows (XAMPP)
# Verifica che Apache abbia permessi scrittura su storage/ e public/uploads/
```

#### 7. Configura Web Server

**Apache (.htaccess)**

Il file `.htaccess` in `public/` gestisce gia il routing. Assicurati che `mod_rewrite` sia abilitato:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**Nginx**

Configurazione esempio:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/coregre/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

#### 8. Setup Cron

```bash
crontab -e

# Aggiungi:
* * * * * php /var/www/coregre/cron.php >> /dev/null 2>&1
```

#### 9. Primo Accesso

Accedi a `http://your-domain.com/login`

Credenziali default (se presenti nel seed):
- Username: admin
- Password: (verifica con amministratore sistema)

IMPORTANTE: Cambia immediatamente la password admin dopo primo accesso.

---

## Development e Debugging

### Environment di Sviluppo

Nel file `.env`:

```env
APP_ENV=development
APP_DEBUG=true
```

### Debug Routes

```
/debug/phpinfo      - Configurazione PHP
/debug/session      - Stato sessione corrente
/debug/routes       - Lista tutte le route registrate
```

### Logs

```bash
# Application logs
tail -f storage/logs/app-$(date +%Y-%m-%d).log

# Cron logs
tail -f storage/logs/cron-$(date +%Y-%m-%d).log
tail -f storage/logs/cron-jobs-$(date +%Y-%m-%d).log

# Web server logs
tail -f /var/log/apache2/error.log      # Apache
tail -f /var/log/nginx/error.log        # Nginx
```

### Database Query Debugging

Eloquent registra automaticamente le query quando `APP_DEBUG=true`. Per debugging dettagliato:

```php
// In controller
use Illuminate\Support\Facades\DB;

// Abilita query log
DB::enableQueryLog();

// Esegui query
$users = User::where('active', 1)->get();

// Visualizza query
dd(DB::getQueryLog());
```

### Performance Profiling

```php
// Misura tempo esecuzione
$start = microtime(true);

// ... codice da profilare ...

$duration = microtime(true) - $start;
error_log("Execution time: {$duration}s");
```

---

## Best Practices

### Sicurezza

1. **Mai committare .env** in version control
2. **Usa sempre prepared statements** (Eloquent lo fa automaticamente)
3. **Valida sempre input utente** con `validate()`
4. **Sanitizza output** con `htmlspecialchars()`
5. **Usa CSRF token** in tutti i form
6. **Implementa rate limiting** su API pubbliche
7. **Log attivita sensibili** con `logActivity()`
8. **Password hash** con `password_hash()` (fatto automaticamente da User model)

### Performance

1. **Eager loading** per evitare N+1 queries:
   ```php
   $items = Model::with('relazione')->get();  // Corretto
   // vs
   $items = Model::all(); // Ogni item carichera la relazione separatamente
   ```

2. **Usa scopes** per query riutilizzabili
3. **Indici database** su colonne frequentemente query
4. **Cache** risultati costosi (implementa sistema cache se necessario)
5. **Ottimizza composer** per production:
   ```bash
   composer install --no-dev --optimize-autoloader --classmap-authoritative
   ```

### Eloquent Best Practices

1. **Mantieni oggetti Eloquent**: Non convertire a array se non necessario
2. **Usa relationships**: Sfrutta `with()`, `has()`, `whereHas()`
3. **Definisci fillable/guarded**: Proteggi da mass assignment
4. **Cast tipi**: Usa `$casts` per conversioni automatiche
5. **Scopes riutilizzabili**: Per logica query comune
6. **Evita query in loop**: Usa eager loading

### Codice Pulito

1. **Nomi descrittivi**: Variabili e metodi chiari
2. **Single Responsibility**: Un metodo = una responsabilità
3. **DRY**: Don't Repeat Yourself
4. **Commenti utili**: Spiega il "perché", non il "cosa"
5. **Validazione input**: Sempre, anche per admin
6. **Error handling**: Try-catch dove appropriato
7. **Type hints**: Usa type declarations PHP 8

### Git Workflow

```bash
# Feature branch
git checkout -b feature/nuovo-modulo

# Commit atomici
git add app/controllers/NuovoModuloController.php
git commit -m "Add NuovoModulo controller"

git add app/models/NuovoModello.php
git commit -m "Add NuovoModello model"

git add routes/web.php
git commit -m "Add routes for nuovo-modulo"

# Push e PR
git push origin feature/nuovo-modulo
```

### Testing

Prima di commit/deploy:

1. **Test manuale** di tutte le funzionalità modificate
2. **Verifica permessi** (utente normale, admin, guest)
3. **Test PJAX** navigation
4. **Test responsive** (mobile, tablet, desktop)
5. **Verifica log** (nessun errore PHP)
6. **Test con dark mode** attivo
7. **Valida HTML/CSS** se modifiche frontend

---

## Troubleshooting Comune

### Problema: 500 Internal Server Error

**Causa**: Errore PHP o configurazione

**Soluzione**:
```bash
# Verifica log
tail -f storage/logs/app-$(date +%Y-%m-%d).log
tail -f /var/log/apache2/error.log

# Abilita debug temporaneamente
APP_DEBUG=true in .env
```

### Problema: Route non trovata (404)

**Causa**: mod_rewrite non abilitato o .htaccess non funzionante

**Soluzione**:
```bash
# Apache
sudo a2enmod rewrite
sudo systemctl restart apache2

# Verifica AllowOverride All in vhost config
```

### Problema: Errore database connection

**Causa**: Credenziali errate o database non esistente

**Soluzione**:
```bash
# Verifica credenziali in .env
# Test connessione MySQL
mysql -u coregre_user -p -h localhost coregre_production
```

### Problema: Permessi file

**Causa**: Apache/PHP non ha permessi scrittura

**Soluzione**:
```bash
chmod -R 755 storage/
chown -R www-data:www-data storage/
```

### Problema: Cron jobs non eseguono

**Causa**: Crontab non configurato o path errato

**Soluzione**:
```bash
# Verifica crontab
crontab -l

# Test manuale
php /path/to/coregre/cron.php

# Verifica log
tail -f storage/logs/cron-$(date +%Y-%m-% d).log
```

---

## Documentazione Aggiuntiva

- **CRON_SYSTEM.md**: Documentazione completa sistema cron
- **MOBILE_API_ARCHITECTURE.md**: Architettura API mobile dettagliata
- **app/views/artisan/documentation.md**: Guida completa Artisan CLI

---

## Supporto e Contributi

Per problemi, bug o richieste di feature, contattare l'amministratore di sistema o aprire issue nel repository interno.

---

## Changelog

### Versione 3.0.0 (2025-09)
- Migrazione completa a Eloquent ORM
- Sistema cron unificato
- API mobile unificate
- Sistema widget dashboard
- Dark mode nativo
- Miglioramenti performance
- Refactoring completo codebase

---

## License

Sistema proprietario per uso interno aziendale.

Copyright 2024-2025 Stefano Solidoro

---

**COREGRE** - Sistema supplementare per sistema ERP Calzaturifici

