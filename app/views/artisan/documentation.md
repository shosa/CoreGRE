# 🔧 WEBGRE Migration & Model System v2.0

Sistema professionale per gestione migrations, schema builder e generazione modelli per WEBGRE Framework.

## 📋 Indice

1. [Panoramica](#-panoramica)
2. [Schema Builder](#-schema-builder)
3. [Migration System](#-migration-system)
4. [Model Generator](#-model-generator)
5. [CLI Interface (Artisan)](#-cli-interface-artisan)
6. [Esempi Pratici](#-esempi-pratici)
7. [Configurazione](#-configurazione)
8. [Best Practices](#-best-practices)

---

## 🎯 Panoramica

Il sistema WEBGRE v2.0 introduce un approccio moderno e professionale per la gestione del database, ispirato ai migliori framework come Laravel ma completamente integrato con l'architettura WEBGRE esistente.

### ✨ Caratteristiche Principali

- **Schema Builder Fluent** - Syntax moderna per definire strutture database
- **Migration System Avanzato** - Versionamento database professionale
- **Model Generator** - Generazione automatica modelli da tabelle esistenti
- **CLI Interface** - Tool da linea di comando per tutte le operazioni
- **Rollback Support** - Rollback sicuro delle migrazioni
- **Backup Automatico** - Backup automatici prima di operazioni critiche

### 🏗️ Architettura

```
core/
├── Schema.php           # Schema Builder fluent
├── Migration.php        # Sistema migrations
├── ModelGenerator.php   # Generatore modelli
└── stubs/              # Template per code generation

database/
├── migrations/         # File migrations
└── backups/           # Backup automatici

app/models/            # Modelli generati
artisan               # CLI interface
```

---

## 🔧 Schema Builder

Il Schema Builder fornisce un'interfaccia fluent per definire e modificare strutture database.

### Creare Tabelle

```php
Schema::create('users', function($table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->timestamps();
    $table->softDeletes();
});
```

### Modificare Tabelle

```php
Schema::table('users', function($table) {
    $table->string('phone')->nullable();
    $table->dropColumn('old_column');
    $table->index(['email', 'name']);
});
```

### Tipi di Colonne Disponibili

#### Numerici
```php
$table->id();                    // AUTO_INCREMENT PRIMARY KEY
$table->bigInteger('user_id');   // BIGINT
$table->integer('count');        // INT
$table->smallInteger('type');    // SMALLINT
$table->tinyInteger('status');   // TINYINT
$table->decimal('price', 8, 2);  // DECIMAL(8,2)
$table->float('rate', 8, 2);     // FLOAT(8,2)
$table->double('amount');        // DOUBLE
```

#### Stringhe
```php
$table->string('name');          // VARCHAR(255)
$table->string('code', 10);      // VARCHAR(10)
$table->text('description');     // TEXT
$table->mediumText('content');   // MEDIUMTEXT
$table->longText('data');        // LONGTEXT
```

#### Date/Time
```php
$table->date('birth_date');      // DATE
$table->dateTime('created_at');  // DATETIME
$table->timestamp('updated_at'); // TIMESTAMP
$table->timestamps();            // created_at + updated_at
```

#### Speciali
```php
$table->boolean('is_active');    // TINYINT(1)
$table->json('metadata');        // JSON
$table->enum('status', ['active', 'inactive']);
```

### Modificatori di Colonna

```php
$table->string('email')->nullable();           // NULL
$table->string('name')->default('Guest');      // DEFAULT
$table->integer('id')->autoIncrement();        // AUTO_INCREMENT
$table->integer('amount')->unsigned();         // UNSIGNED
$table->string('slug')->unique();             // UNIQUE KEY
$table->timestamp('updated_at')->onUpdate('CURRENT_TIMESTAMP');
$table->string('notes')->comment('User notes'); // COMMENT
```

### Indici

```php
// Index semplice
$table->index('email');
$table->index(['user_id', 'created_at'], 'user_date_index');

// Unique index
$table->unique('email');
$table->unique(['user_id', 'type']);

// Primary key composita
$table->primary(['user_id', 'role_id']);
```

### Foreign Keys

```php
// Syntax base
$table->foreign('user_id')->references('id')->on('users');

// Con azioni
$table->foreign('user_id')
      ->references('id')
      ->on('users')
      ->onDelete('CASCADE')
      ->onUpdate('RESTRICT');

// Shorthand
$table->foreignId('user_id')->cascadeOnDelete();
```

### Metodi di Utility

```php
// Verifiche
Schema::hasTable('users');              // true/false
Schema::hasColumn('users', 'email');    // true/false

// Lista tabelle
$tables = Schema::getAllTables();       // ['users', 'posts', ...]

// Struttura tabella
$structure = Schema::getTableStructure('users');

// Operazioni tabelle
Schema::rename('old_table', 'new_table');
Schema::drop('table_name');
Schema::dropIfExists('table_name');
```

---

## 🚀 Migration System

Sistema completo per versionamento e gestione cambiamenti database.

### Creare Migrazioni

#### Via CLI (Consigliato)
```bash
# Migrazione base
php artisan make:migration create_users_table

# Con template
php artisan make:migration create_posts_table --template=create_table --table=posts

# Modificare tabella esistente
php artisan make:migration add_phone_to_users --template=alter_table --table=users
```

#### Via Controller (Web Interface)
Accedi a `/migrations` nell'interfaccia web per usare il builder grafico.

### Struttura Migration

```php
<?php
/**
 * Migration: Create Users Table
 * Created: 2025-01-26 10:30:00
 */

return [
    'up' => function($db) {
        Schema::create('users', function($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
    },

    'down' => function($db) {
        Schema::dropIfExists('users');
    }
];
```

### Eseguire Migrazioni

```bash
# Esegui tutte le migrazioni pending
php artisan migrate

# Dry run (visualizza cosa farebbe)
php artisan migrate --pretend

# Esegui solo N migrazioni
php artisan migrate --step=3

# Stato migrazioni
php artisan migrate:status
```

### Rollback

```bash
# Rollback ultimo batch
php artisan migrate:rollback

# Rollback N steps
php artisan migrate:rollback --step=2

# Rollback tutte le migrazioni
php artisan migrate:reset

# Fresh: drop tutto e ricostruisci
php artisan migrate:fresh
```

### Templates Disponibili

1. **create_table** - Crea nuova tabella
2. **alter_table** - Modifica tabella esistente
3. **add_column** - Aggiungi colonna
4. **drop_column** - Rimuovi colonna
5. **add_index** - Crea indice
6. **drop_index** - Rimuovi indice
7. **blank** - Migrazione personalizzata

### Backup Automatici

Il sistema crea automaticamente backup prima di:
- `migrate:fresh`
- Operazioni critiche
- Su richiesta con `php artisan db:backup`

---

## 🎨 Model Generator

Sistema per generazione automatica di modelli da tabelle esistenti o nuove.

### Creare Modelli

#### Nuovo Modello + Migrazione
```bash
# Crea modello e migrazione
php artisan make:model User --migration

# Con tabella personalizzata
php artisan make:model BlogPost --table=posts --migration
```

#### Da Tabella Esistente
```bash
# Genera modello da tabella esistente
php artisan model:generate users

# Con nome personalizzato
php artisan model:generate user_profiles --name=UserProfile

# Forza sovrascrittura
php artisan model:generate users --force
```

#### Generazione Bulk
```bash
# Genera modelli per tutte le tabelle
php artisan model:generate-all

# Lista modelli esistenti
php artisan model:list
```

### Struttura Modello Generato

```php
<?php
/**
 * User Model
 * Auto-generated from table: users
 *
 * @author WEBGRE Generator
 * @created 2025-01-26 10:30:00
 */

class User extends BaseModel
{
    /**
     * The table associated with the model
     */
    protected $table = 'users';

    /**
     * The primary key for the model
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'name',
        'email',
        'phone'
    ];

    /**
     * The attributes that should be hidden
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    // ================= RELATIONSHIPS =================

    /**
     * Relationship with Role
     */
    public function role()
    {
        return $this->belongsTo('Role', 'role_id', 'id');
    }
}
```

### Auto-Detection Features

Il generator analizza automaticamente:
- **Fillable attributes** - Esclusi auto-increment, timestamps, password
- **Hidden attributes** - Password, tokens, etc.
- **Casts** - Boolean per tinyint(1), array per JSON, etc.
- **Relationships** - Basato su foreign keys
- **Primary key** - Rilevata automaticamente

---

## ⚡ CLI Interface (Artisan)

Tool da linea di comando completo per tutte le operazioni.

### Installazione CLI

Il file `artisan` è già incluso. Renderlo eseguibile:

```bash
chmod +x artisan
```

### Comandi Disponibili

#### Migration Commands
```bash
php artisan make:migration <name>               # Crea migrazione
php artisan migrate                             # Esegui migrazioni
php artisan migrate:rollback                    # Rollback ultimo batch
php artisan migrate:reset                       # Rollback tutto
php artisan migrate:fresh                       # Drop + ricostruisci tutto
php artisan migrate:status                      # Stato migrazioni
```

#### Model Commands
```bash
php artisan make:model <name>                   # Crea modello
php artisan model:generate <table>              # Genera da tabella
php artisan model:generate-all                  # Genera tutti
php artisan model:list                          # Lista modelli
```

#### Database Commands
```bash
php artisan db:backup                           # Backup database
php artisan schema:dump                         # Mostra schema
php artisan schema:dump --table=users           # Schema tabella specifica
```

#### Utility Commands
```bash
php artisan cache:clear                         # Pulisci cache
php artisan help                                # Mostra help
php artisan list                                # Lista comandi
```

### Esempi Uso CLI

```bash
# Workflow completo nuovo modulo
php artisan make:model Product --migration
php artisan migrate
php artisan model:list

# Generazione da DB esistente
php artisan model:generate-all --force
php artisan migrate:status

# Rollback sicuro
php artisan migrate:rollback --pretend
php artisan migrate:rollback
```

---

## 🎯 Esempi Pratici

### Esempio 1: Nuovo Modulo E-commerce

```bash
# 1. Crea modello Product con migrazione
php artisan make:model Product --migration

# 2. Modifica la migrazione generata
# File: database/migrations/2025_01_26_103000_create_products_table.php
```

```php
return [
    'up' => function($db) {
        Schema::create('products', function($table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    },

    'down' => function($db) {
        Schema::dropIfExists('products');
    }
];
```

```bash
# 3. Esegui migrazione
php artisan migrate

# 4. Il modello Product.php è già stato creato!
```

### Esempio 2: Modifica Tabella Esistente

```bash
# 1. Crea migrazione per aggiungere colonne
php artisan make:migration add_social_login_to_users --template=alter_table --table=users
```

```php
return [
    'up' => function($db) {
        Schema::table('users', function($table) {
            $table->string('google_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('avatar_url')->nullable();
            $table->timestamp('last_login_at')->nullable();

            $table->index(['google_id']);
            $table->index(['facebook_id']);
        });
    },

    'down' => function($db) {
        Schema::table('users', function($table) {
            $table->dropColumn(['google_id', 'facebook_id', 'avatar_url', 'last_login_at']);
        });
    }
];
```

### Esempio 3: Sistema Permissions

```bash
# 1. Crea tabelle permissions
php artisan make:migration create_permission_system
```

```php
return [
    'up' => function($db) {
        // Roles table
        Schema::create('roles', function($table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Permissions table
        Schema::create('permissions', function($table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Pivot table role_permissions
        Schema::create('role_permissions', function($table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
        });

        // Add role_id to users
        Schema::table('users', function($table) {
            $table->foreignId('role_id')->nullable()->constrained();
        });
    },

    'down' => function($db) {
        Schema::table('users', function($table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
];
```

```bash
# 2. Esegui e genera modelli
php artisan migrate
php artisan model:generate roles
php artisan model:generate permissions
php artisan model:generate role_permissions
```

---

## ⚙️ Configurazione

### Database Connection

Il sistema usa la configurazione esistente di WEBGRE in `core/config.php`:

```php
// Assicurati che queste costanti siano definite
define('DB_HOST', 'localhost');
define('DB_NAME', 'webgre3');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Directories

Il sistema crea automaticamente le directory necessarie:

```
database/
├── migrations/     # File migrations
├── backups/       # Backup automatici
└── seeds/         # (futuro) Database seeding

app/models/        # Modelli generati

core/stubs/        # Template per generazione codice
```

### Personalizzazione Stubs

Puoi personalizzare i template in `core/stubs/`:

- `model.stub` - Template modello base
- `model_from_table.stub` - Template modello da tabella esistente

### Integration con BaseController

Per integrare con i controller esistenti:

```php
// In un controller
public function __construct() {
    parent::__construct();
    $this->migration = new Migration($this->db);
    $this->modelGenerator = new ModelGenerator($this->db);
}
```

---

## 🏆 Best Practices

### Naming Conventions

#### Migrazioni
```bash
# ✅ Buoni nomi
create_users_table
add_email_to_users
create_products_table
add_index_to_posts

# ❌ Nomi cattivi
migration1
update_stuff
new_changes
```

#### Modelli
```bash
# ✅ Classe singolare, tabella plurale
User -> users
Product -> products
OrderItem -> order_items

# ❌ Incongruenti
Users -> users (classe dovrebbe essere User)
product -> products (classe dovrebbe essere Product)
```

### Schema Design

#### Usa i tipi giusti
```php
// ✅ Corretto
$table->boolean('is_active');           // Per true/false
$table->decimal('price', 10, 2);        // Per valori monetari
$table->timestamp('created_at');        // Per datetime

// ❌ Scorretto
$table->integer('is_active');           // Usa boolean
$table->float('price');                 // Può perdere precisione
$table->string('created_at');           // Usa timestamp
```

#### Foreign Keys
```php
// ✅ Con constraints
$table->foreignId('user_id')->constrained()->cascadeOnDelete();

// ✅ Naming esplicito
$table->foreign('author_id')->references('id')->on('users');

// ❌ Senza constraints
$table->integer('user_id');             // No referential integrity
```

### Migration Safety

#### Sempre testare rollback
```bash
# Test del flusso completo
php artisan migrate
php artisan migrate:rollback
php artisan migrate
```

#### Backup prima di operazioni critiche
```bash
# Backup manuale
php artisan db:backup

# Fresh sempre fa backup automatico
php artisan migrate:fresh  # Crea backup automaticamente
```

#### Usare pretend per verificare
```bash
# Vedi cosa farebbe senza eseguire
php artisan migrate --pretend
php artisan migrate:rollback --pretend
```

### Performance

#### Indici appropriati
```php
// ✅ Indici su colonne per query frequenti
$table->index('email');                 // Per login
$table->index(['user_id', 'created_at']); // Per listing utente
$table->index('slug');                  // Per SEO URLs

// ❌ Troppi indici
$table->index('description');           // TEXT columns - costoso
```

#### Chunking per migrazioni grandi
```php
// Per operazioni su molti record
return [
    'up' => function($db) {
        // Invece di UPDATE massivo, usa chunking se necessario
        $db->execute("UPDATE products SET status = 'active' WHERE status IS NULL");
    }
];
```

### Maintenance

#### Cleanup periodico
```bash
# Cleanup backup vecchi (manuale)
find database/backups -name "*.sql" -mtime +30 -delete

# Verifica integrità migrazioni
php artisan migrate:status
```

#### Monitoring
- Monitora tempo di esecuzione migrazioni (`execution_time` in tabella migrations)
- Controlla dimensioni backup
- Verifica foreign key integrity

---

## 🔍 Troubleshooting

### Problemi Comuni

#### Migration non trova Database
```bash
Error: Database connection failed
```
**Soluzione**: Verifica configurazione in `core/config.php`

#### Permission negata su artisan
```bash
Permission denied: ./artisan
```
**Soluzione**: `chmod +x artisan`

#### Migration già eseguita
```bash
Error: Migration already executed
```
**Soluzione**: Controlla `php artisan migrate:status`

#### Rollback fallisce
```bash
Error: Cannot rollback
```
**Soluzione**:
1. Verifica che il metodo `down` sia implementato
2. Controlla foreign key constraints
3. Usa `--pretend` per debug

### Debug Migration

#### Abilita logging SQL
```php
// In migration, temporaneamente
echo "Executing: CREATE TABLE...\n";
```

#### Controlla tabella migrations
```sql
SELECT * FROM migrations ORDER BY executed_at DESC;
```

#### Verifica checksum
Il sistema calcola SHA256 dei file migration per rilevare modifiche post-esecuzione.

### Recovery

#### Ripristino da backup
```bash
# Ripristina backup
mysql -u root -p webgre3 < database/backups/backup_2025_01_26_103000.sql
```

#### Reset completo
```bash
# ATTENZIONE: Cancella tutto!
php artisan migrate:fresh
```

#### Repair migration tracking
```sql
-- Se tabella migrations è corrotta
DROP TABLE migrations;
-- Poi riesegui prima migrazione
```

---

## 📞 Supporto

Per supporto tecnico o domande sul sistema:

1. **Documentazione**: Consulta questo file
2. **Logs**: Controlla error_log PHP
3. **Debug**: Usa `--pretend` per testare operazioni
4. **Backup**: Sempre disponibili in `database/backups/`

---

## 🎉 Conclusione

Il sistema WEBGRE Migration & Model v2.0 fornisce un framework robusto e professionale per la gestione del database, mantenendo la semplicità d'uso tipica di WEBGRE.

**Vantaggi chiave:**
- ✅ **Versionamento Database** - Tracciamento completo modifiche
- ✅ **Schema Builder Fluent** - Syntax moderna e leggibile
- ✅ **Auto-generation** - Modelli generati automaticamente
- ✅ **CLI Powerful** - Tool completo da linea di comando
- ✅ **Backup Safety** - Backup automatici e rollback sicuri
- ✅ **Integration Ready** - Integrato con l'architettura WEBGRE esistente

Il sistema è pronto per essere usato in produzione e scala facilmente con la crescita del progetto.

**Happy coding! 🚀**