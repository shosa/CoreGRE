# Guida Conversione Controllers a Eloquent

## Lezioni Apprese dalla Conversione ExportController

### 🚫 **ERRORI DA EVITARE**

#### 1. **NON convertire oggetti Eloquent in array**
```php
// ❌ SBAGLIATO - Annulla i benefici di Eloquent
$data = Model::all()->toArray();

// ✅ CORRETTO - Mantieni oggetti Eloquent
$data = Model::all();
```

#### 2. **NON usare funzioni Laravel in standalone**
```php
// ❌ SBAGLIATO - now() non esiste fuori Laravel
$date = now()->subDays(30);

// ✅ CORRETTO - Usa funzioni PHP standard
$date = date('Y-m-d', strtotime('-30 days'));
```

#### 3. **NON usare DB facade in sistema custom**
```php
// ❌ SBAGLIATO - \DB:: non esiste nel nostro sistema
\DB::beginTransaction();

// ✅ CORRETTO - Usa il database handler custom
$this->db->beginTransaction();
```

### ✅ **BEST PRACTICES**

#### 1. **Relationships e Eager Loading**
```php
// ✅ Usa with() per evitare N+1 queries
$documento = ExportDocument::with(['terzista', 'piede', 'articoli', 'mancanti'])->find($id);

// ✅ Accedi alle relazioni direttamente
$terzista = $documento->terzista;
$articoli = $documento->articoli;
```

#### 2. **Query Builder Eloquent**
```php
// ✅ Sostituisci SQL raw con Query Builder
// Prima:
$stmt = $this->db->query("SELECT * FROM exp_documenti WHERE stato = ? ORDER BY data DESC", ['Aperto']);

// Dopo:
$documents = ExportDocument::where('stato', 'Aperto')->orderBy('data', 'desc')->get();
```

#### 3. **Filtri e Scopes**
```php
// ✅ Usa Collection methods per filtri
$articoliNormali = $articoli->filter(function($art) {
    return ($art->is_mancante ?? 0) == 0;
});

// ✅ Definisci scope nei modelli per query comuni
public function scopeOpen($query) {
    return $query->where('stato', 'Aperto');
}
```

#### 4. **Transazioni Semplificate**
```php
// ✅ Per operazioni semplici, non servono transazioni esplicite
$documento->stato = 'Chiuso';
$documento->save(); // Eloquent gestisce automaticamente

// ✅ Solo per operazioni complesse multi-tabella
$this->db->beginTransaction();
try {
    // Multiple operations...
    $this->db->commit();
} catch (Exception $e) {
    $this->db->rollback();
}
```

### 🔄 **PATTERN DI CONVERSIONE**

#### Controllers CRUD Pattern:
```php
// Index
public function index() {
    $items = Model::with('relations')->paginate(20);
    $this->view('view', compact('items'));
}

// Show
public function show($id) {
    $item = Model::with('relations')->find($id);
    if (!$item) {
        $this->setFlash('error', 'Item non trovato');
        $this->redirect('/items');
        return;
    }
    $this->view('show', compact('item'));
}

// Store
public function store() {
    $data = $this->validateInput();
    $item = Model::create($data);
    $this->setFlash('success', 'Item creato');
    $this->redirect('/items');
}

// Update
public function update($id) {
    $item = Model::find($id);
    if ($item) {
        $item->fill($this->validateInput());
        $item->save();
        $this->setFlash('success', 'Item aggiornato');
    }
    $this->redirect('/items');
}
```

### 📝 **CHECKLIST CONVERSIONE CONTROLLER**

- [ ] Sostituire query SQL raw con Eloquent
- [ ] Rimuovere tutti i `->toArray()`
- [ ] Convertire `\DB::` in `$this->db`
- [ ] Sostituire `now()` con `date()`
- [ ] Usare `with()` per eager loading
- [ ] Implementare proper error handling
- [ ] Mantenere oggetti Eloquent per le view
- [ ] Testare tutte le operazioni CRUD

### 🐛 **DEBUGGING COMUNE**

#### Errore: "Call to undefined relationship"
- **Causa**: Relazione non definita nel model
- **Soluzione**: Aggiungere metodo relationship nel model

#### Errore: "Unknown column 'updated_at'"
- **Causa**: Timestamp Eloquent non mappati
- **Soluzione**: Mappare timestamp o disabilitarli nel model

#### Errore: "Attempt to read property on array"
- **Causa**: View ancora usa sintassi array
- **Soluzione**: Convertire view a sintassi oggetti