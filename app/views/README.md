# Guida Conversione Views a Eloquent

## Lezioni Apprese dalla Conversione Export Views

### 🚫 **ERRORE PRINCIPALE: Array vs Objects**

#### Il Controller NON deve convertire in array:
```php
// ❌ SBAGLIATO nel Controller
$data = Model::all()->toArray(); // Annulla i benefici Eloquent
$this->view('template', ['items' => $data]);

// ✅ CORRETTO nel Controller
$data = Model::all(); // Mantieni oggetti Eloquent
$this->view('template', ['items' => $data]);
```

### 🔄 **CONVERSIONE SINTASSI NELLE VIEW**

#### Da Array a Object:
```php
// ❌ Sintassi Array (PHP tradizionale)
<?= htmlspecialchars($documento['id']) ?>
<?= htmlspecialchars($terzista['ragione_sociale']) ?>
<?= $articolo['qta_reale'] ?>

// ✅ Sintassi Object (Eloquent)
<?= htmlspecialchars($documento->id) ?>
<?= htmlspecialchars($terzista->ragione_sociale) ?>
<?= $articolo->qta_reale ?>
```

#### Proprietà Dinamiche:
```php
// ❌ Array con chiavi dinamiche
$piede['voce_' . $i]
$piede['peso_' . $i]

// ✅ Object con proprietà dinamiche
$piede->{'voce_' . $i}
$piede->{'peso_' . $i}
```

### 🔍 **RELATIONSHIPS NELLE VIEW**

#### Accesso Diretto alle Relazioni:
```php
// ✅ Eloquent relationships
<?= $documento->terzista->ragione_sociale ?>
<?= $documento->piede->n_colli ?>
<?= $documento->articoli->count() ?>

// ✅ Null-safe access
<?= $documento->piede?->n_colli ?? 'N/A' ?>
<?= htmlspecialchars($terzista->indirizzo_2 ?? '') ?>
```

#### Loop su Collections:
```php
// ✅ Loop su Collection Eloquent
<?php foreach ($documento->articoli as $articolo): ?>
    <td><?= $articolo->codice_articolo ?></td>
    <td><?= $articolo->descrizione ?></td>
    <td><?= $articolo->qta_reale ?></td>
<?php endforeach; ?>
```

### 📊 **COLLECTION METHODS NELLE VIEW**

#### Sostituire array_filter:
```php
// ❌ array_filter su oggetti
$normali = array_filter($articoli, fn($art) => $art['is_mancante'] == 0);

// ✅ Collection filter
$normali = $articoli->filter(function($art) {
    return ($art->is_mancante ?? 0) == 0;
});
```

#### Métodi Collection utili:
```php
// Count items
<?= $articoli->count() ?>

// Filter and count
<?= $articoli->where('is_mancante', 0)->count() ?>

// Sum values
<?= $articoli->sum('qta_reale') ?>

// Check existence
<?php if ($articoli->isEmpty()): ?>
    <p>Nessun articolo</p>
<?php endif; ?>
```

### 🎨 **PATTERN COMUNI NELLE VIEW**

#### Status Checking:
```php
// ✅ Object properties
<?php if ($documento->stato == 'Aperto'): ?>
    <span class="badge-open">Aperto</span>
<?php else: ?>
    <span class="badge-closed">Chiuso</span>
<?php endif; ?>
```

#### Conditional Display:
```php
// ✅ Null-safe object access
<?php if (!empty($terzista->indirizzo_2)): ?>
    <p><?= htmlspecialchars($terzista->indirizzo_2) ?></p>
<?php endif; ?>

<?php if ($documento->piede?->n_colli): ?>
    <p>Colli: <?= $documento->piede->n_colli ?></p>
<?php endif; ?>
```

#### Calculations:
```php
// ✅ Object properties in calculations
<?php
$subtotal = round(($articolo->qta_reale ?? 0) * ($articolo->prezzo_unitario ?? 0), 2);
$qta_mancante = ($articolo->qta_originale ?? 0) - ($articolo->qta_reale ?? 0);
?>
```

### 🛠️ **FUNZIONI HELPER NELLE VIEW**

