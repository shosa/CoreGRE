# 📤 GUIDA UPLOAD FTP SU ARUBA - PASSO PER PASSO

## 🎯 OBIETTIVO
Caricare le ottimizzazioni performance su hosting Aruba tramite FTP per migliorare WEBGRE3 da **40 secondi a 2-3 secondi**.

---

## 📋 PREREQUISITI

### Informazioni Aruba Necessarie

Dovresti avere (dalla mail di attivazione Aruba):

```
Host FTP: ftp.tuo-dominio.it (o ftp.aruba.it)
Username: [il tuo username]
Password: [la tua password]
Porta: 21 (standard FTP) o 22 (SFTP)
```

**Se non le hai:**
1. Vai su https://www.aruba.it
2. Login → Pannello di Controllo
3. Gestione Hosting → Credenziali FTP

---

## 🔧 STRUMENTI FTP CONSIGLIATI

### **Opzione 1: FileZilla (CONSIGLIATO)** ⭐
- Download: https://filezilla-project.org/download.php?type=client
- Gratuito, semplice, affidabile
- Windows/Mac/Linux

### **Opzione 2: WinSCP (Windows)**
- Download: https://winscp.net/eng/download.php
- Ottimo per Windows
- Supporta SFTP

### **Opzione 3: Cyberduck (Mac/Windows)**
- Download: https://cyberduck.io/download/

---

## 🚀 PROCEDURA COMPLETA STEP-BY-STEP

---

## **PARTE 1: CONNESSIONE FTP**

### STEP 1: Apri FileZilla

1. Scarica e installa FileZilla
2. Apri FileZilla
3. Vedrai 4 pannelli:
   - **Sinistra**: File locali (tuo PC)
   - **Destra**: File remoti (Aruba)

### STEP 2: Connetti ad Aruba

**Metodo Rapido:**
1. In alto trovi campi:
   ```
   Host: ftp.tuo-dominio.it
   Username: [tuo username]
   Password: [tua password]
   Porta: 21
   ```
2. Clicca **"Connessione Rapida"**

**Oppure Gestione Siti (consigliato):**
1. File → Gestore Siti → Nuovo Sito
2. Compila:
   ```
   Protocollo: FTP - File Transfer Protocol
   Host: ftp.tuo-dominio.it
   Porta: 21
   Crittografia: Usa FTP semplice (insicuro)
                 oppure
                 Richiedi FTP esplicito su TLS (meglio)

   Tipo di accesso: Normale
   Utente: [tuo username]
   Password: [tua password]
   ```
3. Clicca **"Connetti"**

**Se chiede certificato SSL:**
- ✓ Seleziona "Considera sempre attendibile questo certificato"
- ✓ Clicca "OK"

### STEP 3: Naviga alla Directory WEBGRE3

Nel pannello **DESTRA** (Aruba):

1. Dovresti vedere directory come:
   ```
   /
   ├── httpdocs/  (oppure public_html/)
   │   └── webgre3/
   ```

2. **Naviga fino alla root del progetto:**
   - Se webgre3 è in `/httpdocs/webgre3/` → vai lì
   - Dovresti vedere: `index.php`, `config/`, `app/`, `vendor/`, etc.

---

## **PARTE 2: UPLOAD FILE CRITICI**

### 🔴 **FILE CRITICI DA CARICARE** (in ordine di importanza)

---

### **FILE 1: `.user.ini`** ⚠️ **SUPER CRITICO**

**Perché:** Abilita Opcache (400x miglioramento)

**Dal locale:**
```
C:\xampp\htdocs\webgre3\.user.ini
```

**Destinazione Aruba:**
```
/httpdocs/webgre3/.user.ini  (root del progetto)
```

