# CoreGRE API Documentation

Documentazione completa delle API REST del sistema CoreGRE.

## Accesso alla Documentazione

### Swagger UI Interattivo

Accedi alla documentazione interattiva Swagger UI:

```
http://localhost/api-docs
```

oppure in produzione:

```
https://your-domain.com/api-docs
```

### Download Specifiche OpenAPI

Scarica il file OpenAPI YAML:

```
http://localhost/openapi.yaml
```

### API Discovery

Ottieni informazioni sulle API disponibili:

```
GET http://localhost/api-docs/discovery
```

## Struttura delle API

### 1. Discovery & Health Check (Pubbliche)

Endpoints per verificare lo stato del sistema:

- `GET /api/discovery` - Informazioni di discovery della rete
- `GET /api/health` - Health check del sistema
- `GET /api/ping` - Ping semplice

**Esempio:**
```bash
curl http://localhost/api/health
```

### 2. Mobile API (Unificate)

API centralizzate per tutte le app mobile (Quality + Repairs).

#### Login Unificato
```
POST /api/mobile/login
```

**Richiesta (Get Users):**
```json
{
  "action": "get_users"
}
```

**Richiesta (Login):**
```json
{
  "action": "login",
  "username": "OP001",
  "password": "1234",
  "app_type": "quality"
}
```

**Header opzionale:**
```
X-App-Type: quality|repairs
```

#### Profilo Operatore
```
GET /api/mobile/profile?id=1
```

#### Riepilogo Giornaliero
```
GET /api/mobile/daily-summary?id=1&data=2025-01-15
```

#### Dati di Sistema
```
GET /api/mobile/system-data?type=all
```

Tipi supportati:
- `all` - Tutti i dati
- `reparti` - Solo reparti
- `linee` - Solo linee
- `taglie` - Solo taglie (richiede parametro `nu`)
- `quality` - Dati specifici per Quality
- `repairs` - Dati specifici per Repairs

#### Verifica Dati
```
POST /api/mobile/check-data
```

**Richiesta:**
```json
{
  "type": "cartellino",
  "value": "24123456"
}
```

o

```json
{
  "type": "commessa",
  "value": "COM123"
}
```

### 3. Quality API (Legacy - Retrocompatibilità)

API specifiche per il controllo qualità.

#### Check Cartellino
```
POST /api/quality/check-cartellino
```

**Richiesta:**
```json
{
  "cartellino": "24123456"
}
```

#### Dettagli Cartellino
```
POST /api/quality/cartellino-details
```

**Richiesta:**
```json
{
  "cartellino": "24123456"
}
```

#### Opzioni CQ
```
POST /api/quality/options
```

**Richiesta (opzionale):**
```json
{
  "cartellino": "24123456"
}
```

#### Salva Controllo Hermes
```
POST /api/quality/save-hermes-cq
```

**Richiesta:**
```json
{
  "numero_cartellino": "24123456",
  "reparto": "PROD",
  "operatore": "OP001",
  "tipo_cq": "interno",
  "paia_totali": 100,
  "cod_articolo": "ART001",
  "articolo": "Scarpa sportiva",
  "linea": "L01",
  "note": "Note controllo",
  "user": "OP001",
  "eccezioni": [
    {
      "taglia": "42",
      "tipo_difetto": "1",
      "note_operatore": "Graffio sulla tomaia",
      "fotoPath": null
    }
  ]
}
```

#### Riepilogo Giornaliero Operatore
```
GET /api/quality/operator-daily-summary?operatore=OP001&data=2025-01-15
```

#### Upload Foto Eccezione
```
POST /api/quality/upload-photo
```

**Form Data:**
- `photo` - File immagine (JPG, JPEG, PNG)
- `cartellino_id` - ID record cartellino
- `tipo_difetto` - ID tipo difetto
- `calzata` - Taglia (opzionale)
- `note` - Note (opzionale)

### 4. Riparazioni Interne API

API per la gestione delle riparazioni interne.

#### Lista Riparazioni
```
GET /api/riparazioni-interne?status=all&operatore=OP001
```

**Parametri Query:**
- `status` - complete|incomplete|all
- `operatore` - Username operatore
- `data` - Data (formato: YYYY-MM-DD)

#### Crea Riparazione
```
POST /api/riparazioni-interne
```

**Richiesta:**
```json
{
  "cartellino": "24123456",
  "commessa": "COM123",
  "causale": "Scollamento suola",
  "operatore": "OP001",
  "reparto": "RIP",
  "laboratorio": 1,
  "data": "2025-01-15",
  "taglie": {
    "P01": 2,
    "P02": 1,
    "P03": 0
  },
  "note": "Riparazione urgente"
}
```

#### Dettagli Riparazione
```
GET /api/riparazioni-interne/show?id=123
```

#### Aggiorna Riparazione
```
POST /api/riparazioni-interne/update
```

**Richiesta:**
```json
{
  "id": 123,
  "cartellino": "24123456",
  "causale": "Causale aggiornata",
  "note": "Note aggiornate"
}
```

#### Completa Riparazione
```
POST /api/riparazioni-interne/complete
```

**Richiesta:**
```json
{
  "id": 123
}
```

