# Guida Conversione Models a Eloquent

## Lezioni Apprese dalla Conversione Export Models

### 🏗️ **STRUTTURA BASE MODEL**

```php
<?php
namespace App\Models;

class ExampleModel extends BaseModel
{
    protected $table = 'nome_tabella_esistente';
    protected $primaryKey = 'id'; // Se diverso da 'id'

    // Map timestamp fields to existing columns
    const CREATED_AT = 'data_creazione';
    const UPDATED_AT = 'data_modifica';
    // Oppure const UPDATED_AT = null; se non esiste

    protected $fillable = [
        // TUTTI i campi che devono essere mass-assignable
    ];

    protected $casts = [
        'campo_data' => 'datetime',
        'campo_decimal' => 'decimal:2',
        'campo_boolean' => 'boolean',
        'campo_integer' => 'integer'
    ];
}
```

### ⚠️ **ERRORI CRITICI DA EVITARE**

#### 1. **Timestamp Mapping Obbligatorio**
```php
// ❌ PROBLEMA: Eloquent cerca created_at/updated_at
// Database ha: data_creazione/data_modifica

// ✅ SOLUZIONE: Mappare sui campi esistenti
const CREATED_AT = 'data_creazione';
const UPDATED_AT = 'data_modifica';

// ✅ Se manca updated_at, disabilitarlo
const UPDATED_AT = null;
```

#### 2. **Fillable Incompleto**
```php
// ❌ PROBLEMA: Campi mancanti non sono mass-assignable
protected $fillable = ['nome', 'email']; // Manca 'telefono'

// ✅ SOLUZIONE: Includere TUTTI i campi necessari
protected $fillable = [
    'nome', 'email', 'telefono', 'indirizzo',
    'voce_1', 'peso_1', 'voce_2', 'peso_2', // etc...
];
```

#### 3. **Nomi Tabella Errati**
```php
// ❌ PROBLEMA: Assume nome tabella plurale inglese
// Laravel cerca 'users', ma DB ha 'utenti'

// ✅ SOLUZIONE: Specificare nome tabella esistente
protected $table = 'utenti'; // Non 'users'
```

### 🔗 **RELATIONSHIPS PATTERN**

#### Relazioni Comuni:
```php
// HasOne (1:1)
public function piede() {
    return $this->hasOne(ExportDocumentFooter::class, 'id_documento');
}

// HasMany (1:N)
public function articoli() {
    return $this->hasMany(ExportArticle::class, 'id_documento');
}

// BelongsTo (N:1)
public function terzista() {
    return $this->belongsTo(ExportTerzista::class, 'id_terzista');
}

// Alias per stesso relationship
public function mancanti() {
    return $this->hasMany(ExportMissingData::class, 'id_documento');
}
```

### 📊 **CAST TYPES ESSENZIALI**

```php
protected $casts = [
    // Date/Time
    'data' => 'date',
    'data_creazione' => 'datetime',

    // Numerici
    'prezzo' => 'decimal:2',
    'quantita' => 'decimal:3',
    'peso' => 'decimal:2',
    'numero_colli' => 'integer',

    // Boolean
    'is_active' => 'boolean',
    'first_boot' => 'boolean',

    // Campi multipli con pattern
    'peso_1' => 'decimal:2',
    'peso_2' => 'decimal:2',
    // ... fino a peso_15
];
```

### 🎯 **SCOPE METHODS UTILI**

```php
// Scope per stati comuni
public function scopeOpen($query) {
    return $query->where('stato', 'Aperto');
}

public function scopeClosed($query) {
    return $query->where('stato', 'Chiuso');
}

// Scope per date
public function scopeRecent($query, $days = 30) {
    $date = date('Y-m-d', strtotime("-{$days} days"));
    return $query->where('data', '>=', $date);
}

// Scope per range
public function scopeDateRange($query, $from, $to) {
    return $query->whereBetween('data', [$from, $to]);
}
```

### 💼 **BUSINESS LOGIC METHODS**

```php
// Checker methods
public function isOpen() {
    return $this->stato === 'Aperto';
}

public function hasArticles() {
    return $this->articoli()->count() > 0;
}

// Formatter methods
public function getFormattedDateAttribute() {
    return $this->data ? $this->data->format('d/m/Y') : '';
}

// Complex calculations
public function getTotalValueAttribute() {
    return $this->articoli->sum(function($art) {
        return $art->qta_reale * $art->prezzo_unitario;
    });
}
```

### 📋 **CHECKLIST CONVERSIONE MODEL**

#### Setup Base:
- [ ] Estendere `BaseModel`
- [ ] Impostare `$table` con nome tabella esistente
- [ ] Mappare timestamp con `CREATED_AT`/`UPDATED_AT`
- [ ] Definire `$fillable` con TUTTI i campi necessari
- [ ] Impostare `$casts` per tipi di dato corretti

#### Relationships:
- [ ] Definire tutte le relazioni (hasOne, hasMany, belongsTo)
- [ ] Verificare foreign key names
- [ ] Creare alias se necessario (es: `mancanti()` per `datiMancanti()`)

#### Business Logic:
- [ ] Aggiungere scope methods utili
- [ ] Implementare checker methods (isOpen, hasData, etc.)
- [ ] Creare accessor per formatting
- [ ] Aggiungere mutator se necessario

#### Testing:
- [ ] Verificare CRUD operations
- [ ] Testare relationships
- [ ] Controllare casting dei tipi
- [ ] Validare business logic methods

### 🔍 **PATTERN SPECIALI**

#### Campi Multipli Numerati:
```php
// Per tabelle con voce_1...voce_15, peso_1...peso_15
protected $fillable = [
    // Metodo efficiente per campi numerati
    ...array_map(fn($i) => "voce_{$i}", range(1, 15)),
    ...array_map(fn($i) => "peso_{$i}", range(1, 15)),
];

protected $casts = [
    // Cast per tutti i pesi
    ...array_combine(
        array_map(fn($i) => "peso_{$i}", range(1, 15)),
        array_fill(0, 15, 'decimal:2')
    ),
];
```

#### Tabelle Legacy senza updated_at:
```php
const CREATED_AT = 'data_creazione';
const UPDATED_AT = null; // Disabilita updated_at

// O completamente disabilita timestamps
public $timestamps = false;
```