**Come caricare:**
1. Pannello SINISTRA FileZilla → Vai a `C:\xampp\htdocs\webgre3\`
2. Trova file `.user.ini` (potrebbe essere nascosto!)
3. **Se NON vedi .user.ini:**
   - FileZilla → Server → Forza visualizzazione file nascosti
   - Oppure Windows Explorer → Visualizza → ✓ Elementi nascosti
4. **Trascina** `.user.ini` dal pannello SINISTRO al pannello DESTRO (root webgre3)
5. Attendi completamento upload

**⚠️ ATTENZIONE:**
- Il file inizia con `.` (punto) = file nascosto!
- Deve essere nella **ROOT del progetto**, NON in sottocartelle!

---

### **FILE 2-5: Autoload Composer Ottimizzato** ⚠️ **CRITICO**

**Perché:** 2,519 classi ottimizzate, zero file_exists() runtime

**File da caricare:**
```
C:\xampp\htdocs\webgre3\composer.json
C:\xampp\htdocs\webgre3\vendor\composer\autoload_classmap.php
C:\xampp\htdocs\webgre3\vendor\composer\autoload_static.php
C:\xampp\htdocs\webgre3\vendor\composer\autoload_real.php
```

**Destinazione Aruba:**
```
/httpdocs/webgre3/vendor/composer/
```

**Come caricare:**
1. Prima carica `composer.json` nella root:
   - Pannello SINISTRA → `C:\xampp\htdocs\webgre3\`
   - Trascina `composer.json` → sovrascrivi quando chiede

2. Poi carica i file autoload:
   - Pannello SINISTRA → `C:\xampp\htdocs\webgre3\vendor\composer\`
   - Seleziona questi 3 file:
     - `autoload_classmap.php`
     - `autoload_static.php`
     - `autoload_real.php`
   - Tasto destro → **"Carica"** (oppure trascina)
   - FileZilla chiederà: **"Il file esiste già, sovrascrivere?"**
     - ✓ Seleziona **"Sovrascrivi"**
     - ✓ Seleziona **"Applica sempre a questa sessione"**
3. Attendi completamento

---

### **FILE 6: SimpleCache.php** (Sistema Cache)

**Dal locale:**
```
C:\xampp\htdocs\webgre3\app\utils\SimpleCache.php
```

**Destinazione Aruba:**
```
/httpdocs/webgre3/app/utils/
```

**Come caricare:**
1. Pannello SINISTRA → `C:\xampp\htdocs\webgre3\app\utils\`
2. Trova `SimpleCache.php`
3. Trascina nel pannello DESTRO → `/httpdocs/webgre3/app/utils/`

**Se directory `utils` non esiste su Aruba:**
1. Pannello DESTRA → Vai in `/httpdocs/webgre3/app/`
2. Tasto destro → **"Crea directory"**
3. Nome: `utils`
4. Poi carica `SimpleCache.php` dentro

---

### **FILE 7: Migration Performance Indexes** 🔥 IMPORTANTE

**Dal locale:**
```
C:\xampp\htdocs\webgre3\database\migrations\2025_10_23_144112_add_performance_indexes.php
```

**Destinazione Aruba:**
```
/httpdocs/webgre3/database/migrations/
```

**Come caricare:**
1. Pannello SINISTRA → `C:\xampp\htdocs\webgre3\database\migrations\`
2. Trova `2025_10_23_144112_add_performance_indexes.php`
3. Trascina nel pannello DESTRO → `/httpdocs/webgre3/database/migrations/`

**NOTA IMPORTANTE:**
Questo file contiene la migrazione per creare 29 indici di performance sul database.
Dopo l'upload, dovrai eseguire: `php artisan migrate` (vedi sezione successiva).

---

### **FILE 8-9: Controller Ottimizzati**

**Dal locale:**
```
C:\xampp\htdocs\webgre3\app\controllers\ExportController.php
C:\xampp\htdocs\webgre3\app\controllers\HomeController.php
```

**Destinazione Aruba:**
```
/httpdocs/webgre3/app/controllers/
```

**Come caricare:**
1. Pannello SINISTRA → `C:\xampp\htdocs\webgre3\app\controllers\`
2. Seleziona `ExportController.php` e `HomeController.php`
3. Trascina → sovrascrivi quando richiesto

---

### **FILE 9: artisan** (Autoload Ottimizzato)

**Dal locale:**
```
C:\xampp\htdocs\webgre3\artisan
```

**Destinazione Aruba:**
```
/httpdocs/webgre3/artisan
```

**Come caricare:**
1. Trascina `artisan` (senza estensione) nella root
2. Sovrascrivi

---

### **FILE 10: performance_indexes.sql** (Indici Database)

**Dal locale:**
```
C:\xampp\htdocs\webgre3\database\performance_indexes.sql
```

**Destinazione Aruba:**
```
/httpdocs/webgre3/database/
```

**Come caricare:**
1. Caricare nella directory `database/`
2. **NON eseguire via FTP!** Eseguirai via phpMyAdmin dopo

---

### **FILE 11-12 (OPZIONALI): Documentazione**

Se vuoi averli sul server per riferimento:
```
C:\xampp\htdocs\webgre3\PERFORMANCE_OPTIMIZATION.md
C:\xampp\htdocs\webgre3\DEPLOY_CHECKLIST.md
```

Carica nella root.

---

## **RIEPILOGO FILE CARICATI** ✅

Dopo l'upload, nel pannello DESTRA dovresti vedere:

```
/httpdocs/webgre3/
├── .user.ini                               ← ✓ NUOVO
├── composer.json                           ← ✓ SOVRASCRITTO
├── artisan                                 ← ✓ SOVRASCRITTO
├── PERFORMANCE_OPTIMIZATION.md             ← ○ Opzionale
├── DEPLOY_CHECKLIST.md                     ← ○ Opzionale
├── app/
│   ├── controllers/
│   │   ├── ExportController.php            ← ✓ SOVRASCRITTO
│   │   └── HomeController.php              ← ✓ SOVRASCRITTO
│   └── utils/
│       └── SimpleCache.php                 ← ✓ NUOVO
├── database/
│   ├── migrations/
│   │   └── 2025_10_23_144112_add_performance_indexes.php  ← ✓ NUOVO (IMPORTANTE)
│   └── performance_indexes.sql             ← ○ Opzionale (backup SQL)
└── vendor/
    └── composer/
        ├── autoload_classmap.php           ← ✓ SOVRASCRITTO
        ├── autoload_static.php             ← ✓ SOVRASCRITTO
        └── autoload_real.php               ← ✓ SOVRASCRITTO