#### Elimina Riparazione
```
POST /api/riparazioni-interne/delete
```

**Richiesta:**
```json
{
  "id": 123
}
```

#### Statistiche
```
GET /api/riparazioni-interne/stats?operatore=OP001&periodo=today
```

**Parametri:**
- `operatore` - Username operatore
- `periodo` - today|week|month

### 5. Dashboard API

API per gestione dashboard e widgets.

#### Preferenze Dashboard
```
GET /api/dashboard/preferences
```

#### Statistiche Dashboard
```
GET /api/dashboard/stats
```

#### Attività Recenti
```
GET /api/dashboard/recent-activities
```

#### Widget Disponibili
```
GET /api/widgets/available
```

#### Widget Abilitati
```
GET /api/widgets/enabled
```

### 6. Search API

API per ricerca globale nel sistema.

```
GET /api/search?q=termine&type=all
```

**Parametri:**
- `q` - Query di ricerca (richiesto)
- `type` - all|cartellini|commesse|articoli|operatori

## Formato Risposte

Tutte le risposte API utilizzano il formato JSON.

### Risposta di Successo

```json
{
  "status": "success",
  "message": "Operazione completata con successo",
  "data": {
    // ... dati richiesti
  }
}
```

### Risposta di Errore

```json
{
  "status": "error",
  "message": "Descrizione errore",
  "code": "ERR_001"
}
```

## Codici di Stato HTTP

- `200` - OK - Operazione completata con successo
- `201` - Created - Risorsa creata con successo
- `400` - Bad Request - Richiesta non valida
- `401` - Unauthorized - Credenziali non valide
- `403` - Forbidden - Accesso negato
- `404` - Not Found - Risorsa non trovata
- `405` - Method Not Allowed - Metodo HTTP non consentito
- `422` - Unprocessable Entity - Errore di validazione
- `500` - Internal Server Error - Errore interno del server

## Autenticazione

### API Mobile

Le API mobile utilizzano autenticazione username/password tramite endpoint di login:

```
POST /api/mobile/login
```

Dopo il login, memorizza i dati utente restituiti per le successive chiamate.

### Header Personalizzati

Alcune API richiedono header specifici:

```
X-App-Type: quality|repairs
```

Questo header specifica quale tipo di app mobile sta chiamando l'API.

## Rate Limiting

Attualmente non è implementato nessun rate limiting.

## CORS

Le API supportano CORS per permettere chiamate da app mobile (Capacitor/Cordova).

## Esempi di Utilizzo

### JavaScript (Fetch API)

```javascript
// Login
const login = async (username, password) => {
  const response = await fetch('http://localhost/api/mobile/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-App-Type': 'quality'
    },
    body: JSON.stringify({
      action: 'login',
      username: username,
      password: password,
      app_type: 'quality'
    })
  });

  return await response.json();
};

// Get profilo
const getProfile = async (userId) => {
  const response = await fetch(`http://localhost/api/mobile/profile?id=${userId}`, {
    headers: {
      'X-App-Type': 'quality'
    }
  });

  return await response.json();
};
```

### cURL

```bash
# Login
curl -X POST http://localhost/api/mobile/login \
  -H "Content-Type: application/json" \
  -H "X-App-Type: quality" \
  -d '{
    "action": "login",
    "username": "OP001",
    "password": "1234",
    "app_type": "quality"
  }'

# Get profilo
curl http://localhost/api/mobile/profile?id=1 \
  -H "X-App-Type: quality"

# Salva controllo qualità
curl -X POST http://localhost/api/quality/save-hermes-cq \
  -H "Content-Type: application/json" \
  -d '{
    "numero_cartellino": "24123456",
    "reparto": "PROD",
    "operatore": "OP001",
    "tipo_cq": "interno",
    "paia_totali": 100,
    "cod_articolo": "ART001",
    "articolo": "Scarpa sportiva",
    "linea": "L01",
    "note": "Test controllo",
    "user": "OP001"
  }'
```

### Python (requests)

```python
import requests

# Login
def login(username, password):
    url = "http://localhost/api/mobile/login"
    headers = {
        "Content-Type": "application/json",
        "X-App-Type": "quality"
    }
    data = {
        "action": "login",
        "username": username,
        "password": password,
        "app_type": "quality"
    }

    response = requests.post(url, json=data, headers=headers)
    return response.json()

# Get profilo
def get_profile(user_id):
    url = f"http://localhost/api/mobile/profile?id={user_id}"
    headers = {"X-App-Type": "quality"}

    response = requests.get(url, headers=headers)
    return response.json()
```

## Testing

Usa Swagger UI per testare interattivamente tutte le API:

1. Apri `http://localhost/api-docs`
2. Espandi l'endpoint che vuoi testare
3. Clicca su "Try it out"
4. Compila i parametri richiesti
5. Clicca su "Execute"

## Supporto

Per supporto o segnalazione problemi:
- Email: support@coregre.local

## Changelog

### v1.0.0 (2025-01-15)
- Prima release della documentazione API
- API Mobile unificate
- API Quality (legacy)
- API Riparazioni Interne
- Dashboard API
- Discovery & Health endpoints