#### Aggiornare helper functions:
```php
// ❌ Helper che aspettano array
function getUniqueCodes($articoli) {
    foreach ($articoli as $articolo) {
        if (!in_array($articolo['voce_doganale'], $codes)) {
            $codes[] = $articolo['voce_doganale'];
        }
    }
}

// ✅ Helper che funzionano con oggetti
function getUniqueCodes($articoli) {
    foreach ($articoli as $articolo) {
        if (!in_array($articolo->voce_doganale, $codes)) {
            $codes[] = $articolo->voce_doganale;
        }
    }
}
```

### ⚡ **PERFORMANCE E OPTIMIZATION**

#### Eager Loading:
```php
// ✅ Controller deve usare with() per evitare N+1
$documenti = ExportDocument::with(['terzista', 'piede', 'articoli'])->get();

// ✅ View può accedere senza query aggiuntive
<?php foreach ($documenti as $doc): ?>
    <?= $doc->terzista->ragione_sociale ?> <!-- No extra query -->
<?php endforeach; ?>
```

#### Avoid Multiple Queries:
```php
// ❌ Multiple queries nella view
<?php foreach ($documenti as $doc): ?>
    <?= $doc->articoli()->count() ?> <!-- Query per ogni documento -->
<?php endforeach; ?>

// ✅ Pre-load con withCount
// Nel controller:
$documenti = ExportDocument::withCount('articoli')->get();

// Nella view:
<?php foreach ($documenti as $doc): ?>
    <?= $doc->articoli_count ?> <!-- No query -->
<?php endforeach; ?>
```

### 🔧 **FORM HANDLING**

#### Form Values da Oggetti:
```php
// ✅ Form edit con oggetti Eloquent
<input type="text"
       name="ragione_sociale"
       value="<?= htmlspecialchars($terzista->ragione_sociale ?? '') ?>">

<textarea name="autorizzazione"><?= htmlspecialchars($terzista->autorizzazione ?? '') ?></textarea>

<select name="stato">
    <option value="Aperto" <?= $documento->stato === 'Aperto' ? 'selected' : '' ?>>Aperto</option>
    <option value="Chiuso" <?= $documento->stato === 'Chiuso' ? 'selected' : '' ?>>Chiuso</option>
</select>
```

### 📋 **CHECKLIST CONVERSIONE VIEW**

#### Ricerca e Sostituzione:
- [ ] `$var['key']` → `$var->key`
- [ ] `$var['dynamic_' . $i]` → `$var->{'dynamic_' . $i}`
- [ ] `array_filter($collection, ...)` → `$collection->filter(...)`
- [ ] `count($array)` → `$collection->count()`

#### Verifica Relationships:
- [ ] Accesso alle relazioni con `->relation`
- [ ] Uso di null-safe operator `?->` dove appropriato
- [ ] Nessuna query N+1 nei loop

#### Form e Input:
- [ ] Valori form da proprietà oggetti
- [ ] Controlli condizionali con proprietà oggetti
- [ ] Escape corretto con `htmlspecialchars()`

#### Functions Helper:
- [ ] Aggiornare helper per accettare oggetti
- [ ] Testare calcoli e aggregazioni
- [ ] Verificare logica di business

### 🚨 **ERRORI COMUNI**

#### 1. **Mixed Array/Object Access**
```php
// ❌ Inconsistent - parte array, parte object
if ($doc['stato'] == 'Aperto' && $doc->terzista) // Confusing

// ✅ Consistent - tutto object
if ($doc->stato == 'Aperto' && $doc->terzista) // Clear
```

#### 2. **Accessing Unloaded Relations**
```php
// ❌ N+1 query problem
<?php foreach ($documenti as $doc): ?>
    <?= $doc->terzista->nome ?> <!-- Query ogni volta -->
<?php endforeach; ?>

// ✅ Pre-load in controller
$documenti = Document::with('terzista')->get();
```

#### 3. **Null Reference Errors**
```php
// ❌ Può causare errore se piede è null
<?= $documento->piede->n_colli ?>

// ✅ Null-safe access
<?= $documento->piede?->n_colli ?? 'N/A' ?>
```

### 💡 **BEST PRACTICES SUMMARY**

1. **Mantieni oggetti Eloquent** - Non convertire in array
2. **Usa eager loading** - Pre-carica relazioni nel controller
3. **Null-safe access** - Usa `??` e `?->` per evitare errori
4. **Collection methods** - Sostituisci array functions con Collection
5. **Consistenza** - Usa sempre sintassi oggetti
6. **Performance** - Evita query nelle view con withCount/with