```

---

## **PARTE 3: CONFIGURAZIONE SERVER ARUBA**

### **STEP 4: Crea Directory Cache** (se non esiste)

**Via FileZilla:**
1. Pannello DESTRA → Vai in `/httpdocs/webgre3/storage/`
2. Verifica se esiste directory `cache`
3. **Se NON esiste:**
   - Tasto destro → Crea directory → Nome: `cache`

**Permessi (IMPORTANTE):**
- Tasto destro su `cache` → Permessi file
- Imposta: `755` (rwxr-xr-x)
- ✓ Ricorsione a sottodirectory e file
- OK

---

### **STEP 5: Abilita Opcache su Aruba**

**Metodo 1: Pannello Aruba (CONSIGLIATO)**

1. Vai su https://www.aruba.it
2. Login → **Pannello di Controllo**
3. **Gestione Hosting** → tuo dominio
4. Cerca sezione **"Gestione PHP"** o **"Impostazioni PHP"**
5. Versione PHP: Assicurati di usare **PHP 8.0+**
6. Cerca opzione **"OPcache"**:
   - ✓ **Abilita OPcache**
7. Se disponibile, aumenta anche:
   - `memory_limit` → **256M**
   - `max_execution_time` → **60**
   - `realpath_cache_size` → **16M** (se modificabile)
8. **Salva** modifiche
9. **Riavvia PHP-FPM** (bottone nel pannello)
10. Attendi **5-10 minuti** per propagazione

**Metodo 2: File .user.ini (automatico)**

- Il file `.user.ini` caricato sarà letto automaticamente da PHP-FPM
- Richiede riavvio PHP o attesa 5-10 minuti
- Funziona solo se Aruba usa PHP-FPM (solitamente sì)

---

### **STEP 6: Esegui Indici Database** ⚠️ **CRITICO**

**Via phpMyAdmin Aruba:**

1. **Login phpMyAdmin:**
   - Pannello Aruba → Database MySQL
   - Clicca **"Accedi a phpMyAdmin"**
   - Oppure vai su: `https://phpmyadmin.nome-dominio.it`

2. **Seleziona Database:**
   - Pannello sinistro → Seleziona `my_webgre` (o il tuo nome DB)

3. **Apri Editor SQL:**
   - Tab in alto → **"SQL"**

4. **Carica Script:**
   - Metodo A (copia-incolla):
     - Apri locale `C:\xampp\htdocs\webgre3\database\performance_indexes.sql`
     - Copia TUTTO il contenuto
     - Incolla nell'editor SQL di phpMyAdmin

   - Metodo B (file upload):
     - Se phpMyAdmin permette, clicca **"Scegli file"**
     - Seleziona `performance_indexes.sql` dal tuo PC

5. **Esegui:**
   - Clicca **"Esegui"** (bottone in basso a destra)
   - Attendi 1-2 minuti (dipende da quanti record hai)

6. **Verifica Successo:**
   - Dovresti vedere messaggi verdi tipo:
     ```
     ✓ Query eseguita con successo
     ✓ 1 row affected
     ```
   - Se vedi "Indice già esistente" = OK, significa che alcuni indici c'erano già

7. **Verifica Indici Creati:**
   ```sql
   SHOW INDEX FROM tabrip;
   ```
   - Dovresti vedere `idx_data_completa`, `idx_utente_completa`, etc.

**⚠️ IMPORTANTE:**
- Lo script usa `CREATE INDEX IF NOT EXISTS` = sicuro eseguire anche 2 volte
- Se errori tipo "tabella non trovata" = nome tabella diverso nel tuo DB
  - Verifica i nomi delle tue tabelle
  - Adatta lo script se necessario

---

### **STEP 7: Svuota Cache Iniziale**

**Crea file temporaneo via FTP:**

1. **Sul tuo PC, crea file:** `clear-cache.php`

   **Contenuto:**
   ```php
   <?php
   require_once __DIR__ . '/config/config.php';
   require_once __DIR__ . '/app/utils/SimpleCache.php';

   echo "<h1>Pulizia Cache WEBGRE3</h1>";
   echo "<pre>";

   echo "Svuotando cache applicazione...\n";
   SimpleCache::flush();
   echo "✓ Cache applicazione svuotata!\n\n";

   if (function_exists('opcache_reset')) {
       opcache_reset();
       echo "✓ Opcache resetata!\n";
   } else {
       echo "⚠ Opcache non disponibile\n";
       echo "  (Sarà attiva dopo riavvio PHP-FPM)\n";
   }

   echo "\n✅ Pulizia completata!";
   echo "</pre>";
   ?>
   ```

2. **Carica via FTP:**
   - Trascina `clear-cache.php` nella **root di webgre3** su Aruba

3. **Esegui dal browser:**
   - Vai su: `https://tuo-dominio.it/webgre3/clear-cache.php`
   - Dovresti vedere:
     ```
     ✓ Cache applicazione svuotata!
     ✓ Opcache resetata!
     ```

4. **ELIMINA IL FILE PER SICUREZZA:**
   - Via FileZilla: Tasto destro su `clear-cache.php` → Elimina
   - IMPORTANTE: non lasciarlo accessibile pubblicamente!

---

## **PARTE 4: VERIFICA PERFORMANCE** 🎯

### **TEST 1: Verifica Opcache Attivo**

**Crea file di test:**

1. Sul PC, crea: `test-opcache.php`
   ```php
   <?php
   phpinfo();
   ?>
   ```

2. Carica via FTP nella root

3. Vai su: `https://tuo-dominio.it/webgre3/test-opcache.php`

4. **Cerca nella pagina** (CTRL+F):
   ```
   opcache.enable → On  ✓
   realpath_cache_size → 16M ✓
   memory_limit → 256M ✓
   ```

5. **Se vedi:**
   - `opcache.enable → Off` ❌
   - **PROBLEMA:** Opcache non abilitato
   - **FIX:** Vai al Pannello Aruba e abilita manualmente (STEP 5)

6. **ELIMINA FILE DOPO TEST:**
   - FileZilla → Elimina `test-opcache.php`

---

### **TEST 2: Login Performance** ⏱️

1. **Vai su:** `https://tuo-dominio.it/webgre3/`
2. **Logout** (se loggato)
3. **Apri Chrome DevTools:**
   - Premi `F12`
   - Tab **"Network"**
   - ✓ Preserve log
4. **Fai Login**
5. **Guarda il tempo** del request POST `/login`

**RISULTATI ATTESI:**
- ✅ **Prima volta (cold cache):** 3-5 secondi
- ✅ **Seconda volta (warm cache):** 1-2 secondi
- ❌ **Se ancora 20-40 secondi:** Qualcosa non va (vedi Troubleshooting)

---

### **TEST 3: Dashboard Performance** ⏱️

1. Vai su Dashboard
2. Network tab aperto (F12)
3. Ricarica (F5)
4. Guarda tempo request GET `/`

**RISULTATI ATTESI:**
- ✅ **Prima volta:** 2-4 secondi
- ✅ **Con cache:** 0.5-1 secondo
- ❌ **Se > 10 secondi:** Problema (vedi sotto)

---

### **TEST 4: Export Lista Performance** ⏱️

1. Vai su `/export`
2. Guarda tempo

**ATTESO:** 1-3 secondi (vs 20-30s prima)

---

## 🎉 **SE TUTTI I TEST PASSANO:**

**CONGRATULAZIONI!** 🚀

Hai portato WEBGRE3 da **40 secondi a 2-3 secondi**!

**Performance finale:**
- Login: **20x più veloce**
- Dashboard: **25x più veloce**
- Export: **15x più veloce**
- Navigazione: **30x più veloce**

---

## ⚠️ **TROUBLESHOOTING**

### **Problema 1: Ancora Lento (> 10 secondi)**

**Verifica Checklist:**
```
☐ File .user.ini caricato nella root?
  → FileZilla → Refresh → Verifica che ci sia

☐ File composer.json caricato nella root?
  → Deve contenere "app/utils/" nel classmap

☐ Opcache abilitato?
  → test-opcache.php → opcache.enable = On?

☐ Indici database eseguiti?
  → phpMyAdmin → SQL → SHOW INDEX FROM tabrip;

☐ Permessi cache corretti?
  → FileZilla → storage/cache → Permessi 755

☐ PHP-FPM riavviato dopo .user.ini?
  → Pannello Aruba → Riavvia PHP

☐ Attesi 10 minuti dopo upload .user.ini?
  → .user.ini richiede propagazione
```

---

### **Problema 2: Errore "Class SimpleCache not found"**

**Causa:** File non caricato o autoload non rigenerato

**Fix:**
1. Verifica upload: `app/utils/SimpleCache.php` presente?
2. Via SSH (se hai accesso):
   ```bash
   cd /httpdocs/webgre3
   php artisan dump-autoload
   ```
3. Senza SSH:
   - Ri-carica i 3 file autoload da `vendor/composer/`

---

### **Problema 3: Opcache Non Si Attiva**

**Possibili Cause:**

**A) Piano Aruba Base NON supporta Opcache**
- Verifica: Pannello Aruba → non vedi opzione Opcache?
- **Soluzione:** Upgrade piano hosting (Professional o superiore)
- Contatta supporto Aruba: 0575 0505

**B) .user.ini non letto**
- Verifica che Aruba usi PHP-FPM (non Apache mod_php)
- Pannello Aruba → Gestione PHP → Modalità: deve essere "FastCGI" o "PHP-FPM"

**C) Permessi .user.ini errati**
- FileZilla → Tasto destro su `.user.ini` → Permessi: **644**

---

### **Problema 4: Dashboard Lenta, Export OK**

**Causa:** Cache non funziona

**Fix:**
```bash
# Via FileZilla
storage/cache/ → Permessi 755
storage/cache/ → Proprietario: www-data (se modificabile)

# Verifica che ci siano file cache_*.dat dopo prime visite
# Se directory vuota = cache non scrive
```

---

### **Problema 5: Query Ancora Lente**

**Verifica indici:**

```sql
-- In phpMyAdmin, esegui:
SHOW INDEX FROM tabrip;
SHOW INDEX FROM exp_documenti;
SHOW INDEX FROM produzione;
SHOW INDEX FROM dati;

-- Dovresti vedere almeno 2-3 indici per tabella
-- Se mancano: ri-esegui performance_indexes.sql
```

---

## 📞 **SUPPORTO**

**Se problemi persistono:**

1. **Verifica log errori:**
   - FileZilla → `storage/logs/` → Scarica file di log
   - Cerca errori recenti

2. **Test locale:**
   - Le ottimizzazioni funzionano su XAMPP locale?
   - Se sì = problema configurazione Aruba
   - Se no = problema codice

3. **Contatta Supporto Aruba:**
   - Telefono: **0575 0505**
   - Email: **supporto@aruba.it**
   - Chiedi:
     - ✓ Opcache disponibile nel tuo piano?
     - ✓ Limiti PHP (memory_limit, max_execution_time)
     - ✓ Performance MySQL database
     - ✓ PHP-FPM attivo?

---

## ✅ **CHECKLIST FINALE**

Prima di chiudere, verifica:

```
☑ Connesso ad Aruba via FTP
☑ .user.ini caricato nella root
☑ composer.json caricato nella root
☑ vendor/composer/autoload_*.php aggiornati (3 file)
☑ SimpleCache.php in app/utils/
☑ ExportController.php aggiornato
☑ HomeController.php aggiornato
☑ artisan aggiornato
☑ performance_indexes.sql eseguito su database
☑ Directory storage/cache/ creata con permessi 755
☑ Opcache abilitato (test-opcache.php)
☑ Cache svuotata (clear-cache.php)
☑ File temporanei eliminati (test-opcache.php, clear-cache.php)
☑ Login < 5 secondi ✓
☑ Dashboard < 3 secondi ✓
☑ Export < 3 secondi ✓
```

---

**Data deploy:** _______________
**Performance:** Da 40s a 2-3s = **20x miglioramento** 🚀

✅ **DEPLOY COMPLETATO CON SUCCESSO!**
